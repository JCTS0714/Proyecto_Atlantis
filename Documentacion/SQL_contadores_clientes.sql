-- =====================================================
-- TABLA PIVOTE: contadores_clientes
-- Relación many-to-many entre contadores y clientes
-- Ejecutar en la base de datos de producción
-- Fecha: 2025-12-02
-- =====================================================

-- Crear la tabla de relación
CREATE TABLE IF NOT EXISTS contadores_clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contador_id INT NOT NULL,
    cliente_id INT NOT NULL,
    fecha_asignacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Índices para búsquedas rápidas
    INDEX idx_contador (contador_id),
    INDEX idx_cliente (cliente_id),
    
    -- Evitar duplicados: un cliente solo puede estar una vez por contador
    UNIQUE KEY unique_contador_cliente (contador_id, cliente_id),
    
    -- Foreign keys (comentadas por si la BD no tiene las restricciones habilitadas)
    -- FOREIGN KEY (contador_id) REFERENCES contadores(id) ON DELETE CASCADE,
    -- FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- MIGRACIÓN DE DATOS EXISTENTES (OPCIONAL)
-- Si hay datos en el campo 'comercio' de contadores que 
-- coincidan con 'empresa' de clientes, migrarlos
-- =====================================================
-- INSERT IGNORE INTO contadores_clientes (contador_id, cliente_id)
-- SELECT c.id, cl.id
-- FROM contadores c
-- INNER JOIN clientes cl ON cl.empresa = c.comercio
-- WHERE c.comercio IS NOT NULL AND c.comercio != '';

-- =====================================================
-- VERIFICAR CREACIÓN
-- =====================================================
-- DESCRIBE contadores_clientes;
-- SELECT COUNT(*) FROM contadores_clientes;
