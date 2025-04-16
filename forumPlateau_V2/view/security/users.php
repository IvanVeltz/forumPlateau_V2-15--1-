<?php 
use App\Session;

$users = $result['data']['users'];
?>
<h2>Liste des utilsateurs</h2>
<?php
if (Session::isAdmin()){
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
                <input type="submit" name="submit" value="submit">
            </form>
            <form action="index.php?ctrl=security&action=deleteUser&id=<?=$user->getId()?>" method="post">
                <input type="submit" name="submit" value="Supprimer utilisateur" class="delete-btn">
            </form>
            <?php
        }
    }
} else {
    Session::addFlash("error", "Vous devez vous connecter pour avoir accès");
    ?>
    <h3 class="message" style="color: red"><?= Session::getFlash("error"); ?></h3>
        <?php
}