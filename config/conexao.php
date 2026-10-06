<?php
function conectar(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $ports = getenv('DB_PORT') ? [(int) getenv('DB_PORT')] : [3306, 3307];
    foreach ($ports as $port) {
        try {
            $db = new mysqli(getenv('DB_HOST') ?: '127.0.0.1', getenv('DB_USER') ?: 'root', getenv('DB_PASSWORD') ?: '', getenv('DB_NAME') ?: 'tcc', $port);
            $db->set_charset('utf8mb4');
            $db->query("SET time_zone = '-03:00'");
            $db->query("SET time_zone = '-03:00'");
            return $db;
        } catch (mysqli_sql_exception $e) { $lastError = $e; }
    }
    throw new RuntimeException('Banco indisponível. Inicie o MySQL no XAMPP e confira a configuração.', 0, $lastError);
}
