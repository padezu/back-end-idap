<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app->get('/getTipos', function (Request $request, Response $response){

    try{

        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        // instantiate database and product object
        $database = new db();
        $db = $database->getConnection();

        $query = "SELECT * FROM PMTIPUSURI";

        // prepare query statement
        $stmt = $db->prepare($query);

        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            // products array
            $tipos_arr["tipos"]=array();
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $tipo = new TipoAdmVO();
                $tipo->Tip_IDUsua = $row['Tip_IDUsua'];
                $tipo->Tip_TipoUs = $row['Tip_TipoUs'];
                $tipo->Tip_Descri = $row['Tip_Descri'];
                $tipo->Tip_FecAlt = $row['Tip_FecAlt'];
                array_push($tipos_arr["tipos"], $tipo);
            }

            $rs['CodigoRespuesta'] = "0";
            $rs['Mensaje'] = "Ejecucion exitosa";
            $rs["data"] = $tipos_arr;
            return $response->withJson($rs);
    
        }else{
            $rs['CodigoRespuesta'] = "2";
            $rs['Mensaje'] = "No existen tipos de usuarios";
            $rs["data"] = null;
            return $response->withJson($rs);
        }



    }catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    }
});





//Obtiene todos los administradores existentes.
$app->post('/getAdmins', function (Request $request, Response $response){

    try{

    $id = $request->getParam('id');

    //instantiate database and product object
    $database = new db();
    $db = $database->getConnection();

    $query = "SELECT  Adm_IDUsua, Adm_Estatu, Adm_IDUsua, Adm_Sucurs, Adm_UsTipo, 
                      Adm_Usuari, Adm_FecAlt, Suc_Nombre, Tip_Descri 
                FROM PMADUSUARI,
                     PMSUCURSAL,
                     PMTIPUSURI
                WHERE Adm_IDUsua <> $id
                  AND Adm_Sucurs =  Suc_IDSucu
                  AND Adm_UsTipo  = Tip_TipoUs
                  AND Adm_Estatu  = 'A'";

    // prepare query statement
    $stmt = $db->prepare($query);
    // execute query
    $stmt->execute();

    if ($stmt->rowCount() > 0){

        // products array
        $users_arr = array();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $arrayUser = array(
                "Adm_IDUsua" => $row['Adm_IDUsua'],
                "Adm_Usuari" => $row['Adm_Usuari'],
                "Adm_Estatu" => $row['Adm_Estatu'],
                "Adm_FecAlt" => $row['Adm_FecAlt'],
                "Adm_UsTipo" => $row['Adm_UsTipo'],
                "Adm_Sucurs" => $row['Adm_Sucurs'],
                "Suc_Nombre" => $row['Suc_Nombre'],
                "Tip_Descri" => $row['Tip_Descri'],
             );

            array_push($users_arr, $arrayUser);

        }

        $rs['CodigoRespuesta'] = "0";
        $rs['Mensaje'] = "Ejecucion exitosa";
        $usuarios_arr = array("usuarios" => $users_arr);
        $rs["data"] = $usuarios_arr;
        return $response->withJson($rs);


    }else{
        $rs['CodigoRespuesta'] = "2";
        $rs['Mensaje'] = "No existen usuarios";
        $rs["data"] = $query;
        return $response->withJson($rs);
    }

    }catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
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
        
        $arrTipos = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            $sucur = new sucursVO();
            $sucur->Suc_IDSucu = $row['Suc_IDSucu'];
            $sucur->Suc_Nombre = $row['Suc_Nombre'];
            $sucur->Suc_Prefij = $row['Suc_Prefij'];
            $sucur->Suc_Estatu = $row['Suc_Estatu'];
            $sucur->Suc_FecAlt = $row['Suc_FecAlt'];
            array_push($arrSucurs, $sucur);
        }

        $rs['CodigoRespuesta'] = "0";
        $rs['Mensaje'] = "Ejecucion exitosa";
        $sucurs_arr = array("sucursales" => $arrSucurs);
        $rs["data"] = $sucurs_arr;
        return $response->withJson($rs);


    }else{
        $rs['CodigoRespuesta'] = "2";
        $rs['Mensaje'] = "No existen tipos de usuarios";
        $rs["data"] = null;
        return $response->withJson($rs);
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

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

            extract($row);

            $jwt = Auth::SignIn([
                'id' => $row['Adm_IDUsua'],
                'name' => $row['Adm_Usuari']
            ]); 
    
            $user_item = new userADM();
            $user_item->Adm_IDUsua = $row['Adm_IDUsua'];
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
        $rs['Mensaje'] = "El usuario $usuario no existe o contraseña incorrecta";
        $rs["data"] = null;
        return $response->withJson($rs);
    }

});

$app->post('/addUsuario', function (Request $request, Response $response){


    try{
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $usuario = $json['usuario'];
        $password = $json['contrasena'];
        $sucursal = $json['sucursal'];
        $tipo = $json['tipo'];
        
        $query = "CALL PMADUSUARIALT('$usuario','$password','A','$tipo',$sucursal)";

        
        // instantiate database and product object
        $database = new db();
        $db = $database->getConnection();
        
        $stmt = $db->prepare($query);
        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                $rs['CodigoRespuesta'] = $row['ErrorCodigo'];
                $rs['Mensaje'] = $row['ErrorMensaje'];
                $rs["data"] = null;
            }

            return $response->withJson($rs);

        }else{    
            $rs['CodigoRespuesta'] = "000002";
            $rs['Mensaje'] = "Error al insertar el usuario";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "000003";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});

$app->post('/updateUsuario', function (Request $request, Response $response){


    try{
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $id = $json['id'];
        $usuario = $json['usuario'];
        $password = $json['contrasena'];
        $sucursal = $json['sucursal'];
        $tipo = $json['tipo'];
        
        $query = "CALL PMADUSUARIMOD($id,'$usuario','$password','A','$tipo',$sucursal)";
        //instantiate database and product object
        $database = new db();
        $db = $database->getConnection();
        
        $stmt = $db->prepare($query);
        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                $rs['CodigoRespuesta'] = $row['ErrorCodigo'];
                $rs['Mensaje'] = $row['ErrorMensaje'];
                $rs["data"] = null;
            }

            return $response->withJson($rs);

        }else{    
            $rs['CodigoRespuesta'] = "000002";
            $rs['Mensaje'] = "Error al modificar al usuario el usuario";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "000003";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});

$app->post('/cancelUsuario', function (Request $request, Response $response){


    try{
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $id = $json['id'];
        
        $query = "CALL PMADUSUARICAN($id)";
        //instantiate database and product object
        $database = new db();
        $db = $database->getConnection();
        
        $stmt = $db->prepare($query);
        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){

                $rs['CodigoRespuesta'] = $row['ErrorCodigo'];
                $rs['Mensaje'] = $row['ErrorMensaje'];
                $rs["data"] = null;
            }

            return $response->withJson($rs);

        }else{    
            $rs['CodigoRespuesta'] = "000002";
            $rs['Mensaje'] = "Error al cancelar al usuario el usuario";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "000003";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});