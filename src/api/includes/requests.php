<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/classes.php';

// HTTP response codes.
const OK = 200;
const CREATED = 201;
const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const FORBIDDEN = 403;
const NOT_FOUND = 404;
const METHOD_NOT_ALLOWED = 405;
const CONFLICT = 409;
const UNSUPPORTED_MEDIA_TYPE = 415;
const INTERNAL_SERVER_ERROR = 500;

// Serializes the given data in JSON format into the body. DOES EXIT.
function send_json(mixed $data, int $code): void
{
  header('Content-Type: application/json; charset=utf-8');
  http_response_code($code);
  exit(json_encode($data));
}

// Deserializes the body into the returned object. MAY EXIT.
function get_json_body(): object
{
  // Checks the "Content-Type" header (must be "application/json")
  if ($_SERVER["CONTENT_TYPE"] != "application/json")
  {
    http_response_code(UNSUPPORTED_MEDIA_TYPE);
    exit;
  }

  // Deserializes the body.
  $body = json_decode(file_get_contents('php://input'));

  // Body null check.
  if (!$body)
    send_json(new ErrorResponse('Request body is not valid JSON'), BAD_REQUEST);

  return $body;
}

// If the user is already logged-in, sends an Unauthorized status code. MAY EXIT.
function check_login(): void
{
  // Checks if the nickname is inside the session array.
  if (!isset($_SESSION['user']))
  {
    header('WWW-Authenticate: SypeLogin realm="Access to Sype"');
    http_response_code(UNAUTHORIZED);
    exit;
  }
}

// Checks if the given body contains a valid "nickname" attribute. MAY EXIT.
function check_nickname(object $body): void
{
  // Existence check.
  if (!isset($body->nickname))
    send_json(new ErrorResponse('"nickname" attribute is not defined'), BAD_REQUEST);

  // Type check (must be a string).
  if (gettype($body->nickname) != 'string')
    send_json(new ErrorResponse('"nickname" attribute must be a string'), BAD_REQUEST);

  // Spaces check.
  if (preg_match('([ \t\n\r\0\x0B])', $body->nickname))
    send_json(new ErrorResponse('"nickname" attribute must not contain spaces'), BAD_REQUEST);

  // Length check (0 < x <= 20).
  if (strlen($body->nickname) < 1 || strlen($body->nickname) > 20)
  {
    send_json(
      new ErrorResponse('"nickname" length must be greater than 0 and less or equal to 20'),
      BAD_REQUEST
    );
  }
}

// Checks if the given body contains a valid "password" attribute. MAY EXIT.
function check_password(object $body): void
{
  // Existence check.
  if (!isset($body->password))
    send_json(new ErrorResponse('"password" attribute is not defined'), BAD_REQUEST);

  // Type check (must be a string).
  if (gettype($body->password) != 'string')
    send_json(new ErrorResponse('"password" attribute must be a string'), BAD_REQUEST);

  // Length check (x > 0).
  if (strlen($body->password) <= 0)
    send_json(new ErrorResponse('"password" cannot be empty'), BAD_REQUEST);
}

// Checks if the logged-in user is the owner of the resource ("user" URL param). MAY EXIT.
function check_ownership(): void
{
  // The "user" URL param is required.
  if (!isset($_GET['user']))
  {
    http_response_code(BAD_REQUEST);
    exit;
  }

  // The "user" URL param must be equal to the currently logged-in user.
  if ($_GET['user'] != $_SESSION['user'])
  {
    http_response_code(FORBIDDEN);
    exit;
  }
}

?>
