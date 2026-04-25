<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../utils/OSS.php';

class ImageController extends BaseController {

    public function upload() {
        $user = $this->authenticate();

        if (!isset($_FILES['image']) || !isset($_POST['type'])) {
            http_response_code(400);
            echo json_encode(["message" => "Missing image or type"]);
            return;
        }

        $file = $_FILES['image'];
        $type = $_POST['type'];
        
        // Validate type
        $allowed_types = ['avatar', 'banner', 'content', 'competition', 'project'];
        if (!in_array($type, $allowed_types)) {
            http_response_code(400);
            echo json_encode(["message" => "Invalid image type"]);
            return;
        }

        try {
            $url = OSS::upload($file, $type);
            
            // Save metadata to DB
            $query = "INSERT INTO images (filename, url, type, size, uploaded_by) VALUES (:filename, :url, :type, :size, :uploaded_by)";
            $stmt = $this->conn->prepare($query);
            $filename = basename($file["name"]);
            $size = $file["size"];
            
            $stmt->bindParam(":filename", $filename);
            $stmt->bindParam(":url", $url);
            $stmt->bindParam(":type", $type);
            $stmt->bindParam(":size", $size);
            $stmt->bindParam(":uploaded_by", $user->id);
            
            $stmt->execute();
            
            echo json_encode([
                "message" => "Image uploaded successfully",
                "url" => $url,
                "filename" => $filename
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["message" => $e->getMessage()]);
        }
    }
}
?>