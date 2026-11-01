<?php
require 'config.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $uid = trim($_POST['user_id'] ?? '');
    $pw  = $_POST['password'] ?? '';
    $cap = strtoupper(trim($_POST['captcha'] ?? ''));

    $real = $_SESSION['captcha'] ?? '';
    unset($_SESSION['captcha']);                       // each CAPTCHA can be used only once

    if ($real === '' || !hash_equals($real, $cap)) {
        $error = 'Wrong CAPTCHA. Try again.';
    } else {
        $st = db()->prepare('SELECT * FROM Login WHERE User_ID = ?');
        $st->execute([$uid]);
        $row = $st->fetch();

        if (!$row || !hash_equals($row['Password_Hash'], make_hash($row['Random_salt'], $pw))) {
            $error = 'Invalid User ID or password.';   // same message for both, on purpose
        } else {
            // Password and CAPTCHA are correct -> go to OTP step
            $otp = (string) random_int(100000, 999999);
            $_SESSION['pending_uid']  = $row['User_ID'];
            $_SESSION['pending_type'] = $row['User_Type'];
            $_SESSION['otp']          = $otp;
            $_SESSION['otp_expiry']   = time() + 300;   // valid for 5 minutes
            $_SESSION['otp_tries']    = 0;

            // When DEMO_MODE is false, send $otp to the user here (mail(), SMS API, ...).
            redirect('otp.php');
        }
    }
}

$flash = get_flash();
page_start('Login');
?>
<h1>Login</h1>
<p class="sub">Student / Faculty</p>

<?php if ($flash): ?><div class="msg ok"><?= e($flash) ?></div><?php endif; ?>
<?php if ($error): ?><div class="msg error"><?= e($error) ?></div><?php endif; ?>

<form method="post" autocomplete="off">
    <?= csrf_field() ?>

    <label for="user_id">User ID</label>
    <input type="text" name="user_id" id="user_id" value="<?= old('user_id') ?>" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <label for="captcha">CAPTCHA</label>
    <div class="captcha">
        <img id="captchaImg" src="captcha.php" alt="CAPTCHA" width="160" height="50">
        <button type="button" id="refresh" title="New CAPTCHA">&#8635; Refresh</button>
    </div>
    <input type="text" name="captcha" id="captcha" placeholder="Type the characters above" required style="margin-top:8px">

    <button type="submit">Login</button>
</form>

<p class="links">New here? <a href="signup.php">Sign up</a></p>

<script>
    // Load a fresh CAPTCHA image without reloading the page
    document.getElementById('refresh').addEventListener('click', function () {
        document.getElementById('captchaImg').src = 'captcha.php?t=' + Date.now();
        document.getElementById('captcha').value = '';
    });
</script>
<?php page_end(); ?>
