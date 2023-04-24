<?php

require_once __DIR__ . './classes.php';

const OK = 200;
const CREATED = 201;
const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const FORBIDDEN = 403;
const METHOD_NOT_ALLOWED = 405;
const CONFLICT = 409;
const UNSUPPORTED_MEDIA_TYPE = 415;
const INTERNAL_SERVER_ERROR = 500;

function send_json(mixed $obj, int $code)
{
  header('Content-Type: application/json; charset=utf-8');
  http_response_code($code);
  echo json_encode($obj);
  exit;
}

function get_json_body()
{
  if ($_SERVER["CONTENT_TYPE"] != "application/json")
  {
    http_response_code(UNSUPPORTED_MEDIA_TYPE);
    exit;
  }

  $body = json_decode(file_get_contents('php://input'));

  if (!$body)
    send_json(new ErrorResponse('Request body is not valid JSON'), BAD_REQUEST);

  return $body;
}

function check_login()
{
  if (!isset($_SESSION['user']))
  {
    http_response_code(UNAUTHORIZED);
    exit;
  }
}

?>
