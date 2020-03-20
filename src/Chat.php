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
            $this->setName($from, $msg); 
            echo printf("Setting new name '%s'for %d", $insert->message, $from->name);
        }
        if ($insert->command == "getNames"){
            $nameArray = ["command" => "getNames"]; 
            foreach($this->clients as $client){ // Envoie à tous les clients existants le resourceId.
                if ($client->resourceId !== $from->resourceId){
                    $nameArray[] = $client->name; 
                }
            }
            var_dump($nameArray);
            $nameArray = json_encode($nameArray);
            $client->send($nameArray); 
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
        $query = $db->prepare('INSERT INTO message(sender, message) VALUES (?, ?)'); 
        $query->execute(array($insert->sender, $insert->message));
    
        foreach ($this->clients as $client) { // Modifier pour envoyer exclusivement au client concerné 
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    public function setName($from, $msg){
        $decodedMsg = json_decode($msg);
        foreach($this->clients as $client){
            if ($from !== $client){
                $client->send($msg); 
            }
        }
        $from->name = $decodedMsg->message; 
        echo printf("Name setted\n");
    }
}