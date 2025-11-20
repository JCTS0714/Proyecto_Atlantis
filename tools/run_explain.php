<?php
// tools/run_explain.php
// Ejecuta EXPLAIN para cada query listada en tools/queries_to_explain.sql
// Requiere que el proyecto tenga la clase Conexion en Ventas/modelos/conexion.php
// Uso: php tools/run_explain.php

require_once __DIR__ . '/../Ventas/modelos/conexion.php';

$outDir = __DIR__ . '/../reports';
if (!is_dir($outDir)) mkdir($outDir, 0755, true);

$queriesFile = __DIR__ . '/queries_to_explain.sql';
if (!file_exists($queriesFile)) {
    echo "No se encontró $queriesFile\n";
    exit(1);
}

$content = file_get_contents($queriesFile);
// Separar por ; pero respetar ; dentro de delimitadores simples/ dobles es complejo;
// asumimos que el archivo contiene queries simples separadas por ";\n"
$parts = preg_split('/;\s*\n/', $content);

try {
    $db = Conexion::conectar();
} catch (Exception $e) {
    echo "No se pudo conectar a la base de datos: " . $e->getMessage() . "\n";
    exit(1);
}

$results = [];
$counter = 0;
foreach ($parts as $raw) {
    $sql = trim($raw);
    if ($sql === '' || stripos($sql, '/*') === 0) continue; // saltar comentarios

    $counter++;
    echo "[$counter] EXPLAIN para query:\n" . substr($sql, 0, 300) . "...\n";

    // Reemplazar placeholders comunes si existen (solo para EXPLAIN local)
    $replacements = [
        ':fechaLimite' => date('Y-m-d', strtotime('-7 days')),
        ':desde' => date('Y-m-01'),
        ':hasta' => date('Y-m-t'),
        ':inicioMes' => date('Y-m-01'),
        ':finMes' => date('Y-m-t'),
        ':inicioSemana' => date('Y-m-d', strtotime('monday this week')),
        ':finSemana' => date('Y-m-d', strtotime('sunday this week')),
        ':nombre' => '%test%'
    ];

    $explainSql = $sql;
    foreach ($replacements as $k => $v) {
        $explainSql = str_replace($k, $db->quote($v), $explainSql);
    }

    // Prepend EXPLAIN
    $stmt = $db->prepare('EXPLAIN ' . $explainSql);
    try {
        $stmt->execute();
        $explainRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $results[] = [
            'query_index' => $counter,
            'query' => $sql,
            'explain' => $explainRows
        ];
        echo "  OK - filas: " . count($explainRows) . "\n\n";
    } catch (Exception $e) {
        $results[] = [
            'query_index' => $counter,
            'query' => $sql,
            'error' => $e->getMessage()
        ];
        echo "  ERROR: " . $e->getMessage() . "\n\n";
    }
}

$outFile = $outDir . '/explain_results_' . date('Ymd_His') . '.json';
file_put_contents($outFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "Resultados guardados en: $outFile\n";

?>