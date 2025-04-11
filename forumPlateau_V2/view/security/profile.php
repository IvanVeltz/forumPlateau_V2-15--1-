<?php

use App\Session;

$user = Session::getUser();
$topics = $result['data']['topics'];
$posts = $result['data']['posts'];

if($user){
    ?>
    <h2>Profil</h2>
    <p>Pseudo : <?= $user->getNickName()?></p> 
    <p>Email : <?= $user->getEmail()?></p>
    <p>Nombre de sujets créés : <?=$topics ?> </p>
    <p>Nombre de messages créés : <?=$posts ?> </p>
    <?php
} 