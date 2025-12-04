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
                        <label for="filtro-periodo"><i class="fa fa-calendar mr-2"></i>Periodo</label>
                        <select class="form-control" id="filtro-periodo" name="periodo">
                            <option value="">Seleccionar periodo</option>
                            <option value="por_mes">Por mes</option>
                            <option value="entre_meses">Entre meses</option>
                            <option value="por_fecha">Por fecha</option>
                            <option value="entre_fechas">Entre fechas</option>
                        </select>
                    </div>

                    <!-- Por mes: Un selector de mes/año -->
                    <div id="filtro-por-mes" class="filtro-periodo-campos" style="display: none;">
                        <div class="form-group">
                            <label for="mes-unico"><i class="fa fa-calendar-o mr-2"></i>Mes</label>
                            <input type="month" class="form-control" id="mes-unico" name="mesUnico">
                        </div>
                    </div>

                    <!-- Entre meses: Dos selectores de mes/año -->
                    <div id="filtro-entre-meses" class="filtro-periodo-campos" style="display: none;">
                        <div class="form-group">
                            <label for="mes-desde"><i class="fa fa-calendar-check-o mr-2"></i>Mes desde</label>
                            <input type="month" class="form-control" id="mes-desde" name="mesDesde">
                        </div>
                        <div class="form-group">
                            <label for="mes-hasta"><i class="fa fa-calendar-times-o mr-2"></i>Mes hasta</label>
                            <input type="month" class="form-control" id="mes-hasta" name="mesHasta">
                        </div>
                    </div>

                    <!-- Por fecha: Un campo de fecha única -->
                    <div id="filtro-por-fecha" class="filtro-periodo-campos" style="display: none;">
                        <div class="form-group">
                            <label for="fecha-unica"><i class="fa fa-calendar-o mr-2"></i>Fecha</label>
                            <input type="date" class="form-control" id="fecha-unica" name="fechaUnica">
                        </div>
                    </div>

                    <!-- Entre fechas: Dos campos de fecha -->
                    <div id="filtro-entre-fechas" class="filtro-periodo-campos" style="display: none;">
                        <div class="form-group">
                            <label for="fecha-contacto-desde"><i class="fa fa-calendar-check-o mr-2"></i>Fecha desde</label>
                            <input type="date" class="form-control" id="fecha-contacto-desde" name="fechaContactoDesde">
                        </div>
                        <div class="form-group">
                            <label for="fecha-contacto-hasta"><i class="fa fa-calendar-times-o mr-2"></i>Fecha hasta</label>
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
.filtro-periodo-campos {
    border-left: 3px solid #007bff;
    padding-left: 15px;
    margin-top: 10px;
    margin-bottom: 10px;
}
</style>

<script>
$(document).ready(function() {
    // Mostrar/ocultar campos según el tipo de periodo seleccionado
    $('#filtro-periodo').change(function() {
        var valor = $(this).val();
        
        // Ocultar todos los campos de periodo
        $('.filtro-periodo-campos').slideUp();
        
        // Mostrar el campo correspondiente
        switch(valor) {
            case 'por_mes':
                $('#filtro-por-mes').slideDown();
                break;
            case 'entre_meses':
                $('#filtro-entre-meses').slideDown();
                break;
            case 'por_fecha':
                $('#filtro-por-fecha').slideDown();
                break;
            case 'entre_fechas':
                $('#filtro-entre-fechas').slideDown();
                break;
        }
    });

    // Validar que mes hasta sea mayor o igual a mes desde
    $('#mes-desde, #mes-hasta').change(function() {
        var mesDesde = $('#mes-desde').val();
        var mesHasta = $('#mes-hasta').val();
        
        if (mesDesde && mesHasta && mesHasta < mesDesde) {
            alert('El mes hasta debe ser mayor o igual al mes desde');
            $('#mes-hasta').val('');
        }
    });

    // Validar que fecha hasta sea mayor o igual a fecha desde
    $('#fecha-contacto-desde, #fecha-contacto-hasta').change(function() {
        var fechaDesde = $('#fecha-contacto-desde').val();
        var fechaHasta = $('#fecha-contacto-hasta').val();
        
        if (fechaDesde && fechaHasta && fechaHasta < fechaDesde) {
            alert('La fecha hasta debe ser mayor o igual a la fecha desde');
            $('#fecha-contacto-hasta').val('');
        }
    });

    // Limpiar filtros
    $('#btn-limpiar-filtros-clientes').click(function() {
        $('#form-filtros-clientes')[0].reset();
        $('.filtro-periodo-campos').slideUp();
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
            mesUnico: $('#mes-unico').val(),
            mesDesde: $('#mes-desde').val(),
            mesHasta: $('#mes-hasta').val(),
            fechaUnica: $('#fecha-unica').val(),
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
