/*================================================
  SISTEMA DE MOSTRAR/OCULTAR COLUMNAS
  Permite al usuario controlar qué columnas ver
================================================*/

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
  // Inicializar después de que DataTables se haya cargado
  setTimeout(function() {
    initializeColumnToggle();
  }, 1000);
});

function initializeColumnToggle() {
  console.log('[ColumnToggle] Inicializando sistema...');
  
  // Buscar todos los checkboxes de toggle
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');
  console.log('[ColumnToggle] Checkboxes encontrados:', checkboxes.length);
  
  // Primero, verificar que los checkboxes existan
  checkboxes.forEach((checkbox, idx) => {
    console.log(`[ColumnToggle] Checkbox ${idx}:`, {
      column: checkbox.dataset.column,
      table: checkbox.dataset.table,
      checked: checkbox.checked,
      element: checkbox
    });
  });
  
  checkboxes.forEach(checkbox => {
    // Agregar evento change a cada checkbox
    checkbox.addEventListener('change', function(e) {
      console.log('[ColumnToggle] ===== EVENTO CHANGE DISPARADO =====');
      console.log('[ColumnToggle] Event:', e);
      console.log('[ColumnToggle] this.checked:', this.checked);
      console.log('[ColumnToggle] this.dataset.column:', this.dataset.column);
      console.log('[ColumnToggle] this.dataset.table:', this.dataset.table);
      
      const columnIndex = parseInt(this.dataset.column);
      const tableId = this.dataset.table;
      const isVisible = this.checked; // Valor ACTUAL después del cambio
      
      console.log('[ColumnToggle] Valores parseados:', {
        columnIndex,
        tableId,
        isVisible
      });
      
      const table = document.getElementById(tableId);
      console.log('[ColumnToggle] Tabla encontrada:', !!table);
      
      if (table) {
        console.log('[ColumnToggle] Llamando toggleColumn con columnIndex=', columnIndex, 'isVisible=', isVisible);
        toggleColumn(table, columnIndex, isVisible);
        saveColumnPreference(tableId, columnIndex, isVisible);
      } else {
        console.warn('[ColumnToggle] Tabla no encontrada:', tableId);
      }
    });
  });
  
  // Cargar preferencias guardadas SOLO después de agregar listeners
  loadColumnPreferences();
}

function toggleColumn(table, columnIndex, isVisible) {
  if (!table) {
    console.warn('[ColumnToggle] Tabla no válida');
    return;
  }

  console.log('[ColumnToggle] Toggling columna', columnIndex, '-> Visible:', isVisible);

  // Método 1: Intentar con thead y tbody directamente
  const headRows = table.querySelectorAll('thead tr');
  const bodyRows = table.querySelectorAll('tbody tr');
  
  console.log('[ColumnToggle] Filas thead:', headRows.length, '| Filas tbody:', bodyRows.length);
  
  // Procesar header
  headRows.forEach(row => {
    const cells = row.querySelectorAll('th, td');
    console.log('[ColumnToggle] Celdas en header row:', cells.length);
    if (cells[columnIndex]) {
      if (isVisible) {
        cells[columnIndex].style.display = '';
      } else {
        cells[columnIndex].style.display = 'none';
      }
      console.log('[ColumnToggle] Header - Columna', columnIndex, '-> ', isVisible ? 'Mostrada' : 'Oculta');
    }
  });
  
  // Procesar body
  bodyRows.forEach((row, rowIndex) => {
    const cells = row.querySelectorAll('th, td');
    console.log('[ColumnToggle] Celdas en body row', rowIndex, ':', cells.length);
    if (cells[columnIndex]) {
      if (isVisible) {
        cells[columnIndex].style.display = '';
      } else {
        cells[columnIndex].style.display = 'none';
      }
    }
  });
  
  console.log('[ColumnToggle] Toggling completado para columna', columnIndex);
}

function saveColumnPreference(tableId, columnIndex, isVisible) {
  try {
    const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
    const prefs = prefsStr ? JSON.parse(prefsStr) : {};
    prefs[columnIndex] = isVisible;
    localStorage.setItem('columnPrefs_' + tableId, JSON.stringify(prefs));
    console.log('[ColumnToggle] Preferencia guardada:', tableId, columnIndex, isVisible);
  } catch (e) {
    console.warn('[ColumnToggle] Error al guardar preferencia:', e);
  }
}

function loadColumnPreferences() {
  console.log('[ColumnToggle] Cargando preferencias...');
  
  const checkboxes = document.querySelectorAll('.column-toggle-checkbox');
  
  checkboxes.forEach(checkbox => {
    const tableId = checkbox.dataset.table;
    const columnIndex = parseInt(checkbox.dataset.column);
    const table = document.getElementById(tableId);
    
    if (table) {
      try {
        const prefsStr = localStorage.getItem('columnPrefs_' + tableId);
        
        if (prefsStr) {
          // Si hay preferencias guardadas, usarlas
          const prefs = JSON.parse(prefsStr);
          if (prefs[columnIndex] !== undefined) {
            const isVisible = prefs[columnIndex];
            checkbox.checked = isVisible;
            toggleColumn(table, columnIndex, isVisible);
            console.log('[ColumnToggle] Preferencia cargada:', tableId, columnIndex, '-> Visible:', isVisible);
          }
        }
        // Si no hay preferencias, dejar como están (todos checked por defecto)
      } catch (e) {
        console.warn('[ColumnToggle] Error al cargar preferencias:', e);
      }
    }
  });
}

// Función global para mostrar/ocultar panel
window.toggleColumnPanel = function(event) {
  event.preventDefault();
  event.stopPropagation();
  
  const btn = event.currentTarget;
  const container = btn.closest('.column-toggle-container');
  const panel = container.querySelector('.column-toggle-panel');
  
  console.log('[ColumnToggle] Toggling panel, visible:', !panel.classList.contains('hidden'));
  
  if (panel) {
    panel.classList.toggle('hidden');
    panel.classList.toggle('visible');
  }
};

// Cerrar panel al hacer clic afuera
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
