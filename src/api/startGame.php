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

kill_game();
$db = get_database();
check_login($db);
$body = get_json_body();
check_difficulty($body);

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

// Retrives the requested number of random words.
$words_n = $obj->words_n;
$stmt = get_random_words_stmt($db);
$stmt->bind_param('i', $words_n);
safe_execute($stmt);

// Stores the game data in the session.
$_SESSION['game_words'] = fetch_firsts($stmt->get_result());
$_SESSION['game_difficulty'] = $difficulty;

// Sends a joined string of the words back.
exit_json(new TextResponse(implode(" ", $_SESSION['game_words'])), OK);

?>
