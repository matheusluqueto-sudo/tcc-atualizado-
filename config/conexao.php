<?php
function conectar(): mysqli
{
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    $config = [
        'host' => '127.0.0.1',
        'user' => 'root',
        'password' => '',
        'database' => 'tcc',
        'port' => 3306,
    ];

    $arquivoPrivado = __DIR__ . '/conexao.local.php';
    if (is_file($arquivoPrivado)) {
        $configuracaoPrivada = require $arquivoPrivado;
        if (!is_array($configuracaoPrivada)) {
            throw new RuntimeException('Configuração privada inválida.');
        }
        $config = array_replace($config, $configuracaoPrivada);
    }

    $variaveis = [
        'host' => 'DB_HOST',
        'user' => 'DB_USER',
        'password' => 'DB_PASSWORD',
        'database' => 'DB_NAME',
        'port' => 'DB_PORT',
    ];
    foreach ($variaveis as $chave => $nome) {
        $valor = getenv($nome);
        if ($valor !== false) {
            $config[$chave] = $valor;
        }
    }

    $porta = filter_var($config['port'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 65535],
    ]);
    if ($porta === false) {
        throw new RuntimeException('Porta do banco de dados inválida.');
    }

    $db = new mysqli(
        (string) $config['host'],
        (string) $config['user'],
        (string) $config['password'],
        (string) $config['database'],
        $porta
    );
    $db->set_charset('utf8mb4');
    $db->query("SET time_zone = '-03:00'");

    return $db;
}
