<?php
require 'config.php';

// Only reachable after a correct password + CAPTCHA
if (empty($_SESSION['pending_uid'])) redirect('login.php');

function cancel_login($message) {
    unset($_SESSION['pending_uid'], $_SESSION['pending_type'], $_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_tries']);
    flash($message);
    redirect('index.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $entered = trim($_POST['otp'] ?? '');

    if (time() > $_SESSION['otp_expiry']) {
        cancel_login('OTP expired. Please log in again.');
    }

    if (hash_equals($_SESSION['otp'], $entered)) {
        // Success: start a fresh session id and log the user in
        $uid  = $_SESSION['pending_uid'];
        $type = $_SESSION['pending_type'];
        session_regenerate_id(true);
        unset($_SESSION['pending_uid'], $_SESSION['pending_type'], $_SESSION['otp'], $_SESSION['otp_expiry'], $_SESSION['otp_tries']);
        $_SESSION['user_id']   = $uid;
        $_SESSION['user_type'] = $type;

        db()->prepare('UPDATE Login SET Last_Login = NOW() WHERE User_ID = ?')->execute([$uid]);
        redirect(strtolower($type) . '_dashboard.php');
    }

    // Wrong OTP: max 3 attempts, then back to the home page
    $_SESSION['otp_tries']++;
    if ($_SESSION['otp_tries'] >= 3) {
        cancel_login('Too many wrong OTP attempts. Please log in again.');
    }
    $error = 'Wrong OTP. ' . (3 - $_SESSION['otp_tries']) . ' attempt(s) left.';
}

page_start('Verify OTP');
?>
<h1>Verify OTP</h1>
<p class="sub">Enter the 6-digit code sent to you</p>

<?php if (DEMO_MODE): ?>
    <div class="msg demo">DEMO MODE - your OTP is<br><b><?= e($_SESSION['otp']) ?></b></div>
<?php endif; ?>
<?php if ($error): ?><div class="msg error"><?= e($error) ?></div><?php endif; ?>

<form method="post" autocomplete="off">
    <?= csrf_field() ?>
    <label for="otp">One-Time Password</label>
    <input type="text" name="otp" id="otp" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus>
    <button type="submit">Verify</button>
</form>

<p class="links"><a href="login.php">Cancel</a></p>
<?php page_end(); ?>
