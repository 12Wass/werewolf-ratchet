<?php
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use MyApp\Chat;
use MyApp\Room; 
    require dirname(__DIR__) . '/vendor/autoload.php';

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(new Chat()),
        ),
        8080
    );

    $server->run();
/*
    $roomServer = IoServer::factory(new HttpServer
                                        (new WsServer
                                            (new Room())
                                      ), 
                                      8080
                                    );

    $roomServer->run();

*/
    // Ne pas oublier de run le serveur (php bin/server.php)