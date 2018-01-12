<?php

class Mysql
{
  private $host   = DB_HOST;
  private $user   = DB_USERNAME;
  private $pass   = DB_PASSWORD;
  private $dbName = DB_NAME;

  private $connection = null;

  public function __construct() 
  {
    $this->connect();
  }

  public function connect() 
  {
    $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->dbName);

    if ($this->connection->connect_error) {
      throw new Exception($this->connection->connect_error);
    } else {
      return true;
    }
  }

  public function disconnect()
  {
    if ($this->connection) {
      return $this->connection->close();
    }
  }

  public function count($table, $fields = null, $conditions = null) 
  {
    $query = "SELECT COUNT({$fields}) FROM {$table}";

    if (!empty($conditions)) {
      $query .= " WHERE {$conditions}";
    }

    if (!($result = $this->connection->query($query))) {
      throw new Exception($this->connection->error);
    }

    $row = mysqli_fetch_row($result);

    return (int) $row[0];
  }

  public function select($table, $fields = null, $conditions = null, $sort = null, $limit = null, $offset = null) 
  {
    $query = '';

    if (empty($fields)) {
      $query = "SELECT * FROM {$table}"; 
    } else {
      $query = "SELECT {$fields} FROM {$table}";
    }

    if (!empty($conditions)) {
      $query .= " WHERE {$conditions}";
    }

    if (!empty($sort)) {
      $query .= " ORDER BY {$sort}";
    }
    
    if (!empty($limit)) {
      $query .= " LIMIT {$limit}";
    }

    if (!empty($offset)) {
      $query .= " OFFSET {$offset}";
    }

    if (!($result = $this->connection->query($query))) {
      throw new Exception($this->connection->error);
    }

    $data = array();

    while ($row = mysqli_fetch_assoc($result)) {
      $data[] = $row;
    }

    return $data;
  }

  public function insert($table, $data) 
  {
    $fields = array_keys($data);

    foreach ($data as $key => $value) {
      $data[$key] = $this->connection->real_escape_string($value);
    }

    $query  = "INSERT into {$table} (" . implode(', ', $fields) . ") VALUES ('" . implode("', '", $data) . "')" ;

    if (!$this->connection->query($query)) {
      throw new Exception($this->connection->error);
    }

    return true;
  }

  public function update($table, $data, $conditions = null) 
  {
    foreach ($data as $key => $value) {
      $value = $this->connection->real_escape_string($value);
      $set[] = "{$key} = '{$value}'";
    }

    $query = "UPDATE {$table} SET " . implode(', ', $set);

    if (!empty($conditions)) {
      $query .= " WHERE {$conditions}";
    }

    if (!$this->connection->query($query)) {
      throw new Exception($this->connection->error);
    }

    return true;
  }

  public function delete($table, $conditions = null) 
  {
    $query = "DELETE FROM {$table}";

    if (!empty($conditions)) {
      $query .= " WHERE {$conditions}";
    }

    if (!$this->connection->query($query)) {
      throw new Exception($this->connection->error);
    }

    return true;
  }
}
