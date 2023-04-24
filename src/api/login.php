<?php

require_once __DIR__ . './includes/init.php';
require_once __DIR__ . './includes/classes.php';
require_once __DIR__ . './includes/database.php';
require_once __DIR__ . './includes/requests.php';

if ($_SERVER['REQUEST_METHOD'] != "POST")
{
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

if (isset($_SESSION['user']))
{
  http_response_code(CONFLICT);
  exit;
}

$body = get_json_body();

if (!isset($body->nickname))
  send_json(new ErrorResponse('"nickname" attribute is not defined'), BAD_REQUEST);

if (!isset($body->password))
  send_json(new ErrorResponse('"password" attribute is not defined'), BAD_REQUEST);

$db = get_database();
$stmt = get_hash_stmt($db);
$nickname = $body->nickname;

if (!$stmt->bind_param('s', $nickname))
  send_json(new ErrorResponse('"nickname" attribute is not valid'), BAD_REQUEST);

$stmt->execute();
$result = $stmt->get_result()->fetch_object();

if (!$result || !password_verify($body->password, $result->hash))
{
  http_response_code(UNAUTHORIZED);
  exit;
}

$_SESSION['user'] = $body->nickname;
http_response_code(OK);
exit;

?>
