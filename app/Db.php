<?php
namespace App;
use PDO; use PDOException;
class Db {
  private PDO $pdo;
  public function __construct(string $dsn, string $user, string $pass) {
    $opts=[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,PDO::ATTR_EMULATE_PREPARES=>false];
    $this->pdo=new PDO($dsn,$user,$pass,$opts);
  }
  public function pdo(): PDO { return $this->pdo; }
}