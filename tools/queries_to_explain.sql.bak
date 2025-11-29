/* Lista de queries candidatas a analizar. El script tools/run_explain.php ejecutará EXPLAIN sobre cada una.
   Edita o añade queries según tu instalación. Mantén cada query terminada por ";" y una nueva línea.
*/

-- 1) Clientes - métricas mensuales (recomendado: usar BETWEEN con :desde/:hasta)
SELECT estado, COUNT(*) as total, DATE_FORMAT(fecha_creacion, '%Y-%m') as periodo
FROM clientes
WHERE 1=1 AND fecha_creacion BETWEEN :desde AND :hasta
GROUP BY estado, DATE_FORMAT(fecha_creacion, '%Y-%m')
ORDER BY periodo, estado;

-- 2) Indicadores clave - clientes ganados este mes
SELECT COUNT(*) as total FROM clientes WHERE estado = 2 AND fecha_creacion BETWEEN '2025-11-01' AND '2025-11-30';

-- 3) Prospectos nuevos vs mes anterior (subconsultas)
SELECT (SELECT COUNT(*) FROM clientes WHERE fecha_creacion BETWEEN '2025-11-01' AND '2025-11-30') as actual,
       (SELECT COUNT(*) FROM clientes WHERE fecha_creacion BETWEEN '2025-10-01' AND '2025-10-31') as anterior;

-- 4) Oportunidades (kanban) - carga masiva
SELECT o.*, c.nombre AS nombre_cliente FROM oportunidades o LEFT JOIN clientes c ON o.cliente_id = c.id ORDER BY o.fecha_modificacion DESC LIMIT 200;

-- 5) Mostrar clientes filtrados con joins (ejemplo con estado prospecto)
SELECT DISTINCT c.* FROM clientes c LEFT JOIN oportunidades o ON c.id = o.cliente_id WHERE c.estado = 0 ORDER BY c.fecha_creacion DESC LIMIT 200;

-- 6) Buscar clientes para Select2
SELECT id, nombre FROM clientes WHERE nombre LIKE :nombre ORDER BY nombre ASC LIMIT 10;

-- 7) Reuniones de la semana
SELECT r.id, r.titulo, r.fecha, r.hora_inicio, r.hora_fin, r.estado, c.nombre as cliente_nombre
FROM reuniones r
LEFT JOIN clientes c ON r.cliente_id = c.id
WHERE r.fecha BETWEEN :inicioSemana AND :finSemana
ORDER BY r.fecha, r.hora_inicio;

-- 8) Incidencias con join de cliente y usuario
SELECT i.*, c.nombre as nombre_cliente, u.nombre as nombre_usuario
FROM incidencias i
LEFT JOIN clientes c ON i.cliente_id = c.id
LEFT JOIN usuarios u ON i.usuario_id = u.id
ORDER BY i.fecha_creacion DESC LIMIT 200;

-- 9) Consultas de verificación de estructura (limitar)
SELECT id, nombre, usuario, perfil, foto, estado, ultimo_login FROM usuarios LIMIT 10;

-- 10) Conteo de oportunidades por cliente (puede canalizarse a contador materializado)
SELECT cliente_id, COUNT(*) as total FROM oportunidades GROUP BY cliente_id HAVING total > 0 LIMIT 100;

/* Añade aquí otras consultas que quieras explicar */
