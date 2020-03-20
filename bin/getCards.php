<?php

require_once("../db/DbConnect.php"); 

$database = new \DbConnect(); 

$db = $database->connect(); 

// Récupération des cartes : 

$getCards = $db->prepare("SELECT * from cards"); 
$getCards->execute(); 
$cards = $getCards->fetchAll(PDO::FETCH_ASSOC);

/* Renvoie un tableau type 
    [0] =>
        {
            "id" => 1
            "name" => "Villageois"
            "description" => "Blablabla"
        }
*/

echo json_encode($cards, JSON_PRETTY_PRINT); 

