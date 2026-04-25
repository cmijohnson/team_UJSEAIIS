# 智链细米安全盾队展示网站

本项目是一个使用 Vue 3 + Vite 前端和 PHP + MySQL 后端构建的团队风采展示网站。包含前台展示页面和后台管理系统。

## 特性

- **前台展示**：首页、队伍风采（成员、指导老师、时间轴）、比赛成果、科研项目、文章详情。
- **后台管理**：管理员登录、文章管理（Markdown 支持）、时间轴管理。
- **对象存储**：支持图片上传接口（可扩展为 OSS/COS）。

## 快速开始

### 1. 数据库准备

1. 确保你已安装 MySQL 8.0+。
2. 登录 MySQL 命令行或使用可视化工具（如 Navicat, phpMyAdmin）。
3. 导入项目根目录下的 `api/db.sql` 文件来创建数据库和表，并初始化管理员账号。

默认管理员账号：
- 用户名：`admin`
- 密码：`password123`

### 2. 后端运行

1. 确保你已安装 PHP 8.0+ 并在系统环境变量中配置了 `php` 命令。
2. 配置数据库连接：如果你的 MySQL 用户名不是 `root` 或密码不是空，请修改 `api/config/database.php` 中的数据库配置信息。
3. 在项目根目录下，启动 PHP 内置服务器（默认运行在 8000 端口）：

```bash
php -S localhost:8000 router.php
```

### 3. 前端运行

1. 确保你已安装 Node.js (建议 v18+) 和 pnpm。
2. 在项目根目录下打开新的终端窗口，安装依赖：

```bash
pnpm install
```

3. 启动 Vite 开发服务器：

```bash
pnpm run dev
```

4. 在浏览器中访问 `http://localhost:5173`。

## 目录结构

- `api/`: PHP 后端代码
  - `config/`: 数据库和 CORS 配置
  - `controllers/`: 业务逻辑控制器
  - `utils/`: JWT 和 OSS 等工具类
  - `db.sql`: 数据库初始化脚本
- `src/`: Vue 前端代码
  - `api/`: Axios 接口请求封装
  - `pages/`: 页面组件
  - `components/`: 复用组件
  - `router/`: Vue Router 路由配置
  - `utils/`: 前端工具类
- `router.php`: PHP 开发服务器路由入口

## 注意事项

- **图片上传**：目前 `api/utils/OSS.php` 使用本地磁盘存储图片（存放在 `uploads/` 目录）。如果你需要使用阿里云 OSS 或腾讯云 COS，请修改该文件中的逻辑。
- **JWT 密钥**：在生产环境中，请务必修改 `api/utils/JWT.php` 中的 `$secret_key`。
- **跨域问题**：Vite 已经在 `vite.config.ts` 中配置了代理 `http://localhost:8000`，开发时不会有跨域问题。如果是生产环境部署，PHP 已经配置了基础的 CORS 响应头。