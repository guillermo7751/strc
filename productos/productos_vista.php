<?php

ini_set("display_errors",1);
error_reporting(E_ALL);

function productos_inicio()
{
	//INCLUIMOS EL ARCHIVO JS
	$resultado= '<script src="productos/productos.js?'.time().'"></script>';

	
	$resultado.= '
                    <body id="page-top">
                        <div id="divModalModulo"></div>
                        <form name="frmSistema" id="frmSistema" method="post"  enctype="multipart/form-data" >
                            <div id="wrapper">
                            
                                <!-- Content Wrapper -->
                                <div id="content-wrapper" class="d-flex flex-column">
                            
                                  <!-- Main Content -->
                                  <div id="content">
								  
                                    <!-- Begin Page Content -->
                                    <div class="container-fluid">
                                        <div class="card shadow mb-4" id="view_productos_inicio">
											<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											  <h5 class="m-0 font-weight-bold">Productos</h5>
											  <button type="button" class="btn btn-default" id="btnNuevoProducto" style="background-color: #30a6fc; color: white;"><i class="fa fa-plus" aria-hidden="true"></i>  Nuevo</button>
												
											</div>
											<div class="card-body" id="div_productos_inicio">
												
											</div>
										</div>
										<div class="card shadow mb-5" style="display:none;" id="view_productos_formulario">
											<div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
											  <h5 class="m-0 font-weight-bold" id="viewFormulario">Agregar Producto</h5>
											  <button type="button" class="btn btn-default btn-danger" id="btnRegresarInicio" style="color: white;"><i class="fa fa-arrow-left" aria-hidden="true"></i>  Regresar</button>
											</div>
											<div class="card-body" id="div_productos_formulario">
												
											</div>
										</div>
                                    </div>
                                    <!-- /.container-fluid -->
                                    <input type="hidden" id="modulo" name="modulo" value="" />
                                  </div>
                                  <!-- End of Main Content -->
                            
                                </div>
                                <!-- End of Content Wrapper -->
                            
                              </div>
                              <!-- End of Page Wrapper -->
                            
                              <!-- Scroll to Top Button-->
                              <a class="scroll-to-top rounded" href="#page-top">
                                <i class="fas fa-angle-up"></i>
                              </a>
                        </form>
                    </body>';

	return $resultado;
	
}

function viewProductos_dameTablaProductos($arrProductos)
{
	$resultado = '<div class="table-responsive bordeada">
					<table class="table table-bordered table-responsive-md table-striped text-center" id="tblProductos" cellspacing="0">
						<thead>
							<tr>
							  <th>Producto</th>
							  <th>Descripción</th>
							  <th>Acciones</th>
							</tr>
						</thead>
						<tbody>';

	if(sizeof($arrProductos)>0)
	{
		foreach($arrProductos as $productoEnCurso)
		{
			$btnVer ='<a href="#" title="Ver" style="margin-left:5px;" class="btn btn-circle btn-primary btn-sm accionVer" data-id="'.$productoEnCurso['id'].'"><i class="far fa-eye"></i></a>';
			$btnEditar ='<a href="#" title="Editar" style="margin-left:5px;" class="btn btn-circle btn-info btn-sm accionEditar" data-id="'.$productoEnCurso['id'].'"><i class="fas fa-edit"></i></a>';
			$btnEliminar ='<a href="#" title="Eliminar" style="margin-left:5px;" class="btn btn-danger btn-circle btn-sm accionEliminar" data-nombre="'.$productoEnCurso['nombre'].'" data-id="'.$productoEnCurso['id'].'"><i class="fas fa-trash"></i></a>';
			
			$resultado.='<tr>
							<td>'.($productoEnCurso['nombre']).'</td>
							<td class="text-left">'.($productoEnCurso['descripcion']).'</td>
							<td>'.$btnVer.$btnEditar.$btnEliminar.'</td>
						</tr>';
		}
	}
	else
	{
		$resultado.='<tr>
						<td></td>
						<td>No se encontraron productos.</td>
						<td></td>
					</tr>';
	}
	
	$resultado.='</tbody>
			</table>
		</div>';
	
		
	return $resultado;
}

function viewProductos_dameFormulario($arrInfoProducto,$tipoAccion)
{
	
	//TABLA DE PRECIOS A LA RENTA
				
	//LLENAMOS LOS VALORES QUE VENGAN DE LA INFO DEL PRODUCTO PARA LA EDICION O LA VISTA
	
	$nombreProducto = '';
	if(isset($arrInfoProducto['nombre']))
	{
		$nombreProducto = $arrInfoProducto['nombre'];
	}
	
	$descripcionProducto = '';
	if(isset($arrInfoProducto['descripcion']))
	{
		$descripcionProducto = $arrInfoProducto['descripcion'];
	}
	
	$imagen = '#';
	if(isset($arrInfoProducto['imagen']))
	{
		$imagen = $arrInfoProducto['imagen'];
	}
	
	$adjunto = 'SIN ADJUNTO';
	if(isset($arrInfoProducto['adjunto']))
	{
		if($arrInfoProducto['adjunto'] != '')
		{
			$adjunto = '<a href="productos/adjuntos/'.$arrInfoProducto['adjunto'].'" download>
							'.$arrInfoProducto['adjunto'].'
						</a>';
		}
	}

	$cont = '
			<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
				<label for="txtNombreProducto" class="label-dark">Nombre del producto</label>
				<input maxlength="100" class="form-control formProductos" id="txtNombreProducto" placeholder="Ingrese el nombre del producto" value="'.$nombreProducto.'">
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
				<label for="txtDescProducto" class="label-dark">Descripción</label>
				<textarea class="form-control formProductos" maxlength="500" id="txtDescProducto" style="resize:none;" placeholder="Ingrese una descripción del producto">'.$descripcionProducto.'</textarea>
			</div>
			<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
				<div class="">
					<label for="" class="label-dark formProductos">Imagen del producto</label>
				</div>
				<div class="">';
			
			$clsBefore = '';
			if($imagen<>'#' || ($imagen=='#' && $tipoAccion == 'ver'))
			{
				$clsBefore = 'image-no-before';		
			}
			
			$divImg = '<div class="image-area mt-4 clsProductoImg '.$clsBefore.'"><img id="resultadoProducto" src="'.$imagen.'" alt="" data-vacio="0" class="img-fluid rounded shadow-sm mx-auto d-block"></div>';
			if($tipoAccion <> 'ver')
			{
				
				$cont.='
						<div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
							
							<input id="uploadProductoImg" type="file" accept="image/png, image/jpeg" onchange="js_productos_cargarImagen(this,\'resultadoProducto\',\'clsProductoImg\');" class="form-control border-0 upload">
							
							<label id="" for="uploadProductoImg" class="font-weight-light text-muted upload-label">Elegir archivo</label>
							<div class="input-group-append">
								<label for="uploadProductoImg" class="btn btn-light m-0 rounded-pill px-4"> <i class="fa fa-cloud-upload mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted formCatalogo">Elegir archivo</small></label>
							</div>
						</div>
						'.$divImg.'
						<br />
						<div class="d-flex justify-content-center">
							<button type="button" type="button" class="btn btn-default btn-danger formProductos" id="btnEliminarImagen"><i class="fas fa-trash" aria-hidden="true"></i>  Quitar imagen</button>
						</div>';
			}
			else
			{
				$cont.=$divImg;
			}
	
	$cont.='						
				</div>
			</div>';
			
	$cont.= '<div class="form-group col-xs-12 col-sm-12 col-md-12 text-left">
				<div class="">
					<label for="" class="label-dark formProductos">Adjunto</label>
				</div>
				<div class="">';
			
			$divAdjunto = '<div id="lblAdjuntoProducto" data-vacio="0" class="">'.$adjunto.'</div>';
			if($tipoAccion <> 'ver')
			{
				
				$cont.='
						<div class="input-group mb-3 px-2 py-2 rounded-pill bg-white shadow-sm">
							
							<input id="uploadProductoAdjunto" type="file" class="form-control border-0 upload">
							
							<label id="" for="uploadProductoAdjunto" class="font-weight-light text-muted upload-label">Elegir archivo</label>
							<div class="input-group-append">
								<label for="uploadProductoAdjunto" class="btn btn-light m-0 rounded-pill px-4"> <i class="fa fa-cloud-upload mr-2 text-muted"></i><small class="text-uppercase font-weight-bold text-muted formCatalogo">Elegir archivo</small></label>
							</div>
						</div>
						'.$divAdjunto.'
						<br />
						<div class="d-flex justify-content-center">
							<button type="button" type="button" class="btn btn-default btn-danger formProductos" id="btnEliminarAdjunto"><i class="fas fa-trash" aria-hidden="true"></i>  Quitar adjunto</button>
						</div>';
			}
			else
			{
				$cont.=$divAdjunto;
			}
	
	$cont.='						
				</div>
			</div>';
			
	if($tipoAccion <> 'ver')
	{
		$cont.='<br /><br />	
				<div class="col-md-12">
					<hr class="solid">
					<div class="d-flex justify-content-center">
						<button type="button" type="button" class="btn btn-default" id="btnGuardarProducto" data-id="" data-accion="" style="background-color: #1cc88a; color: white;"><i class="far fa-save" aria-hidden="true"></i>  Guardar producto</button>
					</div
				</div>
				';
	}
	
	return $cont;
}

?>