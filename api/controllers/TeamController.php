<?php
require_once __DIR__ . '/BaseController.php';

class TeamController extends BaseController {

    public function info() {
        $data = [];

        // Members
        $stmt = $this->conn->prepare("SELECT * FROM members ORDER BY sort_order ASC");
        $stmt->execute();
        $data['members'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Advisors
        $stmt = $this->conn->prepare("SELECT * FROM advisors ORDER BY id ASC");
        $stmt->execute();
        $data['advisors'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Competitions
        $stmt = $this->conn->prepare("SELECT * FROM competitions ORDER BY competition_date DESC");
        $stmt->execute();
        $data['competitions'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Projects
        $stmt = $this->conn->prepare("SELECT * FROM projects ORDER BY start_date DESC");
        $stmt->execute();
        $data['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Timeline (History)
        $stmt = $this->conn->prepare("SELECT * FROM timeline_events ORDER BY year DESC, event_date DESC");
        $stmt->execute();
        $data['history'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($data);
    }
}
?>