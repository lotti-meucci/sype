<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';

// Allowed methods: GET.
if ($_SERVER['REQUEST_METHOD'] != "GET")
{
  header("Allow: GET");
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

check_login();

// Retrives the difficulties.
$db = get_database();
$stmt = get_difficulties_stmt($db);
safe_execute($stmt);

// Sends the difficulties back.
exit_json(fetch_objects($stmt->get_result()), OK);

?>
