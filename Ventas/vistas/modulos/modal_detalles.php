<div class="modal fade" id="modal-detalles-oportunidad" tabindex="-1" role="dialog" aria-labelledby="modal-detalles-oportunidad-label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-detalles-oportunidad-label">
                    <i class="fa fa-info-circle mr-2"></i>Detalles de la Oportunidad
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="detalles-oportunidad-contenido">
                    <!-- El contenido se cargará dinámicamente aquí -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-2"></i>Cerrar
                </button>
                <button type="button" class="btn btn-info" id="btn-ir-calendario" disabled>
                    <i class="fa fa-calendar mr-2"></i>Calendario
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Estilos adicionales para mejorar la presentación del modal -->
<style>
#detalles-oportunidad-contenido .row { margin-bottom: 20px; }

#detalles-oportunidad-contenido h6 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 8px;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

#detalles-oportunidad-contenido p {
    color: #495057;
    font-size: 16px;
    margin-bottom: 0;
    padding: 8px 12px;
    background: white;
    border-radius: 8px;
    border-left: 4px solid #007bff;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

#detalles-oportunidad-contenido .btn { margin: 5px; border-radius: 20px; font-weight: 600; padding: 10px 20px; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
#detalles-oportunidad-contenido .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2); }

/* Estilos específicos para botones de estado */
.btn-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; }
.btn-warning { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); border: none; color: #212529 !important; }
.btn-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); border: none; }
.btn-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border: none; }

/* Iconos para mejor visualización (clases en lugar de nth-child para más robustez) */
#detalles-oportunidad-contenido h6::before {
    margin-right: 8px;
    font-family: 'FontAwesome';
    font-weight: normal;
}

#detalles-oportunidad-contenido h6.title::before { content: '\f02b'; }       /* Título */
#detalles-oportunidad-contenido h6.client::before { content: '\f007'; }      /* Cliente */
#detalles-oportunidad-contenido h6.description::before { content: '\f15c'; } /* Descripción */
#detalles-oportunidad-contenido h6.telefono::before { content: '\f095'; }    /* Teléfono (phone) */
#detalles-oportunidad-contenido h6.empresa::before { content: '\f1ad'; }     /* Empresa (building) */
#detalles-oportunidad-contenido h6.fecha::before { content: '\f073'; }       /* Fecha */
/* Aumentar tamaño del título del modal (no confundir con los h6 internos) */
#modal-detalles-oportunidad-label {
    font-size: 22px;
    font-weight: 700;
}

</style>

<!-- El JS para título dinámico se carga desde la plantilla principal (plantilla.php) -->
<!-- Uso: agregar `data-column="seguimiento"` al trigger o setear `data-column-name` en `#detalles-oportunidad-contenido` -->
