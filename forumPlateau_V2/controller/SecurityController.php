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

    // Affiche la page d'inscription
    public function register () {
        return [
             "view" => VIEW_DIR."security/register.php",
             "meta_description" => "Page d'inscription",
             "data" => []
         ];
    }

    // Affiche la page de connexion
    public function login () {
        return [
            "view" => VIEW_DIR."security/login.php",
            "meta_description" => "Page de connexion",
            "data" => []
        ];
    }

    // Deconnecte l'utilisateur
    public function logout () {
        unset($_SESSION['user']); // unset() détruit la ou les variables dont le nom a été passé en argument
        $this->redirectTo("home", "index");exit;
    }

    // Inscription utilsateur
    public function registerUser () {
        if(isset($_POST['submit'])){
            // filter_input récupère une variable externe et la filtre, ici on filtre les varibale du formulaire
            $nickName = filter_input(INPUT_POST, "nickName", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL, FILTER_VALIDATE_EMAIL);
            $pass1 = filter_input(INPUT_POST, "pass1", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $pass2 = filter_input(INPUT_POST, "pass2", FILTER_SANITIZE_FULL_SPECIAL_CHARS); 
            
            if($nickName && $email && $pass1 && $pass2){
                $userManager =  new UserManager();
                $user = $userManager->findOneByEmail($email);// Trouver si un utilisateur a deja cet email
            
                if($user){
                    $this->redirectTo("security", "register"); // Si oui, on ne fait rien et retourne sur la page d'inscription
                } else {
                    // Si non, on s'assure que les mdp sont les mêmes et on ajoute l'utilisateur à la bdd
                    if ($pass1 == $pass2 && strlen($pass1) >= 5){
                        $dataUser = [
                            'nickName' => $nickName,
                            'email' => $email,
                            'password' => password_hash($pass1, PASSWORD_DEFAULT), // On créé une clé de hachage pour le mot de passe
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

    // Connexion de l'utilisateur
    public function loginUser(){
        if(isset($_POST['submit'])){

            $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL,FILTER_VALIDATE_EMAIL);
            $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            
            if($email && $password){
                $userManager = new UserManager();
                $user = $userManager->findOneByEmail($email);
                if($user){
                    $hash = $user->getPassword();
                    if(password_verify($password, $hash)){ // On vérifie qu'un mot de passe correspond au hachage
                        $now = new \DateTime();
                        $dateBan = new \DateTime($user->getDateBan()); // On recupere la date de ban au format DateTime
                        if (isset($dateBan) && $dateBan <= $now) { // Si la dateBan existe et qu'elle est depassé
                            $data = [
                                'isBan' => 0,
                                'dateBan' => null
                            ];
                            $userManager->update($user->getId(), $data); // On débanne l'utilisateur en bdd
                            $user->setIsBan(0); // On modifie l'user en cours
                        }
                        Session::setUser($user); // On modifie l'user en session
                        
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

    // Page du profil d'un utilisateur
    public function profile (){
        
        $user = Session::getUser();
        $postManager = new PostManager();
        $postsByUser = $postManager->findPostsByUser($user->getId()); // On recupere les messages d'un utilisateur
        $topicManager = new TopicManager();
        $topicsByUser = $topicManager->findTopicsByUser($user->getId()); // On recupere les sujets d'un utilisateur
        $countPosts = 0;
        if($postsByUser){
            foreach($postsByUser as $post){
                $countPosts++; // On compte le nombre de messages d'un utilisateur
            }
        }
        $countTopics = 0;
        if($topicsByUser){
            foreach($topicsByUser as $topic){
                $countTopics++; // On compte le nombre de sujets d'un utilisateur
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

    // Liste des utilisateurs
    public function users(){
        $this->restrictTo("ROLE_ADMIN"); // on limite l'accès à l'admin

        $manager = new UserManager();
        $users = $manager->findAll(['nickName', 'ASC']); // On récupere tous les utilisateurs par ordre alphabetique selon le pseudo

        return [
            "view" => VIEW_DIR."security/users.php",
            "meta_description" => "Liste des utilisateurs du forum",
            "data" => [ 
                "users" => $users 
            ]
        ];
    }

    // Ban d'un utilisateur
    public function ban($id){
        if(isset($_POST['submit'])){
            $durationBan = filter_input(INPUT_POST, "durationBan", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if($durationBan && Session::isAdmin()){
                $dateBan = new \DateTime(); // On créé une variable au format date
            
                switch ($durationBan) { // On compare la variable passé en input et on modifie dateBan en consequence
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
                        $dateBan->modify('+500 year');
                        break;
                    default:
                        return null;
                }
                $userManager = new UserManager();
                $data = [
                    'isBan' => 1,
                    'dateBan' => $dateBan->format('Y-m-d H:i:s')
                ];
                $userManager->update($id, $data); // On modifie le table user avec l'id de l'utilisateur à ban et sa durée
            }
        }
        $this->redirectTo('security','users');
    }

    // Suppression d'un utilisateur
    public function deleteUser($id){
        // On verifie si le formulaire est bien passé, et si c'est l'admin ou l'utilisateur même qui veut supprimer son compte
        if(isset($_POST['submit']) && (Session::isAdmin() || Session::getUser()->getId() === (int)$id )){
            $userManager = new UserManager();
            $userManager->delete($id);
            if(Session::getUser()->getId() === (int)$id){
                unset($_SESSION['user']);
            }
        }
        $this->redirectTo('home', 'index');exit;
    }
}