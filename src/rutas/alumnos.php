<?
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/getAlumnos/{id}', function (Request $request, Response $response){

    try{
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);
        $id = $request->getAttribute('id');

        $query = "select Alu_IDAlum, Alu_Nombre, Alu_ApeMat, Alu_ApePat, Alu_Usuari,
                         Alu_Passwo, Alu_Matric, Alu_Estatu, Tip_Descri, Suc_Nombre,
                         Alu_FecAlt
                    from PMALUMNOS,  
                         PMALUMTIPO,
                         PMSUCURSAL
                    where Alu_Estatu = 'A'
                      and Alu_Sucurs = $id
                      and Alu_Tipo   = Tip_IDTipo 
                      and Alu_Sucurs = Suc_IDSucu";

        $database = new db();
        $db = $database->getConnection();
        
        $stmt = $db->prepare($query);
        // execute query
        $stmt->execute();

        if ($stmt->rowCount() > 0){

            $arrAlumnos = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                $alumno = new alumnoVO();
                $alumno->Alu_IDAlum = $row['Alu_IDAlum'];
                $alumno->Alu_Nombre = $row['Alu_Nombre'];
                $alumno->Alu_ApePat = $row['Alu_ApePat'];
                $alumno->Alu_ApeMat = $row['Alu_ApeMat'];
                $alumno->Alu_Usuari = $row['Alu_Usuari'];
                $alumno->Alu_Passwo = $row['Alu_Passwo'];
                $alumno->Alu_Matric = $row['Alu_Matric'];
                $alumno->Alu_Estatu = $row['Alu_Estatu'];
                $alumno->Tip_Descri = $row['Tip_Descri'];
                $alumno->Suc_Nombre = $row['Suc_Nombre'];
                $alumno->Alu_FecAlt = $row['Alu_FecAlt'];
                array_push($arrAlumnos, $alumno);
            }

            $rs['CodigoRespuesta'] = "0";
            $rs['Mensaje'] = "Ejecucion exitosa";
            $alumnos_arr = array("alumnos" => $arrAlumnos);
            $rs["data"] = $alumnos_arr;
            return $response->withJson($rs);

        }else{    
            
            $rs['CodigoRespuesta'] = "000002";
            $rs['Mensaje'] = "No existen alumnos en esta sucursal";
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

$app->post('/addAlumno', function (Request $request, Response $response){


    try{
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $nombre = $json['nombre'];
        $apaterno = $json['apaterno'];
        $amaterno = $json['amaterno'];
        $usuario = $json['usuario'];
        $password = $json['password'];
        $matricula = $json['matricula'];
        $tipo = $json['tipo'];
        $codactiva = $json['codactiva'];
        $sucursal = $json['sucursal'];
        $tipoalt = $json['tipoalt'];
        
        $query = "CALL PMALUMNOSALT('$nombre','$apaterno','$amaterno', '$usuario','$password','$matricula',$tipo,'$codactiva',$sucursal,'$tipoalt')";

        
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
            $rs['Mensaje'] = "Error al insertar el alumno";
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

$app->post('/cancelAlumno', function (Request $request, Response $response){

    try{    
        
        $apiKey = $request->getHeader('Api-Key')[0];
        $data = Auth::GetData($apiKey);

        $json = $request->getParam('json');
        $id = $json['id'];
        
        $query = "CALL PMALUMNOSCAN($id)";

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
            $rs['Mensaje'] = "Error al cancelar al alumno";
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