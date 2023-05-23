<?php

require_once __DIR__ . '/init.php';

// Class to be serialized into JSON to describe an error.
class ErrorResponse
{
  public readonly string $message;

  function __construct(string $message)
  {
    $this->message = $message;
  }
}


// Class to be serialized into JSON to send back a text (game start).
class TextResponse
{
  public readonly string $text;

  function __construct(string $text)
  {
    $this->text = $text;
  }
}

// Class to be serialized into JSON to send back a nickname.
class NicknameResponse
{
  public readonly string $nickname;

  function __construct(string $nickname)
  {
    $this->nickname = $nickname;
  }
}

// Class to be serialized into JSON to send back the number of errors (game ending).
class ErrorsNumberResponse
{
  public readonly string $errorsNumber;

  function __construct(string $errorsNumber)
  {
    $this->errorsNumber = $errorsNumber;
  }
}

?>
