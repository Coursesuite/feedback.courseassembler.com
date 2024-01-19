<?php

$e = getenv("ORIGIN_URL");
// $e = "http://127.0.0.1:50974"; // localhost

header ("Access-Control-Allow-Origin: ". $e);
header ("Access-Control-Allow-Headers: *");
header ("Access-Control-Allow-Methods: PUT, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS' || $_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    die();
}

require_once('../vendor/autoload.php');

$verifier = Licence::validate(Request::put("hash"));
if (!$verifier->valid) Utils::stop(403, '{"error":"forbidden","hash":"' . Request::put("hash") . '"}');

$ts = time();
$ok = false;
$jobsroot = realpath('../feedback');
$workingdir =  "{$jobsroot}/{$verifier->hash}/{$ts}/";
if (!file_exists($workingdir)) mkdir ($workingdir, 0777, true);
if (!file_exists($workingdir)) {
    Utils::Stop(500, '{"error":"Permissions are preventing feedback recording"}');
}

if ($data = Request::put('image')) {
    list($type, $data) = explode(';', $data);
    list(, $data)      = explode(',', $data);
    $data = base64_decode($data);

    file_put_contents($workingdir . 'screenshot.png', $data);
    $ok = true;
}

$type = Request::put('select');
$value = Request::put('textarea');

if ($type && $value) {
    file_put_contents($workingdir . $type . '.txt', $value);
    $ok = true;
}

if ($ok) {
    Utils::Stop(201,'{"ok":true}', false, 'application/json');
} else {
    Utils::Stop(400,'{"ok":false,"error":"No content"}', false, 'application/json');
}
