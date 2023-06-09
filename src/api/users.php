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
    // Validates the body ("nickname" and "password").
    $body = get_json_body();
    check_nickname($body);
    check_password($body);


    // Adds the new user to the database.

    $stmt = create_user_stmt($db);
    $nickname = $body->nickname;
    $hash = password_hash($body->password, PASSWORD_DEFAULT);

    if (!$stmt->bind_param('ss', $nickname, $hash))
      exit_json(new ErrorResponse('"nickname" is not valid'), BAD_REQUEST);

    safe_execute($stmt, function(int $code)
    {
      // If the nickname is already in use, sends an error.

      if ($code == ER_DUP_ENTRY)
        exit_json(new ErrorResponse('"nickname" already in use'), CONFLICT);
    });

    http_response_code(CREATED);
    exit;

  case 'GET':
    check_login($db);

    // Creates a SQL LIKE pattern using the "q" URL param.
    $pattern = ($_GET['q'] ?? '') . '%';


    // Retrives the users from the database.

    $stmt = get_nicknames_stmt($db);

    if (!$stmt->bind_param('s', $pattern))
      exit_json([], OK);

    safe_execute($stmt);
    $response = fetch_objects($stmt->get_result());

    exit_json($response, OK);

  case 'PATCH':
    check_login($db);
    check_ownership();
    $body = get_json_body();

    // Renaming the user.
    if (isset($body->nickname))
    {
      // Validates the new nickname.
      check_nickname($body);


      // Modifies the nickname on the database.

      $stmt = modify_nickname_stmt($db);
      $new = $body->nickname;
      $id = $_SESSION['user'];

      if (!$stmt->bind_param('si', $new, $id))
        exit_json(new ErrorResponse('"nickname" is not valid'), BAD_REQUEST);

      safe_execute($stmt, function(int $code)
      {
        // If the nickname is already in use, sends an error.

        if ($code == ER_DUP_ENTRY)
          exit_json(new ErrorResponse('"nickname" already in use'), CONFLICT);
      });


      // Changes the nickname of the logged-in user.
      $_SESSION['nickname'] = $new;
    }

    // Changing password.
    if (isset($body->password))
    {
      // Validates the new password.
      check_password($body);


      // Modifies the password on the database by updating the hash.

      $stmt = modify_hash_stmt($db);
      $id = $_SESSION['user'];
      $hash = password_hash($body->password, PASSWORD_DEFAULT);
      $stmt->bind_param('si', $hash, $id);
      safe_execute($stmt);
    }

    // Closes the request.
    http_response_code(OK);
    exit;

  case 'DELETE':
    check_login($db);
    check_ownership();

    // Removes the profile picture.
    remove_session_png();

    // Deletes the user from the database.
    $stmt = delete_user_stmt($db);
    $id =  $_SESSION['user'];
    $stmt->bind_param('i', $id);
    safe_execute($stmt);

    // Logs out.
    session_unset();
    http_response_code(OK);
    exit;

  default:
    header("Allow: PUT, GET, PATCH, DELETE");
    http_response_code(METHOD_NOT_ALLOWED);
    exit;
}

?>
