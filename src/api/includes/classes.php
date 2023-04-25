<?php

require_once __DIR__ . '/init.php';

// Class to be serialized into JSON to describe it.
class ErrorResponse
{
  public readonly string $message;

  function __construct(string $message)
  {
    $this->message = $message;
  }
}

?>
