
	// Silence debug/info logs unless `window.PLANTILLA_DEV === true`
	// Set `window.PLANTILLA_DEV = true` in console for temporary local debugging.
	if (!(window.PLANTILLA_DEV === true)) {
		if (window.console) {
			console.debug = function(){};
			console.info = function(){};
		}
	}

$(document).ready(function() {
	console.log('plantilla.js: $(document).ready ejecutado');
	
	// Global AJAX error logger: helps surface server response bodies for 500s/HTML errors
	$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
		try {
			console.error('Global AJAX error:', settings && settings.url, thrownError, jqxhr && jqxhr.responseText);
			if (jqxhr && jqxhr.responseText) {
				try { var parsed = JSON.parse(jqxhr.responseText); console.error('Global AJAX server JSON:', parsed); } catch(e) { /* not JSON */ }
			}
		} catch(e) { console.warn('Error logging global AJAX error', e); }
	});

	if (typeof $.fn.tree === 'function') {
		$('.sidebar-menu').tree();
	}

	// Tabla de Prospectos (#example2) con opciones completas de paginación
	console.log('plantilla.js: Buscando #example2, encontrado:', $('#example2').length);
	if ($('#example2').length) {
		// Verificar si ya está inicializado
		if ($.fn.DataTable.isDataTable('#example2')) {
			console.log('plantilla.js: #example2 ya está inicializado como DataTable');
		} else {
			console.log('plantilla.js: Inicializando #example2 como DataTable');
			try {
				$('#example2').DataTable({
					"responsive": true,
					"autoWidth": false,
					"pageLength": 10,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
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
				console.log('plantilla.js: #example2 inicializado correctamente');
			} catch(e) {
				console.error('plantilla.js: Error inicializando #example2:', e);
			}
		}
	}

// ============================================
// INICIALIZACIÓN SIMPLE DE TODAS LAS TABLAS
// Replicando exactamente el patrón que funciona
// ============================================

var dtLanguage = {
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
};

var dtOptions = {
	"responsive": true,
	"autoWidth": false,
	"pageLength": 10,
	"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
	"language": dtLanguage
};

// Lista de todas las tablas a inicializar
var tablasContactos = [
	'tablaProspectos',
	'tablaClientes', 
	'tablaSeguimiento',
	'tablaNoClientes',
	'tablaZonaEspera',
	'tablaContadores',
	'tablaVentas'
];

// NO agregar tablaIncidencias aquí - se inicializa en incidencias.js después de cargar datos
// tablasContactos.push('tablaIncidencias');

tablasContactos.forEach(function(tableId) {
	var $tabla = $('#' + tableId);
	if ($tabla.length > 0) {
		if (!$.fn.DataTable.isDataTable('#' + tableId)) {
			console.log('Inicializando DataTable: ' + tableId);
			try {
				$tabla.DataTable(dtOptions);
				console.log(tableId + ' inicializado OK');
			} catch(e) {
				console.error('Error inicializando ' + tableId + ':', e);
			}
		}
	}
});

}); // Fin de $(document).ready()

// Advanced Search integration for DataTables
(function(){
	// helper: parse YYYY-MM-DD or other common formats to Date
	function parseDateString(str){
		if(!str) return null;
		str = String(str).trim();
		// YYYY-MM-DD[ HH:MM:SS]
		var m = str.match(/^(\d{4})-(\d{2})-(\d{2})(?:[ T](\d{2}):(\d{2}):(\d{2}))?/);
		if(m){
			var y = parseInt(m[1],10), mo = parseInt(m[2],10)-1, da = parseInt(m[3],10);
			var hh = m[4] ? parseInt(m[4],10) : 0;
			var mm = m[5] ? parseInt(m[5],10) : 0;
			var ss = m[6] ? parseInt(m[6],10) : 0;
			return new Date(y, mo, da, hh, mm, ss);
		}
		// Try dd-mm-yyyy or dd/mm/yyyy
		m = str.match(/^(\d{2})[-\/](\d{2})[-\/](\d{4})(?:[ T](\d{2}):(\d{2}):(\d{2}))?/);
		if(m){ return new Date(parseInt(m[3],10), parseInt(m[2],10)-1, parseInt(m[1],10), m[4]?parseInt(m[4],10):0, m[5]?parseInt(m[5],10):0, m[6]?parseInt(m[6],10):0); }
		// Fallback to Date parse
		var d = new Date(str);
		if(!isNaN(d)) return d;
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
		var mesUnico = filters && filters.mes_unico ? filters.mes_unico : '';
		var mesDesde = filters && filters.mes_desde ? filters.mes_desde : '';
		var mesHasta = filters && filters.mes_hasta ? filters.mes_hasta : '';
		var fechaUnica = filters && filters.fecha_unica ? parseDateString(filters.fecha_unica) : null;
		var fechaInicio = filters && filters.fecha_inicio ? parseDateString(filters.fecha_inicio) : null;
		var fechaFin = filters && filters.fecha_fin ? parseDateString(filters.fecha_fin) : null;

		// remove previous filter fn for this table if exists to avoid stacking
		if(!window._dtFilterFns) window._dtFilterFns = {};
		if(window._dtFilterFns[tableId]){
			// remove by reference
			$.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn){ return fn !== window._dtFilterFns[tableId]; });
			window._dtFilterFns[tableId] = null;
		}

		if(!periodo){
			// nothing to filter by date; redraw and exit
			table.draw();
			return;
		}

		// create filter function for this table
		var filterFn = function(settings, data){
			if(settings.nTable.id !== tableId) return true; // only affect this table

			var fechaStr = data[11] || data[10] || data[9] || ''; // try several positions
			var fecha = parseDateString(fechaStr);
			if(!fecha) return true;

			var today = new Date();
			// normalize today's date
			today.setHours(0,0,0,0);
			var start, end;
			
			// Nuevos filtros de periodo
			if(periodo === 'por_mes' && mesUnico){
				// mesUnico formato: YYYY-MM
				var parts = mesUnico.split('-');
				var year = parseInt(parts[0]);
				var month = parseInt(parts[1]) - 1; // 0-indexed
				start = new Date(year, month, 1);
				end = new Date(year, month + 1, 0); // último día del mes
			} else if(periodo === 'entre_meses' && mesDesde && mesHasta){
				var partsDesde = mesDesde.split('-');
				var partsHasta = mesHasta.split('-');
				start = new Date(parseInt(partsDesde[0]), parseInt(partsDesde[1]) - 1, 1);
				end = new Date(parseInt(partsHasta[0]), parseInt(partsHasta[1]), 0); // último día del mes hasta
			} else if(periodo === 'por_fecha' && fechaUnica){
				start = new Date(fechaUnica);
				end = new Date(fechaUnica);
			} else if(periodo === 'entre_fechas' && fechaInicio && fechaFin){
				start = fechaInicio; end = fechaFin;
			} else if(periodo === 'today'){
				// Compatibilidad con valores antiguos
				start = new Date(today);
				end = new Date(today);
			} else if(periodo === 'this_week'){
				// week Monday..Sunday
				var day = today.getDay(); // 0=Sun..6=Sat
				var diffToMonday = (day + 6) % 7; // 0 for Monday
				start = new Date(today);
				start.setDate(today.getDate() - diffToMonday);
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

			// normalize boundaries inclusive
			start.setHours(0,0,0,0); end.setHours(23,59,59,999);
			return fecha >= start && fecha <= end;
		};

		// push and remember
		$.fn.dataTable.ext.search.push(filterFn);
		window._dtFilterFns[tableId] = filterFn;

		// redraw
		table.draw();

		table.draw();
	}

	// listen for apply/clear events
	window.addEventListener('advancedSearch:apply', function(e){
		var filters = (e && e.detail) ? e.detail : {};
		console.log('plantilla: advancedSearch:apply received', filters);
		// store global filters map
		if (!window._advancedFilters) window._advancedFilters = {};

		// Tablas que necesitan búsqueda server-side (tienen filtro de servidor)
		var serverSideTables = ['tablaClientes', 'tablaContadores'];

		['tablaProspectos','tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera','example2'].forEach(function(id){
			// Si es una tabla que necesita server-side (por filtro de servidor), usar AJAX
			if (serverSideTables.indexOf(id) !== -1 && (filters.servidor || filters.nombre || filters.telefono || filters.documento || filters.periodo)) {
				window._advancedFilters[id] = filters;
				try {
					console.debug('plantilla: using fetchAndReplaceTable for', id);
					fetchAndReplaceTable(id, filters);
				} catch(err) { console.warn('fetchAndReplaceTable failed', err); }
			} else if (window._serverTables && window._serverTables[id]) {
				window._advancedFilters[id] = filters;
				try {
					console.debug('plantilla: using fetchAndReplaceTable for', id);
					fetchAndReplaceTable(id, filters);
				} catch(err) { console.warn('fetchAndReplaceTable failed', err); }
			} else {
				console.debug('plantilla: server table not found for', id, 'falling back to client filter');
				applyFiltersToTable(id, filters);
			}
		});
	});

// Función para hacer fetch AJAX y reemplazar tabla (siempre disponible para tablas con filtro servidor)
window.fetchAndReplaceTable = function(tableId, filters){
	filters = filters || (window._advancedFilters && window._advancedFilters[tableId]) || {};
	var url = $('#' + tableId).data('ajax') || ('ajax/datatable-clientes.ajax.php');
	var postData = {
		adv_debug: 1,
		draw: 1,
		start: 0,
		length: 1000,
		nombre: filters.nombre || '',
		telefono: filters.telefono || '',
		documento: filters.documento || '',
		adv_nombre: filters.nombre || '',
		adv_telefono: filters.telefono || '',
		adv_documento: filters.documento || '',
		adv_periodo: filters.periodo || '',
		adv_mes_unico: filters.mes_unico || '',
		adv_mes_desde: filters.mes_desde || '',
		adv_mes_hasta: filters.mes_hasta || '',
		adv_fecha_unica: filters.fecha_unica || '',
		adv_fecha_inicio: filters.fecha_inicio || '',
		adv_fecha_fin: filters.fecha_fin || '',
		adv_tipo_fecha: filters.tipo_fecha || 'fecha_creacion',
		adv_servidor: filters.servidor || ''
	};

	console.debug('fetchAndReplaceTable: sending POST to', url, postData);
	$.ajax({
		url: url,
		method: 'POST',
		data: postData,
		dataType: 'json'
	}).done(function(resp){
		console.debug('fetchAndReplaceTable: server response', resp);
		if(!resp){ console.warn('No response'); return; }

		// Replace table body (destroy DataTable temporarily if exists)
		try {
			if ($.fn.DataTable.isDataTable('#'+tableId)) {
				try { $('#'+tableId).DataTable().destroy(); } catch(e){ console.warn('Error destroying DataTable', e); }
			}

			var $tbody = $('#'+tableId+' tbody');
			if ($tbody.length === 0) {
				$('#'+tableId).append('<tbody></tbody>');
				$tbody = $('#'+tableId+' tbody');
			}
			$tbody.empty();
			
			var hasData = Array.isArray(resp.data) && resp.data.length > 0;
			
			if (hasData) {
				resp.data.forEach(function(row){
					var tr = '<tr>';
					row.forEach(function(col, idx){ 
						// Columnas 14 y 15 son fechas ocultas por defecto
						var style = (idx === 14 || idx === 15) ? ' style="display:none;"' : '';
						tr += '<td'+ style +'>'+ (col === null ? '' : col) +'</td>'; 
					});
					tr += '</tr>';
					$tbody.append(tr);
				});
			}
			// No insertar fila con colspan - dejar tbody vacío para que DataTables muestre su mensaje nativo

			// Re-aplicar preferencias de columnas después del redibujado
			if (typeof reapplyColumnPreferencesAfterDraw === 'function') {
				reapplyColumnPreferencesAfterDraw(tableId);
			}

			// Re-inicializar DataTable
			try {
				$('#'+tableId).DataTable({
					"responsive": true,
					"autoWidth": false,
					"pageLength": 10,
					"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Todos"]],
					"language": {
						"sProcessing":     "Procesando...",
						"sLengthMenu":     "Mostrar _MENU_ registros",
						"sZeroRecords":    "No se encontraron resultados",
						"sEmptyTable":     "No se encontraron resultados con los filtros aplicados",
						"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
						"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
						"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
						"sSearch":         "Buscar:",
						"oPaginate": {
							"sFirst":    "Primero",
							"sLast":     "Último",
							"sNext":     "Siguiente",
							"sPrevious": "Anterior"
						}
					}
				});
			} catch(e) { console.warn('Error reinitializing DataTable', e); }

		} catch(e){ console.error('fetchAndReplaceTable failed', e); }
	}).fail(function(xhr){
		console.error('fetchAndReplaceTable AJAX failed', xhr.status, xhr.responseText);
	});
};

// Debug helpers: only enabled when `window.PLANTILLA_DEV === true`.
if (window.PLANTILLA_DEV === true) {
	// expose a function to manually reload a server table and print its last server JSON
	window.debugReloadTable = function(tableId){
		if (window._serverTables && window._serverTables[tableId]){
			try {
				console.debug('debugReloadTable: forcing reload for', tableId);
				window._serverTables[tableId].ajax.reload(function(){
					try { var js = window._serverTables[tableId].ajax.json(); console.debug('debugReloadTable: server json for', tableId, js); } catch(e){ console.warn('debugReloadTable: could not read ajax.json', e); }
				}, false);
			} catch(e){ console.error('debugReloadTable error', e); }
		} else {
			console.warn('debugReloadTable: no server table registered for', tableId);
		}
	};

	// fetch raw server data and replace the table tbody (temporary, destructive)
	window.debugReplaceTableWithRaw = function(tableId, filters){
		filters = filters || (window._advancedFilters && window._advancedFilters[tableId]) || {};
		var url = $('#' + tableId).data('ajax') || ('ajax/datatable-clientes.ajax.php');
		var postData = {
			adv_debug: 1,
			draw: 1,
			start: 0,
			length: 1000,
			nombre: filters.nombre || '',
			telefono: filters.telefono || '',
			documento: filters.documento || '',
			adv_nombre: filters.nombre || '',
			adv_telefono: filters.telefono || '',
			adv_documento: filters.documento || '',
			adv_periodo: filters.periodo || '',
			adv_mes_unico: filters.mes_unico || '',
			adv_mes_desde: filters.mes_desde || '',
			adv_mes_hasta: filters.mes_hasta || '',
			adv_fecha_unica: filters.fecha_unica || '',
			adv_fecha_inicio: filters.fecha_inicio || '',
			adv_fecha_fin: filters.fecha_fin || '',
			adv_tipo_fecha: filters.tipo_fecha || 'fecha_creacion',
			adv_servidor: filters.servidor || ''
		};

		console.debug('debugReplaceTableWithRaw: sending POST to', url, postData);
		$.ajax({
			url: url,
			method: 'POST',
			data: postData,
			dataType: 'json'
		}).done(function(resp){
			console.debug('debugReplaceTableWithRaw: server response', resp);
			if(!resp){ console.warn('No response'); return; }

			// Build a debug banner showing counts only when 0 or 1 row fetched (helpful for pinpointing)
			try {
				// Debug banner removed: do not insert debug info into the DOM in production
			} catch(e){ console.warn('Could not render debug banner', e); }

			// Replace table body (destroy DataTable temporarily if exists)
			try {
				if ($.fn.DataTable.isDataTable('#'+tableId)) {
					try { $('#'+tableId).DataTable().destroy(); } catch(e){ console.warn('Error destroying DataTable', e); }
				}

				var $tbody = $('#'+tableId+' tbody');
				if ($tbody.length === 0) {
					$('#'+tableId).append('<tbody></tbody>');
					$tbody = $('#'+tableId+' tbody');
				}
				$tbody.empty();
				if (Array.isArray(resp.data) && resp.data.length>0) {
					resp.data.forEach(function(row){
						// row is expected to be array of columns
						var tr = '<tr>';
						row.forEach(function(col){ tr += '<td>'+ (col === null ? '' : col) +'</td>'; });
						tr += '</tr>';
						$tbody.append(tr);
					});
				} else {
					$tbody.append('<tr><td colspan="14" style="text-align:center;">No hay datos (resp.data vacío)</td></tr>');
				}

				// Re-aplicar preferencias de columnas después del redibujado
				if (typeof reapplyColumnPreferencesAfterDraw === 'function') {
					reapplyColumnPreferencesAfterDraw(tableId);
				}

			} catch(e){ console.error('debugReplaceTableWithRaw failed', e); }
		}).fail(function(xhr){
			console.error('debugReplaceTableWithRaw AJAX failed', xhr.status, xhr.responseText);
		});
	};
} else {
	// stubs that avoid errors but inform the developer how to enable debug helpers
	window.debugReloadTable = function(tableId){ console.warn('debugReloadTable disabled; set window.PLANTILLA_DEV = true to enable'); };
	window.debugReplaceTableWithRaw = function(tableId, filters){ console.warn('debugReplaceTableWithRaw disabled; set window.PLANTILLA_DEV = true to enable'); };
}

	window.addEventListener('advancedSearch:clear', function(){
		console.log('plantilla: advancedSearch:clear received');
		// Clear column searches and redraw or reload server tables
		['tablaProspectos','tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera','example2'].forEach(function(id){
			if (window._serverTables && window._serverTables[id]) {
				window._advancedFilters[id] = {};
				try { window._serverTables[id].ajax.reload(null, false); } catch(e){ console.warn('reload failed', e); }
			} else if ($.fn.DataTable.isDataTable('#'+id)){
				var t = $('#'+id).DataTable();
				try{ t.columns().search('').draw(); } catch(e){ t.search('').draw(); }
				// remove any custom ext.search filter we may have added for this table
				if(window._dtFilterFns && window._dtFilterFns[id]){
					$.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter(function(fn){ return fn !== window._dtFilterFns[id]; });
					window._dtFilterFns[id] = null;
					try { t.draw(); } catch(e){ /* ignore */ }
				}
			}
		});
	});

})();
