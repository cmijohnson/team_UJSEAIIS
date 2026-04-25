<?php
require_once __DIR__ . '/../api/config/database.php';

$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    fwrite(STDERR, "Database connection failed.\n");
    exit(1);
}

$members = [
    [
        'name' => '张晨',
        'position' => '队长',
        'avatar' => '',
        'bio' => '负责 Web 安全方向训练与项目统筹，长期参与攻防演练与题目复盘。',
        'sort_order' => 1,
    ],
    [
        'name' => '李航',
        'position' => '核心成员',
        'avatar' => '',
        'bio' => '专注二进制分析与漏洞挖掘，参与多个攻防方向科研项目。',
        'sort_order' => 2,
    ],
    [
        'name' => '王越',
        'position' => '开发工程师',
        'avatar' => '',
        'bio' => '负责平台开发与自动化工具建设，支撑团队日常研发交付。',
        'sort_order' => 3,
    ],
    [
        'name' => '赵宁',
        'position' => '安全研究员',
        'avatar' => '',
        'bio' => '关注区块链与智能合约安全，参与审计和安全工具建设。',
        'sort_order' => 4,
    ],
];

$competitions = [
    [
        'name' => '全国大学生信息安全竞赛',
        'competition_date' => '2023-10-15',
        'result' => '全国一等奖',
        'description' => '团队在创新实践赛道中凭借稳定发挥取得优异成绩。',
        'images' => ['https://picsum.photos/600/400?random=101'],
    ],
    [
        'name' => 'XCTF 国际网络安全联赛',
        'competition_date' => '2023-06-20',
        'result' => '分站赛亚军',
        'description' => '在 48 小时攻防对抗中完成多个高难度挑战。',
        'images' => ['https://picsum.photos/600/400?random=102'],
    ],
];

$projects = [
    [
        'title' => '基于区块链的物联网设备身份认证系统',
        'status' => 'ongoing',
        'achievements' => '已完成原型设计与核心模块开发。',
        'description' => '面向海量设备场景，探索轻量级可信身份认证方案。',
        'start_date' => '2023-09-01',
        'end_date' => null,
        'images' => ['https://picsum.photos/400/300?random=201'],
    ],
    [
        'title' => '容器逃逸检测机制研究',
        'status' => 'completed',
        'achievements' => '形成可复用检测规则与实验数据集。',
        'description' => '面向云原生环境，研究基于系统调用序列的异常检测方法。',
        'start_date' => '2022-05-10',
        'end_date' => '2023-03-15',
        'images' => ['https://picsum.photos/400/300?random=202'],
    ],
];

function tableCount(PDO $conn, string $table): int {
    return (int) $conn->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
}

if (tableCount($conn, 'members') === 0) {
    $stmt = $conn->prepare(
        'INSERT INTO members (name, position, avatar, bio, sort_order) VALUES (:name, :position, :avatar, :bio, :sort_order)'
    );

    foreach ($members as $member) {
        $stmt->execute($member);
    }
}

if (tableCount($conn, 'competitions') === 0) {
    $stmt = $conn->prepare(
        'INSERT INTO competitions (name, description, competition_date, result, images) VALUES (:name, :description, :competition_date, :result, :images)'
    );

    foreach ($competitions as $competition) {
        $stmt->execute([
            'name' => $competition['name'],
            'description' => $competition['description'],
            'competition_date' => $competition['competition_date'],
            'result' => $competition['result'],
            'images' => json_encode($competition['images'], JSON_UNESCAPED_UNICODE),
        ]);
    }
}

if (tableCount($conn, 'projects') === 0) {
    $stmt = $conn->prepare(
        'INSERT INTO projects (title, description, status, achievements, images, start_date, end_date) VALUES (:title, :description, :status, :achievements, :images, :start_date, :end_date)'
    );

    foreach ($projects as $project) {
        $stmt->execute([
            'title' => $project['title'],
            'description' => $project['description'],
            'status' => $project['status'],
            'achievements' => $project['achievements'],
            'images' => json_encode($project['images'], JSON_UNESCAPED_UNICODE),
            'start_date' => $project['start_date'],
            'end_date' => $project['end_date'],
        ]);
    }
}

echo "Seed completed.\n";
