-- Script para crear la tabla 'incidencias' relacionada con 'clientes' y 'usuarios'
-- Ejecutar este script en la base de datos 'atlantisbd'

CREATE TABLE incidencias (
    id INT(11) NOT NULL AUTO_INCREMENT,
    correlativo VARCHAR(10) NOT NULL UNIQUE,
    nombre_incidencia VARCHAR(255) NOT NULL,
    cliente_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    fecha DATE NOT NULL,
    prioridad ENUM('alta', 'media', 'baja') NOT NULL DEFAULT 'media',
    observaciones TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Índices para mejorar rendimiento en consultas
CREATE INDEX idx_incidencias_cliente_id ON incidencias(cliente_id);
CREATE INDEX idx_incidencias_usuario_id ON incidencias(usuario_id);
CREATE INDEX idx_incidencias_fecha_creacion ON incidencias(fecha_creacion);
CREATE INDEX idx_incidencias_prioridad ON incidencias(prioridad);
