/*================================================
  SISTEMA DE MOSTRAR/OCULTAR COLUMNAS v2
  Usando data-column names en lugar de índices
================================================*/

// Ejecutar inmediatamente y también en DOMContentLoaded
function startColumnToggle() {
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');

  if (checkboxes.length === 0) {
    return false;
  }

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', function() {
      const columnName = this.dataset.column;
      const tableId = this.dataset.table;
      const isVisible = this.checked;

      const table = document.getElementById(tableId);
      if (table) {
        toggleColumnByName(table, columnName, isVisible);
        saveColumnPreference(tableId, columnName, isVisible);
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
    startColumnToggle();
  }
}, 500);

function toggleColumnByName(table, columnName, isVisible) {
  if (!table) {
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
