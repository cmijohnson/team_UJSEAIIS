<?php
require_once __DIR__ . '/BaseController.php';

class CompetitionController extends BaseController {

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
        $query = "SELECT * FROM competitions ORDER BY competition_date DESC, id DESC";
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

        if (!isset($data->name) || !isset($data->result)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"], JSON_UNESCAPED_UNICODE);
            return;
        }

        $query = "INSERT INTO competitions (name, description, competition_date, result, images)
                  VALUES (:name, :description, :competition_date, :result, :images)";
        $stmt = $this->conn->prepare($query);

        $name = $data->name;
        $description = isset($data->description) ? $data->description : '';
        $competitionDate = isset($data->competition_date) && $data->competition_date !== '' ? $data->competition_date : null;
        $result = $data->result;
        $images = $this->normalizeImages($data);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":competition_date", $competitionDate);
        $stmt->bindParam(":result", $result);
        $stmt->bindParam(":images", $images);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Competition created"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create competition"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function update($id) {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $query = "UPDATE competitions
                  SET name = :name, description = :description, competition_date = :competition_date,
                      result = :result, images = :images
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $name = isset($data->name) ? $data->name : '';
        $description = isset($data->description) ? $data->description : '';
        $competitionDate = isset($data->competition_date) && $data->competition_date !== '' ? $data->competition_date : null;
        $result = isset($data->result) ? $data->result : '';
        $images = $this->normalizeImages($data);

        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":competition_date", $competitionDate);
        $stmt->bindParam(":result", $result);
        $stmt->bindParam(":images", $images);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Competition updated"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update competition"], JSON_UNESCAPED_UNICODE);
        }
    }

    public function delete($id) {
        $this->authenticate();

        $query = "DELETE FROM competitions WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Competition deleted"], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete competition"], JSON_UNESCAPED_UNICODE);
        }
    }
}
?>
