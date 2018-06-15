<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/getSucurs', function (Request $request, Response $response){

    $apiKey = $request->getHeader('Api-Key')[0];
   
    try{

        $data = Auth::GetData($apiKey);
        $database = new db();
        $db = $database->getConnection();

        $query = "select * from PMSUCURSAL where Suc_Estatu = 'A'";
        // prepare query statement
        
        $stmt = $db->prepare($query);

        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            $arrSucurs = array();
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
            $rs['Mensaje'] = "No existen sucursales";
            $rs["data"] = null;
            return $response->withJson($rs);
        }

    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    }

});

$app->post('/addSucursal', function (Request $request, Response $response){


    try{
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $nombre = $json['nombre'];
        $prefijo = $json['prefijo'];
        
        $query = "CALL PMSUCURSALALT('$nombre','$prefijo')";
        
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
            $rs['CodigoRespuesta'] = "3";
            $rs['Mensaje'] = "Error al insertar la sucursal";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});

$app->post('/updateSucursal', function (Request $request, Response $response){


    try{
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $id = $json['id'];
        $nombre = $json['nombre'];
        $prefijo = $json['prefijo'];
        
        $query = "CALL PMSUCURSALMOD($id,'$nombre','$prefijo')";
        
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
            $rs['CodigoRespuesta'] = "3";
            $rs['Mensaje'] = "Error al insertar la sucursal";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});


$app->post('/deleteSucursal', function (Request $request, Response $response){


    try{
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $id = $json['id'];
        
        $query = "CALL PMSUCURSALDEL($id)";
        
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
            $rs['CodigoRespuesta'] = "3";
            $rs['Mensaje'] = "Error al insertar la sucursal";
            $rs["data"] = null;
            return $response->withJson($rs);
        }


    } catch (Exception $e) {
        
        $rs['CodigoRespuesta'] = "1";
        $rs['Mensaje'] = "Sin privilegios de acceso";
        $rs["data"] = null;
        return $response->withJson($rs);
    
    }

});

?>