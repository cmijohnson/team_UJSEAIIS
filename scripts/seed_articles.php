<?php
require_once __DIR__ . '/../api/config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    fwrite(STDERR, "DB_CONNECT_FAILED\n");
    exit(1);
}

$adminId = (int) $conn
    ->query("SELECT id FROM admins WHERE username = 'admin' LIMIT 1")
    ->fetchColumn();

if (!$adminId) {
    fwrite(STDERR, "ADMIN_NOT_FOUND\n");
    exit(1);
}

$articles = [
    [
        'id' => 1,
        'title' => '团队在全国大学生信息安全竞赛中获得一等奖',
        'category' => '比赛动态',
        'content' => "# 团队竞赛简报\n\n近日，团队参加全国大学生信息安全竞赛，并获得一等奖。\n\n## 本次成绩\n\n- Web 方向稳定拿分\n- 二进制方向完成关键突破\n- 团队协作效率明显提升\n\n## 后续计划\n\n1. 完善题目复盘资料\n2. 继续整理训练题库\n3. 输出公开技术总结\n"
    ],
    [
        'id' => 2,
        'title' => '新学期招新启动，欢迎对安全感兴趣的同学加入',
        'category' => '团队通知',
        'content' => "# 招新公告\n\n新学期团队招新已经启动，欢迎对以下方向感兴趣的同学报名：\n\n- Web 安全\n- 二进制安全\n- 区块链安全\n- 安全开发\n\n请准备简短的自我介绍和过往学习经历。\n"
    ],
    [
        'id' => 3,
        'title' => '成员发现开源项目高危漏洞并提交修复建议',
        'category' => '技术分享',
        'content' => "# 漏洞分析记录\n\n团队成员在审查某开源组件时发现一个高危安全问题，并已向上游提交修复建议。\n\n## 影响概述\n\n该问题可能导致未授权访问和敏感数据泄露。\n\n## 处理过程\n\n- 复现漏洞\n- 验证影响范围\n- 提交补丁建议\n- 跟进修复进度\n"
    ]
];

$stmt = $conn->prepare(
    "UPDATE articles
     SET title = :title, content = :content, category = :category, cover_image = '', author_id = :author_id
     WHERE id = :id"
);

foreach ($articles as $article) {
    $stmt->execute([
        ':id' => $article['id'],
        ':title' => $article['title'],
        ':content' => $article['content'],
        ':category' => $article['category'],
        ':author_id' => $adminId
    ]);
}

echo "SEEDED_UTF8_OK\n";
