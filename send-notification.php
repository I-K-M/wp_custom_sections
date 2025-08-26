<?php
$LOG = __DIR__ . '/send-notification.txt';
function dbg($msg) {
    global $LOG;
    $time = date('[Y-m-d H:i:s] ');
    file_put_contents($LOG, $time . $msg . "\n", FILE_APPEND);
}
dbg("→ script launched, origin=" . ($_SERVER['HTTP_ORIGIN'] ?? 'n/a'));
dbg("method: " . ($_SERVER['REQUEST_METHOD'] ?? 'n/a'));
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    dbg("✘ method unallowed");
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}
if (!empty($_POST['b_a75966e4f2b2fa303eff0d97e_cd86731309'])) {
    dbg("✘ bot detected");
    http_response_code(403);
    exit("Bot detected");
}
dbg("auth_token received: " . ($_POST['auth_token'] ?? 'NULL'));
if (!isset($_POST['auth_token']) || $_POST['auth_token'] !== 'FORM_TOKEN') {
    dbg("✘ invalid token");
    http_response_code(403);
    exit;
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
dbg("HTTP_ORIGIN: $origin");
$originOk = strpos($origin, 'client-staging.com') !== false
         || strpos($origin, 'client.com') !== false;
if (!$originOk) {
    dbg("✘ invalid origin: $origin");
    http_response_code(403);
    exit("Invalid origin");
}
$country = $_POST['COUNTRY'] ?? '';
dbg("COUNTRY: $country");
$client  = "example@email.com";
$map = [
  "Asia"           => "example@email.com",
  "Africa"         => "example@email.com",
  "EMEA"           => "example@email.com",
  "North America"  => "example@email.com",
  "South America"  => "example@email.com"
];
$to = [$client];
if (isset($map[$country]) && $map[$country] !== "") {
    $to[] = $map[$country];
}
$toString = implode(",", $to);
dbg("recipients: $toString");
$mailSubject = "Example Website - New Contact Inquiry";
$mailSubject = (string) $mailSubject;
$mailSubject = str_replace(array("\r", "\n"), ' ', $mailSubject);
$source  = $_POST['source_page'] ?? 'unknown page';
dbg("source_page: $source");
$labels = [
    'EMAIL'       => 'Email',
    'FNAME'       => 'First Name',
    'LNAME'       => 'Last Name',
    'MMERGE20'    => 'Phone Number',
    'ORG'         => 'Company',
    'COUNTRY'     => 'Country',
    'source_page' => 'Form Source'
];
$body = "Hello,\n\nWe got a new submission from: $source\n\n";
foreach ($_POST as $key => $value) {
    if (empty(trim($value))) continue;
    if (in_array($key, ['tags', 'b_a75966e4f2b2fa303eff0d97e_cd86731309', 'auth_token', 'subject'])) continue;
    $label = $labels[$key] ?? $key;
    $body .= "$label: " . htmlspecialchars(trim($value)) . "\n";
}
dbg("body built:\n" . str_replace("\n", "\\n", $body));
$from = 'example@email.com';
$headers  = "From: $from\r\n";
$headers .= "Reply-To: $from\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
dbg("headers ready");
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');
add_filter('wp_mail_from', function () {
    return 'example@email.com';
});
add_filter('wp_mail_from_name', function () {
    return 'Client Website';
});
dbg("wp_mail params: to=" . print_r($to, true) . " | subject=" . $mailSubject);
$ok = wp_mail($to, $mailSubject, $body, $headers);
dbg("mail() ret: " . ($ok ? 'OK' : 'FAIL'));
if ($ok) {
    dbg("✔ mail(): OK, exit 200");
    http_response_code(200);
    echo "OK";
} else {
    dbg("✘ mail(): FAIL, exit 500");
    http_response_code(500);
    echo "Sending error";
    exit;
}
