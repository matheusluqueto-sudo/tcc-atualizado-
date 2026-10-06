<?php
declare(strict_types=1);
date_default_timezone_set('America/Sao_Paulo');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax', 'use_strict_mode' => true]);
}
require_once __DIR__ . '/../config/conexao.php';
require_once __DIR__ . '/Models/EpiModel.php';
function e($value): string { return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function redirect(string $url): void { header('Location: ' . $url); exit; }
function csrf(): string {
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return '<input type="hidden" name="csrf" value="' . e($_SESSION['csrf']) . '">';
}
function avatar(array $person): string {
    if (preg_match('~^uploads/avatars/[a-f0-9]{32}\.jpg$~', $person['foto_url'] ?? '')) return $person['foto_url'];
    return in_array($person['foto_url'] ?? '', ['img/boy.png', 'img/woman.png'], true) ? $person['foto_url'] : 'img/avatar.svg';
}
function dateBr($date): string { return $date ? date('d/m/Y H:i', strtotime($date)) : '—'; }
function badge($status): string {
    $colors = ['Entregue'=>'success','Comprado'=>'success','Aprovada'=>'info','Em andamento'=>'info','Recusada'=>'danger','Não comprado'=>'danger','Pendente'=>'warning'];
    return '<span class="badge ' . ($colors[$status] ?? '') . '">' . e($status) . '</span>';
}
