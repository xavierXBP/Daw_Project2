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

     return view('login/login_public',$data);
    }

    public function log_post(){
     helper('form');  
     $correo = $this ->request->getPost('email');
     $code_pass=$this->request->getPost('code_pass');

      $validation_rules = [
      'email' => 'required|valid_email' ,
      'code_pass'=>'required|min_length[5]'
     ];
     $missatges=[
      'email' => [
         'required' => 'el campo email es obligatorio' ,
         'valid_email' =>'debes ingresar un correo valid que tenga formato : exemple@domain.com'
      ],
      'code_pass' => [
         'required' => 'el campo code pass es obligatorio ' ,
         'min_length[5]' => 'como minimo 5 characteres en el code pass .'
      ]
     ];

     $email = \Config\services::email();
     $codegenerated =random_int(15, 18);

  
     $email ->setFrom('ezriguina@inscaparrella.cat','institut caparrella');
     $email ->setTo('jimyydesanta@gmail.com'); 
     $email ->setSubject('Processo de matriculacion instuto caparrella tandada 1');
     $email->setMessage('tu codigo de acceso para matricularse es : '.$codegenerated) ;
    

     if($this->validate($validation_rules,$missatges)){
      
      return view('login/login_code');
      
     
     }else{
         return redirect()->back()->withInput()->with('error',$this->validator);

     }
     
    
  /*  if($email->send()){
       redirect()->to('matricula.php');
    }else{
       echo" no se ha enviado el correo correctamente";
    }
    return view('matricula.php');

    }*/

}
 public function login_code(){
    
  return view('login/login_code'); 
     
 }
 public function login_code_post(){
   helper('form');
   
   return view('matricula/matricula');
 }
}
