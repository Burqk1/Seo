<?php
namespace App\Controllers; use App\Db; use App\Auth;
class TicketController{
  private Db $db; private Auth $auth;
  public function __construct(Db $db, Auth $auth){ $this->db=$db; $this->auth=$auth; }
  public function index(): string{ $this->auth->requireLogin(); $list=$this->db->pdo()->query("SELECT * FROM tickets ORDER BY created_at DESC")->fetchAll(); ob_start(); $title="Destek Talepleri"; include __DIR__."/../Views/tickets_index.php"; return ob_get_clean(); }
  public function store(): void{ $this->auth->requireLogin(); $this->auth->checkCsrf(); $st=$this->db->pdo()->prepare("INSERT INTO tickets(subject,body,status,created_by) VALUES(?,?, 'new', ?)"); $st->execute([$_POST['subject']??'',$_POST['body']??'', $this->auth->user()['id']]); header('Location:/tickets'); }
  public function update(): void{ $this->auth->requireLogin(); $this->auth->checkCsrf(); $st=$this->db->pdo()->prepare("UPDATE tickets SET status=? WHERE id=?"); $st->execute([$_POST['status']??'new',(int)($_POST['id']??0)]); header('Location:/tickets'); }
}