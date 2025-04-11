<?php 
namespace Controller;

use App\AbstractController;
use App\ControllerInterface;
use App\Session;
use Model\Managers\UserManager;

class SecurityController extends AbstractController{
    // contiendra les méthodes liées à l'authentification : register, login et logout

    public function register () {
        return [
             "view" => VIEW_DIR."security/register.php",
             "meta_description" => "Page d'inscription",
             "data" => []
         ];
    }

    public function login () {
        return [
            "view" => VIEW_DIR."security/login.php",
            "meta_description" => "Page de connexion",
            "data" => []
        ];
    }
    public function logout () {
        unset($_SESSION['user']);
        $this->redirectTo("home", "index");exit;
    }

    public function profile (){
        if (SESSION::getUser()){
            $userId = SESSION::getUser()->getId();
            $userManager = new UserManager();


            return [
                "view" => VIEW_DIR."security/profil.php",
                "meta_description" => "Profil de l'utilisateur",

            ]
        }
    }

    public function registerUser () {
        if(isset($_POST['submit'])){
            
            $nickName = filter_input(INPUT_POST, "nickName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
            $pass1 = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $pass2 = filter_input(INPUT_POST, "pass2", FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
            
            if($nickName && $email && $pass1 && $pass2){
                $userManager =  new UserManager();
                $user = $userManager->findOneByEmail($email);
            
                if($user){
                    $this->redirectTo("security", "register");
                } else {
                    if ($pass1 == $pass2 && strlen($pass1) >= 5){
                        $dataUser = [
                            'nickName' => $nickName,
                            'email' => $email,
                            'password' => password_hash($pass1, PASSWORD_DEFAULT),
                            'registrationDate' => date("Y-m-d H:i:s")
                        ];
                        $userManager->add($dataUser);
                        $this->redirectTo("security", "login");exit;
                    }
                }
            } else {
                $this->redirectTo("security", "register");exit;
            }
        } else {
            $this->redirectTo("security", "register");exit;
        }
    }

    public function loginUser(){
        if(isset($_POST['submit'])){

            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL,FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if($email && $password){
                $userManager = new UserManager();
                $user = $userManager->findOneByEmail($email);
                if($user){
                    $hash = $user->getPassword();
                    if(password_verify($password, $hash)){
                        Session::setUser($user);
                        $this->redirectTo("home", "index");exit;
                    } else {
                        $this->redirectTo("security", "login");exit;
                    }
                } else {
                    $this->redirectTo("security", "login");exit;
                }
            } else {
                $this->redirectTo("security", "login");exit;
            }
        } else {
            $this->redirectTo("security", "login");exit;
        }
    }
}