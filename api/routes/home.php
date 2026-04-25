<?php
function getHomeContent($db) {
    // 简单实现
    $stmt = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'team_introduction'");
    $intro = $stmt->fetch();
    
    $stmt = $db->query("SELECT * FROM articles ORDER BY created_at DESC LIMIT 5");
    $latest_news = $stmt->fetchAll();
    
    jsonResponse([
        'banner' => [
            ['id' => 1, 'url' => 'https://picsum.photos/1920/400?random=1'],
            ['id' => 2, 'url' => 'https://picsum.photos/1920/400?random=2']
        ],
        'introduction' => $intro ? $intro['setting_value'] : '我们是一支专业团队。',
        'latest_news' => $latest_news
    ]);
}
