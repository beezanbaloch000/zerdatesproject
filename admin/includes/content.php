<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';
require_admin('../login.php');

$configs = [
    'products' => ['title' => 'Products', 'singular' => 'product', 'table' => 'products', 'folder' => 'products', 'fields' => 'name, slug, short_description, description, image, status, is_featured'],
    'gallery' => ['title' => 'Gallery', 'singular' => 'gallery item', 'table' => 'gallery', 'folder' => 'gallery', 'fields' => 'title, description, image, status'],
    'team' => ['title' => 'Team Members', 'singular' => 'team member', 'table' => 'team_members', 'folder' => 'team', 'fields' => 'name, role, bio, image, status'],
];
$entity = $content_entity ?? '';
$config = $configs[$entity] ?? null;
if (!$config) { http_response_code(404); exit('Content type not found.'); }
$action = $content_action ?? 'index';

function content_redirect(string $entity): never { redirect('index.php'); }
function content_slug_exists(PDO $pdo, string $table, string $slug, int $exceptId = 0): bool {
    $query = $pdo->prepare("SELECT id FROM {$table} WHERE slug = ? AND id <> ? LIMIT 1");
    $query->execute([$slug, $exceptId]);
    return (bool)$query->fetchColumn();
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$item = null;
if (in_array($action, ['edit', 'view'], true)) {
    $query = $pdo->prepare("SELECT * FROM {$config['table']} WHERE id = ?");
    $query->execute([$id]);
    $item = $query->fetch();
    if (!$item) { http_response_code(404); exit(ucfirst($config['singular']) . ' not found.'); }
}

if ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Method not allowed.'); }
    verify_csrf();
    $query = $pdo->prepare("SELECT image FROM {$config['table']} WHERE id = ?");
    $query->execute([$id]);
    $item = $query->fetch();
    if (!$item) { http_response_code(404); exit(ucfirst($config['singular']) . ' not found.'); }
    $delete = $pdo->prepare("DELETE FROM {$config['table']} WHERE id = ?");
    $delete->execute([$id]);
    delete_uploaded_image($item['image'], $config['folder']);
    flash('success', ucfirst($config['singular']) . ' deleted.');
    content_redirect($entity);
}

if (in_array($action, ['create', 'edit'], true) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = post_string('name') ?: post_string('title');
    $role = post_string('role');
    $slug = slugify(post_string('slug') ?: $name);
    $short = post_string('short_description');
    $description = post_string('description') ?: post_string('bio');
    $status = post_string('status') === 'inactive' ? 'inactive' : 'active';
    $featured = isset($_POST['is_featured']) ? 1 : 0;
    $valid = $name !== '' && strlen($name) <= 150;
    if ($entity === 'products') $valid = $valid && $slug !== '' && strlen($slug) <= 180 && $short !== '' && strlen($short) <= 255 && $description !== '';
    if ($entity === 'gallery') $valid = $valid && $description !== '' && strlen($description) <= 255;
    if ($entity === 'team') $valid = $valid && $role !== '' && strlen($role) <= 120;
    if (!$valid) {
        flash('error', 'Please complete all required fields with valid lengths.');
    } elseif ($entity === 'products' && content_slug_exists($pdo, $config['table'], $slug, $id)) {
        flash('error', 'That product slug is already in use.');
    } else {
        $uploaded = upload_image($_FILES['image'] ?? null, $config['folder']);
        $submitted = uploaded_file_was_submitted($_FILES['image'] ?? null);
        if ($submitted && $uploaded === null) {
            flash('error', 'Upload a JPG, PNG, or WebP image up to 2 MB.');
        } else {
            $image = $uploaded ?: ($item['image'] ?? null);
            try {
                if ($action === 'create') {
                    if ($entity === 'products') $query = $pdo->prepare('INSERT INTO products (name, slug, short_description, description, image, status, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?)');
                    elseif ($entity === 'gallery') $query = $pdo->prepare('INSERT INTO gallery (title, description, image, status) VALUES (?, ?, ?, ?)');
                    else $query = $pdo->prepare('INSERT INTO team_members (name, role, bio, image, status) VALUES (?, ?, ?, ?, ?)');
                    $values = $entity === 'products' ? [$name, $slug, $short, $description, $image, $status, $featured] : ($entity === 'gallery' ? [$name, $description, $image, $status] : [$name, $role, $description, $image, $status]);
                } else {
                    if ($entity === 'products') $query = $pdo->prepare('UPDATE products SET name=?, slug=?, short_description=?, description=?, image=?, status=?, is_featured=? WHERE id=?');
                    elseif ($entity === 'gallery') $query = $pdo->prepare('UPDATE gallery SET title=?, description=?, image=?, status=? WHERE id=?');
                    else $query = $pdo->prepare('UPDATE team_members SET name=?, role=?, bio=?, image=?, status=? WHERE id=?');
                    $values = $entity === 'products' ? [$name, $slug, $short, $description, $image, $status, $featured, $id] : ($entity === 'gallery' ? [$name, $description, $image, $status, $id] : [$name, $role, $description, $image, $status, $id]);
                }
                $query->execute($values);
                if ($uploaded && !empty($item['image'])) delete_uploaded_image($item['image'], $config['folder']);
                flash('success', ucfirst($config['singular']) . ($action === 'create' ? ' created.' : ' updated.'));
                content_redirect($entity);
            } catch (PDOException $exception) {
                delete_uploaded_image($uploaded, $config['folder']);
                flash('error', 'The ' . $config['singular'] . ' could not be saved.');
            }
        }
    }
}

$page_title = $config['title'];
require __DIR__ . '/header.php';
$base = '../' . $entity;
if ($action === 'index') {
    $query = $pdo->query("SELECT * FROM {$config['table']} ORDER BY created_at DESC");
    $items = $query->fetchAll();
    $add_label = 'Add ' . $config['singular'];
    echo '<div class="admin-title"><h1>' . e($config['title']) . '</h1><a class="button" href="create.php">' . e($add_label) . ' <span>+</span></a></div><div class="table-responsive"><table class="admin-table"><thead><tr><th>Image</th><th>Name</th>' . ($entity === 'products' ? '<th>Slug</th>' : '') . ($entity === 'team' ? '<th>Role</th>' : '') . '<th>Status</th>' . ($entity === 'products' ? '<th>Featured</th>' : '') . '<th>Created</th><th>Actions</th></tr></thead><tbody>';
    foreach ($items as $row) {
        $label = $row['name'] ?? $row['title'];
        echo '<tr><td><img src="../../' . e($row['image'] ? 'uploads/' . $config['folder'] . '/' . rawurlencode(basename($row['image'])) : 'assets/images/date-fruit.svg') . '" alt="" width="64" height="48"></td><td>' . e($label) . '</td>';
        if ($entity === 'products') echo '<td>' . e($row['slug']) . '</td>';
        if ($entity === 'team') echo '<td>' . e($row['role']) . '</td>';
        echo '<td>' . e(ucfirst($row['status'])) . '</td>';
        if ($entity === 'products') echo '<td>' . ($row['is_featured'] ? 'Yes' : 'No') . '</td>';
        echo '<td>' . e(date('d M Y', strtotime($row['created_at']))) . '</td><td><a href="view.php?id=' . (int)$row['id'] . '">View</a> · <a href="edit.php?id=' . (int)$row['id'] . '">Edit</a> <form method="post" action="delete.php" class="d-inline" data-confirm-delete="Delete this item?"><input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '"><input type="hidden" name="id" value="' . (int)$row['id'] . '"><button type="submit" class="btn btn-link p-0 align-baseline text-danger">Delete</button></form></td></tr>';
    }
    if (!$items) echo '<tr><td colspan="8">No ' . e(strtolower($config['title'])) . ' found.</td></tr>';
    echo '</tbody></table></div>';
} elseif ($action === 'view') {
    $label = $item['name'] ?? $item['title'];
    echo '<div class="admin-title"><h1>' . e($label) . '</h1><a class="button" href="edit.php?id=' . $id . '">Edit <span>↗</span></a></div><div class="admin-card content-view"><img class="content-view-image" src="../../' . e($item['image'] ? 'uploads/' . $config['folder'] . '/' . rawurlencode(basename($item['image'])) : 'assets/images/date-fruit.svg') . '" alt=""><h2>' . e($label) . '</h2>' . ($entity === 'team' ? '<p><strong>Role:</strong> ' . e($item['role']) . '</p>' : '') . '<p>' . nl2br(e($item['description'] ?? $item['bio'] ?? '')) . '</p></div>';
} else {
    $is_edit = $action === 'edit';
    $label = $is_edit ? 'Edit ' . $config['singular'] : 'Add ' . $config['singular'];
    $value = fn(string $field, string $fallback = '') => e($_POST[$field] ?? ($item[$field] ?? $fallback));
    echo '<div class="admin-title"><h1>' . e($label) . '</h1></div><form class="admin-card admin-form" method="post" enctype="multipart/form-data"><input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    if ($entity === 'gallery') echo '<label>Title<input name="title" required value="' . $value('title') . '"></label><label>Caption<input name="description" required value="' . $value('description') . '"></label>';
    else { echo '<label>Name<input name="name" required value="' . $value('name') . '"></label>'; if ($entity === 'products') echo '<label>Slug<input name="slug" value="' . $value('slug') . '"></label><label>Short description<input name="short_description" required value="' . $value('short_description') . '"></label>'; if ($entity === 'team') echo '<label>Role<input name="role" required value="' . $value('role') . '"></label>'; }
    echo '<label>' . ($entity === 'team' ? 'Bio' : ($entity === 'products' ? 'Description' : 'Image')) . ($entity === 'gallery' ? '<input type="file" name="image" accept="image/jpeg,image/png,image/webp"' . (!$is_edit ? ' required' : '') . '>' : '<textarea name="' . ($entity === 'team' ? 'bio' : 'description') . '" rows="6"' . ($entity === 'products' ? ' required' : '') . '>' . $value($entity === 'team' ? 'bio' : 'description') . '</textarea>') . '</label>';
    if ($entity !== 'gallery') echo '<label>Image<input type="file" name="image" accept="image/jpeg,image/png,image/webp"></label>';
    echo '<label>Status<select name="status"><option value="active" ' . ($value('status', 'active') === 'active' ? 'selected' : '') . '>Active</option><option value="inactive" ' . ($value('status') === 'inactive' ? 'selected' : '') . '>Inactive</option></select></label>';
    if ($entity === 'products') echo '<label><input type="checkbox" name="is_featured" ' . (isset($_POST['is_featured']) || (!$is_edit && false) || ($is_edit && !isset($_POST['is_featured']) && !empty($item['is_featured'])) ? 'checked' : '') . '> Feature on the home page</label>';
    echo '<button class="button" type="submit">' . e($is_edit ? 'Save changes' : 'Create ' . $config['singular']) . ' <span>↗</span></button></form>';
}
require __DIR__ . '/footer.php';
