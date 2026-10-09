<?php
declare(strict_types=1);
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

$dataFile = __DIR__ . '/data/visitors.json';
$dataDir = dirname($dataFile);
if (!is_dir($dataDir) && !mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
    http_response_code(500); echo json_encode(['success'=>false,'message'=>'Could not create data directory']); exit;
}
if (!file_exists($dataFile)) {
    file_put_contents($dataFile, json_encode(['total_visits'=>0,'visits'=>[]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), LOCK_EX);
}

// Use the server-observed IP only for an approximate city/country lookup; do not store the IP.
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$location = ['city'=>'Unknown','region'=>'Unknown','country'=>'Unknown','latitude'=>null,'longitude'=>null];
$isPublicIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
if ($isPublicIp) {
    $url = 'http://ip-api.com/json/' . rawurlencode($ip) . '?fields=status,city,regionName,country,lat,lon';
    $context = stream_context_create(['http'=>['timeout'=>2, 'ignore_errors'=>true]]);
    $raw = @file_get_contents($url, false, $context);
    if ($raw !== false) {
        $geo = json_decode($raw, true);
        if (is_array($geo) && ($geo['status'] ?? '') === 'success') {
            $location = [
                'city' => (string)($geo['city'] ?? 'Unknown'),
                'region' => (string)($geo['regionName'] ?? 'Unknown'),
                'country' => (string)($geo['country'] ?? 'Unknown'),
                'latitude' => isset($geo['lat']) ? (float)$geo['lat'] : null,
                'longitude' => isset($geo['lon']) ? (float)$geo['lon'] : null
            ];
        }
    }
}

$entry = [
    'time' => gmdate('c'),
    'location' => $location,
    'page' => 'camera-preview'
];
$fp = fopen($dataFile, 'c+');
if (!$fp) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Could not open visitor database']); exit; }
flock($fp, LOCK_EX);
$contents = stream_get_contents($fp);
$db = json_decode($contents ?: '', true);
if (!is_array($db) || !isset($db['visits']) || !is_array($db['visits'])) $db = ['total_visits'=>0,'visits'=>[]];
$db['total_visits'] = (int)($db['total_visits'] ?? 0) + 1;
$db['visits'][] = $entry;
rewind($fp);
ftruncate($fp, 0);
$written = fwrite($fp, json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
fflush($fp);
flock($fp, LOCK_UN);
fclose($fp);
if ($written === false) { http_response_code(500); echo json_encode(['success'=>false,'message'=>'Could not save visitor record']); exit; }
echo json_encode(['success'=>true,'total_visits'=>$db['total_visits']]);
