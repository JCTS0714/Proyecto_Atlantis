/*================================================
  SISTEMA DE MOSTRAR/OCULTAR COLUMNAS v2
  Usando data-column names en lugar de índices
================================================*/

// Delegated listener: garantiza que cualquier checkbox nuevo/disparado se maneje
document.addEventListener('change', function(e) {
  var tgt = e.target;
  if (!tgt) return;
  if (tgt.classList && tgt.classList.contains && tgt.classList.contains('column-toggle-checkbox')) {
    try {
      var tableId = tgt.dataset.table;
      var columnName = tgt.dataset.column;
      var isVisible = tgt.checked;
      var table = document.getElementById(tableId);
      if (table) {
        // Try to apply via DataTables API (if available) then persist via CSS fallback
        try { toggleColumnByName(table, columnName, isVisible); } catch(e){}
        try { setColumnPreference(tableId, columnName, isVisible); } catch(e){}
      }
    } catch (err) {
      // silencioso
    }
  }
}, false);

// Persist a single column preference and update CSS rules immediately
function setColumnPreference(tableId, columnName, isVisible){
  try{
    var prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    var prefs = prefsStr ? JSON.parse(prefsStr) : {};
    prefs[columnName] = !!isVisible;
    localStorage.setItem('columnPrefs_' + tableId, JSON.stringify(prefs));
  }catch(e){
    try{ localStorage.setItem('columnPrefs_' + tableId, JSON.stringify({[columnName]: !!isVisible})); }catch(e){}
  }
  // Ensure CSS rules are injected and also apply immediate DOM fallback
  try{
    // Inject CSS rules for all hidden columns of this table
    var prefsAllStr = localStorage.getItem('columnPrefs_' + tableId);
    var rules = '';
    if (prefsAllStr) {
      var prefsAll = JSON.parse(prefsAllStr);
      Object.keys(prefsAll).forEach(function(col){
        if (prefsAll[col] === false || prefsAll[col] === 'false'){
          // Hide by data-column attribute (explicit) and also by column position (nth-child)
          rules += '#' + tableId + ' [data-column="' + col + '"]{ display: none !important; }\n';
          try{
            var idxMap = (window._colToggleIndexMap && window._colToggleIndexMap[tableId]) || {};
            var idx = idxMap[col];
            if (typeof idx !== 'undefined' && idx !== null) {
              var pos = parseInt(idx,10) + 1;
              rules += '#' + tableId + ' th:nth-child(' + pos + '), #' + tableId + ' td:nth-child(' + pos + '){ display: none !important; }\n';
            }
          }catch(e){}
        }
      });
    }
    var styleEl = ensureStyleElement(tableId);
    // Ensure the style element is last in head so it has high precedence
    try{ document.head.appendChild(styleEl); }catch(e){}
    styleEl.textContent = rules;

    // Immediate DOM fallback for this specific column (in case CSS isn't applied yet)
    try{
      var cells = document.querySelectorAll('#' + tableId + ' [data-column="' + columnName + '"]');
      cells.forEach(function(cell){
        if (!isVisible) {
          cell.style.setProperty('display', 'none', 'important');
        } else {
          cell.style.removeProperty('display');
        }
      });
    }catch(e){}
  }catch(e){ console.warn('[ColumnToggle-v2] setColumnPreference applyCss error', e); }
}

// Ejecutar inmediatamente y también en DOMContentLoaded
function startColumnToggle() {
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');

  if (checkboxes.length === 0) {
    return false;
  }

  // Collect tableIds to attach DataTable events later
  const tableIds = new Set();

  checkboxes.forEach((checkbox) => {
    if (checkbox.dataset && checkbox.dataset.table) tableIds.add(checkbox.dataset.table);
  });

  // Attach DataTables event handlers to reapply preferences after init/draw
  tableIds.forEach(function(tableId) {
    try {
      var $tbl = $('#' + tableId);
      // build initial map and css
      try{ buildColumnIndexMap(tableId); }catch(e){}
      try{ applyCssHide(tableId); }catch(e){}
      if ($tbl && $tbl.length && $.fn.DataTable) {
        $tbl.on('init.dt draw.dt', function(event) {
          if (window._colToggleApplying && window._colToggleApplying[tableId]) {
            return;
          }
          // Rebuild mapping when DataTable initializes/redraws
          try{ buildColumnIndexMap(tableId); }catch(e){}
          reapplyColumnPreferencesAfterDraw(tableId);
        });
      }
    } catch(e) {
      console.warn('[ColumnToggle-v2] Error attaching DataTable events for', tableId, e);
    }
  });

  // Load preferences into checkboxes and apply css/visibility
  loadColumnPreferences();
  tableIds.forEach(function(tid){ try{ applyCssHide(tid); }catch(e){} });

  return true;
}
// Construye un mapa tableId -> { columnName: dtIndex }
function buildColumnIndexMap(tableId){
  try{
    window._colToggleIndexMap = window._colToggleIndexMap || {};
    var table = document.getElementById(tableId);
    if(!table) return;
    var map = {};
    if($.fn.DataTable && $.fn.DataTable.isDataTable('#'+tableId)){
      var dt = $('#'+tableId).DataTable();
      var cols = dt.columns().count();
      // First pass: header data-column attributes from DT headers
      for(var i=0;i<cols;i++){
        try{
          var hdr = dt.column(i).header();
          if(hdr && hdr.getAttribute){
            var dc = hdr.getAttribute('data-column');
            if(dc) map[dc]=i;
          }
        }catch(e){}
      }
      // Second pass: try match DOM first-row headers with data-column to DT header text
      try{
        var domFirst = table.querySelectorAll('thead tr:first-child th, thead tr:first-child td');
        for(var h=0; h<domFirst.length; h++){
          try{
            var dc = domFirst[h].getAttribute && domFirst[h].getAttribute('data-column');
            if(dc && map[dc]===undefined){
              var domText = (domFirst[h].textContent||domFirst[h].innerText||'').trim();
              for(var j=0;j<cols;j++){
                try{
                  var hdr2 = dt.column(j).header();
                  var hdrText = (hdr2 && (hdr2.textContent||hdr2.innerText) || '').trim();
                  if(hdrText === domText){ map[dc]=j; break; }
                }catch(e){}
              }
            }
          }catch(e){}
        }
      }catch(e){}
      // Third pass: use first tbody row data-column attributes as position hint
      try{
        var firstRow = table.querySelector('#'+tableId+' tbody tr');
        if(firstRow){
          var cells = firstRow.querySelectorAll('td,th');
          for(var k=0;k<cells.length;k++){
            try{
              var dcol = cells[k].getAttribute && cells[k].getAttribute('data-column');
              if(dcol && map[dcol]===undefined){
                // assume column index aligns with cell index for fallback
                map[dcol] = k;
              }
            }catch(e){}
          }
        }
      }catch(e){}
    }
    window._colToggleIndexMap[tableId] = map;
  }catch(e){console.warn('[ColumnToggle-v2] buildColumnIndexMap error', e);} 
  }

// Re-aplicar preferencias de columnas después de que DataTables redibuje la tabla
function reapplyColumnPreferencesAfterDraw(tableId) {
  // Ejecutar con pequeño retardo para dejar que DataTables termine su redraw
  setTimeout(function() {
    const table = document.getElementById(tableId);
    if (!table) return;

    // Establecer guard para evitar re-entradas desde draw.dt
    window._colToggleApplying = window._colToggleApplying || {};
    if (window._colToggleApplying[tableId]) return;
    window._colToggleApplying[tableId] = true;

    const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    let tableData = null;
    try {
      if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
        tableData = $(table).DataTable();
      }
        if (window.PLANTILLA_DEV === true) console.log('[ColumnToggle-v2]  - applying', columnName, '->', prefs[columnName]);
    } catch(e) {
      tableData = null;
    }

    if (prefsStr) {
      const prefs = JSON.parse(prefsStr);
      Object.keys(prefs).forEach(function(columnName) {
        toggleColumnByName(table, columnName, prefs[columnName]);
        // Actualizar checkbox también
        const checkbox = document.querySelector('.column-toggle-checkbox[data-table="' + tableId + '"][data-column="' + columnName + '"]');
        if (checkbox) {
          checkbox.checked = prefs[columnName];
        }
      });
    }

    // Realizar un único ajuste de columnas (si existe DataTable) mientras el guard está activo
    try {
      if (tableData) {
        tableData.columns.adjust();
      }
    } catch(e) {
      // ignore
    }

    // Limpiar guard tras breve espera para permitir otros eventos
    setTimeout(function() {
      window._colToggleApplying[tableId] = false;
    }, 300);
  }, 100);
}

// Intentar inicializar inmediatamente
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', startColumnToggle);
} else {
  startColumnToggle();
}

// Intento alternativo después de un delay
setTimeout(function() {
  if (document.querySelectorAll('.column-toggle-checkbox').length === 0) {
    startColumnToggle();
  }
}, 500);

function toggleColumnByName(table, columnName, isVisible) {
  if (!table) {
    return;
  }

  // Normalizar tableId para usar en los guards (evitar usar mix de table.id vs tableId)
  var tableId = table.id || table.getAttribute('id') || '';

  // Si esta tabla tiene DataTables inicializado, usarlo
  let tableData = null;
  try {
    if ($.fn.DataTable && $.fn.DataTable.isDataTable(table)) {
      tableData = $(table).DataTable();
    }
  } catch(e) {
    tableData = null;
  }

  const cellsToToggle = table.querySelectorAll(`[data-column="${columnName}"]`);

  // Si hay instancia DataTable, encontrar el índice correcto UNA vez y usar la API
  if (tableData) {
    var foundIndex = null;
    try {
      var colCount = tableData.columns().count();

      // Método A: buscar por data-column directamente en headers devueltos por DataTables
      for (var ci = 0; ci < colCount; ci++) {
        try {
          var hdr = tableData.column(ci).header();
          if (hdr && hdr.getAttribute && hdr.getAttribute('data-column') === columnName) {
            foundIndex = ci;
            break;
          }
        } catch(e) {
          // continuar
        }
      }

      // Método B: si no se halló, intentar mapear por texto de header comparando DOM thead
      if (foundIndex === null) {
        try {
          var domHeaders = table.querySelectorAll('thead tr:first-child th, thead tr:first-child td');
          var headerPos = -1;
          for (var h = 0; h < domHeaders.length; h++) {
            try {
              if (domHeaders[h].getAttribute && domHeaders[h].getAttribute('data-column') === columnName) {
                headerPos = h; break;
              }
            } catch(e) {}
          }

          if (headerPos === -1) {
            // intentar buscar en cualquier th del thead
            var allHeaders = table.querySelectorAll('thead th, thead td');
            for (var ah = 0; ah < allHeaders.length; ah++) {
              try {
                if (allHeaders[ah].getAttribute && allHeaders[ah].getAttribute('data-column') === columnName) {
                  headerPos = ah; break;
                }
              } catch(e) {}
            }
          }

          if (headerPos >= 0) {
            // intentar encontrar la columna de DataTables que corresponde al header en esa posición
            for (var ci2 = 0; ci2 < colCount; ci2++) {
              try {
                var hdr2 = tableData.column(ci2).header();
                if (!hdr2) continue;
                var hdrText = (hdr2.textContent || hdr2.innerText || '').trim();
                var domText = (domHeaders[headerPos].textContent || domHeaders[headerPos].innerText || '').trim();
                if (hdrText === domText) { foundIndex = ci2; break; }
              } catch(e) {}
            }
          }
        } catch(e) {
          // ignore
        }
      }
    } catch(e) {
      foundIndex = null;
    }

    if (foundIndex !== null) {
      try {
        // Para evitar que el redraw dispare inmediatamente reapply, usamos el guard
        window._colToggleApplying = window._colToggleApplying || {};
        window._colToggleApplying[tableId] = true;
        // Llamar visible() (permitir redraw) para que DataTables aplique el cambio
        tableData.column(foundIndex).visible(isVisible);
        // Limpiar el guard después de un pequeño delay
        setTimeout(function() { window._colToggleApplying[tableId] = false; }, 250);
      } catch(e) {
        // fallback to DOM per-cell
        cellsToToggle.forEach(cell => {
          if (isVisible) {
            cell.style.display = '';
            cell.style.removeProperty('display');
          } else {
            cell.style.setProperty('display', 'none', 'important');
          }
        });
      }
    } else {
      // No encontramos la cabecera adecuada; fallback al DOM
      cellsToToggle.forEach(cell => {
        if (isVisible) {
          cell.style.display = '';
          cell.style.removeProperty('display');
        } else {
          cell.style.setProperty('display', 'none', 'important');
        }
      });
    }
  } else {
    // Sin DataTables: simple manipulación DOM
    cellsToToggle.forEach(cell => {
      if (isVisible) {
        cell.style.display = '';
        cell.style.removeProperty('display');
      } else {
        cell.style.setProperty('display', 'none', 'important');
      }
    });
  }

  // Si usamos DataTables y ajustamos columnas, pedir solo ajuste (no redraw)
  try {
    if (tableData && !window._colToggleApplying[tableId]) {
      tableData.columns.adjust();
    }
  } catch(e) {
    // ignore
  }
}

function saveColumnPreference(tableId, columnName, isVisible) {
  try {
    const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    const prefs = prefsStr ? JSON.parse(prefsStr) : {};
    prefs[columnName] = isVisible;
    localStorage.setItem('columnPrefs_' + tableId, JSON.stringify(prefs));
    // Aplicar CSS fallback inmediato para ocultar columnas si DataTables no aplica
    try { applyCssHide(tableId); } catch(e) {}
  } catch (e) {
    // ignore
  }
}

// Ensure a <style> element exists for this table to inject column hide rules
function ensureStyleElement(tableId){
  var styleId = 'col-hide-style-' + tableId;
  var el = document.getElementById(styleId);
  if(!el){
    el = document.createElement('style');
    el.id = styleId;
    document.head.appendChild(el);
  }
  return el;
}

// Build CSS rules to hide columns based on stored preferences and inject into the style element
function applyCssHide(tableId){
  try{
    var prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    var rules = '';
    if (prefsStr) {
      var prefs = JSON.parse(prefsStr);
      Object.keys(prefs).forEach(function(col) {
        if (prefs[col] === false || prefs[col] === 'false') {
          // hide both th and td cells with matching data-column under this table
          rules += '#' + tableId + ' [data-column="' + col + '"]{ display: none !important; }\n';
          // also attempt to hide by column position (nth-child) if we have a mapping
          try{
            var idxMap = (window._colToggleIndexMap && window._colToggleIndexMap[tableId]) || {};
            var idx = idxMap[col];
            if (typeof idx !== 'undefined' && idx !== null) {
              var pos = parseInt(idx,10) + 1;
              rules += '#' + tableId + ' th:nth-child(' + pos + '), #' + tableId + ' td:nth-child(' + pos + '){ display: none !important; }\n';
            }
          }catch(e){}
        }
      });
    }
    var el = ensureStyleElement(tableId);
    try{ document.head.appendChild(el); }catch(e){}
    el.textContent = rules;
  }catch(e){ console.warn('[ColumnToggle-v2] applyCssHide error', e); }
}
function loadColumnPreferences() {
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');

  checkboxes.forEach(checkbox => {
    const tableId = checkbox.dataset.table;
    const columnName = checkbox.dataset.column;
    const table = document.getElementById(tableId);

    if (table) {
      const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
      if (prefsStr) {
        const prefs = JSON.parse(prefsStr);
        if (prefs[columnName] !== undefined) {
          checkbox.checked = prefs[columnName];
          toggleColumnByName(table, columnName, prefs[columnName]);
        }
      }
    }
  });
}

window.toggleColumnPanel = function(event) {
  event.preventDefault();
  event.stopPropagation();

  const btn = event.currentTarget;
  const container = btn.closest('.column-toggle-container');
  const panel = container.querySelector('.column-toggle-panel');

  if (panel) {
    panel.classList.toggle('hidden');
    panel.classList.toggle('visible');
  }
};

document.addEventListener('click', function(event) {
  const containers = document.querySelectorAll('.column-toggle-container');
  containers.forEach(container => {
    if (!container.contains(event.target)) {
      const panel = container.querySelector('.column-toggle-panel');
      if (panel && panel.classList.contains('visible')) {
        panel.classList.remove('visible');
        panel.classList.add('hidden');
      }
    }
  });
});
