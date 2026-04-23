<?php
require_once "config/conexion.php";

$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

echo "Tablas en la base de datos:\n";
foreach ($tables as $table) {
    echo "- $table\n";
    $columns = $conn->query("DESCRIBE $table");
    while ($col = $columns->fetch_assoc()) {
        echo "  * {$col['Field']} ({$col['Type']})\n";
    }
}
?>