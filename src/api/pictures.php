<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/classes.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';

function open_session_picture(mysqli $db): object  | false
{
  $stmt = get_user_id_stmt($db);
  $nickname = $_SESSION['user'];
  $stmt->bind_param('s', $nickname);
  safe_execute($stmt);
  $user_id = $stmt->get_result()->fetch_object()->id;
  return fopen(__DIR__ . "/pictures/$user_id.png", "r");
}

$db = get_database();

// Allowed methods: PUT, GET, PATCH, DELETE.
switch ($_SERVER['REQUEST_METHOD'])
{
  case 'PUT':
    check_login();
    check_ownership();
    $file = open_session_picture($db);

    if ($file)
    {
      fclose($file);
      http_response_code(CONFLICT);
      exit;
    }

    // TODO: creation.

    http_response_code(CREATED);
    exit;

  case 'GET':
    exit;

  case 'PATCH':
    check_login();
    check_ownership();
    $file = open_session_picture($db);

    if (!$file)
    {
      http_response_code(CONFLICT);
      exit;
    }

    fclose($file);
    http_response_code(OK);
    exit;

  case 'DELETE':
    exit;

  default:
    header("Allow: PUT, GET, PATCH, DELETE");
    http_response_code(METHOD_NOT_ALLOWED);
    exit;
}

?>
