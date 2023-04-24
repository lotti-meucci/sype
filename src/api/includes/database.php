<?php

require_once __DIR__ . './requests.php';

const ER_DUP_ENTRY = 1062;

function get_database()
{
  try
  {
    return new mysqli('localhost', 'root', '', 'sype');
  }
  catch (Exception)
  {
    http_response_code(INTERNAL_SERVER_ERROR);
    exit;
  }
}

function fetch_objects($result)
{
  $array = [];

  while ($obj = $result->fetch_object())
    array_push($array, $obj);

  return $array;
}

function create_user_stmt($db)
{
  return $db->prepare('INSERT INTO user(nickname, hash) VALUES (?, ?)');
}

function get_nicknames_stmt($db)
{
  return $db->prepare('SELECT nickname FROM user WHERE nickname LIKE ?');
}

function modify_nickname_stmt($db)
{
  return $db->prepare('UPDATE user SET nickname = ? WHERE nickname = ?');
}

function modify_hash_stmt($db)
{
  return $db->prepare('UPDATE user SET hash = ? WHERE nickname = ?');
}

function get_hash_stmt($db)
{
  return $db->prepare('SELECT hash FROM user WHERE nickname = ?');
}

function delete_user_stmt($db)
{
  return $db->prepare('DELETE FROM user WHERE nickname = ?');
}

function get_difficulties_stmt($db)
{
  return $db->prepare('SELECT id level, description, words_n wordsNumber FROM difficulty');
}

?>
