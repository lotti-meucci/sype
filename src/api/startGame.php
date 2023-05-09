<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/requests.php';


// Allowed methods: POST.
if ($_SERVER['REQUEST_METHOD'] != "POST")
{
  header("Allow: POST");
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

$db = get_database();
check_login($db);
$body = get_json_body();
check_difficulty($body);

// Checks if there is already a game ongoing.
if(isset($_SESSION['game_difficulty']))
{
  http_response_code(CONFLICT);
  exit;
}

$stmt = get_words_number_by_difficulty_stmt($db);
$difficulty = $body->difficulty;
$stmt->bind_param('i', $difficulty);
safe_execute($stmt);
$obj = $stmt->get_result()->fetch_object();

if (!$obj)
{
  exit_json(
    new ErrorResponse('"difficulty" is not a valid difficulty level.'),
    BAD_REQUEST
  );
}

$words_n = $obj->words_n;
$stmt = get_random_words_stmt($db);
$stmt->bind_param('i', $words_n);
safe_execute($stmt);

$_SESSION['game_words'] = fetch_firsts($stmt->get_result());
$_SESSION['game_difficulty'] = $difficulty;
exit_json(new TextResponse(implode(" ", $_SESSION['game_words'])), OK);

?>
