<!-- Partial: Modal Info Cliente (solo lectura) -->
<div id="modalInfoCliente" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background:#3c8dbc; color:white;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Información del Prospecto / Oportunidad</h4>
      </div>
      <div class="modal-body">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#tabProspecto" aria-controls="tabProspecto" role="tab" data-toggle="tab">Prospecto</a></li>
          <li role="presentation"><a href="#tabOportunidad" aria-controls="tabOportunidad" role="tab" data-toggle="tab">Oportunidad</a></li>
        </ul>

        <div class="tab-content" style="margin-top:10px;">
          <div role="tabpanel" class="tab-pane active" id="tabProspecto">
            <div class="row">
              <div class="col-md-6"><b>Nombre:</b> <span id="infoNombre"></span></div>
              <div class="col-md-6"><b>Tipo:</b> <span id="infoTipo"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Documento:</b> <span id="infoDocumento"></span></div>
              <div class="col-md-6"><b>Teléfono:</b> <span id="infoTelefono"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Observación:</b> <span id="infoCorreo"></span></div>
              <div class="col-md-6"><b>Motivo:</b> <span id="infoMotivo"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Ciudad:</b> <span id="infoCiudad"></span></div>
              <div class="col-md-6"><b>Migración:</b> <span id="infoMigracion"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Referencia:</b> <span id="infoReferencia"></span></div>
              <div class="col-md-6"><b>Empresa:</b> <span id="infoEmpresa"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha Contacto:</b> <span id="infoFechaContacto"></span></div>
              <div class="col-md-6"><b>Fecha Creación:</b> <span id="infoFechaCreacion"></span></div>
            </div>
          </div>

          <div role="tabpanel" class="tab-pane" id="tabOportunidad">
            <div class="row">
              <div class="col-md-12"><h4 id="infoOportTitulo"></h4></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-12"><b>Descripción:</b> <div id="infoOportDescripcion"></div></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-4"><b>Valor Estimado:</b> <span id="infoOportValor"></span></div>
              <div class="col-md-4"><b>Probabilidad:</b> <span id="infoOportProbabilidad"></span></div>
              <div class="col-md-4"><b>Estado:</b> <span id="infoOportEstado"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha Cierre Estimada:</b> <span id="infoOportFechaCierre"></span></div>
              <div class="col-md-6"><b>Actividad:</b> <span id="infoOportActividad"></span></div>
            </div>
            <div class="row" style="margin-top:8px;">
              <div class="col-md-6"><b>Fecha Actividad:</b> <span id="infoOportFechaActividad"></span></div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
