/*================================================
  SISTEMA DE MOSTRAR/OCULTAR COLUMNAS v2
  Usando data-column names en lugar de índices
================================================*/

// Ejecutar inmediatamente y también en DOMContentLoaded
function startColumnToggle() {
  console.log('[ColumnToggle-v2] Inicializando sistema...');
  
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');
  console.log('[ColumnToggle-v2] Checkboxes encontrados:', checkboxes.length);
  
  if (checkboxes.length === 0) {
    console.warn('[ColumnToggle-v2] No hay checkboxes! El DOM aún no está listo.');
    return false;
  }
  
  checkboxes.forEach((checkbox, idx) => {
    console.log(`[ColumnToggle-v2] Checkbox ${idx}: column="${checkbox.dataset.column}"`);
    
    checkbox.addEventListener('change', function(e) {
      const columnName = this.dataset.column;
      const tableId = this.dataset.table;
      const isVisible = this.checked;
      
      console.log('[ColumnToggle-v2] >>> CAMBIO DETECTADO:', columnName, '-> Visible:', isVisible);
      console.log('[ColumnToggle-v2] Event details:', e);
      console.log('[ColumnToggle-v2] this.checked actual:', this.checked);
      
      const table = document.getElementById(tableId);
      if (table) {
        console.log('[ColumnToggle-v2] Tabla encontrada, toggling...');
        toggleColumnByName(table, columnName, isVisible);
        saveColumnPreference(tableId, columnName, isVisible);
      } else {
        console.warn('[ColumnToggle-v2] Tabla NO encontrada:', tableId);
      }
    });
  });
  
  loadColumnPreferences();
  return true;
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
    console.log('[ColumnToggle-v2] Reintentando inicialización...');
    startColumnToggle();
  }
}, 500);

function toggleColumnByName(table, columnName, isVisible) {
  console.log('[ColumnToggle-v2] toggleColumnByName:', columnName, '->', isVisible);
  
  if (!table) {
    console.warn('[ColumnToggle-v2] Tabla inválida');
    return;
  }
  
  // Si esta tabla tiene DataTables inicializado, usarlo
  const dataTableInstance = $.fn.DataTable.fnTables(true);
  let tableData = null;
  if (dataTableInstance && dataTableInstance.length > 0) {
    for (let i = 0; i < dataTableInstance.length; i++) {
      if ($(dataTableInstance[i]).get(0) === table) {
        tableData = $(dataTableInstance[i]).DataTable();
        break;
      }
    }
  }
  
  const cellsToToggle = table.querySelectorAll(`[data-column="${columnName}"]`);
  console.log('[ColumnToggle-v2] Celdas encontradas para', columnName, ':', cellsToToggle.length);
  
  if (cellsToToggle.length === 0) {
    console.warn('[ColumnToggle-v2] NO se encontraron celdas con data-column=', columnName);
    console.log('[ColumnToggle-v2] Buscando todas las celdas con data-column...');
    const allCells = table.querySelectorAll('[data-column]');
    console.log('[ColumnToggle-v2] Total celdas con data-column:', allCells.length);
    allCells.forEach(cell => {
      console.log('[ColumnToggle-v2]   - data-column:', cell.dataset.column, '| content:', cell.textContent.substring(0, 20));
    });
  }
  
  cellsToToggle.forEach(cell => {
    if (isVisible) {
      cell.style.display = '';
      cell.style.removeProperty('display');
    } else {
      cell.style.setProperty('display', 'none', 'important');
    }
    console.log('[ColumnToggle-v2] Celda toggled:', cell.textContent.substring(0, 20), '| Style:', cell.style.display);
  });
}

function saveColumnPreference(tableId, columnName, isVisible) {
  try {
    const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    const prefs = prefsStr ? JSON.parse(prefsStr) : {};
    prefs[columnName] = isVisible;
    localStorage.setItem('columnPrefs_' + tableId, JSON.stringify(prefs));
    console.log('[ColumnToggle-v2] Preferencia guardada:', tableId, columnName, isVisible);
  } catch (e) {
    console.warn('[ColumnToggle-v2] Error:', e);
  }
}

function loadColumnPreferences() {
  console.log('[ColumnToggle-v2] Cargando preferencias...');
  
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
  
  console.log('[ColumnToggle-v2] Panel toggle clicked');
  
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
