<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_admin();

// Dashboard summary queries use the existing PDO connection and prepared statements.
$count_queries = [
    'total' => $pdo->prepare('SELECT COUNT(*) FROM services'),
    'active' => $pdo->prepare("SELECT COUNT(*) FROM services WHERE status = 'active'"),
    'featured' => $pdo->prepare("SELECT COUNT(*) FROM services WHERE status = 'active' AND is_featured = 1"),
    'messages' => $pdo->prepare('SELECT COUNT(*) FROM contact_messages'),
    'unread' => $pdo->prepare("SELECT COUNT(*) FROM contact_messages WHERE status = 'unread'"),
];
foreach ($count_queries as $query) $query->execute();
$stats = [
    'total' => (int)$count_queries['total']->fetchColumn(),
    'active' => (int)$count_queries['active']->fetchColumn(),
    'featured' => (int)$count_queries['featured']->fetchColumn(),
    'messages' => (int)$count_queries['messages']->fetchColumn(),
    'unread' => (int)$count_queries['unread']->fetchColumn(),
];

$recent_services_query = $pdo->prepare('SELECT id, name, image, status, is_featured, created_at FROM services ORDER BY created_at DESC LIMIT 5');
$recent_services_query->execute();
$recent_services = $recent_services_query->fetchAll();

$recent_messages_query = $pdo->prepare('SELECT id, name, email, subject, status, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5');
$recent_messages_query->execute();
$recent_messages = $recent_messages_query->fetchAll();

$page_title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>
<div class="dashboard-topbar">
    <div><p class="eyebrow mb-1">Overview</p><h1 class="dashboard-heading">Dashboard</h1></div>
    <div class="dashboard-welcome">Welcome, <strong><?= e($_SESSION['admin_name'] ?? 'Administrator') ?></strong></div>
</div>

<?php if ($stats['unread'] > 0): ?><a class="alert alert-warning d-flex align-items-center gap-2 text-decoration-none dashboard-alert" href="messages/index.php"><i class="bi bi-envelope-exclamation"></i> You have <?= $stats['unread'] ?> unread message<?= $stats['unread'] === 1 ? '' : 's' ?>.</a><?php endif; ?>

<section class="mb-5"><div class="row g-4">
    <div class="col-sm-6 col-xl-3"><div class="dashboard-stat"><i class="bi bi-collection"></i><span>Total Services</span><strong><?= $stats['total'] ?></strong><a href="services/index.php">Manage services <i class="bi bi-arrow-up-right"></i></a></div></div>
    <div class="col-sm-6 col-xl-3"><div class="dashboard-stat"><i class="bi bi-check2-circle"></i><span>Active Services</span><strong><?= $stats['active'] ?></strong><a href="services/index.php">View active <i class="bi bi-arrow-up-right"></i></a></div></div>
    <div class="col-sm-6 col-xl-3"><div class="dashboard-stat"><i class="bi bi-star"></i><span>Featured Services</span><strong><?= $stats['featured'] ?></strong><a href="services/index.php">Manage featured <i class="bi bi-arrow-up-right"></i></a></div></div>
    <div class="col-sm-6 col-xl-3"><div class="dashboard-stat"><i class="bi bi-envelope"></i><span>Contact Messages</span><strong><?= $stats['messages'] ?></strong><a href="messages/index.php">Open messages <i class="bi bi-arrow-up-right"></i></a></div></div>
</div></section>

<section class="dashboard-panel mb-5"><div class="dashboard-panel-heading"><h2>Recent Services</h2><a class="text-link" href="services/index.php">View all <i class="bi bi-arrow-up-right"></i></a></div><div class="table-responsive"><table class="admin-table table align-middle mb-0"><thead><tr><th>Image</th><th>Name</th><th>Status</th><th>Featured</th><th>Created Date</th><th>Action</th></tr></thead><tbody>
<?php foreach ($recent_services as $service): ?><tr><td><img class="dashboard-thumb" src="<?= e($service['image'] ? '../uploads/dates/' . rawurlencode(basename($service['image'])) : '../assets/images/date-fruit.svg') ?>" alt=""></td><td><?= e($service['name']) ?></td><td><span class="badge text-bg-<?= $service['status'] === 'active' ? 'success' : 'secondary' ?>"><?= e(ucfirst($service['status'])) ?></span></td><td><span class="badge text-bg-<?= $service['is_featured'] ? 'warning' : 'light' ?>"><?= $service['is_featured'] ? 'Featured' : 'Not Featured' ?></span></td><td><?= e(date('d M Y', strtotime($service['created_at']))) ?></td><td><a class="btn btn-sm btn-outline-success" href="services/view.php?id=<?= (int)$service['id'] ?>">View</a> <a class="btn btn-sm btn-outline-secondary" href="services/edit.php?id=<?= (int)$service['id'] ?>">Edit</a></td></tr><?php endforeach; ?>
<?php if (!$recent_services): ?><tr><td colspan="6" class="empty-state">No services found. <a href="services/create.php">Add Your First Service</a></td></tr><?php endif; ?></tbody></table></div></section>

<section class="dashboard-panel mb-5"><div class="dashboard-panel-heading"><h2>Recent Contact Messages</h2><a class="text-link" href="messages/index.php">View all <i class="bi bi-arrow-up-right"></i></a></div><div class="table-responsive"><table class="admin-table table align-middle mb-0"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
<?php foreach ($recent_messages as $message): ?><tr><td><?= e($message['name']) ?></td><td><?= e($message['email']) ?></td><td><?= e($message['subject']) ?></td><td><span class="badge text-bg-<?= $message['status'] === 'read' ? 'secondary' : 'warning' ?>"><?= e(ucfirst($message['status'])) ?></span></td><td><?= e(date('d M Y', strtotime($message['created_at']))) ?></td><td><a class="btn btn-sm btn-outline-success" href="messages/view.php?id=<?= (int)$message['id'] ?>">View</a></td></tr><?php endforeach; ?>
<?php if (!$recent_messages): ?><tr><td colspan="6" class="empty-state">No contact messages found.</td></tr><?php endif; ?></tbody></table></div></section>

<section class="dashboard-panel quick-actions"><div class="dashboard-panel-heading"><h2>Quick Actions</h2></div><div class="d-flex flex-wrap gap-3"><a class="button" href="services/create.php"><i class="bi bi-plus-circle"></i> Add New Service</a><a class="button" href="services/index.php"><i class="bi bi-calendar2-event"></i> Manage Services</a><a class="button" href="messages/index.php"><i class="bi bi-envelope"></i> View Messages</a><a class="button" href="../index.php"><i class="bi bi-house"></i> Website Home</a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
