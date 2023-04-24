<?php

include './init.php';

if ($_SERVER['REQUEST_METHOD'] != "GET")
{
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

$stmt = get_difficulties_stmt($db);
$stmt->execute();
send_json(fetch_objects($stmt->get_result()), OK);

?>
