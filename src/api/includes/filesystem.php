<?php

function open_session_png(mysqli $db, ?int &$user_id): mixed
{
  $stmt = get_user_id_stmt($db);
  $nickname = $_SESSION['user'];
  $stmt->bind_param('s', $nickname);
  safe_execute($stmt);
  $user_id = $stmt->get_result()->fetch_object()->id;
  return fopen(__DIR__ . "/../pictures/$user_id.png", "r");
}

function store_png(int $id): void
{
  copy('php://input', __DIR__ . "/../pictures/$id.png");
}

?>
