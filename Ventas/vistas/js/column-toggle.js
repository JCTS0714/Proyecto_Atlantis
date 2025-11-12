/*================================================
  SISTEMA DE MOSTRAR/OCULTAR COLUMNAS
  Permite al usuario controlar qué columnas ver
================================================*/

// Esperar a que DataTables esté completamente inicializado
document.addEventListener('DOMContentLoaded', function() {
  setTimeout(function() {
    initializeColumnToggle();
  }, 500);
});

function initializeColumnToggle() {
  // Buscar todos los checkboxes de toggle
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');
  
  checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', function() {
      const columnIndex = parseInt(this.dataset.column);
      const tableId = this.dataset.table;
      const table = document.getElementById(tableId);
      
      if (table) {
        toggleColumn(table, columnIndex, this.checked);
        saveColumnPreference(tableId, columnIndex, this.checked);
      }
    });
  });
  
  // Cargar preferencias guardadas
  loadColumnPreferences();
}

function toggleColumn(table, columnIndex, isVisible) {
  if (!table) return;

  // Obtener todas las filas (headers y body)
  const allRows = table.querySelectorAll('thead tr, tbody tr');
  
  allRows.forEach(row => {
    const cells = row.querySelectorAll('th, td');
    if (cells[columnIndex]) {
      if (isVisible) {
        cells[columnIndex].style.display = '';
      } else {
        cells[columnIndex].style.display = 'none';
      }
    }
  });
}

function saveColumnPreference(tableId, columnIndex, isVisible) {
  try {
    const prefs = JSON.parse(localStorage.getItem('columnPrefs_' + tableId) || '{}');
    prefs[columnIndex] = isVisible;
    localStorage.setItem('columnPrefs_' + tableId, JSON.stringify(prefs));
  } catch (e) {
    console.warn('No se pudo guardar preferencias:', e);
  }
}

function loadColumnPreferences() {
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');
  
  checkboxes.forEach(checkbox => {
    const tableId = checkbox.dataset.table;
    const columnIndex = parseInt(checkbox.dataset.column);
    const table = document.getElementById(tableId);
    
    if (table) {
      try {
        const prefs = JSON.parse(localStorage.getItem('columnPrefs_' + tableId) || '{}');
        const isVisible = prefs[columnIndex] !== undefined ? prefs[columnIndex] : true;
        
        checkbox.checked = isVisible;
        toggleColumn(table, columnIndex, isVisible);
      } catch (e) {
        console.warn('No se pudo cargar preferencias:', e);
      }
    }
  });
}

// Función global para mostrar/ocultar panel
window.toggleColumnPanel = function(btn) {
  const panel = btn.parentElement.querySelector('.column-toggle-panel');
  if (panel) {
    panel.classList.toggle('hidden');
    panel.classList.toggle('visible');
  }
};
