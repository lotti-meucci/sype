<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/requests.php';

// Database errors.
const ER_DUP_ENTRY = 1062;  // Duplicate error.

// Returns a database connection after checking for errors. MAY EXIT.
function get_database(): mysqli
{
  try
  {
    $config = null;
    $json = file_get_contents(__DIR__ . '/../config/database.json');

    if ($json)
      $config = json_decode($json);

    $host = null;
    $password = null;

    if ($config)
    {
      if (isset($config->host) && is_string($config->host))
        $host = $config->host;

      if (isset($config->password) && is_string($config->password))
        $password = $config->password;
    }

    return new mysqli($host ?? 'localhost', 'root', $password ?? '', 'sype');
  }
  catch (Exception)
  {
    http_response_code(INTERNAL_SERVER_ERROR);
    exit;
  }
}

// Fetches every row as an object into an array.
function fetch_objects(mysqli_result $result): array
{
  $array = [];

  while ($obj = $result->fetch_object())
    array_push($array, $obj);

  return $array;
}

// Executes a statement and catches exceptions. MAY EXIT.
// The error code will pass to "$on_error" (callable).
function safe_execute(mysqli_stmt $stmt, ?callable $on_error = null): void
{
  try
  {
    $stmt->execute();
  }
  catch (mysqli_sql_exception $e)
  {
    if ($on_error)
      $on_error($e->getCode());

    http_response_code(INTERNAL_SERVER_ERROR);
    exit;
  }
}


/*
  Database statements.
  These functions return query objects (statements) which can be run on the database.
  Statements may need some parameter to be bound.
*/

// Params: nickname (string), hash (string).
function create_user_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('INSERT INTO user(nickname, hash) VALUES (?, ?)');
}

// Params: nickname_pattern (string).
function get_nicknames_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('SELECT nickname FROM user WHERE nickname LIKE ?');
}

// Params: new_nickname (string), old_nickname (string).
function modify_nickname_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('UPDATE user SET nickname = ? WHERE nickname = ?');
}

// Params: hash (string), nickname (string).
function modify_hash_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('UPDATE user SET hash = ? WHERE nickname = ?');
}

// Params: nickname (string).
function get_hash_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('SELECT hash FROM user WHERE nickname = ?');
}

// Params: nickname (string)
function delete_user_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('DELETE FROM user WHERE nickname = ?');
}

// No params.
function get_difficulties_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('SELECT id level, description, words_n wordsNumber FROM difficulty');
}

// Params: picture_uri (string), nickname (string).
function modify_profile_uri(mysqli $db): mysqli_stmt
{
  return $db->prepare('UPDATE user SET picture_uri = ? WHERE nickname = ?');
}

// Params: user_id (integer), difficulty_id (integer), result (decimal), errors_n (integer).
function create_game_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('INSERT INTO game(
                         user_id,
                         difficulty_id,
                         result,
                         errors_n
                       ) VALUES (?, ?, ?, ?)');
}

// Params: nickname (string).
function get_user_id_stmt(mysqli $db): mysqli_stmt
{
  return $db->prepare('SELECT id FROM user WHERE nickname = ?');
}

// Params: (2) difficulty_id (integer)
function get_rankings(mysqli $db): mysqli_stmt
{
  $scoreFunction = '(g.result + 2 * g.errors_n + POWER(g.errors_n, 1.25))';

  return $db->prepare("SELECT u.nickname, g.datetime, g.result, g.errors_n errorsNumber
                       FROM game g
                       JOIN difficulty d ON d.id = g.difficulty_id
                       JOIN user u ON u.id = g.user_id
                       JOIN (SELECT u.id, MIN($scoreFunction) score
                             FROM game g
                             JOIN difficulty d ON d.id = g.difficulty_id
                             JOIN user u ON u.id = g.user_id
                             WHERE d.id = ?
                             GROUP BY u.id) s ON s.id = u.id
                       WHERE d.id = ? AND $scoreFunction = s.score
                       ORDER BY s.score");
}

?>
