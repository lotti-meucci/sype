<?php

class ErrorResponse
{
  public readonly string $message;

  function __construct($message)
  {
    $this->message = $message;
  }
}

?>
