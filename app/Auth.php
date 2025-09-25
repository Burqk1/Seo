<?php
namespace App;
class Auth {
  private Db $db;
  public function __construct(Db $db){
    $this->db=$db;
    if(session_status()===PHP_SESSION_NONE) session_start();
    if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(20));
  }
  public function csrf(): string { return $_SESSION['csrf']; }
  public function checkCsrf(): void {
    $t=$_POST['_csrf']??$_SERVER['HTTP_X_CSRF']??'';
    if(!$t||$t!==($_SESSION['csrf']??'')){ http_response_code(419); exit('CSRF doğrulaması başarısız'); }
  }
  public function user(): ?array { return $_SESSION['user']??null; }
  public function requireLogin(): void { if(!$this->user()){ header('Location:/login'); exit; } }
  public function login(string $email,string $pass): bool{
    $st=$this->db->pdo()->prepare("SELECT id,name,email,password FROM users WHERE email=? LIMIT 1");
    $st->execute([$email]); $u=$st->fetch();
    if(!$u||!password_verify($pass,$u['password'])) return false;
    $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email']]; return true;
  }
  public function register(string $name,string $email,string $pass): bool{
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)||strlen($pass)<6) return false;
    $hash=password_hash($pass,PASSWORD_BCRYPT);
    $st=$this->db->pdo()->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
    try{ return $st->execute([$name,$email,$hash]); }catch(\Throwable $e){ return false; }
  }
  public function logout(): void { $_SESSION=[]; session_destroy(); }
}