<?php
namespace MyApp;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

require_once("db/DbConnect.php");
mb_internal_encoding("UTF-8"); 

class Chat implements MessageComponentInterface {
    public $clients;

    public function __construct() {
        $this->clients = [];
        $this->database = new \DbConnect(); 
        $this->currentConversation = "general"; 
    }
    public function onOpen(ConnectionInterface $conn) {
        // Store the new connection to send messages to later
        $this->clients[$conn->resourceId] = $conn;
        echo "New connection! ({$conn->resourceId})\n";

    } 
    public function onMessage(ConnectionInterface $from, $msg) {
        $insert = json_decode($msg); 

        if ($insert->command == "genMessage"){
            $this->onGeneralMessage($from, $insert, $msg); 
        }
        if ($insert->command == "setName"){
            if ($this->checkName($insert->message)){
                $this->setName($from, $msg); 
            }
            else {
                $from->send(json_encode(["command" => "nameUsed"]));
                return;  
            }
            // echo printf("Setting new name '%s'for %d", $insert->message, $from->name);
        }
        if ($insert->command == "getNames"){
            $nameArray = ["command" => "getNames"]; 
            foreach($this->clients as $client){ // Envoie à tous les clients existants le resourceId.
                if ($client->resourceId !== $from->resourceId){
                    $nameArray[] = $client->name; 
                }
            }
            $nameArray = json_encode($nameArray);
            $client->send($nameArray); 
        }
        if ($insert->command == "createConversation"){
            if($this->createConversation($insert->message, $from)){
                $from->send(json_encode(["command" => "conversationCreated", "user" => $insert->message])); 
            }
        }
        if ($insert->command == "privateMessage"){
            $this->onPrivateMessage($insert->sender, $insert->receiver, $insert->message); 
        }
    }
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        // C'est ici qu'on va gérer la déconnexion et la notification aux autres du fait que quelqu'un a quitter la conversation
        unset($this->clients[$conn->resourceId]);
        foreach($this->clients as $client){
            if ($conn !== $client){
                $hasDisconnected = ["command" => "disconnectUser", "user" => $conn->name]; 
                $client->send(json_encode($hasDisconnected)); 
            }
        }
        echo "Connection {$conn->resourceId} has disconnected\n";
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
    public function onGeneralMessage($from, $insert, $msg){
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $insert->message, $numRecv, $numRecv == 1 ? '' : 's');

        $db = $this->database->connect(); 

        $query = $db->prepare('INSERT INTO message(sender, receiver, message) VALUES (?, ?, ?)'); 
        $query->execute(array($insert->sender, "general", $insert->message));
 
        foreach ($this->clients as $client) { // Modifier pour envoyer exclusivement au client concerné 
            if ($from !== $client) {
                $client->send($msg);
            }
        }
    }
    public function setName($from, $msg){
        // Rajouter une contrainte qui vérifie si le pseudo choisit n'existe pas déjà. 

        $decodedMsg = json_decode($msg);
        foreach($this->clients as $client){
            if ($from !== $client){
                $client->send($msg);
            }
            if ($from == $client){
                $client->name = $decodedMsg->message; 
            }
        }
    }

    public function createConversation($user, $me){
        // $user = l'user à qui on envoie un message : C'est seulement son nom d'utilisateur. 
        // $me = l'user qui envoie le message : C'est l'objet Ratchet complet

        // Etape 1 : Retrouver le client correspondant à $user sachant que $me est déjà le client complet. 
        foreach($this->clients as $client){
            if($client->name == $user){
                $user = $client; // Rattacher $user à l'objet Ratchet complet le représentant. 
            }
        }

        // Etape 2 : Faire en sorte de les lier à travers une conversation : On stocke le pseudo de l'autre dans l'objet de l'user courant ! 

        $this->currentConversation = $user->name; 
        
        $jsonExample = ["command" => "conversationCreated", "user" => $this->currentConversation]; 
       //  $me->send() // Commande : conversationMessages | Content : tableau contenant tous les messages 

        $db = $this->database->connect(); 

        $query = $db->prepare('SELECT * FROM message WHERE sender = ? AND receiver = ? OR sender = ? AND receiver = ? '); 
        $query->execute(array($me->name, $this->currentConversation, $this->currentConversation, $me->name));
        
        $messagesQuery = $query->fetchAll($db::FETCH_ASSOC); 
        $jsonToSend = json_encode(["command" => "conversationMessages", "messages" => $messagesQuery]); 

        $me->send($jsonToSend); 
        return true; 
    }

    public function checkName($msg){
        // Fonction qui vérifie si le pseudo choisit par l'user en cours n'est pas déjà utilisé. 
        foreach ($this->clients as $client){
            if ($client->name == $msg){
                return false; 
            }
            else {
                return true; 
            }
        }
    }
    public function convGeneralMessage(){
        $this->currentConversation = "general"; 
    }
    public function onPrivateMessage($from, $to, $message){
        foreach ($this->clients as $client){
            if ($to == $client->name){
                $client->send(json_encode(["command" => "privateMessage", "receiver" => $to, "sender" => $from, "message" => $message])); 
               
                $db = $this->database->connect(); 
                $query = $db->prepare('INSERT INTO message(sender, receiver, message) VALUES (?, ?, ?)'); 
                $query->execute(array($from, $to, $message));
            }
        }
    }
}