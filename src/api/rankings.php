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
$db = get_database();

$body = get_json_body();
$difficulty_id = $body->difficulty_id;

// Retrives the rankings from the database.
$stmt = get_rankings_by_difficulty($db);

if (!$stmt->bind_param('i', $difficulty_id))
  exit_json(new ErrorResponse('"difficulty_id" attribute is not valid'), BAD_REQUEST);

safe_execute($stmt);

//sends the rankings back
exit_json(fetch_objects($stmt->get_result()), OK);
exit;
?>
