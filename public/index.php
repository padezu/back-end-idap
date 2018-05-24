<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config/db.php';
require '../model/userADM.php';
require '../model/menuADM.php';

/*
 * Application setup, database connection, data sanitization and user  
 * validation routines are here.
 */
//$config = Factory::fromFile('../config/config.php', true); // Create a Zend Config Object
// Consumes the configuration array
// if ($credentialsAreValid) {

//     $tokenId    = base64_encode(mcrypt_create_iv(32));
//     $issuedAt   = time();
//     $notBefore  = $issuedAt + 10;             //Adding 10 seconds
//     $expire     = $notBefore + 60;            // Adding 60 seconds
//     $serverName = $config->get('serverName'); // Retrieve the server name from config file
    
//     /*
//      * Create the token as an array
//      */
//     $data = [
//         'iat'  => $issuedAt,         // Issued at: time when the token was generated
//         'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
//         'iss'  => $serverName,       // Issuer
//         'nbf'  => $notBefore,        // Not before
//         'exp'  => $expire,           // Expire
//         'data' => [                  // Data related to the signer user
//             'userId'   => $rs['id'], // userid from the users table
//             'userName' => $username, // User name
//         ]
//     ];

//      /*
//       * More code here...
//       */
// }
$app = new \Slim\App;
$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");

    return $response;
});

//ADMINISTRADORES DE ALUMNOS
include  '../src/rutas/admins.php';


$app->run();

