<?php
$db = pg_connect("host=localhost port=5432 dbname=test_db user=postgres password=tu_password");

if (!$db) {
    die("No se pudo conectar: " . pg_last_error());
}
?>
    