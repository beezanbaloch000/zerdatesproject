<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
$featured_limit = 4;
$service_query = $pdo->prepare("SELECT id, name, slug, short_description, description, image, is_featured FROM services WHERE status = :status AND is_featured = 1 ORDER BY created_at DESC LIMIT :service_limit");
$service_query->bindValue(':status', 'active', PDO::PARAM_STR);
$service_query->bindValue(':service_limit', $featured_limit, PDO::PARAM_INT);
$service_query->execute();
$services = $service_query->fetchAll();
$page_title = 'Naturally generous';
$active_page = 'home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero wrap">
	<div class="hero-copy">
		<p class="eyebrow">Local dates. Thoughtfully supplied.</p>
		<h1>From Turbat,<br><span>for every table.</span></h1>
		<p class="lead">Zerwaan Date Supply Company connects customers with traditional and local Balochistan dates, alongside dependable date supply services.</p>
		<a class="button" href="services.php">Explore our services <span>↗</span></a>
	</div>
	<div class="hero-art"><img src="assets/images/date-palm.jpg" alt="Date palm tree"></div>
</section>

<section class="intro band">
	<div class="wrap two-col">
		<div><p class="eyebrow">About Zerwaan</p><h2>A local connection.<br><i>A clear purpose.</i></h2></div>
		<p>Zerwaan Date Supply Company is based in Turbat, Balochistan, Pakistan. We focus on the supply of local and traditional Balochistan dates, with practical support for customers looking for quality products and reliable service.</p>
	</div>
</section>

<section class="services-preview wrap" id="date-varieties">
	<div class="section-heading"><div><p class="eyebrow">Featured varieties</p><h2>Dates from Balochistan</h2></div><a class="text-link" href="services.php">View all services ↗</a></div>
	<div class="row g-4">
		<?php foreach ($services as $service): ?>
			<div class="col-md-6 col-lg-3">
				<article class="card h-100 border-0 shadow-sm service-card">
					<div class="service-image"><img src="<?= e(service_image($service['image'], $service['name'])) ?>" alt="<?= e($service['name']) ?>"></div>
					<div class="card-body service-body"><span class="small text-uppercase text-muted">Featured</span><h3 class="card-title mt-2"><?= e($service['name']) ?></h3><p class="card-text"><?= e($service['short_description']) ?></p><a class="text-link" href="service.php?slug=<?= rawurlencode($service['slug']) ?>">View details <span>↗</span></a></div>
				</article>
			</div>
		<?php endforeach; ?>
		<?php if (!$services): ?><div class="col-12"><p class="text-muted">Service information will be available here soon.</p></div><?php endif; ?>
	</div>
</section>

<section class="band">
	<div class="wrap">
		<div class="section-heading"><div><p class="eyebrow">What we do</p><h2>Supply made simple</h2></div></div>
		<div class="row g-4">
			<div class="col-md-4"><div class="p-4 bg-white h-100"><i class="bi bi-basket2 fs-2 text-warning"></i><h3 class="h4 mt-3">Date supply</h3><p class="text-muted mb-0">Discuss your requirements for local date varieties and regular supply.</p></div></div>
			<div class="col-md-4"><div class="p-4 bg-white h-100"><i class="bi bi-box-seam fs-2 text-warning"></i><h3 class="h4 mt-3">Date packaging</h3><p class="text-muted mb-0">Explore practical packaging options for your product or occasion.</p></div></div>
			<div class="col-md-4"><div class="p-4 bg-white h-100"><i class="bi bi-truck fs-2 text-warning"></i><h3 class="h4 mt-3">Bulk orders</h3><p class="text-muted mb-0">Get in touch about larger quantities and planned date orders.</p></div></div>
		</div>
	</div>
</section>

<section class="wrap values py-5">
	<div class="row g-5 align-items-center">
		<div class="col-lg-5"><p class="eyebrow">Why choose Zerwaan</p><h2>A straightforward way to source local dates.</h2></div>
		<div class="col-lg-7"><div class="row g-4"><div class="col-sm-6"><h3 class="h4">Local focus</h3><p class="text-muted">Our work is centered on traditional and local date varieties from Balochistan.</p></div><div class="col-sm-6"><h3 class="h4">Clear communication</h3><p class="text-muted">Tell us what you need and we can discuss suitable supply or packaging options.</p></div><div class="col-sm-6"><h3 class="h4">Flexible conversations</h3><p class="text-muted">We welcome enquiries from individuals, businesses, retailers, and event organizers.</p></div><div class="col-sm-6"><h3 class="h4">Room to grow</h3><p class="text-muted">Our service list can grow with the products and supply needs of our customers.</p></div></div></div>
	</div>
</section>

<section class="band">
	<div class="wrap two-col"><div><p class="eyebrow">From Turbat, Balochistan</p><h2>Rooted in a place with a long date tradition.</h2></div><p>Turbat is home to local agricultural products and traditional date varieties. Zerwaan represents that local connection through a focused date supply business. For specific product, availability, or order information, please contact us directly.</p></div>
</section>

<section class="callout wrap"><div><p class="eyebrow">Ready to discuss your requirement?</p><h2>Let’s talk<br><i>dates.</i></h2></div><a class="button light" href="contact.php">Contact Zerwaan <span>↗</span></a></section>
<?php require __DIR__ . '/includes/footer.php'; ?>