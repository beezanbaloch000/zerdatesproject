<div class="admin-layout">
    <aside class="admin-sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="adminSidebar" aria-labelledby="adminSidebarLabel">
        <div class="offcanvas-header d-lg-none">
            <h2 class="h5 mb-0" id="adminSidebarLabel">Menu</h2>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#adminSidebar" aria-label="Close menu"></button>
        </div>
        <div class="admin-sidebar-inner">
            <a class="admin-brand" href="<?= $admin_root ?>index.php"><span class="brand-mark">Z</span><span>Zerwaan<br><small>Date Supply Company</small></span></a>
            <p class="admin-nav-label">Workspace</p>
            <nav class="admin-sidebar-nav" aria-label="Admin navigation">
                <a class="<?= $page_title === 'Dashboard' ? 'active' : '' ?>" href="<?= $admin_root ?>index.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                <a class="<?= $page_title === 'Services' ? 'active' : '' ?>" href="<?= $admin_root ?>services/index.php"><i class="bi bi-calendar2-event"></i> Services</a>
                <a href="<?= $admin_root ?>services/create.php"><i class="bi bi-plus-circle"></i> Add Service</a>
                <a class="<?= $page_title === 'Products' ? 'active' : '' ?>" href="<?= $admin_root ?>products/index.php"><i class="bi bi-box-seam"></i> Products</a>
                <a class="<?= $page_title === 'Gallery' ? 'active' : '' ?>" href="<?= $admin_root ?>gallery/index.php"><i class="bi bi-images"></i> Gallery</a>
                <a class="<?= $page_title === 'Team Members' ? 'active' : '' ?>" href="<?= $admin_root ?>team/index.php"><i class="bi bi-people"></i> Team Members</a>
                <a class="<?= $page_title === 'Messages' ? 'active' : '' ?>" href="<?= $admin_root ?>messages/index.php"><i class="bi bi-envelope"></i> Contact Messages</a>
            </nav>
            <div class="admin-sidebar-bottom">
                <a href="<?= $admin_root ?>../index.php"><i class="bi bi-house"></i> View Website</a>
            </div>
        </div>
    </aside>
    <div class="admin-main">
        <header class="admin-topbar">
            <div class="d-flex align-items-center gap-3"><button class="btn admin-menu-button d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebar" aria-controls="adminSidebar" aria-label="Open menu"><i class="bi bi-list"></i></button><div><span class="admin-kicker">Zerwaan admin</span><span class="admin-page-title"><?= e($page_title) ?></span></div></div>
            <div class="d-flex align-items-center gap-3"><span class="admin-user"><i class="bi bi-person-circle"></i> <?= e($_SESSION['admin_name'] ?? 'Administrator') ?></span><form method="post" action="<?= $admin_root ?>logout.php"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-box-arrow-right"></i> Logout</button></form></div>
        </header>
