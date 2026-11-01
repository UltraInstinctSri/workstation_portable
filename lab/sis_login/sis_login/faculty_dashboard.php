<?php
require 'config.php';
require_login('Faculty');

$st = db()->prepare('SELECT * FROM Faculty WHERE Faculty_ID = ?');
$st->execute([$_SESSION['user_id']]);
$me = $st->fetch();

page_start('Faculty Dashboard');
?>
<h1>Faculty Dashboard</h1>
<p class="sub">Welcome, <?= e($me['First_Name'] . ' ' . $me['Last_Name']) ?></p>

<div class="msg ok info">
    <p><b>Faculty ID:</b> <?= e($me['Faculty_ID']) ?></p>
    <p><b>Email:</b> <?= e($me['Email']) ?></p>
</div>

<form method="post" action="logout.php">
    <?= csrf_field() ?>
    <button type="submit">Logout</button>
</form>
<?php page_end(); ?>
