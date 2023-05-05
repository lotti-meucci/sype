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

// If 'user' is null sends all games from db.
if (!isset($_GET['user']))
{
  $stmt = get_all_games_stmt($db);
  safe_execute($stmt);
  exit_json(fetch_objects($stmt->get_result()), OK);
}

// If the user does not exist, sends the Not Found code back.
try
{
  if (!user_exist($db, $_GET['user']))
  {
    http_response_code(NOT_FOUND);
    exit;
  }
}
catch (InvalidArgumentException)
{
  http_response_code(BAD_REQUEST);
  exit;
}

$stmt = get_user_games_stmt($db);

if(!$stmt->bind_param('s', $nickname))
{
  http_response_code(BAD_REQUEST);
  exit;
}

safe_execute($stmt);

// Sends the games back.
exit_json(fetch_objects($stmt->get_result()), OK);

?>
