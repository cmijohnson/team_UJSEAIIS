<?php
require_once __DIR__ . '/BaseController.php';

class MemberController extends BaseController {

    public function index() {
        $query = "SELECT * FROM members ORDER BY sort_order ASC, id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_UNESCAPED_UNICODE);
    }

    public function create() {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->name) || !isset($data->position)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"], JSON_UNESCAPED_UNICODE);
            return;
        }

        $query = "INSERT INTO members (name, position, avatar, bio, sort_order)
                  VALUES (:name, :position, :avatar, :bio, :sort_order)";
        $stmt = $this->conn->prepare($query);

        $name = $data->name;
        $position = $data->position;
        $avatar = isset($data->avatar) ? $data->avatar : '';
        $bio = isset($data->bio) ? $data->bio : '';
        $sortOrder = isset($data->sort_order) ? (int) $data->sort_order : 0;

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":position", $position);
        $stmt->bindParam(":avatar", $avatar);
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":sort_order", $sortOrder, PDO::PARAM_INT);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Member created"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create member"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function update($id) {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $query = "UPDATE members
                  SET name = :name, position = :position, avatar = :avatar, bio = :bio, sort_order = :sort_order
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = isset($data->name) ? $data->name : '';
        $position = isset($data->position) ? $data->position : '';
        $avatar = isset($data->avatar) ? $data->avatar : '';
        $bio = isset($data->bio) ? $data->bio : '';
        $sortOrder = isset($data->sort_order) ? (int) $data->sort_order : 0;

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":position", $position);
        $stmt->bindParam(":avatar", $avatar);
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":sort_order", $sortOrder, PDO::PARAM_INT);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Member updated"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update member"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function delete($id) {
        $this->authenticate();

        $query = "DELETE FROM members WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Member deleted"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete member"], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
