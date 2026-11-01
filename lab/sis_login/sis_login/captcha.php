<?php
// Draws a CAPTCHA as an SVG image (no GD library needed) and remembers the answer in the session.
require 'config.php';

$chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';   // no 0/O/1/I to avoid confusion
$code  = '';
for ($i = 0; $i < 5; $i++) $code .= $chars[random_int(0, strlen($chars) - 1)];
$_SESSION['captcha'] = $code;

header('Content-Type: image/svg+xml');
header('Cache-Control: no-store');

echo '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="50" viewBox="0 0 160 50">';
echo '<rect width="160" height="50" fill="#eef2ff"/>';
for ($i = 0; $i < 6; $i++) {   // noise lines
    printf('<line x1="%d" y1="%d" x2="%d" y2="%d" stroke="#9ca3af" stroke-width="1.5"/>',
        random_int(0, 160), random_int(0, 50), random_int(0, 160), random_int(0, 50));
}
for ($i = 0; $i < 5; $i++) {   // letters, each slightly rotated
    $x = 14 + $i * 28;
    printf('<text x="%d" y="35" font-size="28" font-weight="bold" font-family="monospace" fill="#1e3a8a" transform="rotate(%d %d 25)">%s</text>',
        $x, random_int(-25, 25), $x + 8, $code[$i]);
}
echo '</svg>';
