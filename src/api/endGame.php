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

check_login();
$db = get_database();
$body = get_json_body();
check_result($body);
check_text($body);
$submittedWords = preg_split(SPACES, $body->text, flags: PREG_SPLIT_NO_EMPTY);
$gameWords = $_SESSION['game_words'];
$errors = abs(count($gameWords) - count($submittedWords));

for ($i = 0; $i < count($gameWords) && $i < count($submittedWords); $i++)
  if (strcasecmp($gameWords[$i], $submittedWords[$i]))
    $errors++;

$stmt = create_game_stmt($db);
$result = $body->result;
$stmt->bind_param('idis', $_SESSION['game_difficulty'], $result, $errors, $_SESSION['user']);
safe_execute($stmt);

unset($_SESSION['game_words'], $_SESSION['game_difficulty']);
http_response_code(OK);
exit;

?>
