<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/classes.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';
require_once __DIR__ . '/includes/filesystem.php';

kill_game();
$db = get_database();

// Allowed methods: PUT, GET, PATCH, DELETE.
switch ($_SERVER['REQUEST_METHOD'])
{
  case 'PUT':
    check_png_body();
    check_login($db);
    check_ownership();
    $file = open_session_png($db);

    // If the file already exists, closes the stream and sends a Conflict status code
    if ($file)
    {
      fclose($file);
      http_response_code(CONFLICT);
      exit;
    }

    store_png($_SESSION['user']);
    http_response_code(CREATED);
    exit;

  case 'GET':
    check_login($db);

    if (!isset($_GET['user']))
    {
      http_response_code(BAD_REQUEST);
      exit;
    }

    $user_id;

    // If the user does not exist, sends a Not Found status code.
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

    // Tries to echo the required picture.
    if (!readfile(PICTURES_DIR . "/$user_id.png"))
      http_response_code(NO_CONTENT);

    exit;

  case 'PATCH':
    check_png_body();
    check_login($db);
    check_ownership();
    $file = open_session_png($db);

    // If the file does NOT exists, sends a Conflict status code.
    if (!$file)
    {
      http_response_code(CONFLICT);
      exit;
    }

    // Closes the stream.
    fclose($file);

    store_png($_SESSION['user']);
    http_response_code(OK);
    exit;

  case 'DELETE':
    check_login($db);
    check_ownership();
    http_response_code(remove_session_png($db) ? OK : CONFLICT);
    exit;

  default:
    header("Allow: PUT, GET, PATCH, DELETE");
    http_response_code(METHOD_NOT_ALLOWED);
    exit;
}

?>
