<?php

class Validator
{
  private $data   = array();
  private $errors = array();

  public function setParam($data) 
  {
    $this->data = $data;    
  }

  public function getErrors() 
  {
    return $this->errors;
  }

  public function validate() 
  {
    foreach ($this->data as $d) {
      switch ($d[0]) {
        case 'empty':
          if ($error = $this->isEmpty($d[1], $d[2])) {
            $this->errors[] = $error;
          }
          break;
        case 'length':
          if ($error = $this->validateLength($d[1], $d[2], $d[3], $d[4])) {
            $this->errors[] = $error;
          }
          break;
        case 'digit':
          if ($error = $this->validateDigit($d[1], $d[2])) {
            $this->errors[] = $error;
          }
          break;
      }
    }
  }

  private function isEmpty($data, $label)
  {
    if (strlen($data) === 0) {
      return "{$label} is empty";
    } 
  }

  private function validateDigit($data, $label)
  {
    if (!empty($data) && !ctype_digit((string) $data)) {
      return "Your {$label} must be in digit";
    }
  }

  private function validateLength($data, $label, $lowerLimit, $upperLimit)
  {
    $len = strlen($data);

    if (($len < $lowerLimit || $len > $upperLimit) && ($len !== 0)) {
      if ($lowerLimit === $upperLimit) {
        return "{$label} length must be {$upperLimit} characters";
      } else {
        return "{$label} length must be between {$lowerLimit}-{$upperLimit} characters";
      }
    }
  }
}
