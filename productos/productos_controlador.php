<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

require_once('productos_vista.php');
    
if(!isset($_POST['accion_ajax']))
{
    $paginaInicio = productos_inicio();
    
    echo $paginaInicio;
}
else
{
    require_once('..'.DIRECTORY_SEPARATOR.'motor'.DIRECTORY_SEPARATOR.'funciones.php');
    require_once('productos.class.php');   
   
    bd_conectar();
    
    switch($_POST['accion_ajax'])
    {
        case 'ajax_modulos_dameProductos':
            ajax_modulos_dameProductos();
        break;
    
        case 'ajax_productos_cargaFormulario':
            ajax_productos_cargaFormulario($_POST['tipoAccion'],$_POST['id']);
        break;
    
        case 'ajax_productos_guardarProducto':
            ajax_productos_guardarProducto($_POST['nombre'],$_POST['desc'],$_POST['isImgVacio'],$_POST['isAdjVacio'],$_POST['id']);
        break;
    
        case 'ajax_productos_eliminarProducto':
            ajax_productos_eliminarProducto($_POST['id']);
        break;
    }

}

function ajax_modulos_dameProductos()
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oProductos = new Productos();
    
    $arrProductos = $oProductos->dameProductos();
    $arrError = $oProductos->dameError();
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }
    else
    {
        $tablaCatalogo = viewProductos_dameTablaProductos($arrProductos);
        
        $arrResultado['resultado'] = $tablaCatalogo;
        
    }
   

	echo json_encode($arrResultado);
}


function ajax_productos_cargaFormulario($tipoAccion,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'tipoAccion'=>$tipoAccion,
                          'id'=>$id,
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oProductos = new Productos();
    
    $arrInfoProducto = array();
    if($tipoAccion == 'ver' || $tipoAccion == 'editar')
    {
        $oProductos->idProducto = $id;
        $arrInfoProducto = $oProductos->dameInfoProducto();
        
        $arrError = $oProductos->dameError();
        if($arrError['Error'])
        {
            $arrResultado['Error'] = $arrError['Error'];
            $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
        }               
    }

    $arrResultado['resultado'] = viewProductos_dameFormulario($arrInfoProducto,$tipoAccion);
    

	echo json_encode($arrResultado);
}


function ajax_productos_guardarProducto($nombre,$desc,$isImgVacio,$isAdjVacio,$id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $arrError = array('Error'=>false,'ErrorMensaje'=>'');

    //IMAGEN
    $imgReducida = '';
    
    if(isset($_FILES["imagen"]))
    {
        //REDUCIMOS EL TAMAÑO DE LA IMG.
            
        $imgOriginal = file_get_contents($_FILES['imagen']['tmp_name']);
        $size = getimagesizefromstring($imgOriginal);
        
        $imgReducida = global_reducirImagen($imgOriginal,($size[0]/4),($size[1]/4),$_FILES['imagen']['type']);
        $imgReducida = 'data:'.$_FILES['imagen']['type'].';base64,'.$imgReducida;
    }
    
    $adjuntoNombre = '';
    
    if(isset($_FILES["adjunto"]))
    {
        //ADJUNTO
        $tmp_name = $_FILES["adjunto"]["tmp_name"];
        // basename() puede evitar ataques de denegación de sistema de ficheros;
        // podría ser apropiada más validación/saneamiento del nombre del fichero
        $name = basename($_FILES["adjunto"]["name"]);
        
        $arrName = explode('.',$name);
        
        $adjuntoNombre = time().rand(0,100).'.'.$arrName[1]; 
    }
    
    $oProductos = new Productos();
    
    $oProductos->nombreProducto = utf8_decode($nombre);
    $oProductos->descProducto = utf8_decode($desc);
    $oProductos->imagenProducto = $imgReducida;
    $oProductos->isImgVacio = $isImgVacio;
    $oProductos->adjuntoProducto = $adjuntoNombre;
    $oProductos->isAdjVacio = $isAdjVacio;
    $oProductos->idProducto = $id;
    
    bd_inicia_transaccion();
    
    $oProductos->guardaProducto();
    $arrError = $oProductos->dameError();
    
    if($arrError['Error'])
    {
        bd_rollback();
    }
    else
    {
        if(isset($_FILES["adjunto"]))
        {
            if(!move_uploaded_file($tmp_name, 'adjuntos/'.$adjuntoNombre))
            {
                bd_rollback();
                
                $arrError['Error'] = true;
                $arrError['ErrorMensaje'] = "Error al guardar el adjunto: ".$_FILES["adjunto"]["error"];
            }
            else
            {
                bd_commit();
            }
        }
        else
        {
            bd_commit();
        }
    }
    
    if($arrError['Error'])
    {
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }

	echo json_encode($arrResultado);
}


function ajax_productos_eliminarProducto($id)
{
    $arrResultado = array('funcionRetorno_sistema' => $_POST['funcionRetorno_sistema'],
                          'modalEspera_sistema' => $_POST['modalEspera_sistema'],
                          'resultado'=>'',
                          'Error'=>false,
                          'ErrorMensaje'=>'');
    
    $oProductos = new Productos();
    
    $oProductos->idProducto = $id;
    
    $oProductos->eliminaProducto();
    $arrError = $oProductos->dameError();
    
    if($arrError['Error'])
    {   
        $arrResultado['Error'] = $arrError['Error'];
        $arrResultado['ErrorMensaje'] = $arrError['ErrorMensaje'];
    }

	echo json_encode($arrResultado);
}

?>