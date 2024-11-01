function js_llamadaAjax(archivoRuta, archivoAccion, formData, fnRetorno)
{
	try
    {
		var rand = Math.floor((Math.random() * 100000) + 1);
		
		//MANDO LLAMAR EL COMPONENTE MODAL DE ESPERA
		js_generaModal('divModalEspera_'+rand,'Espere..','','espera');
		
		if(formData==null)
        {
            formData = new FormData();
        }
		
		formData.append('accion_ajax',archivoAccion);
        formData.append('funcionRetorno_sistema',fnRetorno);
		formData.append('modalEspera_sistema','divModalEspera_'+rand);
		
		$.ajax({
            url : archivoRuta,
            type: 'POST',
            contentType: false,
            processData: false,
            data: formData,
            success : js_llamadaAjax_respuesta,
            error : js_errorAjax
        });
	}
	catch(e)
	{
		js_generaModal('divError','Error','Error al realizar la llamada: '+e.text,'error');
	}
}


function js_llamadaAjax_respuesta(data)
{
    try
    {	
        var respuesta = jQuery.parseJSON(data);
		
		//CERRAMOS EL MODAL DE ESPERA GENERADO PREVIAMENTE
		setTimeout(function(){
			js_cerrarModal(respuesta.modalEspera_sistema);
        }, 300);
		
		//DETERMINO EL TIPO DE FUNCION, SI ES STRING O FUNCION
		if (jQuery.isFunction(respuesta.funcionRetorno_sistema))
		{
			fn = respuesta.funcionRetorno_sistema;
		}
		else
		{
			fn = window[respuesta.funcionRetorno_sistema];
		}
		
		if (jQuery.isFunction(fn))
		{
			//Si la recupero o pudo asignar 
			var callbacks = $.Callbacks();
			callbacks.add(fn);
			callbacks.fire(data);
		}
        
    }
    catch(e)
    {
        js_generaModal('divError','Error','Error al recibir la respuesta: '+e,'error');
    }
    
     
}

function js_inicializaDataTable(idTabla,muestraRegistros,muestraFiltro,paginacion,scroll)
{
	var scrollY;
	var scrollCollapse;
	
	if(scroll)
	{
		scrollY = '50vh';
		scrollCollapse = true;
	}
	else
	{
		scrollY = '';
		scrollCollapse = false;
	}
	
	$('#'+idTabla).DataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
        },
		"bLengthChange": muestraRegistros,
		"bFilter": muestraFiltro,
		"bPaginate": paginacion,
		"bInfo" : false,
		"ordering": false,
		"scrollY": scrollY,
        "scrollCollapse": scrollCollapse,
		
	});
}

function js_cambiaViewstack(divOrigen,divDestino)
{
	$("#"+divOrigen).hide();
	
    $("#"+divDestino).show();
}

function js_errorAjax()
{
	js_generaModal('divError','Error','Error en proceso del sistema. Favor de ponerse en contacto con el administrador','alerta');
}