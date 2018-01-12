<?php

class Controller
{
  public function render($view, $data) 
  {
    extract($data, EXTR_SKIP);
    include(VIEW_PATH . $view . '.php');
  }
}
