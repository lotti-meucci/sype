<?php

require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/includes/requests.php';

// Allowed methods: POST.
if ($_SERVER['REQUEST_METHOD'] != "POST")
{
  header("Allow: POST");
  http_response_code(METHOD_NOT_ALLOWED);
  exit;
}

// If the user is NOT logged-in, sends a Conflict status code.
if (!isset($_SESSION['user']))
{
  http_response_code(CONFLICT);
  exit;
}

// Clears the session (logs out).
session_unset();
http_response_code(OK);
exit;

?>
