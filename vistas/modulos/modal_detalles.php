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
/* Layout refinado para modal de detalles: espaciado, tipografía y controles */
#detalles-oportunidad-contenido { font-family: 'Helvetica Neue', Arial, sans-serif; color: #343a40; }

#detalles-oportunidad-contenido .row { margin-bottom: 12px; }

#detalles-oportunidad-contenido h6 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 6px;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.6px;
}

/* Presentación de los valores (p) y campos editables */
#detalles-oportunidad-contenido p,
#detalles-oportunidad-contenido .cliente-field {
    color: #495057;
    font-size: 15px;
    margin-bottom: 0;
    padding: 10px 12px;
    background: #ffffff;
    border-radius: 6px;
    border: 1px solid #e6e9ed;
    box-shadow: 0 1px 2px rgba(16, 24, 40, 0.03);
}

#detalles-oportunidad-contenido .cliente-field { padding: 8px 10px; }

#detalles-oportunidad-contenido .form-check { margin-top: 6px; }

#detalles-oportunidad-contenido .btn { margin: 6px 6px 0 0; border-radius: 8px; font-weight: 600; padding: 8px 16px; transition: all 0.18s ease; box-shadow: 0 2px 6px rgba(16,24,40,0.06); }
#detalles-oportunidad-contenido .btn:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(16,24,40,0.08); }

/* Tonos más sutiles para botones de estado */
.btn-success { background: linear-gradient(135deg, #2bb673 0%, #20c997 100%); border: none; }
.btn-warning { background: linear-gradient(135deg, #ffd659 0%, #ffb459 100%); border: none; color: #212529 !important; }
.btn-info { background: linear-gradient(135deg, #5bc0de 0%, #17a2b8 100%); border: none; }
.btn-danger { background: linear-gradient(135deg, #f76767 0%, #e55353 100%); border: none; }

/* Iconos para mejor visualización (clases en lugar de nth-child para más robustez) */
#detalles-oportunidad-contenido h6::before { margin-right: 8px; font-family: 'FontAwesome'; font-weight: normal; }

#detalles-oportunidad-contenido h6.title::before { content: '\f02b'; }       /* Título */
#detalles-oportunidad-contenido h6.client::before { content: '\f007'; }      /* Cliente */
#detalles-oportunidad-contenido h6.description::before { content: '\f15c'; } /* Descripción */
#detalles-oportunidad-contenido h6.telefono::before { content: '\f095'; }    /* Teléfono (phone) */
#detalles-oportunidad-contenido h6.empresa::before { content: '\f1ad'; }     /* Empresa (building) */
#detalles-oportunidad-contenido h6.fecha::before { content: '\f073'; }       /* Fecha */

/* Checkbox personalizado: color azul para el modal */
#detalles-oportunidad-contenido .form-check-input {
    accent-color: #007bff; /* navegadores modernos */
    width: 18px;
    height: 18px;
    margin-top: 0.15rem;
}

#detalles-oportunidad-contenido .form-check-input:focus {
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

#detalles-oportunidad-contenido .form-check-label {
    margin-left: 6px;
    font-weight: 600;
    color: #2c3e50;
}

/* Highlight temporal cuando un campo se habilita para edición */
@keyframes pulse-highlight {
    0% { transform: scale(1); box-shadow: 0 8px 24px rgba(0,123,255,0.18), 0 0 0 6px rgba(0,123,255,0.08); }
    50% { transform: scale(1.01); box-shadow: 0 18px 48px rgba(0,123,255,0.28), 0 0 0 10px rgba(0,123,255,0.14); }
    100% { transform: scale(1); box-shadow: 0 10px 30px rgba(0,123,255,0.18), 0 0 0 6px rgba(0,123,255,0.08); }
}

/* Estado persistente: fondo y borde permanecen mientras checkbox está marcado */
#detalles-oportunidad-contenido .cliente-field.cliente-field-active {
    border: 2px solid #0056d6; /* azul más intenso */
    background-color: #e6f0ff; /* fondo azul claro más marcado */
    box-shadow: 0 10px 28px rgba(0, 86, 214, 0.12);
    transition: box-shadow 0.28s ease, border-color 0.12s ease, background-color 0.12s ease;
}

/* Clase temporal: pulso breve al activarse, se quita automáticamente pero el estado persistente queda */
#detalles-oportunidad-contenido .cliente-field-pulse {
    animation: pulse-highlight 0.7s ease 1;
}

/* Hacer que el estilo de foco por defecto sea sutil para que el destaque temporal destaque claramente */
#detalles-oportunidad-contenido .cliente-field:focus {
    outline: none;
    border-color: #d1d5db; /* gris claro */
    box-shadow: 0 1px 2px rgba(16,24,40,0.03);
}
/* Aumentar tamaño del título del modal (no confundir con los h6 internos) */
#modal-detalles-oportunidad-label {
    font-size: 22px;
    font-weight: 700;
}

</style>

<!-- El JS para título dinámico se carga desde la plantilla principal (plantilla.php) -->
<!-- Uso: agregar `data-column="seguimiento"` al trigger o setear `data-column-name` en `#detalles-oportunidad-contenido` -->
