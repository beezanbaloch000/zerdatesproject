<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$form_values = [
    'name' => post_string('name'),
    'email' => post_string('email'),
    'phone' => post_string('phone'),
    'subject' => post_string('subject') ?: (string)($_GET['subject'] ?? ''),
    'message' => post_string('message'),
];
$form_errors = [];

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    verify_csrf();

    if (!valid_required($form_values['name'])) $form_errors['name'] = 'Please enter your name.';
    if (!valid_email($form_values['email'])) $form_errors['email'] = 'Please enter a valid email address.';
    if (strlen($form_values['email']) > 190) $form_errors['email'] = 'Email must be 190 characters or fewer.';
    if (!valid_required($form_values['subject'])) $form_errors['subject'] = 'Please enter a subject.';
    if (!valid_required($form_values['message'])) $form_errors['message'] = 'Please enter a message.';
    if (strlen($form_values['name']) > 120) $form_errors['name'] = 'Name must be 120 characters or fewer.';
    if (strlen($form_values['phone']) > 40) $form_errors['phone'] = 'Phone must be 40 characters or fewer.';
    if (strlen($form_values['subject']) > 200) $form_errors['subject'] = 'Subject must be 200 characters or fewer.';
    if (strlen($form_values['message']) > 5000) $form_errors['message'] = 'Message must be 5,000 characters or fewer.';

    if (!$form_errors) {
        $statement = $pdo->prepare('INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)');
        $statement->execute([$form_values['name'], $form_values['email'], $form_values['phone'] ?: null, $form_values['subject'], $form_values['message']]);
        flash('success', 'Thanks for reaching out. Your message has been received.');
        redirect('contact.php');
    }
}

$page_title = 'Contact';
$active_page = 'contact';
require __DIR__ . '/includes/header.php';
?>
<section class="page-hero compact wrap">
    <p class="eyebrow">Get in touch</p>
    <h1>Let’s talk<br><span>dates.</span></h1>
    <p class="lead">Tell us what you are looking for and we will review your enquiry.</p>
</section>

<section class="contact-section wrap">
    <div class="contact-note">
        <p class="eyebrow">Location</p>
        <h2>Turbat, Balochistan, Pakistan</h2>
        <p>Zerwaan Date Supply Company is based in Turbat and focuses on local and traditional Balochistan dates and related supply services.</p>
        <!-- Replace this note with an approved company email address, phone number, or map embed when available. -->
        <p class="small text-muted">Company contact details will be added here when confirmed.</p>
    </div>

    <form class="contact-form needs-validation" id="contact-form" method="post" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <div><label for="name">Name</label><input class="form-control <?= isset($form_errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" required maxlength="120" value="<?= e($form_values['name']) ?>"><div class="invalid-feedback"><?= e($form_errors['name'] ?? 'Please enter your name.') ?></div></div>
        <div><label for="email">Email</label><input class="form-control <?= isset($form_errors['email']) ? 'is-invalid' : '' ?>" id="email" type="email" name="email" required value="<?= e($form_values['email']) ?>"><div class="invalid-feedback"><?= e($form_errors['email'] ?? 'Please enter a valid email address.') ?></div></div>
            <div><label for="email">Email</label><input class="form-control <?= isset($form_errors['email']) ? 'is-invalid' : '' ?>" id="email" type="email" name="email" required maxlength="190" value="<?= e($form_values['email']) ?>"><div class="invalid-feedback"><?= e($form_errors['email'] ?? 'Please enter a valid email address.') ?></div></div>
        <div><label for="phone">Phone <span class="text-muted fw-normal">(optional)</span></label><input class="form-control <?= isset($form_errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" maxlength="40" value="<?= e($form_values['phone']) ?>"><div class="invalid-feedback"><?= e($form_errors['phone'] ?? '') ?></div></div>
        <div><label for="subject">Subject</label><input class="form-control <?= isset($form_errors['subject']) ? 'is-invalid' : '' ?>" id="subject" name="subject" required maxlength="200" value="<?= e($form_values['subject']) ?>"><div class="invalid-feedback"><?= e($form_errors['subject'] ?? 'Please enter a subject.') ?></div></div>
        <div><label for="message">Message</label><textarea class="form-control <?= isset($form_errors['message']) ? 'is-invalid' : '' ?>" id="message" name="message" rows="6" maxlength="5000" required><?= e($form_values['message']) ?></textarea><div class="invalid-feedback"><?= e($form_errors['message'] ?? 'Please enter a message.') ?></div></div>
        <button class="button" type="submit">Send message <span>↗</span></button>
    </form>
</section>

<section class="callout wrap"><div><p class="eyebrow">For supply and packaging enquiries</p><h2>Let’s find the<br><i>right option.</i></h2></div><a class="button light" href="services.php">View services <span>↗</span></a></section>
<?php require __DIR__ . '/includes/footer.php'; ?>