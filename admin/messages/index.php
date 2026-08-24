<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_admin('../login.php');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
	verify_csrf();
	$id = (int)($_POST['id'] ?? 0);
	$status = post_string('status') === 'read' ? 'read' : 'unread';
	$update = $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
	$update->execute([$status, $id]);
	flash('success', 'Message status updated.');
	redirect('index.php');
}

$message_query = $pdo->prepare('SELECT id, name, email, phone, subject, status, created_at FROM contact_messages ORDER BY created_at DESC');
$message_query->execute();
$messages = $message_query->fetchAll();
$page_title = 'Messages';
require __DIR__ . '/../includes/header.php';
?>
<?php
?><div class="admin-title"><h1>Contact Messages</h1></div><div class="table-responsive"><table class="admin-table table align-middle"><thead><tr><th>ID</th><th>Sender</th><th>Email</th><th>Phone</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody><?php foreach ($messages as $message): ?><tr><td><?= (int)$message['id'] ?></td><td><?= e($message['name']) ?></td><td><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></td><td><?= e($message['phone'] ?: 'Not provided') ?></td><td><?= e($message['subject']) ?></td><td><span class="badge text-bg-<?= $message['status'] === 'read' ? 'secondary' : 'warning' ?>"><?= e(ucfirst($message['status'])) ?></span></td><td><?= e(date('d M Y', strtotime($message['created_at']))) ?></td><td><a href="view.php?id=<?= (int)$message['id'] ?>">View</a><form method="post" class="d-inline ms-2"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><input type="hidden" name="status" value="<?= $message['status'] === 'read' ? 'unread' : 'read' ?>"><button class="btn btn-link p-0 align-baseline" type="submit">Mark <?= $message['status'] === 'read' ? 'unread' : 'read' ?></button></form></td></tr><?php endforeach; ?><?php if (!$messages): ?><tr><td colspan="8">No contact messages yet.</td></tr><?php endif; ?></tbody></table></div><?php require __DIR__ . '/../includes/footer.php'; ?>