<h1>S'inscrie</h1>
<form action="index.php?ctrl=security&action=registerUser" method="post">
    <label for="nickName">Pseudo</label>
    <input type="text" name="nickName" id="nickName">

    <label for="email">Mail</label>
    <input type="email" name="email" id="email">

    <label for="pass1">Mot de passe</label>
    <input type="password" name="pass1" id="pass1">

    <label for="pass2">Confirmation du mot de passe</label>
    <input type="password" name="pass2" id="pass2">

    <input type="submit" name="submit" value="S'enregistrer">
</form>