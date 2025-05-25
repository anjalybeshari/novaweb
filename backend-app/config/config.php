<?php

$host     = '192.168.1.210';    // your server’s LAN or public IP
$user     = 'ani';              // the MySQL user you created for her
$password = 'ani_pw';           // the password you set for ani
$database = 'NOVA';
$port     = 3306;               // change only if you’re using a non‐standard port

// Note: mysqli’s constructor signature is mysqli($host, $user, $pass, $db, $port)
$conn = new mysqli($host, $user, $password, $database, $port);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

return $conn;
?>