<?php
/**
 * Zuri Motors - Configuration
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_strict_mode', 1);
    session_start();
}

// ============================================================
// DATABASE - YOUR REAL CREDENTIALS
// ============================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'vxjtgclw_motors');
define('DB_USER', 'vxjtgclw_motors');
define('DB_PASS', '9Ggd9@0QC4?[,?85');
define('DB_CHARSET', 'utf8mb4');

// ============================================================
// PATHS
// ============================================================
define('ROOT_PATH', __DIR__);
// BASE_URL must always point to the application root (where assets/ live), not the current script path.
// Using SCRIPT_NAME would make BASE_URL = /admin or /cars when in subdirs, breaking CSS/JS (e.g. /admin/assets/... 404).
$docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$rootPathFs = rtrim(str_replace('\\', '/', ROOT_PATH), '/');
if ($docRoot !== '' && strpos($rootPathFs, $docRoot) === 0) {
    $basePath = trim(str_replace($docRoot, '', $rootPathFs), '/');
    $basePath = $basePath !== '' ? '/' . $basePath : '';
} else {
    $basePath = rtrim(str_replace('/index.php', '', $_SERVER['SCRIPT_NAME'] ?? ''), '/');
}
define('BASE_URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $basePath);
define('UPLOAD_PATH', ROOT_PATH . '/uploads');
define('UPLOAD_URL', BASE_URL . '/uploads');
define('CARS_UPLOAD_PATH', UPLOAD_PATH . '/cars');
define('AVATARS_UPLOAD_PATH', UPLOAD_PATH . '/avatars');
define('LOGOS_UPLOAD_PATH', UPLOAD_PATH . '/logos');

// ============================================================
// DATABASE CONNECTION
// ============================================================
class Database {
    private static ?PDO $instance = null;
    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                self::$instance = new PDO(
                    'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
                    DB_USER, DB_PASS,
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false]
                );
            } catch (PDOException $e) {
                die('Database connection failed: ' . $e->getMessage());
            }
        }
        return self::$instance;
    }
}

function db(): PDO { return Database::getInstance(); }

// ============================================================
// RESOLVE URL - handles local and external URLs
// ============================================================
function resolveUrl(?string $path): string {
    if (!$path) return '';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '//')) return $path;
    return BASE_URL . '/' . ltrim($path, '/');
}

// ============================================================
// SETTINGS
// ============================================================
class Settings {
    private static array $cache = [];
    private static bool $loaded = false;

    public static function loadAll(): void {
        if (self::$loaded) return;
        try {
            $stmt = db()->query("SELECT setting_key, setting_value FROM settings");
            while ($row = $stmt->fetch()) self::$cache[$row['setting_key']] = $row['setting_value'];
            self::$loaded = true;
        } catch (PDOException $e) {}
    }

    public static function get(string $key, string $default = ''): string {
        self::loadAll();
        return self::$cache[$key] ?? $default;
    }

    public static function set(string $key, string $value, string $group = 'general'): bool {
        try {
            db()->prepare("INSERT INTO settings (setting_key, setting_value, setting_group) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)")
                ->execute([$key, $value, $group]);
            self::$cache[$key] = $value;
            return true;
        } catch (PDOException $e) { return false; }
    }

    public static function getByGroup(string $group): array {
        self::loadAll();
        try {
            $stmt = db()->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_group = ?");
            $stmt->execute([$group]);
            return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (PDOException $e) { return []; }
    }

    public static function clearCache(): void { self::$cache = []; self::$loaded = false; }
}

Settings::loadAll();

// ============================================================
// AUTH
// ============================================================
class Auth {
    public static function check(): bool { return !empty($_SESSION['user_id']); }

    public static function user(): ?array {
        if (!self::check()) return null;
        if (isset($_SESSION['user_data'])) return $_SESSION['user_data'];
        try {
            $stmt = db()->prepare("SELECT * FROM users WHERE id = ? AND is_active = 1");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
            if ($user) { unset($user['password']); $_SESSION['user_data'] = $user; }
            return $user ?: null;
        } catch (PDOException $e) { return null; }
    }

    public static function id(): ?int { return $_SESSION['user_id'] ?? null; }
    public static function role(): ?string { $u = self::user(); return $u['role'] ?? null; }
    public static function isAdmin(): bool { return in_array(self::role(), ['admin', 'staff']); }
    public static function isVendor(): bool { return self::role() === 'vendor'; }

    public static function login(string $email, string $password): array {
        try {
            $stmt = db()->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            if (!$user || !password_verify($password, $user['password']))
                return ['success' => false, 'message' => 'Invalid email or password.'];
            db()->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?")->execute([$user['id']]);
            $_SESSION['user_id'] = $user['id'];
            unset($user['password']);
            $_SESSION['user_data'] = $user;
            self::logActivity('login', 'user', $user['id'], 'User logged in');
            return ['success' => true, 'user' => $user];
        } catch (PDOException $e) { return ['success' => false, 'message' => 'Login error.']; }
    }

    public static function logout(): void {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p["path"], $p["domain"], $p["secure"], $p["httponly"]);
        }
        session_destroy();
    }

    public static function requireLogin(string $redirect = '/login'): void {
        if (!self::check()) { $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; header('Location: ' . BASE_URL . $redirect); exit; }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) { header('Location: ' . BASE_URL . '/'); exit; }
    }

    public static function requireVendor(): void {
        self::requireLogin();
        if (!self::isVendor() && !self::isAdmin()) { header('Location: ' . BASE_URL . '/'); exit; }
    }

    public static function logActivity(string $action, ?string $entityType = null, ?int $entityId = null, ?string $description = null): void {
        try {
            db()->prepare("INSERT INTO activity_log (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)")
                ->execute([self::id(), $action, $entityType, $entityId, $description, $_SERVER['REMOTE_ADDR'] ?? null]);
        } catch (PDOException $e) {}
    }
}

// ============================================================
// CSRF
// ============================================================
class CSRF {
    public static function generate(): string {
        if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
    public static function field(): string { return '<input type="hidden" name="csrf_token" value="' . self::generate() . '">'; }
    public static function verify(?string $token = null): bool {
        $token = $token ?? ($_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '');
        return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    public static function verifyOrDie(): void { if (!self::verify()) { http_response_code(403); die('Invalid security token.'); } }
}

// ============================================================
// FLASH MESSAGES
// ============================================================
class Flash {
    public static function success(string $msg): void { $_SESSION['flash'][] = ['type'=>'success','msg'=>$msg]; }
    public static function error(string $msg): void { $_SESSION['flash'][] = ['type'=>'error','msg'=>$msg]; }
    public static function warning(string $msg): void { $_SESSION['flash'][] = ['type'=>'warning','msg'=>$msg]; }
    public static function info(string $msg): void { $_SESSION['flash'][] = ['type'=>'info','msg'=>$msg]; }
    
    public static function render(): string {
        $msgs = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        if (empty($msgs)) return '';
        $html = '';
        $colors = ['success'=>'bg-emerald-50 border-emerald-400 text-emerald-800','error'=>'bg-red-50 border-red-400 text-red-800','warning'=>'bg-amber-50 border-amber-400 text-amber-800','info'=>'bg-blue-50 border-blue-400 text-blue-800'];
        $icons = ['success'=>'check-circle','error'=>'x-circle','warning'=>'alert-triangle','info'=>'info'];
        foreach ($msgs as $m) {
            $c = $colors[$m['type']] ?? $colors['info'];
            $i = $icons[$m['type']] ?? 'info';
            $html .= '<div class="flash-message border-l-4 px-4 py-3 rounded-r-lg mb-3 flex items-center gap-3 ' . $c . '"><i data-lucide="' . $i . '" class="w-5 h-5 flex-shrink-0"></i><span class="text-sm font-medium">' . htmlspecialchars($m['msg']) . '</span><button onclick="this.parentElement.remove()" class="ml-auto opacity-60 hover:opacity-100"><i data-lucide="x" class="w-4 h-4"></i></button></div>';
        }
        return $html;
    }
}

// ============================================================
// PAGINATION
// ============================================================
class Pagination {
    public int $page, $perPage, $total, $totalPages, $offset;
    
    public function __construct(int $total, int $perPage = 12, ?int $page = null) {
        $this->perPage = max(1, $perPage);
        $this->total = max(0, $total);
        $this->totalPages = max(1, (int)ceil($this->total / $this->perPage));
        $this->page = max(1, min($page ?? (int)($_GET['page'] ?? 1), $this->totalPages));
        $this->offset = ($this->page - 1) * $this->perPage;
    }

    public function render(string $baseUrl = '', array $params = []): string {
        if ($this->totalPages <= 1) return '';
        $html = '<nav class="flex items-center justify-center gap-1 mt-6">';
        if ($this->page > 1) { $params['page'] = $this->page - 1; $html .= '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">&laquo;</a>'; }
        $start = max(1, $this->page - 2); $end = min($this->totalPages, $this->page + 2);
        if ($start > 1) { $params['page'] = 1; $html .= '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">1</a>'; if ($start > 2) $html .= '<span class="px-2 text-gray-400">...</span>'; }
        for ($i = $start; $i <= $end; $i++) { $params['page'] = $i; $active = $i === $this->page ? 'bg-emerald-600 text-white' : 'text-gray-600 hover:bg-gray-100'; $html .= '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="px-3 py-2 rounded-lg text-sm font-medium ' . $active . '">' . $i . '</a>'; }
        if ($end < $this->totalPages) { if ($end < $this->totalPages - 1) $html .= '<span class="px-2 text-gray-400">...</span>'; $params['page'] = $this->totalPages; $html .= '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">' . $this->totalPages . '</a>'; }
        if ($this->page < $this->totalPages) { $params['page'] = $this->page + 1; $html .= '<a href="' . $baseUrl . '?' . http_build_query($params) . '" class="px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-100">&raquo;</a>'; }
        $html .= '</nav>';
        $html .= '<p class="text-center text-sm text-gray-500 mt-2">Showing ' . ($this->offset + 1) . '-' . min($this->offset + $this->perPage, $this->total) . ' of ' . number_format($this->total) . '</p>';
        return $html;
    }
}

// ============================================================
// IMAGE UPLOAD
// ============================================================
class ImageUpload {
    public static function upload(array $file, string $dest, ?string $prefix = null): array {
        if ($file['error'] !== UPLOAD_ERR_OK) return ['success' => false, 'message' => 'Upload error'];
        if ($file['size'] > 10485760) return ['success' => false, 'message' => 'Max 10MB'];
        $allowed = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml'];
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
        if (!in_array($mime, $allowed)) return ['success' => false, 'message' => 'Invalid type'];
        if (!is_dir($dest)) mkdir($dest, 0755, true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION) ?: 'jpg';
        $fn = ($prefix ?? 'img') . '_' . uniqid() . '_' . time() . '.' . strtolower($ext);
        $fp = rtrim($dest, '/') . '/' . $fn;
        if (move_uploaded_file($file['tmp_name'], $fp))
            return ['success' => true, 'filename' => $fn, 'filepath' => $fp, 'url' => str_replace(ROOT_PATH, '', $fp)];
        return ['success' => false, 'message' => 'Save failed'];
    }

    public static function uploadMultiple(array $files, string $dest, ?string $prefix = null): array {
        $results = [];
        for ($i = 0; $i < count($files['name']); $i++) {
            $results[] = self::upload(['name'=>$files['name'][$i],'type'=>$files['type'][$i],'tmp_name'=>$files['tmp_name'][$i],'error'=>$files['error'][$i],'size'=>$files['size'][$i]], $dest, $prefix);
        }
        return $results;
    }

    public static function delete(string $filepath): bool {
        $full = ROOT_PATH . '/' . ltrim($filepath, '/');
        return file_exists($full) ? unlink($full) : false;
    }
}

// ============================================================
// HELPERS
// ============================================================
function formatPrice($amount, bool $cur = true): string { $s = Settings::get('currency_symbol', 'KSh'); return $cur ? $s . ' ' . number_format((float)$amount, 0, '.', ',') : number_format((float)$amount, 0, '.', ','); }
function formatDate(?string $date, string $fmt = 'M d, Y'): string { return $date ? date($fmt, strtotime($date)) : '-'; }
function timeAgo(string $dt): string { $d = (new DateTime())->diff(new DateTime($dt)); if ($d->y) return $d->y.'y ago'; if ($d->m) return $d->m.'mo ago'; if ($d->d) return $d->d.'d ago'; if ($d->h) return $d->h.'h ago'; if ($d->i) return $d->i.'m ago'; return 'Just now'; }
function slugify(string $t): string { return strtolower(trim(preg_replace('~-+~', '-', preg_replace('~[^-\w]+~', '', preg_replace('~[^\pL\d]+~u', '-', $t))), '-')); }
function uniqueSlug(string $slug, string $table, ?int $excludeId = null): string { $o=$slug; $c=1; while(true) { $sql="SELECT COUNT(*) FROM `$table` WHERE slug=?"; $p=[$slug]; if($excludeId){$sql.=" AND id!=?";$p[]=$excludeId;} $s=db()->prepare($sql);$s->execute($p); if($s->fetchColumn()==0) break; $slug=$o.'-'.$c++; } return $slug; }
function clean($input): string { if (is_array($input)) return array_map('clean', $input); return htmlspecialchars(trim((string)$input), ENT_QUOTES, 'UTF-8'); }
function currentPath(): string { return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH); }
function isActivePage(string $p): bool { return strpos(currentPath(), $p) === 0; }
function excerpt(string $t, int $l = 150): string { $t = strip_tags($t); return strlen($t) <= $l ? $t : substr($t, 0, $l) . '...'; }
function formatMileage($m, string $u = 'km'): string { return number_format((int)$m) . ' ' . $u; }
function whatsappLink(?string $msg = null, ?string $num = null): string { $n = preg_replace('/[^0-9]/', '', $num ?? Settings::get('whatsapp', '254700000000')); $u = 'https://wa.me/' . $n; if ($msg) $u .= '?text=' . urlencode($msg); return $u; }
function generateInvoiceNumber(): string { $r = db()->query("SELECT MAX(id) as m FROM invoices")->fetch(); return 'INV-' . date('Y') . '-' . str_pad(($r['m']??0)+1, 5, '0', STR_PAD_LEFT); }
function redirect(string $url): void { header('Location: ' . $url); exit; }
function jsonResponse(array $data, int $code = 200): void { http_response_code($code); header('Content-Type: application/json'); echo json_encode($data); exit; }

function statusBadge(string $status): string {
    $c = ['active'=>'bg-emerald-100 text-emerald-700','pending'=>'bg-amber-100 text-amber-700','sold'=>'bg-blue-100 text-blue-700','reserved'=>'bg-purple-100 text-purple-700','draft'=>'bg-gray-100 text-gray-600','new'=>'bg-blue-100 text-blue-700','contacted'=>'bg-cyan-100 text-cyan-700','in_progress'=>'bg-amber-100 text-amber-700','qualified'=>'bg-emerald-100 text-emerald-700','converted'=>'bg-green-100 text-green-700','lost'=>'bg-red-100 text-red-700','closed'=>'bg-gray-100 text-gray-600','sent'=>'bg-blue-100 text-blue-700','partially_paid'=>'bg-amber-100 text-amber-700','paid'=>'bg-emerald-100 text-emerald-700','overdue'=>'bg-red-100 text-red-700','cancelled'=>'bg-gray-100 text-gray-500','completed'=>'bg-emerald-100 text-emerald-700','failed'=>'bg-red-100 text-red-700','low'=>'bg-gray-100 text-gray-600','medium'=>'bg-blue-100 text-blue-700','high'=>'bg-orange-100 text-orange-700','urgent'=>'bg-red-100 text-red-700','used'=>'bg-gray-100 text-gray-600','certified'=>'bg-blue-100 text-blue-700','general'=>'bg-gray-100 text-gray-600','test_drive'=>'bg-blue-100 text-blue-700','price_quote'=>'bg-purple-100 text-purple-700','sell_car'=>'bg-amber-100 text-amber-700','financing'=>'bg-emerald-100 text-emerald-700','callback'=>'bg-cyan-100 text-cyan-700','archived'=>'bg-gray-100 text-gray-500','refunded'=>'bg-purple-100 text-purple-700'];
    $color = $c[$status] ?? 'bg-gray-100 text-gray-600';
    return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ' . $color . '">' . ucwords(str_replace('_', ' ', $status)) . '</span>';
}

function getCarImage(?int $carId): string {
    if (!$carId) return BASE_URL . '/assets/images/car-placeholder.jpg';
    try {
        $stmt = db()->prepare("SELECT image_url FROM car_images WHERE car_id = ? AND is_primary = 1 LIMIT 1"); $stmt->execute([$carId]); $img = $stmt->fetchColumn();
        if ($img) return resolveUrl($img);
        $stmt = db()->prepare("SELECT image_url FROM car_images WHERE car_id = ? ORDER BY sort_order LIMIT 1"); $stmt->execute([$carId]); $img = $stmt->fetchColumn();
        return $img ? resolveUrl($img) : BASE_URL . '/assets/images/car-placeholder.jpg';
    } catch (PDOException $e) { return BASE_URL . '/assets/images/car-placeholder.jpg'; }
}

function seoMeta(string $title = '', string $desc = '', string $image = '', string $url = ''): string {
    $site = Settings::get('company_name', 'Zuri Motors');
    $title = $title ?: Settings::get('meta_title', $site);
    $desc = $desc ?: Settings::get('meta_description', '');
    $url = $url ?: (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $h = '<title>'.clean($title).'</title>'."\n";
    $h .= '<meta name="description" content="'.clean($desc).'">'."\n";
    $h .= '<meta property="og:title" content="'.clean($title).'">'."\n";
    $h .= '<meta property="og:description" content="'.clean($desc).'">'."\n";
    $h .= '<meta property="og:url" content="'.clean($url).'">'."\n";
    $h .= '<meta property="og:site_name" content="'.clean($site).'">'."\n";
    $h .= '<link rel="canonical" href="'.clean($url).'">'."\n";
    return $h;
}

function breadcrumbs(array $items): string {
    $h = '<nav><ol class="flex items-center gap-2 text-sm">';
    foreach ($items as $i => $item) {
        $last = $i === count($items) - 1;
        if (!$last && isset($item['url'])) $h .= '<li><a href="'.$item['url'].'" class="text-gray-500 hover:text-emerald-600">'.clean($item['label']).'</a><span class="text-gray-300 mx-1">/</span></li>';
        else $h .= '<li class="text-gray-800 font-medium">'.clean($item['label']).'</li>';
    }
    $h .= '</ol></nav>';
    return $h;
}

// Ensure dirs
foreach ([UPLOAD_PATH, CARS_UPLOAD_PATH, AVATARS_UPLOAD_PATH, LOGOS_UPLOAD_PATH, ROOT_PATH.'/logs', ROOT_PATH.'/uploads/categories'] as $d) { if (!is_dir($d)) @mkdir($d, 0755, true); }
