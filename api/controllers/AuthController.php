<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../utils/JWT.php';

class AuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->username) || !isset($data->password)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing username or password"]);
            return;
        }

        $query = "SELECT id, username, password_hash, email FROM admins WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $data->username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($data->password, $row['password_hash'])) {
                $token_payload = [
                    "iss" => "team_showcase",
                    "aud" => "team_showcase",
                    "iat" => time(),
                    "exp" => time() + (60 * 60 * 24), // 24 hours
                    "data" => [
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email']
                    ]
                ];

                $jwt = JWT::encode($token_payload);

                // Update last login
                $update_query = "UPDATE admins SET last_login = NOW() WHERE id = :id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(":id", $row['id']);
                $update_stmt->execute();

                http_response_code(200);
                echo json_encode([
                    "message" => "Login successful",
                    "token" => $jwt,
                    "user_info" => [
                        "id" => $row['id'],
                        "username" => $row['username'],
                        "email" => $row['email']
                    ]
                ]);
            } else {
                http_response_code(401);
                echo json_encode(["message" => "Invalid password"]);
            }
        } else {
            http_response_code(401);
            echo json_encode(["message" => "User not found"]);
        }
    }
}
?>