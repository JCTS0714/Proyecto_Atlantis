<div class="modal fade" id="modal-filtros-clientes" tabindex="-1" role="dialog" aria-labelledby="modal-filtros-clientes-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-filtros-clientes-label">
                    <i class="fa fa-filter mr-2"></i>Filtros de Clientes
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-filtros-clientes">
                    <div class="form-group">
                        <label for="filtro-nombre"><i class="fa fa-user mr-2"></i>Nombre</label>
                        <input type="text" class="form-control" id="filtro-nombre" name="nombre" placeholder="Buscar por nombre">
                    </div>

                    <div class="form-group">
                        <label for="filtro-tipo"><i class="fa fa-id-card mr-2"></i>Tipo</label>
                        <select class="form-control" id="filtro-tipo" name="tipo">
                            <option value="">Todos los tipos</option>
                            <option value="DNI">DNI</option>
                            <option value="RUC">RUC</option>
                            <option value="otros">Otros</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filtro-documento"><i class="fa fa-address-card mr-2"></i>Documento</label>
                        <input type="text" class="form-control" id="filtro-documento" name="documento" placeholder="Buscar por documento">
                    </div>

                    <div class="form-group">
                        <label for="filtro-telefono"><i class="fa fa-phone mr-2"></i>Teléfono</label>
                        <input type="text" class="form-control" id="filtro-telefono" name="telefono" placeholder="Buscar por teléfono">
                    </div>

                    <div class="form-group">
                        <label for="filtro-correo"><i class="fa fa-envelope mr-2"></i>Observacion</label>
                        <input type="text" class="form-control" id="filtro-correo" name="correo" placeholder="Buscar por Observacion">
                    </div>

                    <div class="form-group">
                        <label for="filtro-ciudad"><i class="fa fa-map-marker mr-2"></i>Ciudad</label>
                        <input type="text" class="form-control" id="filtro-ciudad" name="ciudad" placeholder="Buscar por ciudad">
                    </div>

                    <div class="form-group">
                        <label for="filtro-migracion"><i class="fa fa-globe mr-2"></i>Migración</label>
                        <input type="text" class="form-control" id="filtro-migracion" name="migracion" placeholder="Buscar por migración">
                    </div>

                    <div class="form-group">
                        <label for="filtro-referencia"><i class="fa fa-link mr-2"></i>Referencia</label>
                        <input type="text" class="form-control" id="filtro-referencia" name="referencia" placeholder="Buscar por referencia">
                    </div>

                    <div class="form-group">
                        <label for="filtro-empresa"><i class="fa fa-building mr-2"></i>Empresa</label>
                        <input type="text" class="form-control" id="filtro-empresa" name="empresa" placeholder="Buscar por empresa">
                    </div>

                    <div class="form-group">
                        <label for="filtro-periodo"><i class="fa fa-calendar mr-2"></i>Periodo de Contacto</label>
                        <select class="form-control" id="filtro-periodo" name="periodo">
                            <option value="">Todos los periodos</option>
                            <option value="diario">Diario</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensual">Mensual</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>

                    <div id="filtro-fechas-personalizado" style="display: none;">
                        <div class="form-group">
                            <label for="fecha-contacto-desde"><i class="fa fa-calendar-check-o mr-2"></i>Fecha Contacto Desde</label>
                            <input type="date" class="form-control" id="fecha-contacto-desde" name="fechaContactoDesde">
                        </div>
                        <div class="form-group">
                            <label for="fecha-contacto-hasta"><i class="fa fa-calendar-times-o mr-2"></i>Fecha Contacto Hasta</label>
                            <input type="date" class="form-control" id="fecha-contacto-hasta" name="fechaContactoHasta">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filtro-estado"><i class="fa fa-tag mr-2"></i>Estado</label>
                        <select class="form-control" id="filtro-estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="2">Cliente</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-limpiar-filtros-clientes">
                    <i class="fa fa-eraser mr-2"></i>Limpiar Filtros
                </button>
                <button type="button" class="btn btn-primary" id="btn-aplicar-filtros-clientes">
                    <i class="fa fa-check mr-2"></i>Aplicar Filtros
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#filtro-fechas-personalizado {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-top: 10px;
}
</style>

<script>
$(document).ready(function() {
    // Mostrar/ocultar campos de fecha cuando se selecciona "Personalizado"
    $('#filtro-periodo').change(function() {
        if ($(this).val() === 'personalizado') {
            $('#filtro-fechas-personalizado').slideDown();
        } else {
            $('#filtro-fechas-personalizado').slideUp();
        }
    });

    // Validar que las fechas estén dentro del mismo mes solo si el periodo es personalizado
    $('#fecha-contacto-desde, #fecha-contacto-hasta').change(function() {
        if ($('#filtro-periodo').val() === 'personalizado') {
            var fechaDesde = new Date($('#fecha-contacto-desde').val());
            var fechaHasta = new Date($('#fecha-contacto-hasta').val());

            if ($('#fecha-contacto-desde').val() && $('#fecha-contacto-hasta').val()) {
                var mesDesde = fechaDesde.getMonth();
                var mesHasta = fechaHasta.getMonth();
                var añoDesde = fechaDesde.getFullYear();
                var añoHasta = fechaHasta.getFullYear();

                // Comentado para permitir rangos en diferentes meses y años
                // if (mesDesde !== mesHasta || añoDesde !== añoHasta) {
                //     alert('Las fechas deben estar dentro del mismo mes y año');
                //     $(this).val('');
                // }
            }
        }
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros-clientes').click(function() {
        $('#form-filtros-clientes')[0].reset();
        $('#filtro-fechas-personalizado').slideUp();
        aplicarFiltrosClientes(); // Aplicar filtros vacíos para mostrar todo
    });

    // Aplicar filtros
    $('#btn-aplicar-filtros-clientes').click(function() {
        aplicarFiltrosClientes();
        $('#modal-filtros-clientes').modal('hide');
    });

    function aplicarFiltrosClientes() {
        var filtros = {
            nombre: $('#filtro-nombre').val(),
            tipo: $('#filtro-tipo').val(),
            documento: $('#filtro-documento').val(),
            telefono: $('#filtro-telefono').val(),
            correo: $('#filtro-correo').val(),
            ciudad: $('#filtro-ciudad').val(),
            migracion: $('#filtro-migracion').val(),
            referencia: $('#filtro-referencia').val(),
            empresa: $('#filtro-empresa').val(),
            periodo: $('#filtro-periodo').val(),
            fechaContactoDesde: $('#fecha-contacto-desde').val(),
            fechaContactoHasta: $('#fecha-contacto-hasta').val(),
            estado: $('#filtro-estado').val()
        };

        // Aquí se implementará la lógica de filtrado
        console.log('Aplicando filtros de clientes:', filtros);

        // Llamar a la función de filtrado (se implementará en clientes.js)
        if (typeof filtrarClientes === 'function') {
            filtrarClientes(filtros);
        }
    }
});
</script>
