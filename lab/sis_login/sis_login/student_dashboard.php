<?php
require 'config.php';
require_login('Student');

$st = db()->prepare('SELECT * FROM Student WHERE Student_ID = ?');
$st->execute([$_SESSION['user_id']]);
$me = $st->fetch();

page_start('Student Dashboard');
?>
<h1>Student Dashboard</h1>
<p class="sub">Welcome, <?= e($me['First_Name'] . ' ' . $me['Last_Name']) ?></p>

<div class="msg ok info">
    <p><b>Student ID:</b> <?= e($me['Student_ID']) ?></p>
    <p><b>Email:</b> <?= e($me['Email']) ?></p>
</div>

<form method="post" action="logout.php">
    <?= csrf_field() ?>
    <button type="submit">Logout</button>
</form>
<?php page_end(); ?>
