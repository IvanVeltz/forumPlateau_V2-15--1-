<?php
    use App\Session as Session;
    $category = $result["data"]['category']; 
    $topics = $result["data"]['topics']; 
?>

<h1>Liste des topics</h1>

<?php
foreach($topics as $topic ){ ?>
    <p><a href="index.php?ctrl=forum&action=listPostsByTopic&id=<?=$topic->getId() ?>"><?= $topic ?></a> par <?= $topic->getUser() ?> le <?= $topic->getCreationDate() ?></p>
    <?php if ((Session::getUser() && Session::getUser() == $topic->getUser()) || Session::isAdmin()){
    ?>
    <form action="index.php?ctrl=forum&action=deleteTopic&id=<?= $topic->getId() ?>" method="post">
        <input type="submit" name="submit" value="Supprimer le sujet">
    </form>
<?php }
    if ((Session::getUser() && Session::getUser() == $topic->getUser())){
        if (!$topic->getClosed()){
    ?>
    <form action="index.php?ctrl=forum&action=updateTopic&id=<?= $topic->getId() ?>" method="post">
        <button type="button">Modifier le topic</button>
        <label for="title">Votre nouveau topic</label>
        <textarea name="title" cols="50" rows="1"><?= $topic ?></textarea>
        <input type="submit" name="submit" value="Valider">
    </form>
<?php }
}
    if ((Session::getUser() && Session::getUser() == $topic->getUser()) || Session::isAdmin()){
        if (!$topic->getClosed()){
            ?>
    <form action="index.php?ctrl=forum&action=closeTopic&id=<?= $topic->getId() ?>" method="post">
    <input type="submit" name="submit" value="Cloturer le sujet">
    </form>
    <?php
    }
}
}

if (Session::getUser()){
?>
<h2>Ajoutez un sujet</h2>
<form action="index.php?ctrl=forum&action=addTopic&id=<?= $category->getId() ?>" method="post">
    <label for="title">Votre sujet</label>
    <input type="text" name="title" id="title">
    <label for="text">Votre message</label>
    <textarea name="text" id="text" cols="50" rows="5"></textarea>
    <button type="submit" name="submit">Envoyer</button>
</form>
<?php
} else {
    ?>
<h2>Vous devez vous connecté pour ajouter un sujet</h2>
    <?php
}