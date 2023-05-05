<?php

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/database.php';

const PICTURES_DIR = __DIR__ . "/../pictures";

function open_session_png(mysqli $db, ?int &$user_id): mixed
{
  user_exist($db, $_SESSION['user'], $user_id);
  return fopen(PICTURES_DIR . "/$user_id.png", "r");
}

function store_png(int $id): void
{
  copy('php://input', PICTURES_DIR . "$id.png");
}

?>
