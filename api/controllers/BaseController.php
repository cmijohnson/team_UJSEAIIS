<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/JWT.php';

class BaseController {
    protected $db;
    protected $conn;
    protected $user;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    protected function authenticate() {
        $token = JWT::get_bearer_token();
        if (!$token) {
            http_response_code(401);
            echo json_encode(["message" => "Access denied. No token provided."]);
            exit();
        }

        $decoded = JWT::decode($token);
        if (!$decoded) {
            http_response_code(401);
            echo json_encode(["message" => "Access denied. Invalid token."]);
            exit();
        }

        $this->user = $decoded->data;
        return $this->user;
    }
}
?>