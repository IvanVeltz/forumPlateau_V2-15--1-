<?php 
$users = $result['data']['users'];
?>
<h2>Liste des utilsateurs</h2>
<?php
foreach ($users as $user){
    ?>
    <p><?=$user?></p>
    <?php 
    if ($user->getRole() != "ROLE_ADMIN"){
    ?>
    <form action="index.php?ctrl=security&action=ban&id=<?=$user->getId()?>" method="post">
        <label for="durationBan">Durée de bannissement</label>
        <select name="durationBan" id="banDuration">
            <option value="day">1 jour</option>
            <option value="week">1 semaine</option>
            <option value="month">1 mois</option>
            <option value="permanent">Permannet</option>
        </select>
        <input type="submit" value="submit">
    </form>
    <?php
    }
}