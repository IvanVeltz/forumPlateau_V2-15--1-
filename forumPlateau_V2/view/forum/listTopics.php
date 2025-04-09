<?php
    $category = $result["data"]['category']; 
    $topics = $result["data"]['topics']; 
?>

<h1>Liste des topics</h1>

<?php
foreach($topics as $topic ){ ?>
    <p><a href="index.php?ctrl=forum&action=listPostsByTopic&id=<?=$topic->getId() ?>"><?= $topic ?></a> par <?= $topic->getUser() ?> le <?= $topic->getCreationDate() ?></p>
<?php }
?>
<h2>Ajoutez un sujet</h2>
<form action="index.php?ctrl=forum&action=addTopic&id=<?= $category->getId() ?>" method="post">
    <label for="title">Votre sujet</label>
    <input type="text" name="title" id="title">
    <label for="text">Votre message</label>
    <textarea name="text" id="text" cols="50" rows="5"></textarea>
    <button type="submit" name="submit">Envoyer</button>
</form>