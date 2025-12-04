<!-- Partial: Modal Info Cliente (solo lectura) -->
<style>
  /* Estilos para modal info cliente */
  #modalInfoCliente .modal-content {
    border-radius: 8px;
    overflow: hidden;
  }
  #modalInfoCliente .modal-header {
    background: linear-gradient(135deg, #3c8dbc 0%, #2c6d9c 100%);
    border-bottom: none;
    padding: 15px 20px;
  }
  #modalInfoCliente .modal-title {
    font-weight: 600;
    font-size: 18px;
  }
  #modalInfoCliente .nav-tabs {
    border-bottom: 2px solid #e0e0e0;
  }
  #modalInfoCliente .nav-tabs > li > a {
    border: none;
    color: #666;
    font-weight: 500;
    padding: 10px 20px;
    margin-right: 5px;
    border-radius: 4px 4px 0 0;
    transition: all 0.2s;
  }
  #modalInfoCliente .nav-tabs > li > a:hover {
    background: #f5f5f5;
    color: #333;
  }
  #modalInfoCliente .nav-tabs > li.active > a,
  #modalInfoCliente .nav-tabs > li.active > a:focus,
  #modalInfoCliente .nav-tabs > li.active > a:hover {
    background: #3c8dbc;
    color: white;
    border: none;
  }
  #modalInfoCliente .info-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    border-left: 4px solid #3c8dbc;
  }
  #modalInfoCliente .info-card.orange-border {
    border-left-color: #f39c12;
  }
  #modalInfoCliente .info-card.green-border {
    border-left-color: #00a65a;
  }
  #modalInfoCliente .info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e9ecef;
  }
  #modalInfoCliente .info-item:last-child {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
  }
  #modalInfoCliente .info-label {
    font-weight: 600;
    color: #3c8dbc;
    min-width: 130px;
    font-size: 13px;
  }
  #modalInfoCliente .info-label i {
    margin-right: 8px;
    width: 16px;
    text-align: center;
  }
  #modalInfoCliente .info-value {
    color: #333;
    font-size: 14px;
    flex: 1;
  }
  #modalInfoCliente .info-section-title {
    font-size: 14px;
    font-weight: 600;
    color: #3c8dbc;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 2px solid #3c8dbc;
    display: flex;
    align-items: center;
  }
  #modalInfoCliente .info-section-title.orange {
    color: #f39c12;
    border-bottom-color: #f39c12;
  }
  #modalInfoCliente .info-section-title.green {
    color: #00a65a;
    border-bottom-color: #00a65a;
  }
  #modalInfoCliente .info-section-title i {
    margin-right: 8px;
  }
  #modalInfoCliente .fecha-item {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 6px;
  }
  #modalInfoCliente .oport-title {
    font-size: 18px;
    font-weight: 600;
    color: #333;
    margin-bottom: 15px;
    padding: 10px 15px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 6px;
    border-left: 4px solid #00a65a;
  }
  #modalInfoCliente .oport-empty {
    text-align: center;
    padding: 30px;
    color: #999;
    font-style: italic;
  }
  #modalInfoCliente .oport-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 15px;
  }
  #modalInfoCliente .oport-stat {
    flex: 1;
    text-align: center;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
  }
  #modalInfoCliente .oport-stat-value {
    font-size: 18px;
    font-weight: 700;
    color: #3c8dbc;
  }
  #modalInfoCliente .oport-stat-label {
    font-size: 11px;
    color: #666;
    text-transform: uppercase;
    margin-top: 4px;
  }
  
  /* ========== TEMA OSCURO ========== */
  [data-theme="dark"] #modalInfoCliente .modal-content,
  [data-theme="dark"] #modalInfoCliente .modal-body {
    background: #192734 !important;
  }
  [data-theme="dark"] #modalInfoCliente .nav-tabs {
    border-bottom-color: #38444d;
  }
  [data-theme="dark"] #modalInfoCliente .nav-tabs > li > a {
    color: #8b98a5;
  }
  [data-theme="dark"] #modalInfoCliente .nav-tabs > li > a:hover {
    background: #273340;
    color: #e7e9ea;
  }
  [data-theme="dark"] #modalInfoCliente .info-card {
    background: #273340 !important;
  }
  [data-theme="dark"] #modalInfoCliente .info-item {
    border-bottom-color: #38444d;
  }
  [data-theme="dark"] #modalInfoCliente .info-label {
    color: #1d9bf0 !important;
  }
  [data-theme="dark"] #modalInfoCliente .info-value {
    color: #e7e9ea !important;
  }
  [data-theme="dark"] #modalInfoCliente .info-section-title {
    color: #1d9bf0 !important;
    border-bottom-color: #1d9bf0 !important;
  }
  [data-theme="dark"] #modalInfoCliente .info-section-title.orange {
    color: #f39c12 !important;
    border-bottom-color: #f39c12 !important;
  }
  [data-theme="dark"] #modalInfoCliente .info-section-title.green {
    color: #00a65a !important;
    border-bottom-color: #00a65a !important;
  }
  [data-theme="dark"] #modalInfoCliente .fecha-item {
    background: #273340 !important;
  }
  [data-theme="dark"] #modalInfoCliente .oport-title {
    background: linear-gradient(135deg, #273340 0%, #192734 100%) !important;
    color: #e7e9ea !important;
  }
  [data-theme="dark"] #modalInfoCliente .oport-stat {
    background: #273340 !important;
    border-color: #38444d !important;
  }
  [data-theme="dark"] #modalInfoCliente .oport-stat-value {
    color: #1d9bf0;
  }
  [data-theme="dark"] #modalInfoCliente .oport-stat-label {
    color: #8b98a5;
  }
  [data-theme="dark"] #modalInfoCliente .oport-empty {
    color: #8b98a5;
  }
  [data-theme="dark"] #modalInfoCliente #infoOportDescripcion {
    color: #e7e9ea !important;
  }
  [data-theme="dark"] #modalInfoCliente .modal-footer {
    background: #192734 !important;
    border-top-color: #38444d;
  }
  [data-theme="dark"] #modalInfoCliente .modal-footer .btn-default {
    background: #273340;
    color: #e7e9ea;
    border-color: #38444d;
  }
  [data-theme="dark"] #modalInfoCliente .modal-footer .btn-default:hover {
    background: #38444d;
    color: #fff;
  }
</style>

<div id="modalInfoCliente" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background: linear-gradient(135deg, #3c8dbc 0%, #2c6d9c 100%); color:white;">
        <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:0.8;">&times;</button>
        <h4 class="modal-title"><i class="fa fa-info-circle"></i> Información del Prospecto / Oportunidad</h4>
      </div>
      <div class="modal-body" style="padding: 20px;">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tabProspecto" aria-controls="tabProspecto" role="tab" data-toggle="tab">
              <i class="fa fa-user"></i> Prospecto
            </a>
          </li>
          <li role="presentation">
            <a href="#tabOportunidad" aria-controls="tabOportunidad" role="tab" data-toggle="tab">
              <i class="fa fa-briefcase"></i> Oportunidad
            </a>
          </li>
        </ul>

        <div class="tab-content" style="margin-top:15px;">
          <!-- Tab Prospecto -->
          <div role="tabpanel" class="tab-pane active" id="tabProspecto">
            <div class="row">
              <div class="col-md-6">
                <div class="info-card">
                  <div class="info-section-title"><i class="fa fa-id-card"></i> Datos Personales</div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-user"></i> Nombre:</span>
                    <span class="info-value" id="infoNombre">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-tag"></i> Tipo:</span>
                    <span class="info-value" id="infoTipo">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-id-badge"></i> Documento:</span>
                    <span class="info-value" id="infoDocumento">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-phone"></i> Teléfono:</span>
                    <span class="info-value" id="infoTelefono">-</span>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-card">
                  <div class="info-section-title"><i class="fa fa-building"></i> Información Comercial</div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-briefcase"></i> Empresa:</span>
                    <span class="info-value" id="infoEmpresa">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-map-marker"></i> Ciudad:</span>
                    <span class="info-value" id="infoCiudad">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-exchange"></i> Migración:</span>
                    <span class="info-value" id="infoMigracion">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-link"></i> Referencia:</span>
                    <span class="info-value" id="infoReferencia">-</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="info-card orange-border">
                  <div class="info-section-title orange">
                    <i class="fa fa-sticky-note"></i> Observaciones y Motivo
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-comment"></i> Observación:</span>
                    <span class="info-value" id="infoCorreo">-</span>
                  </div>
                  <div class="info-item">
                    <span class="info-label"><i class="fa fa-question-circle"></i> Motivo:</span>
                    <span class="info-value" id="infoMotivo">-</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="info-item fecha-item">
                  <span class="info-label"><i class="fa fa-calendar-check-o"></i> Fecha Contacto:</span>
                  <span class="info-value" id="infoFechaContacto">-</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-item fecha-item">
                  <span class="info-label"><i class="fa fa-calendar-plus-o"></i> Fecha Creación:</span>
                  <span class="info-value" id="infoFechaCreacion">-</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Tab Oportunidad -->
          <div role="tabpanel" class="tab-pane" id="tabOportunidad">
            <div id="oportunidadContent">
              <div class="oport-title" id="infoOportTitulo">Sin oportunidades</div>
              
              <div class="oport-stats">
                <div class="oport-stat">
                  <div class="oport-stat-value" id="infoOportValor">-</div>
                  <div class="oport-stat-label">Valor Estimado</div>
                </div>
                <div class="oport-stat">
                  <div class="oport-stat-value" id="infoOportProbabilidad">-</div>
                  <div class="oport-stat-label">Probabilidad</div>
                </div>
                <div class="oport-stat">
                  <div class="oport-stat-value" id="infoOportEstado">-</div>
                  <div class="oport-stat-label">Estado</div>
                </div>
              </div>

              <div class="info-card green-border">
                <div class="info-section-title green">
                  <i class="fa fa-file-text"></i> Descripción
                </div>
                <div id="infoOportDescripcion" class="info-value">-</div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="info-card">
                    <div class="info-item">
                      <span class="info-label"><i class="fa fa-calendar"></i> Fecha Cierre:</span>
                      <span class="info-value" id="infoOportFechaCierre">-</span>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-card">
                    <div class="info-item">
                      <span class="info-label"><i class="fa fa-tasks"></i> Actividad:</span>
                      <span class="info-value" id="infoOportActividad">-</span>
                    </div>
                    <div class="info-item">
                      <span class="info-label"><i class="fa fa-clock-o"></i> Fecha Act.:</span>
                      <span class="info-value" id="infoOportFechaActividad">-</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">
          <i class="fa fa-times"></i> Cerrar
        </button>
      </div>
    </div>
  </div>
</div>
