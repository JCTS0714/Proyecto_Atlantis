
    $('.sidebar-menu').tree()

    $('#example2').DataTable({
        "language": {

		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
		"sFirst":    "Primero",
		"sLast":     "Último",
		"sNext":     "Siguiente",
		"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
    }
});

// Centralizar inicialización para tablas de contacto si existen
function initContactTable(tableId) {
	if ($('#' + tableId).length) {
		var $table = $('#' + tableId);
		var ajaxUrl = $table.data('ajax') || null;
		var dtOptions = {
			"language": {
				"sProcessing":     "Procesando...",
				"sLengthMenu":     "Mostrar _MENU_ registros",
				"sZeroRecords":    "No se encontraron resultados",
				"sEmptyTable":     "Ningún dato disponible en esta tabla",
				"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
				"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
				"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
				"sInfoPostFix":    "",
				"sSearch":         "Buscar:",
				"sUrl":            "",
				"sInfoThousands":  ",",
				"sLoadingRecords": "Cargando...",
				"oPaginate": {
					"sFirst":    "Primero",
					"sLast":     "Último",
					"sNext":     "Siguiente",
					"sPrevious": "Anterior"
				},
				"oAria": {
					"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
					"sSortDescending": ": Activar para ordenar la columna de manera descendente"
				}
			},
			"responsive": true,
			"autoWidth": false
		};

		if (ajaxUrl) {
			// server-side processing
			dtOptions.serverSide = true;
			dtOptions.processing = true;
			dtOptions.ajax = {
				url: ajaxUrl,
				type: 'GET',
				data: function(d) {
					// merge last known advanced filters for this table
					var filters = window._advancedFilters && window._advancedFilters[tableId] ? window._advancedFilters[tableId] : {};
					return $.extend({}, d, filters);
				}
			};
		}

		// store instance
		var table = $table.DataTable(dtOptions);
		if (ajaxUrl) {
			// keep a reference for later reloads
			if (!window._serverTables) window._serverTables = {};
			window._serverTables[tableId] = table;
		}
	}
}

// Inicializar tablas de contactos comunes
initContactTable('tablaClientes');
initContactTable('tablaSeguimiento');
initContactTable('tablaNoClientes');
initContactTable('tablaZonaEspera');

// Advanced Search integration for DataTables
(function(){
	// helper: parse YYYY-MM-DD or other common formats to Date
	function parseDateString(str){
		if(!str) return null;
		// Try ISO first
		var d = new Date(str);
		if(!isNaN(d)) return d;
		// Try dd-mm-yyyy or dd/mm/yyyy
		var m = str.match(/(\d{2})[-\/](\d{2})[-\/](\d{4})/);
		if(m){ return new Date(m[3], parseInt(m[2],10)-1, m[1]); }
		return null;
	}

	function applyFiltersToTable(tableId, filters){
		if(!$.fn.DataTable.isDataTable('#'+tableId)) return;
		var table = $('#'+tableId).DataTable();

		// Column indexes for tablaClientes (and similar): Nombre=1, Documento=3, Telefono=4, FechaCreacion=11
		try {
			table.column(1).search(filters.nombre || '', true, false);
			table.column(3).search(filters.documento || '', true, false);
			table.column(4).search(filters.telefono || '', true, false);
		} catch(e){
			// ignore if columns don't match
		}

		// date filter using DataTables ext.search
		var periodo = (filters && filters.periodo) ? filters.periodo : '';
		var fechaInicio = filters && filters.fecha_inicio ? parseDateString(filters.fecha_inicio) : null;
		var fechaFin = filters && filters.fecha_fin ? parseDateString(filters.fecha_fin) : null;

		// push a custom filter that checks the specific table only
		$.fn.dataTable.ext.search.push(function(settings, data){
			if(settings.nTable.id !== tableId) return true; // only affect this table
			if(!periodo) return true;

			var fechaStr = data[11] || data[10] || ''; // try Fecha Creacion or Fecha Contacto
			var fecha = parseDateString(fechaStr);
			if(!fecha) return true;

			var today = new Date();
			today.setHours(0,0,0,0);
			var start, end;
			if(periodo === 'today'){
				start = new Date(today);
				end = new Date(today);
			} else if(periodo === 'this_week'){
				var day = today.getDay();
				start = new Date(today);
				start.setDate(today.getDate() - day + 1);
				end = new Date(start);
				end.setDate(start.getDate() + 6);
			} else if(periodo === 'this_month'){
				start = new Date(today.getFullYear(), today.getMonth(), 1);
				end = new Date(today.getFullYear(), today.getMonth()+1, 0);
			} else if(periodo === 'custom' && fechaInicio && fechaFin){
				start = fechaInicio; end = fechaFin;
			} else {
				return true;
			}

			// normalize
			start.setHours(0,0,0,0); end.setHours(23,59,59,999);
			return fecha >= start && fecha <= end;
		});

		table.draw();
	}

	// listen for apply/clear events
	window.addEventListener('advancedSearch:apply', function(e){
		var filters = (e && e.detail) ? e.detail : {};
		// store global filters map
		if (!window._advancedFilters) window._advancedFilters = {};

		['tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera'].forEach(function(id){
			// If server-side table exists, set filters and reload via ajax
			if (window._serverTables && window._serverTables[id]) {
				window._advancedFilters[id] = filters;
				try { window._serverTables[id].ajax.reload(null, false); } catch(err) { console.warn('reload failed', err); }
			} else {
				applyFiltersToTable(id, filters);
			}
		});
	});

	window.addEventListener('advancedSearch:clear', function(){
		// Clear column searches and redraw or reload server tables
		['tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera'].forEach(function(id){
			if (window._serverTables && window._serverTables[id]) {
				window._advancedFilters[id] = {};
				try { window._serverTables[id].ajax.reload(null, false); } catch(e){ console.warn('reload failed', e); }
			} else if ($.fn.DataTable.isDataTable('#'+id)){
				var t = $('#'+id).DataTable();
				try{ t.columns().search('').draw(); } catch(e){ t.search('').draw(); }
			}
		});
	});

})();
