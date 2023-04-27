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

//need to be modified, this is a placeholder only for tests
$difficulty_id = 1;

// Retrives the rankings from the database.
$stmt = get_rankings_by_difficulty($db);

if (!$stmt->bind_param('i', $difficulty_id))
  exit_json([], OK);

safe_execute($stmt);
$response = fetch_objects($stmt->get_result());

exit_json($response, OK);

?>
