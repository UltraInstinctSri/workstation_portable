<?php
require 'config.php';
page_start('Student Information System');

$flash = get_flash();
$type  = $_SESSION['user_type'] ?? '';
?>
<h1>Student Information System</h1>
<p class="sub">NIT Silchar - Registration &amp; Login</p>

<?php if ($flash): ?><div class="msg error"><?= e($flash) ?></div><?php endif; ?>

<?php if ($type): ?>
    <a class="btn" href="<?= strtolower($type) ?>_dashboard.php">Go to my dashboard</a>
<?php else: ?>
    <a class="btn" href="login.php">Login</a>
    <a class="btn light" href="signup.php">Sign up</a>
<?php endif; ?>
<?php page_end(); ?>
