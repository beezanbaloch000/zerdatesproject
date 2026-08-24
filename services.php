<?php
require_once __DIR__ . '/config/database.php'; require_once __DIR__ . '/includes/functions.php';
$service_query = $pdo->prepare("SELECT id, name, slug, short_description, image, is_featured FROM services WHERE status = :status ORDER BY is_featured DESC, created_at DESC");
$service_query->execute(['status' => 'active']);
$services = $service_query->fetchAll();
$page_title = 'Services'; $active_page = 'services'; require __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact wrap"><p class="eyebrow">Our services</p><h1>Local dates,<br><span>your way.</span></h1><p class="lead">Browse our active date varieties and related supply services.</p></section>
<section class="services-list wrap">
	<div class="row align-items-center mb-4">
		<div class="col-lg-7"><h2 class="h1 mb-2">What we offer</h2><p class="text-muted mb-0"><?= count($services) ?> available service<?= count($services) === 1 ? '' : 's' ?></p></div>
		<div class="col-lg-5 mt-3 mt-lg-0"><label class="visually-hidden" for="service-search">Search services</label><div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span><input class="form-control" id="service-search" type="search" placeholder="Search services"></div></div>
	</div>
	<div class="row g-4" id="service-list">
		<?php foreach ($services as $service): ?>
			<div class="col-md-6 col-lg-4 service-result" data-service-search="<?= e(strtolower($service['name'] . ' ' . $service['short_description'])) ?>">
				<article class="card h-100 border-0 shadow-sm service-card">
					<div class="service-image"><img src="<?= e(service_image($service['image'], $service['name'])) ?>" alt="<?= e($service['name']) ?>"></div>
					<div class="card-body service-body d-flex flex-column"><h3 class="card-title"><?= e($service['name']) ?></h3><p class="card-text text-muted"><?= e($service['short_description']) ?></p><a class="text-link mt-auto" href="service.php?slug=<?= rawurlencode($service['slug']) ?>">View Details <span>↗</span></a></div>
				</article>
			</div>
		<?php endforeach; ?>
		<?php if (!$services): ?><div class="col-12"><p class="text-muted">No active services are available yet.</p></div><?php endif; ?>
	</div>
	<p class="text-muted d-none mt-4" id="service-no-results">No services match your search.</p>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>