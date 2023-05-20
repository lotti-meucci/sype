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

class TextResponse
{
  public readonly string $text;

  function __construct(string $text)
  {
    $this->text = $text;
  }
}

class NicknameResponse
{
  public readonly string $nickname;

  function __construct(string $nickname)
  {
    $this->nickname = $nickname;
  }
}

class ErrorsNumberResponse
{
  public readonly string $errorsNumber;

  function __construct(string $errorsNumber)
  {
    $this->errorsNumber = $errorsNumber;
  }
}

?>
