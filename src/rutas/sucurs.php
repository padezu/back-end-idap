<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/getSucurs', function (Request $request, Response $response){

    $apiKey = $request->getHeader('Api-Key')[0];//$request->getHeader('HTTP_API_KEY');    
   
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

?>