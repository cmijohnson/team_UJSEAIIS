<?php
require_once __DIR__ . '/config/cors.php';
require_once __DIR__ . '/config/database.php';

function respond($data, $status = 200) {
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

function getConnectionSafe() {
    $database = new Database();
    return $database->getConnection();
}

function getHomeContentPayload($conn) {
    $introduction = '智链细米安全盾队专注于网络安全、区块链安全与攻防实践，持续参与竞赛、研究与开源安全建设。';
    $latestNews = [];

    if ($conn) {
        $stmt = $conn->prepare("SELECT setting_value FROM settings WHERE setting_key = 'team_introduction' LIMIT 1");
        $stmt->execute();
        $intro = $stmt->fetch();
        if ($intro && !empty($intro['setting_value'])) {
            $introduction = $intro['setting_value'];
        }

        $stmt = $conn->prepare("SELECT id, title, cover_image, created_at FROM articles ORDER BY created_at DESC LIMIT 5");
        $stmt->execute();
        $latestNews = $stmt->fetchAll();
    }

    if (empty($latestNews)) {
        $latestNews = [
            ['id' => 1, 'title' => '团队在全国大学生信息安全竞赛中获得一等奖', 'created_at' => '2023-10-15 00:00:00'],
            ['id' => 2, 'title' => '新学期招新启动，欢迎对安全感兴趣的同学加入', 'created_at' => '2023-09-01 00:00:00'],
            ['id' => 3, 'title' => '成员发现开源项目高危漏洞并提交修复建议', 'created_at' => '2023-08-20 00:00:00'],
        ];
    }

    return [
        'banner' => [
            ['id' => 1, 'url' => 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&q=80&w=1200'],
            ['id' => 2, 'url' => 'https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&q=80&w=1200'],
        ],
        'introduction' => $introduction,
        'latest_news' => $latestNews
    ];
}

function getMockArticles() {
    return [
        [
            'id' => 1,
            'title' => '团队在全国大学生信息安全竞赛中获得一等奖',
            'category' => '比赛动态',
            'cover_image' => '',
            'view_count' => 128,
            'created_at' => '2023-10-15 09:30:00',
            'author_name' => 'admin',
            'content' => "# 团队竞赛简报\n\n近日，团队参加全国大学生信息安全竞赛，并获得一等奖。\n\n## 本次成绩\n\n- Web 方向稳定拿分\n- 二进制方向完成关键突破\n- 团队协作效率明显提升\n\n## 后续计划\n\n1. 完善题目复盘资料\n2. 继续整理训练题库\n3. 输出公开技术总结\n"
        ],
        [
            'id' => 2,
            'title' => '新学期招新启动，欢迎对安全感兴趣的同学加入',
            'category' => '团队通知',
            'cover_image' => '',
            'view_count' => 86,
            'created_at' => '2023-09-01 18:00:00',
            'author_name' => 'admin',
            'content' => "# 招新公告\n\n新学期团队招新已经启动，欢迎对以下方向感兴趣的同学报名：\n\n- Web 安全\n- 二进制安全\n- 区块链安全\n- 安全开发\n\n请准备简短的自我介绍和过往学习经历。"
        ],
        [
            'id' => 3,
            'title' => '成员发现开源项目高危漏洞并提交修复建议',
            'category' => '技术分享',
            'cover_image' => '',
            'view_count' => 203,
            'created_at' => '2023-08-20 14:20:00',
            'author_name' => 'admin',
            'content' => "# 漏洞分析记录\n\n团队成员在审查某开源组件时发现一个高危安全问题，并已向上游提交修复建议。\n\n## 影响概述\n\n该问题可能导致未授权访问和敏感数据泄露。\n\n## 处理过程\n\n- 复现漏洞\n- 验证影响范围\n- 提交补丁建议\n- 跟进修复进度\n"
        ]
    ];
}

function getArticlesPayload($conn) {
    if ($conn) {
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        $offset = ($page - 1) * $limit;

        $query = "SELECT a.id, a.title, a.category, a.cover_image, a.view_count, a.created_at, u.username as author_name
                  FROM articles a
                  LEFT JOIN admins u ON a.author_id = u.id";

        if ($category) {
            $query .= " WHERE a.category = :category";
        }

        $query .= " ORDER BY a.created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($query);

        if ($category) {
            $stmt->bindParam(':category', $category);
        }
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll();

        $countQuery = "SELECT COUNT(*) as total FROM articles";
        if ($category) {
            $countQuery .= " WHERE category = :category";
        }
        $countStmt = $conn->prepare($countQuery);
        if ($category) {
            $countStmt->bindParam(':category', $category);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetch()['total'];

        return [
            'data' => $articles,
            'meta' => [
                'current_page' => $page,
                'per_page' => $limit,
                'total' => $total,
                'total_pages' => (int)ceil($total / $limit)
            ]
        ];
    }

    $articles = array_map(function ($article) {
        unset($article['content']);
        return $article;
    }, getMockArticles());

    return [
        'data' => $articles,
        'meta' => [
            'current_page' => 1,
            'per_page' => count($articles),
            'total' => count($articles),
            'total_pages' => 1
        ]
    ];
}

function getArticlePayload($conn, $id) {
    if ($conn) {
        $updateQuery = "UPDATE articles SET view_count = view_count + 1 WHERE id = :id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bindParam(':id', $id);
        $updateStmt->execute();

        $query = "SELECT a.*, u.username as author_name
                  FROM articles a
                  LEFT JOIN admins u ON a.author_id = u.id
                  WHERE a.id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $article = $stmt->fetch();

        if ($article) {
            return $article;
        }
    }

    foreach (getMockArticles() as $article) {
        if ((int)$article['id'] === (int)$id) {
            return $article;
        }
    }

    return null;
}

function getCompetitionsPayload($conn) {
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM competitions ORDER BY competition_date DESC");
        $stmt->execute();
        $competitions = $stmt->fetchAll();
        foreach ($competitions as &$competition) {
            $competition['images'] = json_decode($competition['images'], true) ?: [];
        }
        if (!empty($competitions)) {
            return $competitions;
        }
    }

    return [
        [
            'id' => 1,
            'name' => '全国大学生信息安全竞赛',
            'competition_date' => '2023-10-15',
            'result' => '全国一等奖',
            'description' => '团队在创新实践赛道中凭借稳定发挥取得优异成绩。',
            'images' => ['https://picsum.photos/600/400?random=101']
        ],
        [
            'id' => 2,
            'name' => 'XCTF 国际网络安全联赛',
            'competition_date' => '2023-06-20',
            'result' => '分站赛亚军',
            'description' => '在 48 小时攻防对抗中完成多个高难度挑战。',
            'images' => ['https://picsum.photos/600/400?random=102']
        ]
    ];
}

function getProjectsPayload($conn) {
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM projects ORDER BY start_date DESC");
        $stmt->execute();
        $projects = $stmt->fetchAll();
        foreach ($projects as &$project) {
            $project['images'] = json_decode($project['images'], true) ?: [];
        }
        if (!empty($projects)) {
            return $projects;
        }
    }

    return [
        [
            'id' => 1,
            'title' => '基于区块链的物联网设备身份认证系统',
            'status' => 'ongoing',
            'achievements' => '已完成原型设计与核心模块开发。',
            'description' => '面向海量设备场景，探索轻量级可信身份认证方案。',
            'start_date' => '2023-09-01',
            'images' => ['https://picsum.photos/400/300?random=201']
        ],
        [
            'id' => 2,
            'title' => '容器逃逸检测机制研究',
            'status' => 'completed',
            'achievements' => '形成可复用检测规则与实验数据集。',
            'description' => '面向云原生环境，研究基于系统调用序列的异常检测方法。',
            'start_date' => '2022-05-10',
            'images' => ['https://picsum.photos/400/300?random=202']
        ]
    ];
}

function getAdvisorsPayload($conn) {
    if ($conn) {
        $stmt = $conn->prepare("SELECT * FROM advisors ORDER BY id ASC");
        $stmt->execute();
        $advisors = $stmt->fetchAll();
        if (!empty($advisors)) {
            return $advisors;
        }
    }

    return [
        [
            'id' => 1,
            'name' => '王教授',
            'title' => '计算机学院教授 / 博士生导师',
            'research_fields' => '网络与信息安全、区块链安全、密码学',
            'profile' => '长期从事网络空间安全相关研究，指导多项安全方向课题。',
            'avatar' => 'https://randomuser.me/api/portraits/men/32.jpg',
            'contact' => 'wang@example.edu.cn'
        ],
        [
            'id' => 2,
            'name' => '李副教授',
            'title' => '网络空间安全系副主任',
            'research_fields' => '软件安全、漏洞挖掘、AI 安全',
            'profile' => '聚焦软件与系统安全研究，长期指导学生参加安全竞赛。',
            'avatar' => 'https://randomuser.me/api/portraits/women/44.jpg',
            'contact' => 'li@example.edu.cn'
        ]
    ];
}

$requestUri = strtok($_SERVER['REQUEST_URI'], '?');
$method = $_SERVER['REQUEST_METHOD'];
$path = strpos($requestUri, '/api') === 0 ? substr($requestUri, 4) : $requestUri;
$conn = getConnectionSafe();

if ($path === '/admin/login' && $method === 'POST') {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();
    exit();
}

if ($path === '/articles' && $method === 'GET') {
    respond(getArticlesPayload($conn));
}

if ($path === '/articles' && $method === 'POST') {
    require_once __DIR__ . '/controllers/ArticleController.php';
    $controller = new ArticleController();
    $controller->create();
    exit();
}

if (preg_match('#^/articles/(\d+)$#', $path, $matches) && $method === 'GET') {
    $article = getArticlePayload($conn, $matches[1]);
    if ($article) {
        respond($article);
    }

    respond(['message' => 'Article not found'], 404);
}

if (preg_match('#^/articles/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/controllers/ArticleController.php';
    $controller = new ArticleController();
    $controller->update($matches[1]);
    exit();
}

if (preg_match('#^/articles/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/controllers/ArticleController.php';
    $controller = new ArticleController();
    $controller->delete($matches[1]);
    exit();
}

if ($path === '/timeline' && $method === 'GET') {
    require_once __DIR__ . '/controllers/TimelineController.php';
    $controller = new TimelineController();
    $controller->index();
    exit();
}

if ($path === '/timeline' && $method === 'POST') {
    require_once __DIR__ . '/controllers/TimelineController.php';
    $controller = new TimelineController();
    $controller->create();
    exit();
}

if (preg_match('#^/timeline/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/controllers/TimelineController.php';
    $controller = new TimelineController();
    $controller->update($matches[1]);
    exit();
}

if (preg_match('#^/timeline/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/controllers/TimelineController.php';
    $controller = new TimelineController();
    $controller->delete($matches[1]);
    exit();
}

if ($path === '/upload/image' && $method === 'POST') {
    require_once __DIR__ . '/controllers/ImageController.php';
    $controller = new ImageController();
    $controller->upload();
    exit();
}

if ($path === '/team/info' && $method === 'GET') {
    require_once __DIR__ . '/controllers/TeamController.php';
    $controller = new TeamController();
    $controller->info();
    exit();
}

if ($path === '/members' && $method === 'GET') {
    require_once __DIR__ . '/controllers/MemberController.php';
    $controller = new MemberController();
    $controller->index();
    exit();
}

if ($path === '/members' && $method === 'POST') {
    require_once __DIR__ . '/controllers/MemberController.php';
    $controller = new MemberController();
    $controller->create();
    exit();
}

if (preg_match('#^/members/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/controllers/MemberController.php';
    $controller = new MemberController();
    $controller->update($matches[1]);
    exit();
}

if (preg_match('#^/members/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/controllers/MemberController.php';
    $controller = new MemberController();
    $controller->delete($matches[1]);
    exit();
}

if ($path === '/home/content' && $method === 'GET') {
    respond(getHomeContentPayload($conn));
}

if ($path === '/competitions' && $method === 'GET') {
    respond(getCompetitionsPayload($conn));
}

if ($path === '/competitions' && $method === 'POST') {
    require_once __DIR__ . '/controllers/CompetitionController.php';
    $controller = new CompetitionController();
    $controller->create();
    exit();
}

if (preg_match('#^/competitions/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/controllers/CompetitionController.php';
    $controller = new CompetitionController();
    $controller->update($matches[1]);
    exit();
}

if (preg_match('#^/competitions/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/controllers/CompetitionController.php';
    $controller = new CompetitionController();
    $controller->delete($matches[1]);
    exit();
}

if ($path === '/projects' && $method === 'GET') {
    respond(getProjectsPayload($conn));
}

if ($path === '/projects' && $method === 'POST') {
    require_once __DIR__ . '/controllers/ProjectController.php';
    $controller = new ProjectController();
    $controller->create();
    exit();
}

if (preg_match('#^/projects/(\d+)$#', $path, $matches) && $method === 'PUT') {
    require_once __DIR__ . '/controllers/ProjectController.php';
    $controller = new ProjectController();
    $controller->update($matches[1]);
    exit();
}

if (preg_match('#^/projects/(\d+)$#', $path, $matches) && $method === 'DELETE') {
    require_once __DIR__ . '/controllers/ProjectController.php';
    $controller = new ProjectController();
    $controller->delete($matches[1]);
    exit();
}

if ($path === '/advisors' && $method === 'GET') {
    respond(getAdvisorsPayload($conn));
}

respond(['message' => 'Not Found'], 404);
?>
