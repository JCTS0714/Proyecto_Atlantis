-- Documentacion/index_suggestions.sql
-- SQL sugerido para mejorar el rendimiento (aplicar en STAGING primero).
-- Ejecutar cada ALTER TABLE en entorno de pruebas y verificar con EXPLAIN después.

-- 1) Índices en clientes (fechas, estado, nombre)
ALTER TABLE clientes ADD INDEX idx_clientes_estado_fecha (estado, fecha_creacion);
ALTER TABLE clientes ADD INDEX idx_clientes_fecha_creacion (fecha_creacion);
ALTER TABLE clientes ADD INDEX idx_clientes_nombre (nombre);
-- Opcional (búsqueda infix / texto completo):
-- ALTER TABLE clientes ADD FULLTEXT idx_clientes_fulltext (nombre, empresa, correo);

-- 2) Índices en oportunidades
ALTER TABLE oportunidades ADD INDEX idx_oportunidades_fecha_modificacion (fecha_modificacion);
ALTER TABLE oportunidades ADD INDEX idx_oportunidades_estado_fecha (estado, fecha_modificacion);
ALTER TABLE oportunidades ADD INDEX idx_oportunidades_cliente (cliente_id);

-- 3) Índices en reuniones
ALTER TABLE reuniones ADD INDEX idx_reuniones_fecha (fecha);
-- Opcional compuesto si hay muchas consultas por cliente+fecha
-- ALTER TABLE reuniones ADD INDEX idx_reuniones_cliente_fecha (cliente_id, fecha);

-- 4) Índices en incidencias
ALTER TABLE incidencias ADD INDEX idx_incidencias_fecha_creacion (fecha_creacion);
ALTER TABLE incidencias ADD INDEX idx_incidencias_cliente (cliente_id);

-- 5) Notas de seguridad y mitigación
-- - Aplica los índices en horario de baja carga o en una réplica de lectura para evitar impacto en producción.
-- - Antes de aplicar, tomar un backup o snaphot de la base de datos.
-- - En tablas muy grandes, crear índices CON ALGORITHM=INPLACE si lo soporta tu versión de MySQL/MariaDB.
-- - Para revertir un índice:
--   ALTER TABLE <tabla> DROP INDEX <nombre_indice>;

-- 6) Validación rápida (ejemplos):
-- EXPLAIN SELECT COUNT(*) FROM clientes WHERE estado = 2 AND fecha_creacion BETWEEN '2025-11-01' AND '2025-11-30';
-- EXPLAIN SELECT o.*, c.nombre FROM oportunidades o LEFT JOIN clientes c ON o.cliente_id = c.id ORDER BY o.fecha_modificacion DESC LIMIT 1000;

-- 7) Siguientes pasos recomendados
-- - Habilitar slow_query_log en staging y recopilar queries > 0.5s.
-- - Ejecutar pt-query-digest sobre el slow log para priorizar optimizaciones.
-- - Considerar FULLTEXT o motor de búsqueda para autocompletados intensivos.
-- - Evaluar materializar contadores (ej: total_oportunidades en clientes) si los conteos son frecuentes.
