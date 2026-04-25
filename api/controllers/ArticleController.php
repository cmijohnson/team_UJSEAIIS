<?php
require_once __DIR__ . '/BaseController.php';

class ArticleController extends BaseController {

    public function index() {
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $query = "SELECT a.id, a.title, a.category, a.cover_image, a.view_count, a.created_at, u.username as author_name 
                  FROM articles a 
                  LEFT JOIN admins u ON a.author_id = u.id";
        
        if ($category) {
            $query .= " WHERE a.category = :category";
        }
        
        $query .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        
        if ($category) {
            $stmt->bindParam(":category", $category);
        }
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        
        $stmt->execute();
        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM articles";
        if ($category) {
            $countQuery .= " WHERE category = :category";
        }
        $countStmt = $this->conn->prepare($countQuery);
        if ($category) {
            $countStmt->bindParam(":category", $category);
        }
        $countStmt->execute();
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        echo json_encode([
            "data" => $articles,
            "meta" => [
                "current_page" => $page,
                "per_page" => $limit,
                "total" => $total,
                "total_pages" => ceil($total / $limit)
            ]
        ]);
    }

    public function show($id) {
        // Update view count
        $updateQuery = "UPDATE articles SET view_count = view_count + 1 WHERE id = :id";
        $updateStmt = $this->conn->prepare($updateQuery);
        $updateStmt->bindParam(":id", $id);
        $updateStmt->execute();

        $query = "SELECT a.*, u.username as author_name 
                  FROM articles a 
                  LEFT JOIN admins u ON a.author_id = u.id 
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($article) {
            echo json_encode($article);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Article not found"]);
        }
    }

    public function create() {
        $user = $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->title) || !isset($data->content) || !isset($data->category)) {
            http_response_code(400);
            echo json_encode(["message" => "Missing required fields"]);
            return;
        }

        $query = "INSERT INTO articles (title, content, category, cover_image, author_id) VALUES (:title, :content, :category, :cover_image, :author_id)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":content", $data->content);
        $stmt->bindParam(":category", $data->category);
        $cover_image = isset($data->cover_image) ? $data->cover_image : null;
        $stmt->bindParam(":cover_image", $cover_image);
        $stmt->bindParam(":author_id", $user->id);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "Article created", "id" => $this->conn->lastInsertId()]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to create article"]);
        }
    }

    public function update($id) {
        $this->authenticate();
        $data = json_decode(file_get_contents("php://input"));

        $query = "UPDATE articles SET title = :title, content = :content, category = :category, cover_image = :cover_image WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":title", $data->title);
        $stmt->bindParam(":content", $data->content);
        $stmt->bindParam(":category", $data->category);
        $cover_image = isset($data->cover_image) ? $data->cover_image : null;
        $stmt->bindParam(":cover_image", $cover_image);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Article updated"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to update article"]);
        }
    }

    public function delete($id) {
        $this->authenticate();

        $query = "DELETE FROM articles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);

        if ($stmt->execute()) {
            echo json_encode(["message" => "Article deleted"]);
        } else {
            http_response_code(503);
            echo json_encode(["message" => "Unable to delete article"]);
        }
    }
}
?>