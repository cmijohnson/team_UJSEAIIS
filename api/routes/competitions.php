<?php
function getCompetitions($db) {
    $stmt = $db->query("SELECT * FROM competitions ORDER BY competition_date DESC");
    $competitions = $stmt->fetchAll();
    
    // 解析 JSON
    foreach ($competitions as &$comp) {
        $comp['images'] = json_decode($comp['images'], true) ?: [];
    }
    
    jsonResponse(['data' => $competitions]);
}
