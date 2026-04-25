<?php
function uploadImage($db) {
    if (!isset($_FILES['image'])) {
        jsonResponse(['error' => 'No image uploaded'], 400);
    }
    
    // 这里应该是上传到对象存储的逻辑
    // 为了演示，这里假设上传成功并返回一个占位图 URL
    $type = $_POST['type'] ?? 'content';
    $filename = $_FILES['image']['name'];
    $size = $_FILES['image']['size'];
    
    // 模拟的 URL
    $url = 'https://picsum.photos/800/600?random=' . rand(1, 1000);
    
    // 插入数据库
    $stmt = $db->prepare("INSERT INTO images (filename, url, type, size, uploaded_by) VALUES (?, ?, ?, ?, ?)");
    // 假设 uploaded_by = 1 (admin)
    $stmt->execute([$filename, $url, $type, $size, 1]);
    
    jsonResponse([
        'url' => $url,
        'filename' => $filename,
        'size' => $size
    ]);
}