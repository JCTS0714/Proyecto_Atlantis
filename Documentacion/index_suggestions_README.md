# Índices sugeridos — instrucciones de aplicación y validación

Este README explica cómo aplicar los índices propuestos con seguridad y cómo validar su efecto.

Recomendación general
- Aplica estos cambios primero en un entorno de staging o en una réplica de lectura. No aplique directamente en producción sin pruebas.
- Hacer un backup y/o snapshot antes de aplicar índices.
- Aplica los ALTER TABLE en ventanas de baja carga.

Pasos para aplicar en Windows (XAMPP)
1. Abre PowerShell como administrador y entra en la carpeta del proyecto (si usas cliente MySQL local):

```powershell
cd C:\xampp\htdocs\Proyecto_Atlantis
```

2. Conéctate a MySQL (reemplaza usuario, contraseña y base):

```powershell
mysql -u root -p atlantisbd
# pega la contraseña cuando la solicite
```

3. Ejecuta el archivo con los ALTER TABLE (desde dentro del cliente mysql):

```sql
SOURCE C:/xampp/htdocs/Proyecto_Atlantis/Documentacion/index_suggestions.sql;
```

O desde PowerShell sin entrar al cliente:

```powershell
mysql -u root -p atlantisbd < C:/xampp/htdocs/Proyecto_Atlantis/Documentacion/index_suggestions.sql
```

Validación (EXPLAIN)
- Después de aplicar cada índice ejecuta EXPLAIN en las queries problemáticas (ejemplos):

```sql
EXPLAIN SELECT COUNT(*) FROM clientes WHERE estado = 2 AND fecha_creacion BETWEEN '2025-11-01' AND '2025-11-30';
EXPLAIN SELECT o.id, o.titulo, o.estado, c.nombre FROM oportunidades o LEFT JOIN clientes c ON o.cliente_id = c.id ORDER BY o.fecha_modificacion DESC LIMIT 100;
EXPLAIN SELECT id, nombre FROM clientes WHERE nombre LIKE 'term%' ORDER BY nombre ASC LIMIT 10;
```

- En EXPLAIN busca que la columna `type` deje de ser `ALL` y pase a `range`, `ref` o `eq_ref`, y que `Extra` no diga `Using filesort` para ORDER BY costosos.

Monitoreo adicional
- Habilitar slow query log en staging (temporalmente) para capturar queries costosas:

```sql
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 0.5; -- segundos
```

- Analiza slow log con `pt-query-digest` (Percona Toolkit) si está disponible.

Reversión
- Para eliminar un índice si causa problemas:

```sql
ALTER TABLE clientes DROP INDEX idx_clientes_estado_fecha;
```

Notas finales
- Si las búsquedas de Select2 siguen lentas con `%term%`, considera cambiar a búsquedas por prefijo (`term%`) o usar FULLTEXT / un motor de búsqueda externo.
- Si quieres, puedo generar un pequeño script que ejecute automáticamente EXPLAIN sobre las consultas problemáticas y compare resultados antes/después (lo preparo si lo pides).
