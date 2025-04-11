<?php
namespace Model\Managers;

use App\Manager;
use App\DAO;

class PostManager extends Manager{

    // on indique la classe POO et la table correspondante en BDD pour le manager concerné
    protected $className = "Model\Entities\Post";
    protected $tableName = "post";

    public function __construct(){
        parent::connect();
    }

    // récupérer tous les posts d'un topic spécifique (par son id)
    public function findPostsByTopic($id) {

        $sql = "SELECT * 
                FROM ".$this->tableName." p 
                WHERE p.topic_id = :id
                ORDER BY p.creationDate";
       
        // la requête renvoie plusieurs enregistrements --> getMultipleResults
        return  $this->getMultipleResults(
            DAO::select($sql, ['id' => $id]), 
            $this->className
        );
    }

    public function findPostsByUser($id){
        
        $sql = "SELECT *
                FROM ".$this->tableName." p
                WHERE p.user_id = :id
        ";

        return $this->getMultipleResults(
            DAO::select($sql, ['id' => $id]),
            $this->className
        );
    }
}