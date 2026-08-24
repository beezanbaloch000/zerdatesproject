<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_admin('../login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	exit('Method not allowed.');
}

verify_csrf();
$id = (int)($_POST['id'] ?? 0);
$statement = $pdo->prepare('SELECT image FROM services WHERE id = ?');
$statement->execute([$id]);
$service = $statement->fetch();

if (!$service) {
	http_response_code(404);
	exit('Service not found.');
}

$delete = $pdo->prepare('DELETE FROM services WHERE id = ?');
$delete->execute([$id]);
delete_service_image($service['image']);
flash('success', 'Service deleted.');
redirect('index.php');
