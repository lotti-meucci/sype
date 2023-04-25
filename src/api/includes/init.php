<?php
  /*
    Request initialization.
    This file must be included as first instruction in every PHP file.
  */

  require_once __DIR__ . './requests.php';

  // Removes the "Content-Type" header from the response.
  header('Content-Type:');

  // Every response will be "Forbidden" by default.
  http_response_code(FORBIDDEN);

  session_start();
?>
