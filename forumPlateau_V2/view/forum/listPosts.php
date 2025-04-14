<?php
    use App\Session as Session;
    $topic = $result["data"]['topic']; 
    $posts = $result["data"]['posts']; 

if(Session::getUser()){
    ?>
    <h1><?= $topic->getTitle() ?></h1>
    <?php
    if (!Session::getUser()->getIsBan()){
    foreach($posts as $post ){?>

        <p><?= $post ?> par 
        <?php if ($post->getUser()->getIsBan()){
            echo "Utilsateur inconnu";
        } else{
            echo $post->getUser();
        }
            ?> 
        le <?= $post->getCreationDate() ?></p>
        <?php
        if ((Session::getUser() && Session::getUser() == $post->getUser()) || Session::isAdmin()){
        ?>
        <form action="index.php?ctrl=forum&action=deletePost&id=<?= $post->getId() ?>" method="post">
            <input type="submit" name="submit" value="Supprimer message">
        </form>
        <?php
        }
        if ((Session::getUser() && Session::getUser() == $post->getUser())){
            if (!$topic->getClosed()){
        ?>
        <form action="index.php?ctrl=forum&action=updatePost&id=<?= $post->getId() ?>" method="post">
            <button type="button">Modifier le message</button>
            <label for="text">Votre message</label>
            <textarea name="text" cols="50" rows="1"><?= $post ?></textarea>
            <input type="submit" name="submit" id="submit" value="Valider">
        </form>
    <?php 
            } 
    }
    }
    if (Session::getUser()){
        if (!$topic->getClosed()){
    ?>

            <h2>Ajoutez un message</h2>
            <form action="index.php?ctrl=forum&action=addPost&id=<?= $topic->getId() ?>" method="post">
                <label for="text">Votre message</label>
                <textarea name="text" id="text" cols="50" rows="1"></textarea>
                <button type="submit" name="submit">Envoyer</button>
            </form>
            <?php
        } else {
            ?>
            <h2>Ce sujet est bloqué</h2>
            <?php
        }
    }
} else {
    ?>
    <h2>Vous êtes ban et n'avez pas accès</h2>
    <?php
}} else {
    Session::addFlash("error", "Vous devez vous connecter pour avoir accès");
    ?>
    <h2><?= Session::getFlash("error"); ?></h2>
        <?php
}