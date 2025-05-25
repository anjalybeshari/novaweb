<?php
// test-db.php
$conn = include __DIR__ . '/config.php';

if ($conn->connect_error) {
    die('CONNECT ERROR: ' . $conn->connect_error);
}
echo 'OK – connected as ' . $conn->host_info;