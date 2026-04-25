<?php
// 跨域配置
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 数据库配置
define('DB_HOST', 'localhost');
define('DB_NAME', 'team_showcase');
define('DB_USER', 'root');
define('DB_PASS', '');

// JWT密钥 (简单模拟)
define('JWT_SECRET', 'team_showcase_secret_key_2024');

// 错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// JSON 响应助手
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}
