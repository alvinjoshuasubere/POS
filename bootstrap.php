<?php
declare(strict_types=1);
ini_set('display_errors', '0');
error_reporting(E_ALL);
if (PHP_VERSION_ID < 80500) { http_response_code(500); exit('PHP 8.5 or newer required.'); }
if (session_status() !== PHP_SESSION_ACTIVE) {
 session_set_cookie_params(['httponly'=>true,'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off','samesite'=>'Lax','path'=>'/']);
 session_start();
}
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: strict-origin-when-cross-origin');
$config = require (file_exists(__DIR__.'/config.local.php') ? __DIR__.'/config.local.php' : __DIR__.'/config.php');
function db(): PDO {
 global $config;
 static $pdo;
 if (!$pdo) {
  $dsn = 'mysql:host='.$config['db_host'].';port='.$config['db_port'].';dbname='.$config['db_name'].';charset=utf8mb4';
  $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false]);
 }
 return $pdo;
}
function e(mixed $v): string {return htmlspecialchars((string)$v, ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');}
function csrf(): string { return $_SESSION['csrf'] ??= bin2hex(random_bytes(32)); }
function check_csrf(): void {if (!hash_equals(csrf(), (string)($_POST['csrf']??''))) {http_response_code(419); exit('Invalid form token. Refresh and retry.');}}
function require_login(): void {if (!isset($_SESSION['user'])) {header('Location: index.php?page=login');exit;}}
function is_admin(): bool {return ($_SESSION['user']['role']??'') === 'admin';}
function require_admin(): void {require_login();if(!is_admin()){http_response_code(403);exit('Access denied');}}
function redirect(string $page): never {header('Location: index.php?page='.$page);exit;}
function flash(string $message, bool $error=false): void {$_SESSION['flash']=['text'=>$message,'error'=>$error];}
function setting(string $key, string $default=''): string {static $cache=[];if(!array_key_exists($key,$cache)){try{$q=db()->prepare('SELECT value FROM settings WHERE name=?');$q->execute([$key]);$cache[$key]=$q->fetchColumn();}catch(Throwable){$cache[$key]=false;}}return $cache[$key]!==false ? (string)$cache[$key] : $default;}
function money(float|int|string $n): string {return '₱'.number_format((float)$n,2);}
function amount(mixed $v): int {if(!is_string($v)&&!is_numeric($v))throw new InvalidArgumentException('Invalid amount');$v=(string)$v;if(!preg_match('/^\d{1,9}(?:\.\d{1,2})?$/',$v))throw new InvalidArgumentException('Invalid money amount');$parts=explode('.',$v);return (int)$parts[0]*100 + (int)str_pad($parts[1]??'0',2,'0');}
function dec(int $cents): string {return number_format($cents/100,2,'.','');}
