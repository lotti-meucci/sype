<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

// Pictures folder path.
const PICTURES_DIR = __DIR__ . "/../pictures";

// Opens a file stream of the picture of the specified user.
// Returns a file stream or false if an error occurs.
function open_session_png(): mixed
{
  $user_id = $_SESSION['user'];
  return fopen(PICTURES_DIR . "/$user_id.png", "r");
}

// Removes the pictures of the specified user.
// Returns true if the file has been removed, otherwise false.
function remove_session_png(): bool
{
  $user_id = $_SESSION['user'];
  return unlink(PICTURES_DIR . "/$user_id.png");
}

// Stores the body inside of a picture file with the specified user ID.
function store_png(int $id): void
{
  copy('php://input', PICTURES_DIR . "/$id.png");
}

?>
