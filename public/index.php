<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../config/db.php';
require '../model/userADM.php';
require '../model/menuADM.php';
require '../config/tokenJWT.php';
require '../model/sucursVO.php';

$app = new \Slim\App;

//ADMINISTRADORES DE ALUMNOS
require_once('../src/rutas/admins.php');

//SUCURSALES DISPONIBES
require_once('../src/rutas/sucurs.php');

$app->run();

