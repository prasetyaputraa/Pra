<?php

class Paginator
{
  private $page         = 1;
  private $totalPages   = 1;
  private $itemCount    = 0;
  private $itemsPerPage = 10;
  private $range        = 5;

  public function __construct($itemCount, $itemsPerPage, $page, $range) 
  {
    if (is_numeric($itemCount)) {
      $this->itemCount = $itemCount;
    }

    if (is_numeric($itemsPerPage)) {
      $this->itemsPerPage = $itemsPerPage;
    }

    $this->totalPages = (int) ceil($this->itemCount / $this->itemsPerPage);

    if ($this->totalPages < 1) {
      $this->totalPages = 1;
    }

    $this->setPage($page);

    if (is_numeric($range)) {
      $this->range = $range;
    }
  }

  public function setPage($page)
  {
    if ($page && is_numeric($page)) {
      $page = (int) $page;

      if ($page > $this->totalPages) {
        $page = $this->totalPages;
      } elseif ($page < 1) {
        $page = 1;
      }
    } else {
      $page = 1;
    }

    $this->page = (int) $page;
  }

  public function getPage() 
  {
    return $this->page;
  }

  public function getPrev()
  {
    if ($this->page > 1) {
      return $this->page - 1;
    }
  }

  public function getNext()
  {
    if (($this->page + 1) <= $this->totalPages) {
      return $this->page + 1;
    }
  }

  public function getOffset() 
  {
    return ($this->page - 1) * $this->itemsPerPage;
  }

  public function paginate()
  {
    $totalPages = $this->totalPages;
    $page       = $this->page;
    $radius     = ceil(($this->range - 1) / 2);
    $rightRange = $radius;
    $leftRange  = $radius;

    if (($this->range % 2) === 0) {
      $rightRange = $radius;
      $leftRange  = $radius - 1;

      if ($page > ($totalPages - $radius) && $totalPages > ($radius + 1)) {
        $rightRange = $totalPages - $page;
        $leftRange  = ($radius * 2) - ($rightRange - 1);
      }

      if ($page < $radius) {
        $rightRange = ($radius * 2) - $page;
        $leftRange  = ($radius * 2) - $rightRange;  
      }
    } else {
      if ($page > ($totalPages - $radius) && $totalPages > ($radius + 1)) {
        $rightRange = $totalPages - $page;
        $leftRange  = ($radius * 2) - $rightRange;
      }

      if ($page < ($radius + 1)) {
        $rightRange = ($radius * 2 + 1) - $page;
        $leftRange  = ($radius * 2) - $rightRange;  
      }
    }

    if (($page - $rightRange) < 1) {
      $start = 1;
    } else {
      $start = $page - $leftRange;
    }

    if ($start < 1) {
      $start = 1;
    }

    if (($page + $rightRange) > $totalPages) {
      $end = $totalPages;
    } else {
      $end = $page + $rightRange;
    }

    return array(
      'start' => $start,
      'end'   => $end
    );
  }
}
