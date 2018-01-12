<?php

function h($str) 
{
  return htmlentities($str, ENT_QUOTES, 'UTF-8');
}

function get_input_value($index)
{
  if (isset($_POST[$index])) {
    return (strlen($_POST[$index]) !== 0) ? trim($_POST[$index]) : null;
  } elseif (isset($_GET[$index])) {
    return (strlen($_GET[$index]) !== 0) ? trim($_GET[$index]) : null;
  }
}

function get_file($file)
{
  return (isset($_FILES[$file])) ? $_FILES[$file] : null;
}

function get_file_attribute($name, $attr)
{
  return (isset($_FILES[$name][$attr])) ? $_FILES[$name][$attr] : null;
}

function output_http_status($code)
{
  switch ($code) {
    case 400:
      $message = "Bad Request";
      break;
    case 404:
      $message = 'Not Found';
      break;
    case 500:
      $message = 'Internal Server Error';
      break;
  }

  header("HTTP/1.1 {$code} {$message}");
}
