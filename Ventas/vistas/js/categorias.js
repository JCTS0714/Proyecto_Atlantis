/**para editar categoria */

$(document).on("click", ".btnEditarcategoria", function(){

 var idCategoria = $(this).attr("idCategoria");



 var datos = new FormData();

 datos.append("idCategoria", idCategoria);



 $.ajax({

  url: "ajax/categorias.ajax.php",

  method: "POST",

  data: datos,

  cache: false,

  contentType: false,

  processData: false,

  dataType: "json",

  success: function(respuesta){

   $("#editarCategoria").val(respuesta["categoria"]);

   $("#idCategoria").val(respuesta["id"]);

  }

 });

});
/**para elimimnar categoria */

$(document).on("click", ".btnEliminarCategoria", function(){

 var idCategoria = $(this).attr("idCategoria");



 Swal.fire({

  title: '¿Está seguro de eliminar la categoría?',

  icon: 'warning',

  showCancelButton: true,

  confirmButtonColor: '#3085d6',

  cancelButtonColor: '#d33',

  confirmButtonText: 'Sí, eliminar',

  cancelButtonText: 'Cancelar'

 }).then((result) => {

  if (result.isConfirmed) {

   window.location = "index.php?ruta=categorias&idCategoriaEliminar=" + idCategoria;

  }

 })

});

