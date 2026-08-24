<?php
require_once __DIR__ . '/../../includes/functions.php';
$page_title = $page_title ?? 'Admin';
$admin_flash = consume_flash();
$script_path = $_SERVER['SCRIPT_NAME'] ?? '';
$admin_root = (preg_match('#/(services|products|gallery|team)/#', $script_path) || strpos($script_path, '/messages/') !== false) ? '../' : '';
$asset_path = $admin_root === '' ? 'assets/' : '../assets/';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | Zerwaan Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= $asset_path ?>css/style.css" rel="stylesheet">
</head>
<body>
<?php require __DIR__ . '/navbar.php'; ?>
<?php if ($admin_flash): ?><div class="admin-wrap pb-0"><div class="flash alert alert-<?= $admin_flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert"><?= e($admin_flash['message']) ?><button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div></div><?php endif; ?>
<div class="admin-wrap">
