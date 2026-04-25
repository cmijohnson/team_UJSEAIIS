<?php
require_once __DIR__ . '/BaseController.php';

class ProjectController extends BaseController {

    private function normalizeImages($data) {
        if (!isset($data->images)) {
            return json_encode([], JSON_UNESCAPED_UNICODE);
        }

        if (is_array($data->images)) {
            return json_encode($data->images, JSON_UNESCAPED_UNICODE);
        }

        return json_encode([], JSON_UNESCAPED_UNICODE);
    }

    public function index() {
        $query = "SELECT * FROM projects ORDER BY start_date DESC, id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item['images'] = json_decode($item['images'], true) ?: [];
        }

        echo json_encode($items, JSON_UNESCAPED_UNICODE);
    }

    public function create() {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || !isset($data->status)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"], JSON_UNESCAPED_UNICODE);
            return;
        }

        $query = "INSERT INTO projects (title, description, status, achievements, images, start_date, end_date)
                  VALUES (:title, :description, :status, :achievements, :images, :start_date, :end_date)";
        $stmt = $this->conn->prepare($query);

        $title = $data->title;
        $description = isset($data->description) ? $data->description : '';
        $status = $data->status;
        $achievements = isset($data->achievements) ? $data->achievements : '';
        $images = $this->normalizeImages($data);
        $startDate = isset($data->start_date) && $data->start_date !== '' ? $data->start_date : null;
        $endDate = isset($data->end_date) && $data->end_date !== '' ? $data->end_date : null;

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":achievements", $achievements);
        $stmt->bindParam(":images", $images);
        $stmt->bindParam(":start_date", $startDate);
        $stmt->bindParam(":end_date", $endDate);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Project created"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create project"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function update($id) {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $query = "UPDATE projects
                  SET title = :title, description = :description, status = :status,
                      achievements = :achievements, images = :images,
                      start_date = :start_date, end_date = :end_date
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $title = isset($data->title) ? $data->title : '';
        $description = isset($data->description) ? $data->description : '';
        $status = isset($data->status) ? $data->status : 'ongoing';
        $achievements = isset($data->achievements) ? $data->achievements : '';
        $images = $this->normalizeImages($data);
        $startDate = isset($data->start_date) && $data->start_date !== '' ? $data->start_date : null;
        $endDate = isset($data->end_date) && $data->end_date !== '' ? $data->end_date : null;

        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":achievements", $achievements);
        $stmt->bindParam(":images", $images);
        $stmt->bindParam(":start_date", $startDate);
        $stmt->bindParam(":end_date", $endDate);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Project updated"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update project"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function delete($id) {
        $this->authenticate();

        $query = "DELETE FROM projects WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Project deleted"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete project"], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
