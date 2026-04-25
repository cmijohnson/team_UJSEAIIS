<?php
function getProjects($db) {
    $stmt = $db->query("SELECT * FROM projects ORDER BY start_date DESC");
    $projects = $stmt->fetchAll();
    
    foreach ($projects as &$proj) {
        $proj['images'] = json_decode($proj['images'], true) ?: [];
    }
    
    jsonResponse(['data' => $projects]);
}
