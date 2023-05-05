<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/classes.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';
require_once __DIR__ . '/includes/filesystem.php';

$db = get_database();

// Allowed methods: PUT, GET, PATCH, DELETE.
switch ($_SERVER['REQUEST_METHOD'])
{
  case 'PUT':
    check_png_body();
    check_login();
    check_ownership();
    $user_id;
    $file = open_session_png($db, $user_id);

    if ($file)
    {
      fclose($file);
      http_response_code(CONFLICT);
      exit;
    }

    store_png($user_id);
    http_response_code(CREATED);
    exit;

  case 'GET':
    check_login();

    if (!isset($_GET['user']))
    {
      http_response_code(BAD_REQUEST);
      exit;
    }

    $user_id;

    // If the user does not exist, sends the Not Found code back.
    try
    {
      if (!user_exist($db, $_GET['user'], $user_id))
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

    http_response_code(OK);

    if (!readfile(PICTURES_DIR . "/$user_id.png"))
      http_response_code(NO_CONTENT);

    exit;

  case 'PATCH':
    check_png_body();
    check_login();
    check_ownership();
    $user_id;
    $file = open_session_png($db, $user_id);

    if (!$file)
    {
      http_response_code(CONFLICT);
      exit;
    }

    fclose($file);
    store_png($user_id);
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
