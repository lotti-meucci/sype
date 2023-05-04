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

if (!isset($_GET['difficulty']) || !ctype_digit($_GET['difficulty']))
{
  http_response_code(BAD_REQUEST);
  exit;
}

// Retrives the rankings from the database.
$db = get_database();
$stmt = get_rankings_stmt($db);
$id = $_GET['difficulty'];

if (!$stmt->bind_param('ii', $id, $id))
{
  http_response_code(BAD_REQUEST);
  exit;
}

safe_execute($stmt);

// Sends the rankings back.
exit_json(fetch_objects($stmt->get_result()), OK);
exit;

?>
