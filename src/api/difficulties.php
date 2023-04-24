<?php

require_once __DIR__ . './includes/init.php';
require_once __DIR__ . './includes/database.php';
require_once __DIR__ . './includes/requests.php';

if ($_SERVER['REQUEST_METHOD'] != "GET")
{
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

$db = get_database();
$stmt = get_difficulties_stmt($db);
$stmt->execute();
send_json(fetch_objects($stmt->get_result()), OK);

?>
