<?php

require_once __DIR__ . './includes/init.php';
require_once __DIR__ . './includes/requests.php';

if ($_SERVER['REQUEST_METHOD'] != "POST")
{
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

if (!isset($_SESSION['user']))
{
  http_response_code(CONFLICT);
  exit;
}

session_unset();

?>
