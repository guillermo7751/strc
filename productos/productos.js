var modulo = 'productos/productos_controlador.php';

$(document).ready(function(){

    js_productos_dameProductos();
    
    $('body').on('click', '#btnNuevoProducto', function() {
        $('#viewFormulario').text('Agregar Producto');
        
        js_cambiaViewstack('view_productos_inicio','view_productos_formulario');
        js_productos_cargaFormulario('nuevo','');
        
    });
    
    $('body').on('click', '#btnRegresarInicio', function() {
        js_cambiaViewstack('view_productos_formulario','view_productos_inicio');
        $('#div_productos_formulario').html('');
    });
    
    $('body').on('click', '#btnGuardarProducto', function(event) {
        event.preventDefault();
        js_productos_guardarProducto($(this).data('id'));
    });
    
    $('body').on('click', '.accionEliminar', function() {
        
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        js_generaModal('confirmEliminar','Eliminar Producto','¿Seguro que desea eliminar el producto "'+nombre+'"?','confirmacion','js_productos_eliminarProducto',id);
    });
    
    $('body').on('click', '.accionEditar', function() {
        $('#viewFormulario').text('Editar Producto');
        
        var id = $(this).data('id');
        js_cambiaViewstack('view_productos_inicio','view_productos_formulario');
        js_productos_cargaFormulario('editar',id);
    });
    
    $('body').on('click', '.accionVer', function() {
        $('#viewFormulario').text('Ver Producto');
        
        var id = $(this).data('id');
        js_cambiaViewstack('view_productos_inicio','view_productos_formulario');
        js_productos_cargaFormulario('ver',id);
    });
    
    $('body').on('change', '#uploadProductoImg', function() {
        js_productos_cargarImagen(this,'resultadoProducto','clsProductoImg');
    });
    
    $('body').on('click', '#btnEliminarImagen', function() {
        $('#resultadoProducto').data('vacio','1');
        js_quitarImagen('uploadProductoImg','resultadoProducto','clsProductoImg');
    });
    
    $('body').on('change', '#uploadProductoAdjunto', function() {
        
        var size = (this.files[0].size/1024/1024);
        
        if(size > 2)
        {
            js_generaModal('divError','Error','El archivo seleccionado excede el máximo de 2MB por archivo.','alerta');
            $('#lblAdjuntoProducto').html('SIN ADJUNTO');
            $('#uploadProductoAdjunto').val('');
            $('#lblAdjuntoProducto').data('vacio','1');
        }
        else
        {
            var archivo = $(this).val();
            archivo = archivo.replace(/.*[\/\\]/, '');
            $('#lblAdjuntoProducto').html(archivo);
            $('#lblAdjuntoProducto').data('vacio','0');
        }
    });
    
    $('body').on('click', '#btnEliminarAdjunto', function() {
        $('#lblAdjuntoProducto').html('SIN ADJUNTO');
        $('#uploadProductoAdjunto').val('');
        $('#lblAdjuntoProducto').data('vacio','1');
    });
});

function js_productos_dameProductos()
{
    try
    {
        js_llamadaAjax(modulo,'ajax_modulos_dameProductos',null,'js_modulos_dameProductosResultado');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_modulos_dameProductosResultado(data)
{
    try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'alerta');
        }
        else
        {
            $('#div_productos_inicio').html(respuesta.resultado);
            
            js_inicializaDataTable('tblProductos',true,true,true,false);
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_productos_cargaFormulario(tipoAccion,id)
{
    try
    {
        $('#div_productos_formulario').html('');
        
        var formData = new FormData();
            
        formData.append('tipoAccion',tipoAccion);
        formData.append('id',id);    
        
        js_llamadaAjax(modulo,'ajax_productos_cargaFormulario',formData,'js_productos_cargaFormularioResultado');
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}


function js_productos_cargaFormularioResultado(data)
{
    try
    {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            $('#div_productos_formulario').html(respuesta.resultado);
            
            if(respuesta.tipoAccion == 'ver')
            {
                js_deshabilitaFormulario();
            }
            else if(respuesta.tipoAccion == 'nuevo' || respuesta.tipoAccion == 'editar')
            {
                $('#btnGuardarProducto').data('id',respuesta.id);
                $('#btnGuardarProducto').data('accion',respuesta.tipoAccion);
            }
        
            if($('#resultadoProducto').prop('src')!= window.location.href)
            {
                $('.clsProductoImg').addClass('image-no-before');
            }
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_productos_guardarProducto(id)
{
    try
    {
        
        var mensajeError = '';
        
        var nombre = $('#txtNombreProducto').val();
        var desc = $('#txtDescProducto').val();
        var imagen = $('#uploadProductoImg')[0].files[0];
        var isImgVacio = $('#resultadoProducto').data('vacio');
        var isAdjVacio = $('#lblAdjuntoProducto').data('vacio');
        var adjunto = $('#uploadProductoAdjunto')[0].files[0];
        
        if(desc == '')
        {
            mensajeError = 'Favor de asignar una descripción al producto.';
        }
        
        if(nombre == '')
        {
            mensajeError = 'Favor de asignar un nombre al producto.';
        }
                
        if(mensajeError == '')
        {
            var formData = new FormData();
            formData.append('nombre',nombre);
            formData.append('desc',desc);
            formData.append('imagen',imagen);
            formData.append('isImgVacio',isImgVacio);
            formData.append('isAdjVacio',isAdjVacio);
            formData.append('adjunto',adjunto);
            formData.append('id',id);
            js_llamadaAjax(modulo,'ajax_productos_guardarProducto',formData,'js_productos_guardarProductoResultado');
        }
        else
        {
            js_generaModal('divError','Alerta',mensajeError,'alerta');
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_productos_cargarImagen(data,idImg,cls)
{
    
    var imgValida = js_cargarImagen(data,idImg,cls);
    
    if(imgValida)
    {
        $('#resultadoProducto').data('vacio','0');
    }
}

function js_productos_guardarProductoResultado(data)
{
     try
     {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_cambiaViewstack('view_productos_formulario','view_productos_inicio');
            js_productos_dameProductos();
            $('#div_productos_formulario').html('');
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_productos_eliminarProducto(id)
{
    try
    {
        var formData = new FormData();
        formData.append('id',id);
        js_llamadaAjax(modulo,'ajax_productos_eliminarProducto',formData,'js_productos_eliminarProductoResultado');
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    }
}

function js_productos_eliminarProductoResultado(data)
{
    try
     {
        var respuesta = jQuery.parseJSON(data);
        
        if(respuesta.Error)
        {
            js_generaModal('divError','Error','Error al ejecutar el proceso: '+respuesta.ErrorMensaje,'error');
        }
        else
        {
            js_productos_dameProductos();
        }
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al ejecutar el proceso: '+e,'error');
    } 
}

function js_deshabilitaFormulario()
{
    $('.formProductos').prop('disabled',true);
    $('#btnEliminarImagen').addClass('disabled');
}

