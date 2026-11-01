<?php
require 'config.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();
    $type     = $_POST['type'] ?? '';
    $first    = trim($_POST['first_name'] ?? '');
    $last     = trim($_POST['last_name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $uid      = trim($_POST['user_id'] ?? '');
    $pw       = $_POST['password'] ?? '';
    $pw2      = $_POST['confirm'] ?? '';

    if (!in_array($type, ['Student', 'Faculty'], true)) $errors[] = 'Choose Student or Faculty.';
    if ($first === '' || $last === '')                  $errors[] = 'Enter your first and last name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))     $errors[] = 'Enter a valid email address.';
    $errors = array_merge($errors, validate_signup($uid, $username, $email, $pw, $pw2));

    if (!$errors) {
        $salt = bin2hex(random_bytes(16));
        try {
            $db = db();
            $db->beginTransaction();
            // $type is one of the two whitelisted values, so it is safe to use as table name
            $db->prepare("INSERT INTO $type ({$type}_ID, First_Name, Last_Name, Email) VALUES (?, ?, ?, ?)")
               ->execute([$uid, $first, $last, $email]);
            $db->prepare('INSERT INTO Login (User_ID, User_Type, Username, Password_Hash, Random_salt) VALUES (?, ?, ?, ?, ?)')
               ->execute([$uid, $type, $username, make_hash($salt, $pw), $salt]);
            $db->commit();

            flash('Registration successful. Please log in.');
            redirect('login.php');
        } catch (PDOException $ex) {
            if (db()->inTransaction()) db()->rollBack();
            $errors[] = $ex->getCode() == '23000'
                ? 'That User ID, username or email is already registered.'
                : 'Could not register. Please try again.';
        }
    }
}

page_start('Sign up');
?>
<h1>Sign up</h1>
<p class="sub">Create your Student or Faculty account</p>

<div id="jsErrors" class="msg error" style="display:none"></div>
<?php if ($errors): ?>
    <div class="msg error"><ul><?php foreach ($errors as $m) echo '<li>' . e($m) . '</li>'; ?></ul></div>
<?php endif; ?>

<form id="signupForm" method="post" novalidate>
    <?= csrf_field() ?>

    <label for="type">I am a</label>
    <select name="type" id="type" required>
        <option value="Student" <?= ($_POST['type'] ?? '') === 'Student' ? 'selected' : '' ?>>Student</option>
        <option value="Faculty" <?= ($_POST['type'] ?? '') === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
    </select>

    <label for="first_name">First name</label>
    <input type="text" name="first_name" id="first_name" value="<?= old('first_name') ?>" required>

    <label for="last_name">Last name</label>
    <input type="text" name="last_name" id="last_name" value="<?= old('last_name') ?>" required>

    <label for="email">Email</label>
    <input type="email" name="email" id="email" value="<?= old('email') ?>" required>

    <label for="username">Username</label>
    <input type="text" name="username" id="username" value="<?= old('username') ?>" required>

    <label for="user_id">User ID (your Student / Faculty ID)</label>
    <input type="text" name="user_id" id="user_id" value="<?= old('user_id') ?>" required>

    <label for="password">Password</label>
    <input type="password" name="password" id="password" required>

    <label for="confirm">Confirm password</label>
    <input type="password" name="confirm" id="confirm" required>

    <button type="submit">Create account</button>
</form>

<p class="links">Already registered? <a href="login.php">Login</a></p>
<script src="validate.js"></script>
<?php page_end(); ?>
