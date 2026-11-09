<?php
if (!isset($_GET['token']) || $_GET['token'] !== 'hc_export_98234710293847') {
    http_response_code(403);
    die('Forbidden');
}

require_once __DIR__ . '/config/db.php';

header('Content-Type: text/plain; charset=utf-8');

echo "-- Hypecrews Database Export\n";
echo "SET FOREIGN_KEY_CHECKS=0;\n\n";

$tablesStmt = $pdo->query("SHOW TABLES");
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "-- Table: `$table`\n";
    echo "DROP TABLE IF EXISTS `$table`;\n";
    
    $createStmt = $pdo->query("SHOW CREATE TABLE `$table`");
    $createRow = $createStmt->fetch(PDO::FETCH_ASSOC);
    $createSql = $createRow['Create Table'] ?? array_values($createRow)[1];
    echo $createSql . ";\n\n";
    
    $rowsStmt = $pdo->query("SELECT * FROM `$table`");
    $rows = $rowsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($rows)) {
        $cols = array_keys($rows[0]);
        $escapedCols = array_map(function($c) { return "`" . str_replace("`", "``", $c) . "`"; }, $cols);
        $colList = implode(", ", $escapedCols);
        
        echo "INSERT INTO `$table` ($colList) VALUES\n";
        $valStrings = [];
        foreach ($rows as $row) {
            $vals = [];
            foreach ($row as $val) {
                if ($val === null) {
                    $vals[] = "NULL";
                } else {
                    $vals[] = $pdo->quote($val);
                }
            }
            $valStrings[] = "(" . implode(", ", $vals) . ")";
        }
        echo implode(",\n", $valStrings) . ";\n\n";
    }
}

echo "SET FOREIGN_KEY_CHECKS=1;\n";
