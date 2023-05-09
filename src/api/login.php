<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/classes.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';


kill_game();
$db = get_database();

// Allowed methods: GET, POST.
switch ($_SERVER['REQUEST_METHOD'])
{
  case 'GET':
    check_login($db);
    exit_json(new NicknameResponse($_SESSION['nickname']), OK);

  case 'POST':

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

    $stmt = get_hash_stmt($db);
    $nickname = $body->nickname;

    if (!$stmt->bind_param('s', $nickname))
      exit_json(new ErrorResponse('"nickname" is not valid'), BAD_REQUEST);

    safe_execute($stmt);
    $result = $stmt->get_result()->fetch_object();


    // Verifies the password.
    if (!$result || !password_verify($body->password, $result->hash))
    {
      header('WWW-Authenticate: SypeLogin realm="Access to Sype"');
      http_response_code(UNAUTHORIZED);
      exit;
    }

    // Sends a new session token via cookies (session attacks prevention).
    session_regenerate_id();

    // Puts the user ID inside the session array (logs in).
    $id;
    user_exist($db, $body->nickname, $id);
    $_SESSION['user'] = $id;
    $_SESSION['nickname'] = $body->nickname;
    http_response_code(OK);
    exit;

  default:
    header("Allow: GET, POST");
    http_response_code(METHOD_NOT_ALLOWED);
    exit;
}

?>
