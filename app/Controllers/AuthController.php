<?php
namespace App\Controllers; use App\Db; use App\Auth;
class AuthController{
  private Db $db; private Auth $auth;
  public function __construct(Db $db, Auth $auth){ $this->db=$db; $this->auth=$auth; }
  public function showLogin(?string $error=null): string{ $csrf=$this->auth->csrf(); ob_start(); $title="Giriş Yap"; include __DIR__."/../Views/auth_login.php"; return ob_get_clean(); }
  public function postLogin(): void{ $this->auth->checkCsrf(); $ok=$this->auth->login($_POST['email']??'',$_POST['password']??''); if($ok){ header('Location:/'); } else { echo $this->showLogin('E-posta ya da şifre hatalı'); } }
  public function postRegister(): void{ $this->auth->checkCsrf(); $ok=$this->auth->register($_POST['name']??'',$_POST['email']??'',$_POST['password']??''); echo $this->showLogin($ok?'Kayıt başarılı, giriş yapın.':'Kayıt başarısız.'); }
  public function logout(): void{ $this->auth->logout(); header('Location:/login'); }
}