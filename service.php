<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$slug = trim((string)($_GET['slug'] ?? ''));
$statement = $pdo->prepare("SELECT * FROM services WHERE slug = ? AND status = 'active' LIMIT 1");
$statement->execute([$slug]);
$service = $statement->fetch();
if (!$service) { http_response_code(404); exit('Service not found.'); }
$page_title = $service['name']; $active_page = 'services'; require __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact wrap"><p class="eyebrow">Zerwaan Dates</p><h1><?= e($service['name']) ?></h1><p class="lead"><?= e($service['short_description']) ?></p></section>
<section class="story band"><div class="wrap two-col"><div class="service-image"><img src="<?= e(service_image($service['image'], $service['name'])) ?>" alt="<?= e($service['name']) ?>"></div><div><p><?= nl2br(e($service['description'])) ?></p><a class="button" href="contact.php?subject=<?= rawurlencode($service['name']) ?>">Enquire about this <span>↗</span></a></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>