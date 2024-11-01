<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

class Productos
{
    private $error = false;
    private $errorMensaje = '';
    private $sql = '';
    
    public $idProducto;
    public $nombreProducto;
    public $descProducto;
    public $imagenProducto;
    public $isImgVacio;
    public $adjuntoProducto;
    public $isAdjVacio;
    
    function __construct()
    {
        
    }
    
    public function dameError()
    {
       return array("ErrorMensaje"=>$this->errorMensaje,"Error"=>$this->error);
    }
	
    public function defineError($a_error)
    {
         $this->error=$a_error["Error"];
         $this->errorMensaje=$a_error["ErrorMensaje"];
    }
    
    public function dameSQL(){
            return $this->sql;
    }


    function dameProductos()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_productos_dameProductos()";
        $stmt = bd_preparaSQL($this->sql,array());
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {
            if(bd_cuentaRegistros($stmt['res'])>0)
            {
                while($fila = bd_dameRegistro($stmt['res']))
                {
                    array_push($arrResultado,$fila);
                }
            }
            
            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        }


        return $arrResultado;

    }
    
    function guardaProducto()
    {
        
        $arrResultado = array();
        
        $imagen = $this->imagenProducto;
        if($this->imagenProducto == '' || !$this->imagenProducto)
        {
            $imagen = null;
        }
        
        $adjunto = $this->adjuntoProducto;
        if($this->adjuntoProducto == '' || !$this->adjuntoProducto)
        {
            $adjunto = null;
        }
        
        $this->sql = "CALL sp_productos_guardaProducto(?,?,?,?,?,?,?)";
        $stmt = bd_preparaSQL($this->sql,array('1_s'=>$this->nombreProducto,
                                         '2_s'=>$this->descProducto,
                                         '3_s'=>$imagen,
                                         '4_i'=>$this->isImgVacio,
                                         '5_s'=>$adjunto,
                                         '6_i'=>$this->isAdjVacio,
                                         '7_i'=>$this->idProducto)
                             );
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {   
            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        }
    }
    
    function eliminaProducto()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_productos_eliminaProducto(?)";
        $stmt = bd_preparaSQL($this->sql,array('1_i'=>$this->idProducto)
                             );
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {
            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        }
    }
    
    /****************************************************************************************************
    FUNCION: dameInfoProducto()
    OBJETIVO: DEVUELVE LA INFORMACIÓN Y PRECIOS DE UN PRODUCTO 
    PARAMETROS: OBJETO:PROPIEDAD
    ****************************************************************************************************/
    function dameInfoProducto()
    {
        $arrResultado = array();
        
        $this->sql = "CALL sp_productos_dameInfoProducto(".$this->idProducto.")";
        $stmt = bd_preparaSQL($this->sql,array());
        
        if($stmt['Error'])
        {
            $this->defineError(array('Error'=>true,'ErrorMensaje'=>$stmt['ErrorMensaje']));
        }
        else
        {
            if(bd_cuentaRegistros($stmt['res'])>0)
            {
                $arrResultado = bd_dameRegistro($stmt['res']);
            }
            
            bd_stmt_liberaResultSet($stmt['stmt'], $stmt['res']);
        }


        return $arrResultado;

    }
    
}
?>
