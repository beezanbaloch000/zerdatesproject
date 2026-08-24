<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" aria-label="Main navigation">
    <div class="container">
        <a class="navbar-brand brand" href="index.php"><span class="brand-mark">Z</span><span>Zerwaan <em>Date Supply Company</em></span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavigation" aria-controls="mainNavigation" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNavigation">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
                <li class="nav-item"><a class="nav-link <?= $active_page === 'home' ? 'active fw-semibold' : '' ?>" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link <?= $active_page === 'about' ? 'active fw-semibold' : '' ?>" href="about.php">About Us</a></li>
                <li class="nav-item"><a class="nav-link <?= $active_page === 'services' ? 'active fw-semibold' : '' ?>" href="services.php">Services</a></li>
                <li class="nav-item"><a class="nav-link <?= $active_page === 'contact' ? 'active fw-semibold' : '' ?>" href="contact.php">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>
