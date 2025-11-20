//**===========================================
// CARGAR LA TABLA DINAMICA DE VENTAS PRODUCTOS
// ============================================ */
/* $.ajax({
    url: "ajax/datatable-ventas.ajax.php",
    success: function(respuesta) {
        console.log("respuesta",respuesta);
    }
}) */
//DATATABLE
$(".tablaVentas").DataTable({

    "ajax": "ajax/datatable-ventas.ajax.php",
    "deferRender": true,
    "retrieve": true,
    "processing": true,
    "language": {

		"sProcessing":     "Procesando...",
		"sLengthMenu":     "Mostrar _MENU_ registros",
		"sZeroRecords":    "No se encontraron resultados",
		"sEmptyTable":     "Ningún dato disponible en esta tabla",
		"sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
		"sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
		"sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
		"sInfoPostFix":    "",
		"sSearch":         "Buscar:",
		"sUrl":            "",
		"sInfoThousands":  ",",
		"sLoadingRecords": "Cargando...",
		"oPaginate": {
		"sFirst":    "Primero",
		"sLast":     "Último",
		"sNext":     "Siguiente",
		"sPrevious": "Anterior"
		},
		"oAria": {
			"sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
			"sSortDescending": ": Activar para ordenar la columna de manera descendente"
		}
    }
});
//**===========================================
// CAPTURAMOS EL ID DEL PRODUCTO
//============================================ */
$(".tablaVentas tbody").on("click","button.btnAgregarProductos",function(){

	var idProducto = $(this).attr("idProducto");//CAPTURAMOS EL ID DEL PRODUCTO DE DATATABLE-VENTAS.PHP
	//console.log("idProducto",idProducto);
	$(this).removeClass("btn-primary btnAgregarProductos");
	$(this).addClass("btn-default");
	var datos = new FormData();
	datos.append("idProducto", idProducto);

	$.ajax({
		url: "ajax/productos.ajax.php",
		method: "POST",
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: "json",
		success: function(respuesta) {
			//console.log(respuesta);//MUESTRA LO QUE NOS TRAE EL ID DEL PRODUCTO SELECCIONADO
			var descripcion = respuesta["descripcion"];
			var stock = respuesta["stock"];
			var precio = respuesta["precio_venta"];
			//VALIDAR CUANDO EL STOCK SEA CERO
			if(stock ==0){
				swal.fire({
                        icon: "success",
                        title: "¡NO HAY STOCK DISPONIBLES!",
                        showConfirmButton: true,
                        confirmButtonText: "Cerrar",
			});
			$("button[idProducto='"+idProducto+"']").addClass("btn-primary");
			return;
	    }
		$(".nuevoProducto").append(

			'<div class="row" style="padding: 5px 15px"'+
			'<!-- Descripción del producto-->'+
                    '<div class="col-xs-6" style="padding-right:0px">'+

                      '<div class="input-group">'+

                        '<span class="input-group-addon"><button type="button" class="btn btn-danger btn-xs"><i class="fa fa-times"></i></button></span>'+
                        '<input type="text" class="form-control" id="agregarProducto" name="agregarProducto" value="'+descripcion+'" requerid readonly>'+

                      '</div>'+

                    '</div>'+
                    '<!--CANTIDAD del producto -->'+
                    '<div class="col-xs-3">'+

                      '<input type="number" class="form-control" id="nuevaCantidadProducto" name="nuevaCantidadProducto" min="1" value="1" stock="'+stock+'" requerid>'+

                    '</div>'+
                      '<!--PRECIO del producto -->'+
                      '<div class="col-xs-3" style="padding-left:px">'+

                        '<div class="input-group">'+
                          '<span class="input-group-addon"><i class="ion ion-social-usd"></i></span>'+
                          '<input type="number" min="1" class="form-control" id="nuevoPrecioProducto" precioReal="'+precio+'" name="nuevoPrecioProducto" value="'+precio+'" readonly requerid>'+
                          
                        '</div>'+
                      '</div>'+
					  '</div>')
    }
		},
		error: function(xhr, status, error) {
			// Productos endpoint removed — show message and restore button
			swal.fire({
				icon: "warning",
				title: "Funcionalidad de productos deshabilitada",
				text: "El módulo de productos fue eliminado. No es posible agregar productos.",
				showConfirmButton: true,
				confirmButtonText: "Cerrar",
			});
			$("button[idProducto='"+idProducto+"']").removeClass("btn-default").addClass("btn-primary btnAgregarProductos");
		}
  });
});


