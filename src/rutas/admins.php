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
        $users_arr["usuario"]=array();

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

            //array_push($users_arr["usuario"], $user_item);
            //array_push($rs["data"], $users_arr);

        }

        return $response->withJson($rs);


    }else{
        echo json_encode("Error");
    }

});


//Obtiene administrador por ID.
$app->get('/getAdmins/{id}', function (Request $request, Response $response){

    $config = new Zend\Config\Config(include '../config/config.php');
    //echo $config->webhost;
    
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

    //Respueasta generica
    $rs = array();

    $config = new Zend\Config\Config(include '../config/config.php');
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

        $tokenId    = base64_encode(random_bytes(32));
        $issuedAt   = time();
        $notBefore  = $issuedAt + 10;   //Adding 10 seconds
        $expire     = $notBefore + 60;  // Adding 60 seconds
        $serverName = $config->webhost; // Retrieve the server name from config file

        $data = [
            'iat'  => $issuedAt,         // Issued at: time when the token was generated
            'jti'  => $tokenId,          // Json Token Id: an unique identifier for the token
            'iss'  => $serverName,       // Issuer
            'nbf'  => $notBefore,        // Not before
            'exp'  => $expire,           // Expire
            'data' => [                  // Data related to the signer user
                'userId'   => $tokenId, // userid from the users table
                'userName' => $usuario // User name
            ]
        ];

        $secretKey = base64_decode($config->gjwtKey);

        $pade = new \Firebase\JWT\JWT; 
        $jwt = $pade->encode(
            $data,      //Data to be encoded in the JWT
            $secretKey, // The signing key
            'HS512'     // Algorithm used to sign the token, see https://tools.ietf.org/html/draft-ietf-jose-json-web-algorithms-40#section-3
        ); 


        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);

            $user_item = new userADM();
            $user_item->Adm_Estatu = $row['Adm_Estatu'];
            $user_item->Adm_UsTipo = $row['Adm_UsTipo'];
            $user_item->Adm_Sucurs = $row['Adm_Sucurs'];
            $user_item->Adm_ApiKey =  $jwt;
            
            $queryMen = "select * from PMOPCMENAD where Opc_TipAdm = '$user_item->Adm_UsTipo' and Opc_Estatu = 'A'";
            // prepare query statement
            $stmtMen = $db->prepare($queryMen);
            // execute query
            $stmtMen->execute();

            $arrMenus = array();

            while ($rowMen = $stmtMen->fetch(PDO::FETCH_ASSOC)){

                $menu = new menuADM();
                $menu->Opc_Descri = $rowMen['Opc_Descri'];
                $menu->Opc_RutaPa = $rowMen['Opc_RutaPa'];
                array_push($arrMenus, $menu);
            }

            $rs['CodigoRespuesta'] = "0";
            $rs['Mensaje'] = "Ejecucion exitosa";
            $user_item->Adm_Menus = $arrMenus;
            $user_arr = array("usuario" => $user_item);
            $rs["data"] = $user_arr;
            return $response->withJson($rs);
        }

    }else{  

        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "El usuario no existe";
        $rs["data"] = null;
        return $response->withJson($rs);
    }

});