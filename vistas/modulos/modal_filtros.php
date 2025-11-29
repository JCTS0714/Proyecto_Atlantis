<div class="modal fade" id="modal-filtros" tabindex="-1" role="dialog" aria-labelledby="modal-filtros-label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-filtros-label">
                    <i class="fa fa-filter mr-2"></i>Filtros del Tablero Kanban
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-filtros">
                    <div class="form-group">
                        <label for="filtro-estado"><i class="fa fa-tag mr-2"></i>Estado</label>
                        <select class="form-control" id="filtro-estado" name="filtroEstado">
                            <option value="">Todos los estados</option>
                            <option value="1">Seguimiento</option>
                            <option value="2">Calificado</option>
                            <option value="3">Propuesto</option>
                            <option value="4">Ganado</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="filtro-periodo"><i class="fa fa-calendar mr-2"></i>Periodo</label>
                        <select class="form-control" id="filtro-periodo" name="filtroPeriodo">
                            <option value="">Todos los periodos</option>
                            <option value="diario">Diario</option>
                            <option value="semanal">Semanal</option>
                            <option value="mensual">Mensual</option>
                            <option value="personalizado">Personalizado</option>
                        </select>
                    </div>
                    
                    <div id="filtro-fechas-personalizado" style="display: none;">
                        <div class="form-group">
                            <label for="fecha-inicio"><i class="fa fa-calendar-check-o mr-2"></i>Fecha Inicio</label>
                            <input type="date" class="form-control" id="fecha-inicio" name="fechaInicio">
                        </div>
                        <div class="form-group">
                            <label for="fecha-fin"><i class="fa fa-calendar-times-o mr-2"></i>Fecha Fin</label>
                            <input type="date" class="form-control" id="fecha-fin" name="fechaFin">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="btn-limpiar-filtros">
                    <i class="fa fa-eraser mr-2"></i>Limpiar Filtros
                </button>
                <button type="button" class="btn btn-primary" id="btn-aplicar-filtros">
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
    $('#fecha-inicio, #fecha-fin').change(function() {
        if ($('#filtro-periodo').val() === 'personalizado') {
            var fechaInicio = new Date($('#fecha-inicio').val());
            var fechaFin = new Date($('#fecha-fin').val());
            
            if ($('#fecha-inicio').val() && $('#fecha-fin').val()) {
                var mesInicio = fechaInicio.getMonth();
                var mesFin = fechaFin.getMonth();
                var añoInicio = fechaInicio.getFullYear();
                var añoFin = fechaFin.getFullYear();
                
                // Comentado para permitir rangos en diferentes meses y años
                // if (mesInicio !== mesFin || añoInicio !== añoFin) {
                //     alert('Las fechas deben estar dentro del mismo mes y año');
                //     $(this).val('');
                // }
            }
        }
    });
    
    // Limpiar filtros
    $('#btn-limpiar-filtros').click(function() {
        $('#form-filtros')[0].reset();
        $('#filtro-fechas-personalizado').slideUp();
        aplicarFiltros(); // Aplicar filtros vacíos para mostrar todo
    });
    
    // Aplicar filtros
    $('#btn-aplicar-filtros').click(function() {
        aplicarFiltros();
        $('#modal-filtros').modal('hide');
    });
    
    function aplicarFiltros() {
        var filtroEstado = $('#filtro-estado').val();
        var filtroPeriodo = $('#filtro-periodo').val();
        var fechaInicio = $('#fecha-inicio').val();
        var fechaFin = $('#fecha-fin').val();
        
        // Aquí se implementará la lógica de filtrado
        console.log('Aplicando filtros:', {
            estado: filtroEstado,
            periodo: filtroPeriodo,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin
        });
        
        // Llamar a la función de filtrado (se implementará en oportunidades.js)
        if (typeof filtrarOportunidades === 'function') {
            filtrarOportunidades(filtroEstado, filtroPeriodo, fechaInicio, fechaFin);
        }
    }
});
</script>
