<?php
echo "PHP version: " . PHP_VERSION . "<br>";
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'desconocido') . "<br>";
echo "Fecha/hora del servidor: " . date('Y-m-d H:i:s');
