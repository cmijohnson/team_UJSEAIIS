<?php
function getArticles($db) {
    $category = $_GET['category'] ?? '';
    $sql = "SELECT id, title, category, cover_image, view_count, created_at FROM articles";
    $params = [];
    if ($category) {
        $sql .= " WHERE category = ?";
        $params[] = $category;
    }
    $sql .= " ORDER BY created_at DESC";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    jsonResponse(['data' => $stmt->fetchAll()]);
}

function getArticleDetail($db, $id) {
    $stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        jsonResponse(['error' => 'Article not found'], 404);
    }
    
    // 增加浏览量
    $db->prepare("UPDATE articles SET view_count = view_count + 1 WHERE id = ?")->execute([$id]);
    
    jsonResponse(['data' => $article]);
}
