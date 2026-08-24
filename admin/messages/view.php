<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_admin('../login.php');
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
	verify_csrf();
	$status = post_string('status') === 'read' ? 'read' : 'unread';
	$update = $pdo->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
	$update->execute([$status, $id]);
	flash('success', 'Message status updated.');
	redirect('view.php?id=' . $id);
}

$statement = $pdo->prepare('SELECT id, name, email, phone, subject, message, status, created_at FROM contact_messages WHERE id = ?');
$statement->execute([$id]);
$message = $statement->fetch();
if (!$message) { http_response_code(404); exit('Message not found.'); }
$page_title = 'Message';
require __DIR__ . '/../includes/header.php';
?>
<?php
?><div class="admin-title"><h1><?= e($message['subject']) ?></h1><a href="index.php" class="button">Back to messages</a></div><div class="admin-card"><dl class="row"><dt class="col-sm-3">Sender</dt><dd class="col-sm-9"><?= e($message['name']) ?></dd><dt class="col-sm-3">Email</dt><dd class="col-sm-9"><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></dd><dt class="col-sm-3">Phone</dt><dd class="col-sm-9"><?= e($message['phone'] ?: 'Not provided') ?></dd><dt class="col-sm-3">Received</dt><dd class="col-sm-9"><?= e($message['created_at']) ?></dd><dt class="col-sm-3">Status</dt><dd class="col-sm-9"><span class="badge text-bg-<?= $message['status'] === 'read' ? 'secondary' : 'warning' ?>"><?= e(ucfirst($message['status'])) ?></span></dd></dl><hr><h2 class="h4">Message</h2><p><?= nl2br(e($message['message'])) ?></p><form method="post"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$message['id'] ?>"><input type="hidden" name="status" value="<?= $message['status'] === 'read' ? 'unread' : 'read' ?>"><button class="button" type="submit">Mark <?= $message['status'] === 'read' ? 'unread' : 'read' ?></button></form></div><?php require __DIR__ . '/../includes/footer.php'; ?>