<?php

class Model
{
  protected $table = '';

  private $db = null;
  
  public function __construct() 
  {
    $this->db = new Mysql();

    $this->connect();
  }

  public function connect() 
  {
    $this->db->connect();
  }

  public function disconnect() 
  {
    $this->db->disconnect();
  }

  public function count($fields, $conditions = null) 
  {
    return $this->db->count($this->table, $fields, $conditions);
  }

  public function fetch($fields = null, $conditions = null, $sort = null, $limit = null, $offset = null)
  {
    return $this->db->select($this->table, $fields, $conditions, $sort, $limit, $offset);
  }

  public function save($data) 
  {
    return $this->db->insert($this->table, $data);
  }

  public function update($data, $conditions = null) 
  {
    return $this->db->update($this->table, $data, $conditions);
  }

  public function delete($conditions = null) 
  {
    return $this->db->delete($this->table, $conditions);
  }
}
