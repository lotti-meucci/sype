<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/classes.php';

// Patterns
const SPACES = '([ \t\n\r\0\x0B])';

// Sizes.
const KIB = 1024;
const MIB = KIB * KIB;

// HTTP response codes.
const OK = 200;
const CREATED = 201;
const NO_CONTENT = 204;
const BAD_REQUEST = 400;
const UNAUTHORIZED = 401;
const FORBIDDEN = 403;
const NOT_FOUND = 404;
const METHOD_NOT_ALLOWED = 405;
const CONFLICT = 409;
const CONTENT_TOO_LARGE = 413;
const UNSUPPORTED_MEDIA_TYPE = 415;
const INTERNAL_SERVER_ERROR = 500;

// Serializes the given data in JSON format into the body. DOES EXIT.
function exit_json(mixed $data, int $code): void
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

  // Checks the length of the body (max 1 KiB).
  if ((int)$_SERVER["CONTENT_LENGTH"] > KIB)
  {
    http_response_code(CONTENT_TOO_LARGE);
    exit;
  }

  // Deserializes the body.
  $body = json_decode(file_get_contents('php://input'));

  // Body null check.
  if (!$body)
    exit_json(new ErrorResponse('Request body is not valid JSON'), BAD_REQUEST);

  return $body;
}

// Checks if the body is a PNG picture. MAY EXIT.
function check_png_body(): void
{
  if ($_SERVER["CONTENT_TYPE"] != "image/png")
  {
    http_response_code(UNSUPPORTED_MEDIA_TYPE);
    exit;
  }

  // Checks the length of the body (max 2 MiB).
  if ((int)$_SERVER["CONTENT_LENGTH"] > 2 * MIB)
  {
    http_response_code(CONTENT_TOO_LARGE);
    exit;
  }
}

// If the user is already logged-in, sends an Unauthorized status code. MAY EXIT.
function check_login(mysqli $db): void
{
  // Checks if the user ID is inside the session array.
  if (isset($_SESSION['user']))
  {
    $stmt = get_nickname_by_id_stmt($db);
    $user_id = $_SESSION['user'];
    $stmt->bind_param('i', $user_id);
    safe_execute($stmt);
    $obj = $stmt->get_result()->fetch_object();

    // Checks if the user is real.
    if ($obj)
    {
      $_SESSION['nickname'] = $obj->nickname;
      return;
    }

    // Logs out.
    session_unset();
  }

  header('WWW-Authenticate: SypeLogin realm="Access to Sype"');
  http_response_code(UNAUTHORIZED);
  exit;
}

// Checks if the given body contains a valid "nickname" attribute. MAY EXIT.
function check_nickname(object $body): void
{
  // Existence check.
  if (!isset($body->nickname))
    exit_json(new ErrorResponse('"nickname" is not defined'), BAD_REQUEST);

  // Type check (must be a string).
  if (!is_string($body->nickname))
    exit_json(new ErrorResponse('"nickname" must be a string'), BAD_REQUEST);

  // Spaces check.
  if (preg_match(SPACES, $body->nickname))
    exit_json(new ErrorResponse('"nickname" must not contain spaces'), BAD_REQUEST);

  // Length check (0 < x <= 20).
  if (strlen($body->nickname) < 1 || strlen($body->nickname) > 20)
  {
    exit_json(
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
    exit_json(new ErrorResponse('"password" is not defined'), BAD_REQUEST);

  // Type check (must be a string).
  if (!is_string($body->password))
    exit_json(new ErrorResponse('"password" must be a string'), BAD_REQUEST);

  // Length check (x > 0).
  if (strlen($body->password) <= 0)
    exit_json(new ErrorResponse('"password" cannot be empty'), BAD_REQUEST);
}

// Checks if the given body contains a valid "difficulty" attribute. MAY EXIT.
function check_difficulty(object $body): void
{
  if (!isset($body->difficulty))
    exit_json(new ErrorResponse('"difficulty" is not defined'), BAD_REQUEST);

  // Type check (must be an integer).
  if (!is_int($body->difficulty) || $body->difficulty <= 0)
    exit_json(new ErrorResponse('"difficulty" must be a positive integer'), BAD_REQUEST);
}

// Checks if the given body contains a valid "text" attribute. MAY EXIT.
function check_text(object $body): void
{
  if (!isset($body->text))
    exit_json(new ErrorResponse('"text" is not defined'), BAD_REQUEST);

  // Type check (must be float).
  if (!is_string($body->text))
    exit_json(new ErrorResponse('"text" must be string'), BAD_REQUEST);
}

// Checks if the given body contains a valid "result" attribute. MAY EXIT.
function check_result(object $body): void
{
  if (!isset($body->result))
    exit_json(new ErrorResponse('"result" is not defined'), BAD_REQUEST);

  // Type check (must be float).
  if (!is_float($body->result) || $body->result <= 0)
    exit_json(new ErrorResponse('"result" must be a positive float'), BAD_REQUEST);
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
  if ($_GET['user'] != $_SESSION['nickname'])
  {
    http_response_code(FORBIDDEN);
    exit;
  }
}

// Stops the current game with no effects.
function kill_game()
{
  unset($_SESSION['game_words'], $_SESSION['game_difficulty']);
}

?>
