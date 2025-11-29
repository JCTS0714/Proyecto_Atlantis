/*================================================
  AUTO-WRAP TABLAS CON SCROLL RESPONSIVO
  Envuelve automáticamente todas las tablas
  con contenedor para scroll horizontal
================================================*/

document.addEventListener('DOMContentLoaded', function() {
  initializeResponsiveTables();
});

function initializeResponsiveTables() {
  const tables = document.querySelectorAll('table.dataTable');
  
  tables.forEach(table => {
    // Solo envolver si no está ya envuelto
    if (!table.parentElement.classList.contains('table-responsive-wrapper')) {
      const wrapper = document.createElement('div');
      wrapper.classList.add('table-responsive-wrapper');
      table.parentNode.insertBefore(wrapper, table);
      wrapper.appendChild(table);
    }
  });
}
