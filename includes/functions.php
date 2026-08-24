<?php
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure_cookie = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $secure_cookie,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

if (!empty($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
    session_unset();
}
$_SESSION['last_activity'] = time();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $location): never
{
    header("Location: $location");
    exit;
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function consume_flash(): ?array
{
    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function display_flash(?array $message): void
{
    if (!$message) return;
    $class = $message['type'] === 'success' ? 'success' : 'danger';
    echo '<div class="alert alert-' . e($class) . '">' . e($message['message']) . '</div>';
}

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(string $loginPath = 'login.php'): void
{
    if (!is_admin()) {
        flash('error', 'Please sign in to continue.');
        redirect($loginPath);
    }
}

function post_string(string $key): string
{
    return trim((string)($_POST[$key] ?? ''));
}

function valid_required(string $value): bool
{
    return trim($value) !== '';
}

function valid_email(string $value): bool
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? '';
    return trim($value, '-');
}

function service_slug_exists(PDO $pdo, string $slug, int $exceptId = 0): bool
{
    $query = $pdo->prepare('SELECT id FROM services WHERE slug = ? AND id <> ? LIMIT 1');
    $query->execute([$slug, $exceptId]);
    return (bool)$query->fetchColumn();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf_token'] ?? '', (string)($_POST['csrf_token'] ?? ''))) {
        http_response_code(419);
        exit('Invalid request token.');
    }
}

function service_image(?string $image, ?string $serviceName = null): string
{
    if ($image) return 'uploads/dates/' . rawurlencode(basename($image));
    $lock = $serviceName ? abs(crc32($serviceName)) % 1000 : 1;
    return 'https://loremflickr.com/900/700/dates,fruit?lock=' . $lock;
}

function upload_service_image(?array $file): ?string
{
    return upload_image($file, 'dates');
}

function upload_image(?array $file, string $folder): ?string
{
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 2 * 1024 * 1024) return null;
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $dimensions = @getimagesize($file['tmp_name']);
    if (!isset($allowed[$mime]) || $allowed[$mime] !== $extension || $dimensions === false || $dimensions[0] > 5000 || $dimensions[1] > 5000) return null;
    $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
    $directory = __DIR__ . '/../uploads/' . $folder;
    if (!is_dir($directory) && !mkdir($directory, 0755, true)) return null;
    $destination = $directory . '/' . $filename;
    return move_uploaded_file($file['tmp_name'], $destination) ? $filename : null;
}

function uploaded_file_was_submitted(?array $file): bool
{
    return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

function delete_service_image(?string $image): void
{
    delete_uploaded_image($image, 'dates');
}

function delete_uploaded_image(?string $image, string $folder): void
{
    if ($image) {
        $path = __DIR__ . '/../uploads/' . $folder . '/' . basename($image);
        if (is_file($path)) unlink($path);
    }
}

function content_image(?string $image, string $folder): string
{
    return $image ? 'uploads/' . rawurlencode($folder) . '/' . rawurlencode(basename($image)) : 'assets/images/date-fruit.svg';
}
