<?php
function adminLogin($db) {
    $input = json_decode(file_get_contents('php://input'), true);
    $username = $input['username'] ?? '';
    $password = $input['password'] ?? '';
    
    $stmt = $db->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password_hash'])) {
        // 简单生成一个 token
        $token = base64_encode(json_encode(['id' => $admin['id'], 'username' => $username, 'exp' => time() + 86400]));
        
        $db->prepare("UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?")->execute([$admin['id']]);
        
        jsonResponse([
            'token' => $token,
            'user_info' => [
                'id' => $admin['id'],
                'username' => $admin['username']
            ]
        ]);
    } else {
        jsonResponse(['error' => 'Invalid credentials'], 401);
    }
}
