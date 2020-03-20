<?php
session_start(); 
if (isset($_POST['partyName']) && isset($_POST['partyCode']) && strlen($_POST['partyName']) > 5 && strlen($_POST['partyCode'] > 5)){
        $partyName = $_POST['partyName']; 
        $partyCode = $_POST['partyCode']; 

        require_once("../db/DbConnect.php"); 

        $database = new \DbConnect(); 

        $db = $database->connect(); 

        // Récupération de la partie : 

        $getParty = $db->prepare("SELECT * from party WHERE name = ? AND code = ?"); 
        $getParty->execute(array($partyName, $partyCode)); 
        $party = $getParty->fetchAll(PDO::FETCH_ASSOC); 

        // Vrai fonctionnement : On permets à l'utilisateur de rejoindre la salle de partie. 
        $_SESSION['currentParty']['name'] = $partyName; 
        $_SESSION['currentParty']['code'] = $partyCode; 

        include "../templates/pseudo.html";

} else if(isset($_POST['pseudo']) && strlen($_POST['pseudo']) > 3) {
    $_SESSION['currentParty']['pseudo'] = $_POST['pseudo']; 
    var_dump($_SESSION); 
} else {
    echo "Le pseudo rentré est trop court. Veuillez réessayer";
}