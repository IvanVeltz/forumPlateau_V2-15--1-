<?php 
namespace Controller;

use App\AbstractController;
use App\ControllerInterface;
use App\Session;
use Model\Managers\UserManager;
use Model\Managers\PostManager;
use Model\Managers\TopicManager;

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
                        $now = new \DateTime();
                        $dateBan = new \DateTime($user->getDateBan()); // Convertir la chaîne en DateTime
                        if ($dateBan <= $now) { // Comparaison correcte entre objets DateTime
                            $data = [
                                'isBan' => 0,
                                'dateBan' => null
                            ];
                            $userManager->update($user->getId(), $data);
                            $user->setIsBan(0);
                            
                        }
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
    public function profile (){
        
        $user = Session::getUser();
        $postManager = new PostManager();
        $postsByUser = $postManager->findPostsByUser($user->getId());
        $topicManager = new TopicManager();
        $topicsByUser = $topicManager->findTopicsByUser($user->getId());
        $countPosts = 0;
        if($postsByUser){
            foreach($postsByUser as $post){
                $countPosts++;
            }
        }
        $countTopics = 0;
        if($topicsByUser){
            foreach($topicsByUser as $topic){
                $countTopics++;
            }
        }

        return [
                "view" => VIEW_DIR."security/profile.php",
                "meta_description" => "Profil de l'utilisateur",
                "data" => [
                    "topics" => $countTopics,
                    "posts" => $countPosts
                ]
        ];
    }

    public function users(){
        $this->restrictTo("ROLE_ADMIN");

        $manager = new UserManager();
        $users = $manager->findAll(['nickName', 'ASC']);

        return [
            "view" => VIEW_DIR."security/users.php",
            "meta_description" => "Liste des utilisateurs du forum",
            "data" => [ 
                "users" => $users 
            ]
        ];
    }

    public function ban($id){
        if(isset($_POST['submit'])){
            $durationBan = filter_input(INPUT_POST, "durationBan", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if($durationBan && Session::isAdmin()){
                $dateBan = new \DateTime();
            
                switch ($durationBan) {
                    case 'day':
                        $dateBan->modify('+1 day');
                        break;
                    case 'week':
                        $dateBan->modify('+7 days');
                        break;
                    case 'month':
                        $dateBan->modify('+1 month');
                        break;
                    case 'permanent':
                        return "9999-12-31 23:59:59";
                    default:
                        return null;
                }
                $userManager = new UserManager();
                $data = [
                    'isBan' => 1,
                    'dateBan' => $dateBan->format('Y-m-d H:i:s')
                ];
                $userManager->update($id, $data);
            }
        }
        $this->redirectTo('security','users');
    }

    public function deleteUser($id){
        var_dump($id);
        if(isset($_POST['submit']) && Session::isAdmin()){
            $userManager = new UserManager();
            $postManager = 
            $userManager->delete($id);
        }
        $this->redirectTo('security', 'users');
    }
}