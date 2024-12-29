<?php
require_once 'config/database.php';

$stmt = $pdo->query("SELECT * FROM events");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($events);
echo "</pre>";
?>
