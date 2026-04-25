<?php
require_once __DIR__ . '/BaseController.php';

class TimelineController extends BaseController {

    public function index() {
        $query = "SELECT * FROM timeline_events ORDER BY year DESC, event_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($events);
    }

    public function create() {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->year) || !isset($data->title) || !isset($data->description)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            return;
        }

        $query = "INSERT INTO timeline_events (year, title, description, event_date) VALUES (:year, :title, :description, :event_date)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":year", $data->year);
        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":description", $data->description);
        $event_date = isset($data->event_date) ? $data->event_date : null;
        $stmt->bindParam(":event_date", $event_date);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Timeline event created", "id" => $this->conn->lastInsertId()]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create timeline event"]);
        }
    }

    public function update($id) {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $query = "UPDATE timeline_events SET year = :year, title = :title, description = :description, event_date = :event_date WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":year", $data->year);
        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":description", $data->description);
        $event_date = isset($data->event_date) ? $data->event_date : null;
        $stmt->bindParam(":event_date", $event_date);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Timeline event updated"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update timeline event"]);
        }
    }

    public function delete($id) {
        $this->authenticate();

        $query = "DELETE FROM timeline_events WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Timeline event deleted"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete timeline event"]);
        }
    }
}
?>