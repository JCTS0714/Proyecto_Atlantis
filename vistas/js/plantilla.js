// Envolver todo en $(document).ready() para asegurar que el DOM esté listo
$(document).ready(function() {

	// Silence debug/info logs unless `window.PLANTILLA_DEV === true`
	// Set `window.PLANTILLA_DEV = true` in console for temporary local debugging.
	if (!(window.PLANTILLA_DEV === true)) {
		if (window.console) {
			console.debug = function(){};
			console.info = function(){};
		}
	}

	// Global AJAX error logger: helps surface server response bodies for 500s/HTML errors
	$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
		try {
			console.error('Global AJAX error:', settings && settings.url, thrownError, jqxhr && jqxhr.responseText);
			if (jqxhr && jqxhr.responseText) {
				try { var parsed = JSON.parse(jqxhr.responseText); console.error('Global AJAX server JSON:', parsed); } catch(e) { /* not JSON */ }
			}
		} catch(e) { console.warn('Error logging global AJAX error', e); }
	});

	$('.sidebar-menu').tree()

    // Inicializar tabla #example2 (prospectos) con opciones completas
    if ($('#example2').length) {
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
    }

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
				type: 'POST', // use POST to reliably send filters and avoid URL length issues
				cache: false,
						beforeSend: function(jqXhr, settings) {
							try { console.debug('DataTable ajax beforeSend for', tableId, settings.url); } catch(e){}
						},
						data: function(d) {
					// merge last known advanced filters for this table
					var filters = window._advancedFilters && window._advancedFilters[tableId] ? window._advancedFilters[tableId] : {};
							try {
								var merged = $.extend({}, d, filters);
								// Add debug flag so server can return diagnostic info
								merged.adv_debug = 1;
								if (window.console && window.console.debug) console.debug('DataTable ajax data for', tableId, { dtParams: d, filters: filters, merged: merged });
								return merged;
							} catch(e) {
								console.warn('Error merging DataTable params', e);
								return d;
							}
				}
						,dataSrc: function(json) {
							try { console.debug('DataTable ajax response for', tableId, json); } catch(e){}
							// store last ajax response for debugging
							if (!window._lastAjaxResponse) window._lastAjaxResponse = {};
							window._lastAjaxResponse[tableId] = json;
							if (!json) return [];
							// If server returned DataTables error format, log it
							if (json.error) console.error('DataTable server error:', json.error);
							return json.data || [];
						},
						error: function(xhr, textStatus, errorThrown) {
							console.error('DataTable AJAX error for', tableId, { status: xhr.status, statusText: xhr.statusText, responseText: xhr.responseText });
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
			if(periodo === 'today'){
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
		try { console.debug('plantilla: advancedSearch:apply received', filters); } catch(e){}
		// store global filters map
		if (!window._advancedFilters) window._advancedFilters = {};

		['tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera','example2'].forEach(function(id){
			// If server-side table exists, store filters and reload via AJAX
			if (window._serverTables && window._serverTables[id]) {
				window._advancedFilters[id] = filters;
				// Reload server-side DataTable with new filters (no destruir, solo recargar)
				try {
					if (window._serverTables[id].ajax && typeof window._serverTables[id].ajax.reload === 'function') {
						window._serverTables[id].ajax.reload(null, false);
						console.debug('plantilla: triggered ajax.reload for', id);
					}
				} catch(reloadErr) { console.warn('server table reload failed for', id, reloadErr); }
			} else {
				console.debug('plantilla: server table not found for', id, 'falling back to client filter');
				applyFiltersToTable(id, filters);
			}
		});
	});

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
			adv_fecha_inicio: filters.fecha_inicio || '',
			adv_fecha_fin: filters.fecha_fin || ''
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
		// Clear column searches and redraw or reload server tables
		['tablaClientes','tablaSeguimiento','tablaNoClientes','tablaZonaEspera','example2'].forEach(function(id){
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

}); // Fin de $(document).ready()
