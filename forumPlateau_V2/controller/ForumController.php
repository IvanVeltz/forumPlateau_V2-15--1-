<?php
namespace Controller;

use App\Session;
use App\AbstractController;
use App\ControllerInterface;
use App\DAO;
use Model\Managers\CategoryManager;
use Model\Managers\TopicManager;
use Model\Managers\PostManager;

class ForumController extends AbstractController implements ControllerInterface{

    public function index() {
        
        // créer une nouvelle instance de CategoryManager
        $categoryManager = new CategoryManager();
        // récupérer la liste de toutes les catégories grâce à la méthode findAll de Manager.php (triés par nom)
        $categories = $categoryManager->findAll(["name", "DESC"]);

        // le controller communique avec la vue "listCategories" (view) pour lui envoyer la liste des catégories (data)
        return [
            "view" => VIEW_DIR."forum/listCategories.php",
            "meta_description" => "Liste des catégories du forum",
            "data" => [
                "categories" => $categories
            ]
        ];
    }

    public function listTopicsByCategory($id) {

        $topicManager = new TopicManager();
        $categoryManager = new CategoryManager();
        $category = $categoryManager->findOneById($id);
        $topics = $topicManager->findTopicsByCategory($id);

        return [
            "view" => VIEW_DIR."forum/listTopics.php",
            "meta_description" => "Liste des topics par catégorie : ".$category,
            "data" => [
                "category" => $category,
                "topics" => $topics
            ]
        ];
    }

    public function listPostsByTopic($id){

        $postManager = new PostManager();
        $topicManager = new TopicManager();
        $topic = $topicManager->findOneById($id);
        $posts = $postManager->findPostsByTopic($id);

        return [
            "view" => VIEW_DIR."forum/listPosts.php",
            "meta_description" => "Liste des posts par topics : ".$topic,
            "data" => [
                "topic" => $topic,
                "posts" => $posts
            ]
        ];

    }

    

    public function addTopic($id){
        
        if(isset($_POST['submit']) && (Session::getUser())){
            $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $text = filter_input(INPUT_POST, "text", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $user = Session::getUser();
            $dataTopic = [
                "title" => $title,
                "creationDate" => date("Y-m-d H:i:s"),
                "user_id" => $user->getId(),
                "category_id" => $id
            ];
            
            $topicManager = new TopicManager();
            $topic = $topicManager->add($dataTopic);

            $dataPost = [
                "text" => $text, 
                "creationDate" => date("Y-m-d H:i:s"),
                "user_id" => $user->getId(), 
                "topic_id" => $topic
            ];
            $postManager = new PostManager();
            $postManager->add($dataPost);

            $this->redirectTo("forum", "listTopicsByCategory", $id);

        }
    }

    public function deleteTopic($id){
        if (isset($_POST['submit']) && Session::getUser()){
            $topicManager = new TopicManager();
            $topic = $topicManager->findOneById($id);
            var_dump($id);
            $user = Session::getUser();
            if (($user->getId() == $topic->getUser()->getId()) || Session::isAdmin()){
                $topicManager->delete($id);  
            } 
            $this->redirectTo("forum", "listTopicsByCategory", $topic->getCategory()->getId());exit;
            
        } $this->redirectTo("home", "index");exit;
    }

    public function updateTopic($id){
        if(isset($_POST['submit']) && Session::getUser()){
            $topicManager = new TopicManager();
            $topic = $topicManager->findOneById($id);
            $user = Session::getUser();
            if ($user->getId() == $topic->getUser()->getId()){
                $title = filter_input(INPUT_POST, "title", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data = [
                    "title" => $title
                ];
                $t = $topicManager->update($id, $data);
            }
            $this->redirectTo("forum", "listTopicsByCategory", $topic->getCategory()->getId());exit;
        } $this->redirectTo("home", "index");exit;
    }

    public function closeTopic($id){
        if (isset($_POST['submit']) && Session::getUser()){
            $user = Session::getUser();
            $topicManager = new TopicManager();
            $topic = $topicManager->findOneById($id);
            if (($user->getId() == $topic->getUser()->getId()) || Session::isAdmin()){
                $topicManager->closeTopicById($id);  
            } 
            $this->redirectTo("forum", "listTopicsByCategory", $topic->getCategory()->getId());exit;
        } $this->redirectTo("home", "index");exit;
    }

    public function addPost($id){
        
        if(isset($_POST['submit']) && (Session::getUser())){
            $text = filter_input(INPUT_POST, "text", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $user = Session::getUser();
            $data = [
                "text" => $text, 
                "creationDate" => date("Y-m-d H:i:s"),
                "user_id" => $user->getId(), 
                "topic_id" => $id
            ];
            $postManager = new PostManager();
            $postManager->add($data);

            // On rappele la vue 
            $this->redirectTo("forum", "listPostsByTopic", $id);

        }
    }

    public function deletePost($id){
        if (isset($_POST['submit']) && Session::getUser()){
            
            $postManager = new PostManager();
            $post = $postManager->findOneById($id);
            $topicId = $post->getTopic()->getId();
            $posts = $postManager->findPostsByTopic($topicId);
            $posts = iterator_to_array($posts);
            $user = Session::getUser();
            if ($user->getId() == $post->getUser()->getId() || Session::isAdmin()){
                if (count($posts) > 1){
                    $postManager->delete($id);
                } else {
                    $this->deleteTopic($topicId);
                    $this->redirectTo("home", "index");exit;
                }  
            } 
            $this->redirectTo("forum", "listPostsByTopic", $topicId);exit;
            
        } $this->redirectTo("home", "index");exit;
    }

    public function updatePost($id){
        if (isset($_POST['submit']) && Session::getUser()){
            $postManager = new PostManager();
            $post = $postManager->findOneById($id);
            $user = Session::getUser();
            if ($user->getId() == $post->getUser()->getId()){
                $text = filter_input(INPUT_POST, "text", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $data = [
                    "text" => $text
                ];
                $postManager->update($id, $data);
            }
            $this->redirectTo("forum", "listPostsByTopic", $post->getTopic()->getId());exit;
        } $this->redirectTo("home", "index");exit;
    }
}