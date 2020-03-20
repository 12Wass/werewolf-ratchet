<?php 

/* 
    Cette page va lister les dernières parties lancées et récupérer deux infos : 
        - Le numéro de la partie à rejoindre 
        - Le code secret de la partie à rejoindre. 
*/ 

session_start(); 
?> 

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Werewolves</title>
    <script src="js/uikit.js"></script>
    <script src="js/uikit-icons.js"></script>
    <link rel="stylesheet" type="text/css" href="css/uikit.css">
    <style>
    * {
        margin-top: 10%;
    }
    </style>
</head>
<body>
<div class="uk-card uk-card-body uk-card-default uk-width-1-2@m uk-align-center">
    <h1 class="uk-heading-2xlarge uk-text-center uk-card-title">Werewolves</h1>
    <form action="bin/joinParty.php" method="POST">
    <div class="uk-container-small uk-align-center">
        <input type="text" name="partyName" id="partyName" class="uk-input" placeholder="Numéro de la partie">
        <input type="text" name="partyCode" id="partyCode" class="uk-input" placeholder="Code secret">
        <button class="uk-button uk-button-primary uk-button-large uk-width-1-1">Valider</button>
    </div>
</div>
</form>
</body>
</html>