<?php
declare(strict_types=1);
session_start();

// IMPORTANT: Change this password before uploading to a public website.
const ADMIN_PASSWORD = 'experiance181';

if (isset($_POST['password'])) {
    if (hash_equals(ADMIN_PASSWORD, (string)$_POST['password'])) {
        session_regenerate_id(true);
        $_SESSION['visitor_admin'] = true;
    } else {
        $loginError = 'Incorrect password.';
    }
}
if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: visitors-admin.php');
    exit;
}
if (empty($_SESSION['visitor_admin'])) {
    ?><!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Visitor Tracker Login</title><style>body{font:16px Arial;background:#f4f5f7;display:grid;place-items:center;min-height:100vh;margin:0}.card{background:white;padding:28px;border-radius:14px;width:min(340px,85vw);box-shadow:0 8px 30px #0001}input,button{box-sizing:border-box;width:100%;padding:12px;margin-top:12px}button{background:#111;color:white;border:0;border-radius:8px;cursor:pointer}.error{color:#b00020}</style><form class="card" method="post"><h2>Visitor tracker</h2><p>Sign in to view visit statistics.</p><?php if (!empty($loginError)) echo '<p class="error">'.htmlspecialchars($loginError, ENT_QUOTES, 'UTF-8').'</p>'; ?><input type="password" name="password" placeholder="Admin password" required autofocus><button type="submit">Sign in</button></form></html><?php
    exit;
}
$file = __DIR__ . '/data/visitors.json';
$db = is_file($file) ? json_decode((string)file_get_contents($file), true) : null;
if (!is_array($db)) $db = ['total_visits'=>0,'visits'=>[]];
$visits = array_reverse($db['visits'] ?? []);
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Visitor Tracker</title><style>body{font:15px Arial,sans-serif;margin:0;background:#f5f6f8;color:#202124}.wrap{max-width:1000px;margin:auto;padding:24px}.top{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}.metric{background:#fff;padding:22px;border-radius:14px;margin:18px 0;box-shadow:0 2px 10px #00000008}.count{font-size:40px;font-weight:700}table{border-collapse:collapse;width:100%;background:white;border-radius:12px;overflow:hidden}th,td{text-align:left;padding:12px;border-bottom:1px solid #eee}th{background:#eee}a{color:#111}small{color:#555}@media(max-width:600px){.table-wrap{overflow:auto}table{min-width:620px}}</style></head><body><main class="wrap"><div class="top"><h1>Visitor tracker</h1><a href="?logout=1">Log out</a></div><section class="metric"><div>Total page visits</div><div class="count"><?= (int)($db['total_visits'] ?? 0) ?></div><small>Each page load counts as one visit.</small></section><h2>Recent visits</h2><div class="table-wrap"><table><thead><tr><th>Date/time (UTC)</th><th>City</th><th>Region</th><th>Country</th></tr></thead><tbody><?php if (!$visits): ?><tr><td colspan="4">No visits recorded yet.</td></tr><?php else: foreach (array_slice($visits, 0, 500) as $v): $loc = $v['location'] ?? []; ?><tr><td><?= h($v['time'] ?? '') ?></td><td><?= h($loc['city'] ?? 'Unknown') ?></td><td><?= h($loc['region'] ?? 'Unknown') ?></td><td><?= h($loc['country'] ?? 'Unknown') ?></td></tr><?php endforeach; endif; ?></tbody></table></div><p><small>Locations are approximate IP-based estimates, not precise GPS. <a href="data/visitors.json" onclick="return false">JSON database is not publicly served.</a></small></p></main></body></html>
