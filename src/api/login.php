<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/classes.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';

// Allowed methods: POST.
if ($_SERVER['REQUEST_METHOD'] != "POST")
{
  header("Allow: POST");
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

// If the user is already logged-in, sends a Conflict status code.
if (isset($_SESSION['user']))
{
  http_response_code(CONFLICT);
  exit;
}

// Validates the body ("nickname" and "password").
$body = get_json_body();
check_nickname($body);
check_password($body);


// Retrives the password hash for the specified user.

$db = get_database();
$stmt = get_hash_stmt($db);
$nickname = $body->nickname;

if (!$stmt->bind_param('s', $nickname))
  send_json(new ErrorResponse('"nickname" attribute is not valid'), BAD_REQUEST);

safe_execute($stmt);
$result = $stmt->get_result()->fetch_object();


// Verifies the password.
if (!$result || !password_verify($body->password, $result->hash))
{
  header('WWW-Authenticate: SypeLogin realm="Access to Sype"');
  http_response_code(UNAUTHORIZED);
  exit;
}

// Puts the nickname inside the session array (logs in).
$_SESSION['user'] = $body->nickname;
http_response_code(OK);
exit;

?>
