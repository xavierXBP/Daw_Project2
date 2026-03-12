<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Email;

class LoginController extends BaseController
{
    public function index()
    {
     
    }
    public function log (){
     helper('form');

     $title = "LOG PARA MATRICULARTE";
     $data = [$title];   

     echo view('login/login_public',$data);
    }

public function log_post(){

helper('form');

$validation = [
'email' => 'required|valid_email',
'code_pass' => 'required|min_length[5]'
];

if(!$this->validate($validation)){
    return redirect()->back()->withInput()->with('errors',$this->validator);
}

$email_user = $this->request->getPost('email');

$code = random_int(100000,999999);

session()->set('login_code',$code);
session()->set('login_email',$email_user);

$email = \Config\Services::email();

$email->setFrom('instituto@test.com','Instituto');
$email->setTo($email_user);
$email->setSubject('Codigo matricula');
$email->setMessage('Tu codigo es: '.$code);

$email->send();

return redirect()->to('/public/login_code');
}
 public function login_code(){
  helper('form');

  return view('login/login_code'); 
     
 }
 
 public function login_code_post(){
   helper('form');
   $correo = $this->request->getPost('email');
   $code_pass=$this->request->getPost('code_pass');
   $validation_rules=[
   'email' => 'required',
   'code_pass'=> 'required'
   ];

   if(!$this->validate($validation_rules)){
      redirect()->back()->withInput()->with('error',$validation_rules);
   }
   return redirect()->to('matricula');
 }
}
