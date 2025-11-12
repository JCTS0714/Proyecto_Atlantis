# Documentación Completa del Módulo Post-Ventas - Proyecto Atlantis

## Resumen Ejecutivo
El módulo de post-ventas en Proyecto Atlantis es un sistema integral para la gestión de soporte y seguimiento post-venta, compuesto por varios submódulos interconectados que permiten manejar incidencias, oportunidades, clientes y actividades de seguimiento.

## Arquitectura General

### Estructura de Directorios
```
Ventas/
├── ajax/                    # Llamadas AJAX para operaciones asíncronas
├── config/                  # Configuraciones del sistema
├── controladores/           # Lógica de negocio (MVC)
├── css/                     # Estilos personalizados
├── docs/                    # Documentación adicional
├── modelos/                 # Modelos de datos (MVC)
├── scripts/                 # Scripts SQL y utilitarios
├── utils/                   # Utilidades auxiliares
├── vistas/                  # Vistas y plantillas
│   ├── modulos/            # Módulos principales
│   ├── js/                 # JavaScript del frontend
│   └── plugins/            # Plugins externos
└── index.php               # Punto de entrada principal
```

## Estados del Sistema

### Estados de Clientes
- **ESTADO_PROSPECTO (0)**: Lista de prospectos
- **ESTADO_SEGUIMIENTO (1)**: Lista de seguimiento
- **ESTADO_CLIENTE (2)**: Lista de clientes
- **ESTADO_NO_CLIENTE (3)**: Lista de no-clientes
- **ESTADO_EN_ESPERA (4)**: Zona de espera
- **ESTADO_PERDIDO_KANBAN (5)**: Oportunidad Perdida

### Estados de Oportunidades (Kanban CRM)
- **KANBAN_NUEVO (1)**: Columna Nuevo
- **KANBAN_CALIFICADO (2)**: Columna Calificado
- **KANBAN_PROPUESTO (3)**: Columna Propuesto
- **KANBAN_GANADO (4)**: Columna Ganado
- **KANBAN_PERDIDO (5)**: Columna Perdido

## Módulos Principales

### 1. Gestión de Incidencias

#### Estructura de la Tabla `incidencias`
```sql
CREATE TABLE incidencias (
    id INT(11) NOT NULL AUTO_INCREMENT,
    correlativo VARCHAR(10) NOT NULL UNIQUE,
    nombre_incidencia VARCHAR(255) NOT NULL,
    cliente_id INT(11) NOT NULL,
    usuario_id INT(11) NOT NULL,
    fecha_solicitud DATE DEFAULT CURRENT_DATE,
    prioridad ENUM('alta', 'media', 'baja') NOT NULL DEFAULT 'media',
    observaciones TEXT,
    columna_backlog ENUM('En proceso', 'Validado', 'Terminado') NOT NULL DEFAULT 'En proceso',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Índices de rendimiento
CREATE INDEX idx_incidencias_cliente_id ON incidencias(cliente_id);
CREATE INDEX idx_incidencias_usuario_id ON incidencias(usuario_id);
CREATE INDEX idx_incidencias_fecha_creacion ON incidencias(fecha_creacion);
CREATE INDEX idx_incidencias_prioridad ON incidencias(prioridad);
CREATE INDEX idx_incidencias_columna_backlog ON incidencias(columna_backlog);
CREATE INDEX idx_incidencias_fecha_solicitud ON incidencias(fecha_solicitud);
```

#### Funcionalidades del Modelo de Incidencias
- **Generación automática de correlativos**: Formato 00001, 00002, etc.
- **Paginación**: Soporte para mostrar incidencias paginadas
- **Búsqueda por cliente**: Autocompletado con Select2
- **Filtrado por columna del backlog**: En proceso, Validado, Terminado
- **Priorización**: Alta, Media, Baja con colores diferenciados

#### Controlador de Incidencias
Maneja las operaciones CRUD:
- Crear incidencia con correlativo automático
- Editar incidencia existente
- Eliminar incidencia
- Mostrar incidencias paginadas o filtradas
- Generar correlativos únicos
- Buscar clientes para asignación

#### Vista de Incidencias
- **Tabla DataTable**: Listado completo con filtros y búsqueda
- **Modal de registro**: Formulario con validaciones
- **Modal de edición**: Actualización de datos existentes
- **Select2 para clientes**: Búsqueda en tiempo real
- **Generación automática de correlativos**

#### JavaScript de Incidencias
- **Carga dinámica**: AJAX para mostrar datos
- **Validaciones**: Cliente-side y server-side
- **SweetAlert2**: Notificaciones de usuario
- **DataTable**: Tabla interactiva con paginación
- **Select2**: Búsqueda de clientes con autocompletado

### 2. Backlog de Incidencias (Kanban)

#### Funcionalidades
- **Tablero Kanban**: Tres columnas (En proceso, Validado, Terminado)
- **Drag & Drop**: jQuery UI Sortable para mover tarjetas
- **Lista lateral**: Incidencias recientes para navegación rápida
- **Modal de detalles**: Edición inline de incidencias
- **Colores por prioridad**: Visualización diferenciada
- **Resaltado dinámico**: Animaciones al seleccionar desde lista lateral

#### Estructura del Backlog
```php
// Columnas del Kanban
$incidenciasEnProceso = ControladorIncidencias::ctrMostrarIncidenciasPorColumna("En proceso");
$incidenciasValidadas = ControladorIncidencias::ctrMostrarIncidenciasPorColumna("Validado");
$incidenciasTerminadas = ControladorIncidencias::ctrMostrarIncidenciasPorColumna("Terminado");
```

#### Funciones de Color y Prioridad
```php
function getPrioridadColor($prioridad) {
    switch ($prioridad) {
        case 'baja': return 'warning'; // amarillo
        case 'media': return 'warning'; // naranja
        case 'alta': return 'danger'; // rojo
    }
}

function getPrioridadBorder($prioridad) {
    switch ($prioridad) {
        case 'baja': return 'border-warning';
        case 'media': return 'border-info';
        case 'alta': return 'border-danger';
    }
}
```

#### AJAX del Backlog
- **actualizarColumna**: Actualiza la columna del backlog al mover tarjetas
- **obtenerDetalleIncidencia**: Carga datos para modal de edición
- **actualizarIncidencia**: Guarda cambios desde modal

### 3. Sistema CRM (Customer Relationship Management)

#### Estructura de la Tabla `oportunidades`
```sql
-- Tabla principal de oportunidades
CREATE TABLE oportunidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    valor_estimado DECIMAL(10,2),
    probabilidad INT,
    estado INT DEFAULT 1,
    fecha_cierre_estimada DATE,
    actividad VARCHAR(255),
    fecha_actividad DATE,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
```

#### Funcionalidades del CRM
- **Tablero Kanban**: Cuatro columnas (Seguimiento, Calificado, Propuesto, Ganado)
- **Creación de oportunidades**: Desde clientes existentes
- **Drag & Drop**: Movimiento entre estados
- **Filtros avanzados**: Por fecha, cliente, estado
- **Modal de detalles**: Edición completa de oportunidades
- **Zona de espera**: Prospectos pendientes

#### Modelo CRM
- **mdlMostrarOportunidades**: Listado con filtros opcionales
- **mdlRegistrarOportunidad**: Creación de nuevas oportunidades
- **mdlActualizarEstado**: Cambio de estado en Kanban
- **mdlActualizarOportunidad**: Edición completa
- **mdlCrearReunion**: Programación de reuniones
- **mdlMostrarClientesOrdenados**: Clientes con actividades recientes

### 4. Módulo de Seguimiento

#### Funcionalidades
- **Listado de clientes en seguimiento**: Estado = 1
- **Tabla DataTable**: Información completa de clientes
- **Edición de datos**: Modal de edición
- **Eliminación**: Con confirmación
- **Navegación**: Desde otros módulos

#### Vista de Seguimiento
```php
// Carga clientes en estado de seguimiento
$clientes = ControladorOportunidad::ctrMostrarClientes("estado", 1);
```

### 5. Gestión de Clientes

#### Estados y Transiciones
Los clientes pueden transitar entre diferentes estados según su relación con la empresa:
- Prospecto → Seguimiento → Cliente
- Cualquier estado → No cliente
- Cliente → Zona de espera

#### Funcionalidades de Clientes
- **Registro completo**: Datos personales, contacto, empresa
- **Actividades**: Historial de interacciones
- **Oportunidades**: Vinculación con CRM
- **Incidencias**: Soporte post-venta

## Flujo de Base de Datos

### Conexión Principal
```php
// Archivo: modelos/conexion.php
class Conexion {
    static public function conectar() {
        try {
            $link = new PDO("mysql:host=localhost;dbname=atlantisbd",
                           "usuario", "password");
            $link->exec("set names utf8");
            return $link;
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }
}
```

### Relaciones Principales
```
clientes (id) ← oportunidades (cliente_id)
clientes (id) ← incidencias (cliente_id)
usuarios (id) ← incidencias (usuario_id)
usuarios (id) ← oportunidades (usuario_id)
clientes (id) ← reuniones (cliente_id)
```

## Scripts de Base de Datos

### Creación de Tabla Incidencias
```sql
-- scripts/crear_tabla_incidencias.sql
-- Crear tabla con todas las columnas e índices
```

### Modificaciones de Tabla
```sql
-- scripts/alter_tabla_incidencias_backlog.sql
-- Agregar columna columna_backlog

-- scripts/alter_tabla_incidencias_fecha_solicitud.sql
-- Agregar columna fecha_solicitud

-- scripts/alter_tabla_incidencias_eliminar_fecha.sql
-- Eliminar columna fecha obsoleta
```

### Notificaciones
```sql
-- scripts/agregar_ultima_notificacion.sql
-- Agregar columna ultima_notificacion a reuniones
```

## Sistema de Notificaciones

### Consultas de Notificaciones
```php
// scripts/consulta_notificaciones.php
// Reuniones pendientes de notificar (hoy + 3 días)

SELECT r.*, c.nombre AS nombre_cliente
FROM reuniones r
LEFT JOIN clientes c ON r.cliente_id = c.id
WHERE r.usuario_id = :usuario_id
AND r.fecha BETWEEN :hoy AND DATE_ADD(:hoy, INTERVAL 3 DAY)
AND (r.ultima_notificacion IS NULL OR r.ultima_notificacion <> :hoy)
AND CONCAT(r.fecha, ' ', r.hora_fin) >= NOW()
```

### Programación de Notificaciones
- **Frecuencia**: Diaria
- **Anticipación**: Hasta 3 días antes
- **Alcance**: Reuniones del usuario logueado
- **Prevención de duplicados**: Campo `ultima_notificacion`

## Interfaz de Usuario

### Tecnologías Frontend
- **Bootstrap 3.3.7**: Framework CSS
- **jQuery**: Manipulación DOM
- **DataTables**: Tablas interactivas
- **Select2**: Campos de búsqueda
- **SweetAlert2**: Notificaciones
- **FullCalendar**: Calendario de eventos
- **jQuery UI**: Drag & Drop

### Estilos Personalizados
- **estilos_kanban.css**: Estilos específicos del Kanban
- **custom.css**: Personalizaciones generales
- **Colores por prioridad**: Amarillo (baja), Azul (media), Rojo (alta)

### JavaScript Modular
Cada módulo tiene su archivo JS dedicado:
- `incidencias.js`: Gestión de incidencias
- `backlog.js`: Funcionalidad Kanban
- `clientes.js`: Gestión de clientes
- `oportunidades.js`: CRM
- `calendario.js`: Calendario de reuniones

## API AJAX

### Endpoints Principales

#### Incidencias
- `ajax/incidencias.ajax.php?action=generarCorrelativo`
- `ajax/incidencias.ajax.php?action=buscarClientes`
- `ajax/incidencias.ajax.php?action=mostrarIncidencias`
- `ajax/incidencias.ajax.php?action=obtenerIncidencia`

#### Backlog
- `ajax/backlog.ajax.php?action=actualizarColumna`
- `ajax/backlog.ajax.php?action=obtenerDetalleIncidencia`
- `ajax/backlog.ajax.php?action=actualizarIncidencia`

#### CRM
- `ajax/crm.ajax.php?action=mostrarOportunidades`
- `ajax/crm.ajax.php?action=actualizarEstado`
- `ajax/oportunidades.ajax.php?action=crearOportunidad`

#### Clientes
- `ajax/clientes.ajax.php?action=mostrarClientes`
- `ajax/clientes.ajax.php?action=editarCliente`

## Seguridad y Validaciones

### Validaciones del Sistema
- **Sesiones**: Control de acceso por usuario
- **CSRF**: Protección contra ataques cross-site
- **SQL Injection**: Prepared statements en todos los queries
- **XSS**: Sanitización de inputs
- **Validación de datos**: Client-side y server-side

### Permisos
- **Por módulo**: Acceso restringido según rol
- **Por acción**: Crear, editar, eliminar según permisos
- **Auditoría**: Registro de cambios importantes

## Flujo de Trabajo Post-Ventas

### 1. Recepción de Incidencia
1. Cliente reporta problema/incidencia
2. Se registra en el sistema con correlativo automático
3. Se asigna prioridad (alta/media/baja)
4. Se asigna a usuario responsable

### 2. Gestión en Backlog
1. Incidencia entra en "En proceso"
2. Se trabaja en la resolución
3. Se mueve a "Validado" cuando está lista
4. Se finaliza en "Terminado"

### 3. Seguimiento de Clientes
1. Prospectos pasan a seguimiento
2. Se crean oportunidades en CRM
3. Se programan reuniones y actividades
4. Se convierten en clientes activos

### 4. Soporte Continuo
1. Gestión de incidencias recurrentes
2. Seguimiento de satisfacción
3. Programación de mantenimientos
4. Notificaciones automáticas

## Métricas y Reportes

### KPIs del Sistema
- **Tiempo de resolución**: Promedio por prioridad
- **Satisfacción del cliente**: Encuestas post-resolución
- **Conversión de prospectos**: Tasa de conversión
- **Eficiencia del backlog**: Tiempo en cada columna

### Reportes Disponibles
- **Incidencias por mes**: Tendencias
- **Clientes por estado**: Distribución
- **Oportunidades por etapa**: Embudo de ventas
- **Reuniones programadas**: Calendario

## Consideraciones Técnicas

### Rendimiento
- **Índices de BD**: Optimizados para consultas frecuentes
- **Paginación**: Implementada en listados largos
- **Lazy loading**: Carga diferida de datos
- **Cache**: Implementado donde sea necesario

### Escalabilidad
- **Arquitectura MVC**: Separación clara de responsabilidades
- **Modularidad**: Componentes reutilizables
- **APIs REST**: Preparado para integración externa
- **Base de datos**: Estructura normalizada

### Mantenimiento
- **Logs**: Registro de errores y actividades
- **Backups**: Estrategia de respaldo de BD
- **Versionado**: Control de cambios en código
- **Documentación**: Actualizada y completa

## Guía de Implementación

### Requisitos del Sistema
- **PHP 7.0+**: Backend
- **MySQL 5.7+**: Base de datos
- **Apache/Nginx**: Servidor web
- **Composer**: Gestión de dependencias PHP

### Instalación
1. **Clonar repositorio**
2. **Configurar base de datos**: Ejecutar scripts SQL
3. **Instalar dependencias**: `composer install`
4. **Configurar conexión**: Editar `modelos/conexion.php`
5. **Cargar datos iniciales**: Ejecutar scripts de prueba

### Configuración Inicial
1. **Crear usuario administrador**
2. **Configurar estados del sistema**
3. **Cargar datos de prueba**
4. **Verificar permisos de archivos**

### Pruebas
- **Scripts de prueba**: `crear_datos_prueba_zona_espera.php`
- **Verificación de BD**: `verificar_estructura_bd.php`
- **Test de funcionalidades**: Archivos `test_*.html`

## Conclusión

El módulo de post-ventas de Proyecto Atlantis es un sistema completo y robusto que integra gestión de incidencias, CRM, seguimiento de clientes y soporte técnico. Su arquitectura modular y escalable permite una fácil expansión y mantenimiento, mientras que su interfaz intuitiva facilita el uso diario por parte del personal.

Esta documentación proporciona toda la información necesaria para entender, mantener y expandir el sistema de post-ventas, asegurando que cualquier desarrollador pueda continuar el trabajo o reimplementar el módulo si fuera necesario.
