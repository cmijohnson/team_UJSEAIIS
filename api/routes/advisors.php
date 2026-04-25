<?php
function getAdvisors($db) {
    $stmt = $db->query("SELECT * FROM advisors ORDER BY id ASC");
    $advisors = $stmt->fetchAll();
    jsonResponse(['data' => $advisors]);
}
