<?php

require_once __DIR__ . './includes/init.php';
require_once __DIR__ . './includes/classes.php';
require_once __DIR__ . './includes/database.php';
require_once __DIR__ . './includes/requests.php';

function check_nickname($body)
{
  if (!isset($body->nickname))
    send_json(new ErrorResponse('"nickname" attribute is not defined'), BAD_REQUEST);

  if (gettype($body->nickname) != 'string')
    send_json(new ErrorResponse('"nickname" attribute must be a string'), BAD_REQUEST);

  if (preg_match('([ \t\n\r\0\x0B])', $body->nickname))
    send_json(new ErrorResponse('"nickname" attribute must not contain spaces'), BAD_REQUEST);

  if (strlen($body->nickname) < 1 || strlen($body->nickname) > 20)
  {
    send_json(
      new ErrorResponse('"nickname" length must be greater than 0 and less or equal to 20'),
      BAD_REQUEST
    );
  }
}

function check_password($body)
{
  if (!isset($body->password))
    send_json(new ErrorResponse('"password" attribute is not defined'), BAD_REQUEST);

  if (gettype($body->password) != 'string')
    send_json(new ErrorResponse('"password" attribute must be a string'), BAD_REQUEST);

  if (strlen($body->password) == 0)
    send_json(new ErrorResponse('"password" cannot be empty'), BAD_REQUEST);
}

$db = get_database();

switch ($_SERVER['REQUEST_METHOD'])
{
  case 'PUT':
    $body = get_json_body();
    check_nickname($body);
    check_password($body);
    $stmt = create_user_stmt($db);
    $nickname = $body->nickname;
    $hash = password_hash($body->password, PASSWORD_DEFAULT);

    if (!$stmt->bind_param('ss', $nickname, $hash))
      send_json(new ErrorResponse('"nickname" attribute is not valid'), BAD_REQUEST);

    try
    {
      $stmt->execute();
    }
    catch (mysqli_sql_exception $e)
    {
      if ($e->getCode() == ER_DUP_ENTRY)
        send_json(new ErrorResponse('"nickname" already in use'), CONFLICT);

      http_response_code(INTERNAL_SERVER_ERROR);
      exit;
    }

    http_response_code(CREATED);
    exit;

  case 'GET':
    check_login();
    $pattern = '%' . ($_GET['user'] ?? '') . '%';
    $stmt = get_nicknames_stmt($db);

    if (!$stmt->bind_param('s', $pattern))
      send_json([], OK);

    $stmt->execute();
    $response = fetch_objects($stmt->get_result());

    send_json($response, OK);
    break;

  case 'PATCH':
    check_login();

    if (!isset($_GET['user']))
    {
      http_response_code(BAD_REQUEST);
      exit;
    }

    if ($_GET['user'] != $_SESSION['user'])
    {
      http_response_code(FORBIDDEN);
      exit;
    }

    $body = get_json_body();

    if (isset($body->nickname))
    {
      check_nickname($body);
      $stmt = modify_nickname_stmt($db);
      $new = $body->nickname;
      $old = $_SESSION['user'];

      if (!$stmt->bind_param('ss', $new, $old))
        send_json(new ErrorResponse('"nickname" attribute is not valid'), BAD_REQUEST);

      try
      {
        $stmt->execute();
      }
      catch (mysqli_sql_exception $e)
      {
        if ($e->getCode() == ER_DUP_ENTRY)
          send_json(new ErrorResponse('"nickname" already in use'), CONFLICT);

        http_response_code(INTERNAL_SERVER_ERROR);
        exit;
      }

      $_SESSION['user'] = $new;
    }

    if (isset($body->password))
    {
      check_password($body);
      $stmt = modify_hash_stmt($db);
      $nickname = $_SESSION['user'];
      $hash = password_hash($body->password, PASSWORD_DEFAULT);

      if (!$stmt->bind_param('ss', $hash, $nickname))
        send_json(new ErrorResponse('"nickname" attribute is not valid'), BAD_REQUEST);

      try
      {
        $stmt->execute();
      }
      catch (mysqli_sql_exception $e)
      {
        http_response_code(INTERNAL_SERVER_ERROR);
        exit;
      }
    }

    http_response_code(OK);
    exit;

  case 'DELETE':
    check_login();

    if (!isset($_GET['user']))
    {
      http_response_code(BAD_REQUEST);
      exit;
    }

    if ($_GET['user'] != $_SESSION['user'])
    {
      http_response_code(FORBIDDEN);
      exit;
    }

    $stmt = delete_user_stmt($db);
    $stmt->bind_param('s', $_SESSION['user']);

    try
    {
      $stmt->execute();
    }
    catch (mysqli_sql_exception $e)
    {
      http_response_code(INTERNAL_SERVER_ERROR);
      exit;
    }

    session_unset();
    break;

  default:
    http_response_code(METHOD_NOT_ALLOWED);
    exit;
}

?>
