<?

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app = new \Slim\App;

//Obtiene todos los administradores existentes.
$app->get('/getAdmins', function (Request $request, Response $response){

    // instantiate database and product object
    $database = new db();
    $db = $database->getConnection();


    $query = "SELECT * FROM PMADUSUARI";

    // prepare query statement
    $stmt = $db->prepare($query);

    // execute query
    $stmt->execute();

    if ($stmt->rowCount() > 0){

        // products array
        $users_arr["usuarios"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);

            $user_item = new userADM();
            $user_item->Adm_IDUsua = $row['Adm_IDUsua'];
            $user_item->Adm_Usuari = $row['Adm_Usuari'];
            $user_item->Adm_Passwo = $row['Adm_Passwo'];
            $user_item->Adm_Estatu = $row['Adm_Estatu'];
            $user_item->Adm_FecAlt = $row['Adm_FecAlt'];
            $user_item->Adm_FecCan = $row['Adm_FecCan'];
            $user_item->Adm_UsTipo = $row['Adm_UsTipo'];
            $user_item->Adm_Sucurs = $row['Adm_Sucurs'];
            $user_item->Adm_FecSis = $row['Adm_FecSis'];

            array_push($users_arr["usuarios"], $user_item);
        }

        return $response->withJson($users_arr);


    }else{
        echo json_encode("Error");
    }

});


//Obtiene administrador por ID.
$app->get('/getAdmins/{id}', function (Request $request, Response $response){

    //obtenemos el id del administrador
    $id = $request->getAttribute('id');

    // instantiate database and product object
    $database = new db();
    $db = $database->getConnection();

    $query = "SELECT * FROM PMADUSUARI WHERE Adm_IDUsua  ='$id'";

    // prepare query statement
    $stmt = $db->prepare($query);

    // execute query
    $stmt->execute();

    if ($stmt->rowCount() > 0){

        // products array
        $users_arr["usuarios"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);

            $user_item = new userADM();
            $user_item->Adm_IDUsua = $row['Adm_IDUsua'];
            $user_item->Adm_Usuari = $row['Adm_Usuari'];
            $user_item->Adm_Passwo = $row['Adm_Passwo'];
            $user_item->Adm_Estatu = $row['Adm_Estatu'];
            $user_item->Adm_FecAlt = $row['Adm_FecAlt'];
            $user_item->Adm_FecCan = $row['Adm_FecCan'];
            $user_item->Adm_UsTipo = $row['Adm_UsTipo'];
            $user_item->Adm_Sucurs = $row['Adm_Sucurs'];
            $user_item->Adm_FecSis = $row['Adm_FecSis'];

            array_push($users_arr["usuarios"], $user_item);
        }

        return $response->withJson($users_arr);


    }else{
        echo json_encode("Error");
    }

});

//Login de un usuario administrador.
$app->post('/IniciarSesion', function (Request $request, Response $response){

    //obtenemos el id del administrador
    $usuario = $request->getParam('usuario');
    $contrasena = $request->getParam('contrasena');

    // instantiate database and product object
    $database = new db();
    $db = $database->getConnection();

    $query = "SELECT * FROM PMADUSUARI WHERE Adm_Usuari  ='$usuario' AND Adm_Passwo = '$contrasena'";

    // prepare query statement
    $stmt = $db->prepare($query);

    // execute query
    $stmt->execute();

    if ($stmt->rowCount() > 0){

        // products array
        $users_arr["usuarios"]=array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);

            $user_item = new userADM();
            $user_item->Adm_IDUsua = $row['Adm_IDUsua'];
            $user_item->Adm_Usuari = $row['Adm_Usuari'];
            $user_item->Adm_Passwo = $row['Adm_Passwo'];
            $user_item->Adm_Estatu = $row['Adm_Estatu'];
            $user_item->Adm_FecAlt = $row['Adm_FecAlt'];
            $user_item->Adm_FecCan = $row['Adm_FecCan'];
            $user_item->Adm_UsTipo = $row['Adm_UsTipo'];
            $user_item->Adm_Sucurs = $row['Adm_Sucurs'];
            $user_item->Adm_FecSis = $row['Adm_FecSis'];

            array_push($users_arr["usuarios"], $user_item);
        }

        return $response->withJson($users_arr);


    }else{
        echo json_encode("Error");
    }

});