<?php
    $topic = $result["data"]['topic']; 
    $posts = $result["data"]['posts']; 
?>

<h1><?= $topic->getTitle() ?></h1>

<?php
foreach($posts as $post ){?>

    <p><?= $post ?> par <?= $post->getUser() ?> le <?= $post->getCreationDate() ?></p>
<?php }
?>
<h2>Ajoutez un message</h2>
<form action="index.php?ctrl=forum&action=addPost&id=<?= $topic->getId() ?>" method="post">
    <label for="text">Votre message</label>
    <textarea name="text" id="text" cols="50" rows="1"></textarea>
    <button type="submit" name="submit">Envoyer</button>
</form>