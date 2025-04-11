<?php
namespace Controller;

use App\AbstractController;
use App\ControllerInterface;
use App\Session;
use Model\Managers\UserManager;

class HomeController extends AbstractController implements ControllerInterface {

    public function index(){
        return [
            "view" => VIEW_DIR."home.php",
            "meta_description" => "Page d'accueil du forum"
        ];
    }
        
    public function users(){
        $this->restrictTo("ROLE_USER");

        $manager = new UserManager();
        $users = $manager->findAll(['register_date', 'DESC']);

        return [
            "view" => VIEW_DIR."security/users.php",
            "meta_description" => "Liste des utilisateurs du forum",
            "data" => [ 
                "users" => $users 
            ]
        ];
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
}
