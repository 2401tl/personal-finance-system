# 简账 · 个人记账系统（simple-book）

一个基于 **Laravel + MySQL + Bootstrap 5** 的个人记账 Web 应用，覆盖账单记录、账户管理、分类管理、收支统计与个人设置。前端页面由 Blade 渲染外壳，业务数据通过 `/api` 下的 JSON 接口读写，图表使用 Chart.js 绘制。

> 项目定位：数据可视化课程 / 课程设计项目，代码保持单体结构（前后端同仓库），便于本地运行与二次改造。

---

## 功能特性

| 模块 | 页面路由 | 主要能力 |
| --- | --- | --- |
| 登录 / 注册 | `/` | 手机号 + 密码登录、注册、记住我 |
| 仪表盘 | `/dashboard` | 本月收支概览、收支趋势、最近账单 |
| 账单管理 | `/transactions` | 账单增删改查、按类型 / 日期 / 父分类筛选、分页、CSV 导出 |
| 统计分析 | `/statistics` | 收入 / 支出 / 结余汇总、趋势图、分类占比、账户支出、支出排行 |
| 分类管理 | `/categories` | 两级分类（父分类 + 子分类）、图标与颜色、预算、排序 |
| 账户管理 | `/accounts` | 银行卡 / 支付宝 / 微信 / 现金账户、余额、账户间转账 |
| 个人中心 | `/profile` | 资料修改、头像上传、密码修改、记账天数与统计 |
| 系统设置 | `/settings` | 货币、日期格式、分页、预算提醒、主题、备份等偏好 |

**核心业务规则**

- 新增 / 修改 / 删除收支账单时，自动同步对应账户余额（收入加、支出减，修改先回滚旧值再应用新值）。
- 账户转账在数据库事务中加锁执行：校验双方账户与余额，扣减转出账户、增加转入账户，并生成一条 `transfer` 类型流水。
- 删除账户时，关联账单的 `account_id` / `transfer_to_account_id` 置空，不连带删除账单。
- 删除父分类时，其子分类一并删除；子分类不可再添加下级分类。
- 所有写操作均按 `user_id` 校验归属，非本人数据返回 403。

---

## 技术栈

**后端**

- PHP `^8.3`
- Laravel `^13.8`（`composer.json` 约束）
- MySQL（`utf8mb4`），表前缀可配置（示例 `t_`）
- Eloquent ORM、FormRequest 表单验证、API Resource 输出层、Service 层封装业务
- Session（Cookie）认证：`auth` 中间件 + CSRF Token
- Pest / PHPUnit 测试脚手架

**前端**

- Blade 模板 + 原生 JavaScript（`fetch`）
- Bootstrap `5.3.8`
- Chart.js `3.9.1`
- Flatpickr（日期选择，中文语言包）
- Font Awesome（图标）
- Vite + Tailwind 依赖已安装，但当前页面通过 `asset()` 直接引用 `public/` 静态资源

---

## 目录结构

```
simple-book/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 页面与 API 控制器
│   │   ├── Requests/           # 表单验证（Auth / Account / Category / Transaction / Profile）
│   │   └── Resources/          # 接口响应资源（User / Account / Category / Transaction）
│   ├── Models/                 # User、Account、Category、Transaction、UserSetting
│   ├── Services/               # AccountService、StatisticsService
│   └── Traits/ApiResponse.php  # 统一接口响应封装
├── bootstrap/app.php           # 路由与中间件注册（含 API 会话中间件）
├── config/                     # 框架配置
├── database/
│   ├── migrations/             # 用户、分类、账户、账单、设置等表结构
│   ├── seeders/                # 演示数据（用户 / 分类 / 账户 / 账单）
│   └── factories/
├── public/                     # 入口 index.php 与实际运行的前端资源
│   ├── css/  js/               # 各页面样式与脚本
│   ├── plugs/                  # Bootstrap、Chart.js、Flatpickr、Font Awesome
│   └── uploads/avatars/        # 用户头像上传目录
├── resources/
│   ├── views/                  # Blade 页面与公共组件（header / footer / nav）
│   ├── css/  js/  plugs/       # 与 public 下的同名资源副本（源文件）
├── routes/
│   ├── web.php                 # 页面路由
│   └── api.php                 # JSON 接口路由（自动挂载 /api 前缀）
├── tests/                      # Pest 测试
├── tools/                      # 辅助脚本（Python）
├── .env.example                # 环境变量模板
└── vite.config.js
```

---

## 数据模型

| 表 | 关键字段 | 说明 |
| --- | --- | --- |
| `users` | `name`、`email`、`phone`、`password`、`bio`、`avatar` | 用户；登录以 `phone` 作为账号 |
| `categories` | `user_id`、`type`、`parent_id`、`name`、`icon`、`color`、`budget`、`sort_order` | 两级分类，`parent_id` 自关联 |
| `accounts` | `user_id`、`name`、`type`、`balance`、`icon`、`note` | 账户，余额 `decimal(12,2)` |
| `transactions` | `user_id`、`category_id`、`account_id`、`transfer_to_account_id`、`type`、`amount`、`date`、`note` | 账单流水 |
| `user_settings` | `user_id`、`settings`(JSON) | 用户偏好，缺失时使用默认值 |

**枚举值**

- 账单类型 `type`：`expense`（支出）、`income`（收入）、`transfer`（转账）
- 分类类型 `type`：`expense`、`income`
- 账户类型 `type`：`bank`（银行卡）、`alipay`（支付宝）、`wechat`（微信支付）、`cash`（现金）

**关系**

```
User 1─n Category 1─n Transaction n─1 Account
User 1─n Account         User 1─1 UserSetting
Category parent_id ──> Category（父子自关联）
```

---

## 环境要求

- PHP >= 8.3，需启用 `pdo_mysql`、`mbstring`、`openssl`、`fileinfo`、`tokenizer`、`xml`、`ctype`
- MySQL >= 5.7（或 MariaDB 10.3+）
- Composer 2.x
- Node.js >= 18（可选，仅在需要 Vite 构建时使用）

---

## 快速开始

```bash
# 1. 安装后端依赖
composer install

# 2. 准备环境文件并生成应用密钥
copy .env.example .env        # Linux / macOS 使用 cp
php artisan key:generate

# 3. 在 MySQL 中创建数据库（表由迁移生成）
#    CREATE DATABASE simple_book DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 4. 按需修改 .env 中的数据库配置
#    DB_HOST / DB_PORT / DB_DATABASE / DB_USERNAME / DB_PASSWORD
#    DB_PREFIX=t_      # 表前缀，留空则不加前缀

# 5. 建表并写入演示数据
php artisan migrate --seed

# 6. 启动本地服务
php artisan serve
```

浏览器访问 <http://127.0.0.1:8000>。

> 若修改了 `DB_PREFIX`，迁移生成的表名会随之变化（如 `t_users`、`t_transactions`），执行 `migrate` 前请确保数据库为空或前缀一致。

### 演示账号

| 字段 | 值 |
| --- | --- |
| 手机号 | `13800138000` |
| 邮箱 | `test@example.com` |
| 密码 | `123456` |

演示数据包含餐饮 / 交通 / 购物 / 娱乐等支出分类及其子分类、工资 / 奖金 / 投资等收入分类，以及银行卡、支付宝、微信、现金四个账户和若干示例账单。

---

## API 一览

所有接口位于 `/api` 前缀下，除登录、注册外均需登录态（Session Cookie），并携带 CSRF Token（`public/js/common.js` 会自动从页面 `meta[name="csrf-token"]` 读取）。

统一响应结构：

```json
{
  "code": 200,
  "message": "success",
  "data": {}
}
```

### 认证

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| POST | `/api/login` | 登录（`username` 传手机号，`password`，可选 `remember`） |
| POST | `/api/register` | 注册（`username`、`email`、`phone`、`password`） |
| POST | `/api/logout` | 退出登录 |
| GET | `/api/me` | 当前登录用户 |

### 账单

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| GET | `/api/transactions` | 列表；支持 `type`、`start_date`、`end_date`、`parent_id`、`page_size`，返回分页与区间收支汇总 |
| POST | `/api/transactions` | 新增账单（`category_id`、`account_id`、`type`、`amount`、`date`、`note`） |
| PUT | `/api/transactions/{id}` | 修改账单 |
| DELETE | `/api/transactions/{id}` | 删除账单 |

### 分类

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| GET | `/api/categories` | 父分类列表（含子分类与账单数量），可按 `type` 过滤 |
| POST | `/api/categories` | 新增分类（父 / 子） |
| PUT | `/api/categories/{id}` | 修改分类 |
| DELETE | `/api/categories/{id}` | 删除分类（父分类会连带删除子分类） |

### 账户

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| GET | `/api/accounts` | 账户列表 + 总资产 / 账户数 / 本月支出统计 |
| POST | `/api/accounts` | 新增账户 |
| PUT | `/api/accounts/{id}` | 修改账户 |
| DELETE | `/api/accounts/{id}` | 删除账户 |
| POST | `/api/accounts/transfer` | 账户转账（`from_account_id`、`to_account_id`、`amount`、`note`） |

### 统计与用户

| 方法 | 路径 | 说明 |
| --- | --- | --- |
| GET | `/api/dashboard` | 仪表盘数据，`period` 可选 `week` / `month` / `year` / `all` |
| GET | `/api/statistics` | 汇总、趋势、支出分类、收入分类、账户支出、支出排行；`period=range` 时配合 `start_date`、`end_date` |
| GET | `/api/profile` | 个人资料与统计（记账天数、记录数、分类数、月均支出） |
| PUT | `/api/profile` | 修改资料 |
| PUT | `/api/profile/password` | 修改密码 |
| GET | `/api/settings` | 读取用户设置（不存在则返回默认值） |
| PUT | `/api/settings` | 更新用户设置（按字段合并） |

页面级头像上传走 `POST /user/avatar`（`multipart/form-data`，字段名 `avatar`，最大 10MB，保存至 `public/uploads/avatars/`）。

---

## 关键配置（`.env`）

| 变量 | 示例 | 说明 |
| --- | --- | --- |
| `APP_NAME` | `个人记账系统` | 站点名称，显示在页面标题 |
| `APP_URL` | `http://localhost:8000` | 应用访问地址 |
| `DB_CONNECTION` | `mysql` | 数据库驱动 |
| `DB_DATABASE` | `simple_book` | 数据库名 |
| `DB_PREFIX` | `t_` | 表前缀，留空为无前缀 |
| `SESSION_DRIVER` | `file` | 会话驱动（也可用 `database`） |
| `APP_UPLOAD_ACC_PATH` | `/uploads/avatars/` | 头像上传相对路径 |

注意：`public/uploads/avatars/` 目录需具备写权限。

---

## 开发与测试

```bash
# 代码风格检查（Laravel Pint）
./vendor/bin/pint

# 运行测试
php artisan test

# 同时启动 Web 服务、队列与 Vite（composer.json 已配置）
composer run dev
```

---

## 当前完成度与后续可做

- 后端接口（认证、账单、分类、账户、统计、资料、设置）已实现，账户页与分类页前端已接入 `/api`。
- 仪表盘与统计分析页的前端脚本（`public/js/dashboard.js`、`public/js/statistics.js`）目前使用内置演示数据，后端 `/api/dashboard`、`/api/statistics` 已就绪，替换为 `fetch` 调用即可切换真实数据。
- 账单列表页、个人中心、系统设置页仍含静态原型交互，可按需逐步对接接口。
- 设置项中的预算告警、每日提醒、自动备份等目前仅存偏好值，尚未实现对应业务逻辑。
- `resources/css|js|plugs` 与 `public/` 下资源为副本关系，改动时请保持同步或收敛为单一来源。

---

## 许可证

基于 Laravel 骨架项目，遵循 [MIT license](https://opensource.org/licenses/MIT)。
