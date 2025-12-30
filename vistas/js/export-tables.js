/**
 * =====================================================
 * MÓDULO DE EXPORTACIÓN DE TABLAS A EXCEL/CSV
 * =====================================================
 * 
 * Versión: 1.0.0
 * Fecha: 2025-12-10
 * 
 * Proporciona funcionalidad de exportación para DataTables
 * Compatible con server-side processing y filtros avanzados
 * 
 * Dependencias:
 * - jQuery
 * - DataTables
 * - DataTables Buttons
 * - JSZip (para Excel)
 * 
 * @namespace ExportTables
 */

var ExportTables = (function($) {
  'use strict';

  // =====================================================
  // CONFIGURACIÓN GENERAL
  // =====================================================

  var config = {
    debug: false,
    defaultExportOptions: {
      columns: ':visible:not(.no-export)',
      orthogonal: 'export',
      format: {
        body: function(data, row, column, node) {
          // Limpiar HTML de los datos
          if (typeof data === 'string') {
            // Remover tags HTML
            var tmp = document.createElement('DIV');
            tmp.innerHTML = data;
            data = tmp.textContent || tmp.innerText || '';
            
            // Limpiar espacios extra
            data = data.trim();
          }
          return data;
        }
      }
      ,
      // Ensure client-side exports only include applied search/order by default
      modifier: {
        search: 'applied',
        order: 'applied',
        page: 'all'
      }
    }
  };

  // =====================================================
  // CONFIGURACIONES ESPECÍFICAS POR TABLA
  // =====================================================

  var tableConfigs = {
    'tablaClientes': {
      filename: 'Clientes_Oportunidades',
      title: 'Listado de Clientes (Oportunidades)',
      serverSide: true,
      messageTop: function() {
        return 'Fecha de exportación: ' + new Date().toLocaleDateString('es-PE');
      }
    },
    'tablaSeguimiento': {
      filename: 'Seguimiento_Prospectos',
      title: 'Seguimiento de Prospectos',
      serverSide: true
    },
    'tablaNoClientes': {
      filename: 'No_Clientes',
      title: 'Listado de No Clientes',
      serverSide: true
    },
    'tablaZonaEspera': {
      filename: 'Zona_Espera',
      title: 'Zona de Espera',
      serverSide: true
    },
    'example2': {
      filename: 'Prospectos',
      title: 'Listado de Prospectos',
      serverSide: true
    },
    'tablaProspectos': {
      filename: 'Prospectos',
      title: 'Listado de Prospectos',
      serverSide: true
    },
    'tablaContadores': {
      filename: 'Contadores',
      title: 'Listado de Contadores',
      serverSide: false
    },
    'tablaIncidencias': {
      filename: 'Incidencias',
      title: 'Listado de Incidencias',
      serverSide: false
    },
    'tablaUsuarios': {
      filename: 'Usuarios',
      title: 'Listado de Usuarios',
      serverSide: false
    },
    'tablaArchivadas': {
      filename: 'Reuniones_Archivadas',
      title: 'Reuniones Archivadas',
      serverSide: false
    },
    'tablaReunionesPasadas': {
      filename: 'Reuniones_Pasadas',
      title: 'Reuniones Pasadas',
      serverSide: false
    }
  };

  // =====================================================
  // FUNCIONES PRIVADAS
  // =====================================================

  /**
   * Registra mensajes de debug
   * @private
   */
  function log() {
    if (config.debug && window.console) {
      console.log('[ExportTables]', ...arguments);
    }
  }

  /**
   * Obtiene la configuración de una tabla específica
   * @private
   * @param {string} tableId - ID de la tabla
   * @returns {Object} Configuración de la tabla
   */
  function getTableConfig(tableId) {
    return tableConfigs[tableId] || {
      filename: tableId,
      title: 'Exportación',
      serverSide: false
    };
  }

  /**
   * Genera el nombre de archivo con fecha
   * @private
   * @param {string} baseFilename - Nombre base del archivo
   * @returns {string} Nombre de archivo con fecha
   */
  function generateFilename(baseFilename, tableId) {
    var date = new Date();
    var dateStr = date.getFullYear() + 
                  ('0' + (date.getMonth() + 1)).slice(-2) + 
                  ('0' + date.getDate()).slice(-2);

    // Determinar si hay filtros activos para la tabla específica
    var hasFilters = false;
    try{
      if (tableId) {
        // check advanced filters
        if (window._advancedFilters && window._advancedFilters[tableId] && Object.keys(window._advancedFilters[tableId]).length > 0) {
          hasFilters = true;
        }

        // check datatable global search or column search
        var tbl = $('#' + tableId);
        if (tbl && tbl.length && $.fn.DataTable && $.fn.DataTable.isDataTable('#' + tableId)) {
          var dt = tbl.DataTable();
          var globalSearch = (dt.search && dt.search()) ? dt.search().toString().trim() : '';
          if (globalSearch) hasFilters = true;
          try{
            dt.columns().every(function(){
              var s = this.search && this.search();
              if (s && String(s).trim()) { hasFilters = true; }
            });
          }catch(e){ /* ignore */ }
        }
      } else {
        // fallback: check any advanced filters
        if (window._advancedFilters) {
          for (var key in window._advancedFilters) {
            if (window._advancedFilters[key] && Object.keys(window._advancedFilters[key]).length > 0) { hasFilters = true; break; }
          }
        }
      }
    }catch(e){ /* ignore errors */ }

    return baseFilename + '_' + dateStr + (hasFilters ? '_Filtrado' : '');
  }

  /**
   * Crea la configuración de botones para una tabla
   * @private
   * @param {string} tableId - ID de la tabla
   * @param {Object} customConfig - Configuración personalizada
   * @returns {Array} Array de configuración de botones
   */
  function createButtonsConfig(tableId, customConfig) {
    var tableConfig = getTableConfig(tableId);
    var exportOptions = $.extend({}, config.defaultExportOptions, customConfig.exportOptions || {});
    
    // Determinar si incluir PDF (solo para tablaClientes y tablaContadores)
    var includePDF = (tableId === 'tablaClientes' || tableId === 'tablaContadores');
    // Determinar si incluir Print (solo para tablaClientes)
    var includePrint = (tableId === 'tablaClientes');

    var buttons = [
      {
        extend: 'excelHtml5',
        text: '<i class="fa fa-file-excel-o"></i> Excel',
        className: 'btn btn-success btn-sm export-btn',
        title: tableConfig.title,
        filename: function() {
          return generateFilename(tableConfig.filename, tableId);
        },
        exportOptions: exportOptions,
        messageTop: tableConfig.messageTop ? tableConfig.messageTop() : null,
        customize: function(xlsx) {
          // Personalización del archivo Excel
          var sheet = xlsx.xl.worksheets['sheet1.xml'];
          
          // Aplicar estilos a las cabeceras
          $('row:first c', sheet).attr('s', '2');
          
          // Auto-ajustar ancho de columnas (aproximado)
          var col = $('col', sheet);
          col.each(function() {
            $(this).attr('width', 20);
          });
        }
      },
      {
        extend: 'csvHtml5',
        text: '<i class="fa fa-file-text-o"></i> CSV',
        className: 'btn btn-info btn-sm export-btn',
        title: tableConfig.title,
        filename: function() {
          return generateFilename(tableConfig.filename, tableId);
        },
        exportOptions: exportOptions,
        bom: true
      },
      {
        extend: 'copyHtml5',
        text: '<i class="fa fa-copy"></i> Copiar',
        className: 'btn btn-default btn-sm export-btn',
        title: tableConfig.title,
        exportOptions: exportOptions,
        success: function() {
          // Notificación de éxito
          if (window.Swal) {
            Swal.fire({
              icon: 'success',
              title: 'Copiado',
              text: 'Los datos se han copiado al portapapeles',
              timer: 2000,
              showConfirmButton: false
            });
          } else {
            alert('Datos copiados al portapapeles');
          }
        }
      }
    ];
    
    // Agregar botón PDF solo para clientes y contadores
    if (includePDF) {
      buttons.push({
        extend: 'pdfHtml5',
        text: '<i class="fa fa-file-pdf-o"></i> PDF',
        className: 'btn btn-danger btn-sm export-btn',
        title: tableConfig.title,
        filename: function() {
          return generateFilename(tableConfig.filename);
        },
        exportOptions: exportOptions,
        orientation: 'landscape',
        pageSize: 'LEGAL',
        customize: function(doc) {
          // Personalización del PDF
          doc.defaultStyle.fontSize = 9;
          doc.styles.tableHeader.fontSize = 10;
          doc.styles.tableHeader.fillColor = '#3c8dbc';
          doc.styles.tableHeader.alignment = 'left';
          
          // Agregar fecha de exportación
          if (tableConfig.messageTop) {
            doc.content.splice(1, 0, {
              text: tableConfig.messageTop(),
              style: 'subheader',
              margin: [0, 5, 0, 10]
            });
          }
        }
      });
    }
    
    // Agregar botón Print solo para clientes y contadores
    if (includePrint) {
      buttons.push({
        extend: 'print',
        text: '<i class="fa fa-print"></i> Imprimir',
        className: 'btn btn-primary btn-sm export-btn',
        title: tableConfig.title,
        exportOptions: exportOptions,
        customize: function(win) {
          // Personalizar la ventana de impresión
          $(win.document.body)
            .css('font-size', '10pt')
            .prepend(
              '<div style="text-align:center; margin-bottom:20px;">' +
              '<h2>' + tableConfig.title + '</h2>' +
              '<p>Fecha: ' + new Date().toLocaleDateString('es-PE') + '</p>' +
              '</div>'
            );

          $(win.document.body).find('table')
            .addClass('compact')
            .css('font-size', 'inherit');
        }
      });
    }

    return buttons;
  }

  /**
   * Exporta datos de tabla con server-side processing
   * @private
   * @param {string} tableId - ID de la tabla
   * @param {string} format - Formato de exportación (excel, csv, etc)
   */
  function exportServerSideData(tableId, format) {
    log('Exportando datos server-side para', tableId, 'formato:', format);

    // Mostrar loader
    if (window.Swal) {
      Swal.fire({
        title: 'Exportando...',
        text: 'Por favor espere mientras se preparan los datos',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: function() {
          Swal.showLoading();
        }
      });
    }

    // Obtener filtros actuales
    var filters = {};
    if (window._advancedFilters && window._advancedFilters[tableId]) {
      filters = window._advancedFilters[tableId];
    }

    // Obtener búsqueda global y detectar si existen filtros activos (global/column/advanced)
    var table = $('#' + tableId).DataTable();
    var search = '';
    var onlyFiltered = false;
    try{
      if (table && table.search) {
        search = table.search() || '';
        if (String(search).trim()) onlyFiltered = true;
      }
      // check column searches
      try{
        table.columns().every(function(){
          var s = this.search && this.search();
          if (s && String(s).trim()) { onlyFiltered = true; }
        });
      }catch(e){ /* ignore */ }
      // advanced filters
      if (window._advancedFilters && window._advancedFilters[tableId] && Object.keys(window._advancedFilters[tableId]).length > 0) {
        onlyFiltered = true;
      }
    }catch(e){ /* ignore */ }

    // Hacer request AJAX para obtener todos los datos (o sólo los filtrados según onlyFiltered)
    $.ajax({
      url: 'ajax/export-data.ajax.php',
      method: 'POST',
      data: {
        tabla: tableId,
        filters: filters,
        search: search,
        onlyFiltered: onlyFiltered ? 1 : 0
      },
      dataType: 'json',
      success: function(response) {
        log('Datos recibidos:', response);

        if (response.error) {
          if (window.Swal) {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: response.error
            });
          } else {
            alert('Error: ' + response.error);
          }
          return;
        }

        // Crear tabla temporal con todos los datos
        var tempTableId = tableId + '_temp_export';
        var $tempTable = $('<table id="' + tempTableId + '" style="display:none;"></table>');
        
        // Copiar estructura de cabeceras
        var $originalTable = $('#' + tableId);
        var $thead = $originalTable.find('thead').clone();
        $tempTable.append($thead);

        // Agregar datos
        var $tbody = $('<tbody></tbody>');
        if (response.data && response.data.length > 0) {
          response.data.forEach(function(row) {
            var $tr = $('<tr></tr>');
            if (Array.isArray(row)) {
              row.forEach(function(cell) {
                $tr.append('<td>' + (cell || '') + '</td>');
              });
            }
            $tbody.append($tr);
          });
        }
        $tempTable.append($tbody);

        // Agregar tabla temporal al DOM
        $('body').append($tempTable);

        // Inicializar DataTable en tabla temporal (sin paginación)
        var tempDT = $tempTable.DataTable({
          paging: false,
          searching: false,
          info: false,
          buttons: createButtonsConfig(tableId, {})
        });

        // Ejecutar exportación del formato solicitado
        setTimeout(function() {
          var buttonIndex = format === 'excel' ? 0 : 
                           format === 'csv' ? 1 : 
                           format === 'copy' ? 2 : 0;
          
          tempDT.button(buttonIndex).trigger();

          // Limpiar tabla temporal
          setTimeout(function() {
            tempDT.destroy();
            $tempTable.remove();
            
            if (window.Swal) {
              Swal.close();
            }
          }, 500);
        }, 300);
      },
      error: function(xhr, status, error) {
        log('Error en exportación:', error);
        
        if (window.Swal) {
          Swal.fire({
            icon: 'error',
            title: 'Error de Exportación',
            text: 'No se pudieron obtener los datos. Por favor intente nuevamente.'
          });
        } else {
          alert('Error al exportar datos');
        }
      }
    });
  }

  // =====================================================
  // API PÚBLICA
  // =====================================================

  return {
    /**
     * Inicializa la exportación para una tabla específica
     * @public
     * @param {string} tableId - ID de la tabla
     * @param {Object} customConfig - Configuración personalizada (opcional)
     */
    init: function(tableId, customConfig) {
      customConfig = customConfig || {};
      
      log('Inicializando exportación para', tableId);

      // Verificar que jQuery DataTables esté disponible
      if (!$.fn.DataTable) {
        console.error('DataTables no está cargado');
        return;
      }

      // Verificar que DataTables Buttons esté disponible
      if (!$.fn.DataTable.Buttons) {
        console.error('DataTables Buttons no está cargado');
        return;
      }

      // Verificar que la tabla exista y esté inicializada como DataTable
      if (!$('#' + tableId).length) {
        log('Tabla no encontrada:', tableId);
        return;
      }

      if (!$.fn.DataTable.isDataTable('#' + tableId)) {
        log('Tabla no es DataTable aún:', tableId);
        return;
      }

      var table = $('#' + tableId).DataTable();
      var tableConfig = getTableConfig(tableId);

      // Verificar si ya tiene botones
      if (table.buttons().count() > 0) {
        log('La tabla ya tiene botones configurados:', tableId);
        return;
      }

      // Crear botones
      var buttons = createButtonsConfig(tableId, customConfig);

      // Agregar botones a la tabla
      try {
        table.buttons().destroy();
        new $.fn.dataTable.Buttons(table, {
          buttons: buttons
        });

        // Insertar botones en el contenedor personalizado
        var containerId = 'export-' + tableId;
        if ($('#' + containerId).length) {
          table.buttons().container().appendTo('#' + containerId);
          log('Botones agregados al contenedor:', containerId);
        } else {
          // Si no hay contenedor, agregar antes de la tabla
          table.buttons().container().prependTo($('#' + tableId + '_wrapper .col-sm-6:eq(0)'));
          log('Botones agregados al wrapper de tabla');
        }

        log('Exportación inicializada exitosamente para', tableId);
      } catch (error) {
        console.error('Error al inicializar botones para', tableId, error);
      }
    },

    /**
     * Inicializa exportación para todas las tablas DataTables en la página
     * @public
     */
    initAll: function() {
      log('Inicializando exportación para todas las tablas');

      var self = this;
      
      // Esperar a que todas las tablas estén inicializadas
      setTimeout(function() {
        $('.tabla, .tablas, table.dataTable').each(function() {
          var tableId = $(this).attr('id');
          if (tableId && $.fn.DataTable.isDataTable('#' + tableId)) {
            self.init(tableId);
          }
        });
      }, 1000);
    },

    /**
     * Exporta datos respetando filtros actuales (para server-side)
     * @public
     * @param {string} tableId - ID de la tabla
     * @param {string} format - Formato: 'excel', 'csv', 'copy'
     */
    exportWithFilters: function(tableId, format) {
      format = format || 'excel';
      var tableConfig = getTableConfig(tableId);

      if (tableConfig.serverSide) {
        exportServerSideData(tableId, format);
      } else {
        // Para tablas sin server-side, usar exportación normal
        var table = $('#' + tableId).DataTable();
        var buttonIndex = format === 'excel' ? 0 : 
                         format === 'csv' ? 1 : 
                         format === 'copy' ? 2 : 0;
        
        if (table.button(buttonIndex)) {
          table.button(buttonIndex).trigger();
        }
      }
    },

    /**
     * Habilita modo debug
     * @public
     */
    enableDebug: function() {
      config.debug = true;
      log('Modo debug habilitado');
    },

    /**
     * Obtiene la configuración actual
     * @public
     * @returns {Object} Configuración actual
     */
    getConfig: function() {
      return config;
    },

    /**
     * Registra una configuración personalizada para una tabla
     * @public
     * @param {string} tableId - ID de la tabla
     * @param {Object} customConfig - Configuración personalizada
     */
    registerTableConfig: function(tableId, customConfig) {
      tableConfigs[tableId] = $.extend({}, tableConfigs[tableId] || {}, customConfig);
      log('Configuración registrada para', tableId);
    }
  };

})(jQuery);

// =====================================================
// AUTO-INICIALIZACIÓN
// =====================================================

// Inicializar automáticamente cuando el DOM esté listo
$(document).ready(function() {
  // Esperar a que DataTables se inicialice completamente
  setTimeout(function() {
    if (window.ExportTables && typeof window.ExportTables.initAll === 'function') {
      // No auto-inicializar todas, dejar que plantilla.js lo haga por tabla
      // ExportTables.initAll();
    }
  }, 2000);
});
