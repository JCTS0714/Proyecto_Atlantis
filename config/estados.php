<?php
// Configuración de estados del sistema
define('ESTADO_PROSPECTO', 0);       // Lista de prospectos
define('ESTADO_SEGUIMIENTO', 1);     // Lista de seguimiento
define('ESTADO_CLIENTE', 2);         // Lista de clientes
define('ESTADO_NO_CLIENTE', 3);      // Lista de no-clientes
define('ESTADO_EN_ESPERA', 4);       // Zona de espera
define('ESTADO_PERDIDO_KANBAN', 5);  // Oportunidad Perdida

// Estados de oportunidad para el tablero Kanban
define('KANBAN_NUEVO', 1);           // Columna Nuevo -> Lista Seguimiento (1)
define('KANBAN_CALIFICADO', 2);      // Columna Calificado -> Lista Seguimiento (1)
define('KANBAN_PROPUESTO', 3);       // Columna Propuesto -> Lista Seguimiento (1)
define('KANBAN_GANADO', 4);          // Columna Ganado -> Lista Clientes (2)
define('KANBAN_PERDIDO', 5);         // Columna imaginaria Perdido -> Lista No Clientes (3)
define('KANBAN_ZONA_ESPERA', 6);     // Columna imaginaria Zona de Espera -> Lista Zona de Espera (4)
?>
