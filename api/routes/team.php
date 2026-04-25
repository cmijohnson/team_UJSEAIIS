<?php
function getTeamInfo($db) {
    $stmt = $db->query("SELECT * FROM members ORDER BY sort_order ASC");
    $members = $stmt->fetchAll();
    
    $stmt = $db->query("SELECT * FROM timeline_events ORDER BY year DESC, event_date DESC");
    $history = $stmt->fetchAll();
    
    jsonResponse([
        'members' => $members,
        'culture' => [
            'vision' => '成为顶尖的安全研究团队',
            'mission' => '探索前沿技术，培养卓越人才'
        ],
        'history' => $history
    ]);
}
