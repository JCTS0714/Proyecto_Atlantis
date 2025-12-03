<?php
  // Listar contadores con comercios relacionados
  $contadores = ModeloContador::mdlMostrarContadoresConComercios();
?>
<div class="content-wrapper">
  <section class="content-header">
    <h1>Contadores</h1>
  </section>
  <section class="content">
    
    <!-- Búsqueda Avanzada -->
    <?php @include 'advanced_search.php'; ?>
    
    <div class="box">
      <div class="box-header">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAgregarContador">
          <i class="fa fa-plus"></i> Agregar Contador
        </button>
        
        <!-- Botón Mostrar/Ocultar Columnas -->
        <div class="column-toggle-container" style="margin-top:10px;">
          <button class="btn btn-default btn-toggle-columns" onclick="toggleColumnPanel(event)" title="Mostrar/Ocultar Columnas">
            <i class="fa fa-columns"></i> Mostrar/Ocultar Columnas
          </button>
          <div class="column-toggle-panel hidden">
            <h5>Mostrar/Ocultar Columnas</h5>
            <div class="column-toggle-list">
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-numero" checked>
                <label>N°</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-comercios" checked>
                <label>Comercio(s)</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-nombre" checked>
                <label>Nombre Contador</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-nombre-celular" checked>
                <label>Nombre en Celular</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-telefono" checked>
                <label>Teléfono</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-link" checked>
                <label>Link</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-usuario" checked>
                <label>Usuario</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-contrasena" checked>
                <label>Contraseña</label>
              </div>
              <div class="column-toggle-item">
                <input type="checkbox" class="column-toggle-checkbox" data-table="tablaContadores" data-column="col-servidor" checked>
                <label>Servidor</label>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive" id="tablaContadores">
          <thead>
            <tr>
              <th style="width: 40px;">#</th>
              <th style="width: 50px;" data-column="col-numero">N°</th>
              <th data-column="col-comercios">Comercio(s)</th>
              <th data-column="col-nombre">Nombre Contador</th>
              <th data-column="col-nombre-celular">Nombre en Celular</th>
              <th data-column="col-telefono">Teléfono</th>
              <th data-column="col-link">Link</th>
              <th data-column="col-usuario">Usuario</th>
              <th data-column="col-contrasena">Contraseña</th>
              <th data-column="col-servidor">Servidor</th>
              <th style="width: 100px;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($contadores as $key => $c): ?>
              <tr>
                <td><?php echo $key + 1; ?></td>
                <td data-column="col-numero"><?php echo htmlspecialchars($c['nro']); ?></td>
                <td data-column="col-comercios"><?php echo htmlspecialchars($c['comercios_lista'] ?: $c['comercio'] ?: '-'); ?></td>
                <td data-column="col-nombre"><?php echo htmlspecialchars($c['nom_contador']); ?></td>
                <td data-column="col-nombre-celular"><?php echo htmlspecialchars($c['titular_tlf']); ?></td>
                <td data-column="col-telefono"><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td data-column="col-link">
                  <?php if (!empty($c['link'])): 
                    $linkUrl = $c['link'];
                    if (!preg_match('/^https?:\/\//i', $linkUrl)) {
                      $linkUrl = 'https://' . $linkUrl;
                    }
                  ?>
                    <a href="<?php echo htmlspecialchars($linkUrl); ?>" target="_blank" class="btn btn-xs btn-info">
                      <i class="fa fa-external-link"></i> Ver
                    </a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td data-column="col-usuario"><?php echo htmlspecialchars($c['usuario']); ?></td>
                <td data-column="col-contrasena"><?php echo htmlspecialchars($c['contrasena']); ?></td>
                <td data-column="col-servidor"><?php echo htmlspecialchars($c['servidor'] ?? ''); ?></td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btn-sm btnEditarContador" data-id="<?php echo $c['id']; ?>" data-toggle="modal" data-target="#modalEditarContador">
                      <i class="fa fa-pencil"></i>
                    </button>
                    <a href="contadores?idContadorEliminar=<?php echo $c['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Está seguro de eliminar este contador?');">
                      <i class="fa fa-trash"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<!-- Modal Agregar -->
<div id="modalAgregarContador" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="formAgregarContador" autocomplete="off">
        <div class="modal-header" style="background: linear-gradient(135deg, #3c8dbc 0%, #2c6d9c 100%); color:white;">
          <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
          <h4 class="modal-title"><i class="fa fa-calculator"></i> Agregar Contador</h4>
        </div>
        <div class="modal-body">
          <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-6">
              <!-- N° generado automáticamente -->
              <div class="form-group">
                <label><i class="fa fa-hashtag"></i> N°</label>
                <input id="nuevoNro" class="form-control" name="nuevoNro" readonly style="background-color: #f5f5f5;">
              </div>
              
              <!-- Comercio(s) - Select2 múltiple -->
              <div class="form-group">
                <label><i class="fa fa-building"></i> Comercio(s) <small class="text-muted">(buscar por empresa)</small></label>
                <div id="comerciosContainer">
                  <div class="comercio-item" style="margin-bottom: 8px;">
                    <select class="form-control select2-comercio" name="nuevosComercios[]" style="width: calc(100% - 45px); display: inline-block;">
                    </select>
                    <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio" style="display: none;" title="Quitar">
                      <i class="fa fa-minus"></i>
                    </button>
                  </div>
                </div>
                <button type="button" class="btn btn-success btn-xs" id="btnAgregarComercio" style="margin-top: 5px;">
                  <i class="fa fa-plus"></i> Agregar otro comercio
                </button>
              </div>
              
              <!-- Nombre Contador -->
              <div class="form-group">
                <label><i class="fa fa-user"></i> Nombre Contador</label>
                <input class="form-control" name="nuevoNomContador" placeholder="Nombre completo del contador" autocomplete="off">
              </div>
              
              <!-- Nombre grabado en el celular -->
              <div class="form-group">
                <label><i class="fa fa-address-book"></i> Nombre grabado en el celular</label>
                <input class="form-control" name="nuevoTitularTlf" placeholder="Nombre como aparece en el celular" autocomplete="off">
              </div>
            </div>
            
            <!-- Columna derecha -->
            <div class="col-md-6">
              <!-- Teléfono -->
              <div class="form-group">
                <label><i class="fa fa-phone"></i> Teléfono</label>
                <input class="form-control" name="nuevoTelefono" placeholder="Número de teléfono" autocomplete="off">
              </div>
              
              <!-- Link -->
              <div class="form-group">
                <label><i class="fa fa-link"></i> Link</label>
                <input type="text" class="form-control" name="nuevoLink" placeholder="ejemplo.dominio.com" autocomplete="off" pattern="^(https?:\/\/)?[a-zA-Z0-9][a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/.*)?$|^$" title="Ingrese un dominio válido (ej: ejemplo.com o https://ejemplo.com)">
              </div>
              
              <!-- Usuario -->
              <div class="form-group">
                <label><i class="fa fa-user-circle"></i> Usuario</label>
                <input class="form-control" name="nuevoUsuario" placeholder="Usuario de acceso" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
              </div>
              
              <!-- Contraseña -->
              <div class="form-group">
                <label><i class="fa fa-lock"></i> Contraseña</label>
                <input class="form-control" name="nuevoContrasena" type="text" placeholder="Contraseña de acceso" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
              </div>
              
              <!-- Servidor -->
              <div class="form-group">
                <label><i class="fa fa-server"></i> Servidor</label>
                <select class="form-control" name="nuevoServidor">
                  <option value="">Seleccionar servidor...</option>
                  <option value="LORITO">LORITO</option>
                  <option value="ATLANTIS FAST">ATLANTIS FAST</option>
                  <option value="ATLANTIS POS">ATLANTIS POS</option>
                  <option value="ATLANTIS ONLINE">ATLANTIS ONLINE</option>
                  <option value="ATLANTIS VIP">ATLANTIS VIP</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Campo oculto para IDs de comercios -->
          <input type="hidden" name="comercioIds" id="comercioIds">
        </div>
        <div class="modal-footer" style="background-color: #f5f5f5;">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
          <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Guardar</button>
        </div>
        <?php
          $crear = new ControladorContador();
          $crear->ctrCrearContador();
        ?>
      </form>
    </div>
  </div>
</div>

<!-- Modal Editar -->
<div id="modalEditarContador" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="formEditarContador" autocomplete="off">
        <div class="modal-header" style="background: linear-gradient(135deg, #f39c12 0%, #d68910 100%); color:white;">
          <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
          <h4 class="modal-title"><i class="fa fa-edit"></i> Editar Contador</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="idContador" name="idContador">
          
          <div class="row">
            <!-- Columna izquierda -->
            <div class="col-md-6">
              <!-- N° (solo lectura) -->
              <div class="form-group">
                <label><i class="fa fa-hashtag"></i> N°</label>
                <input id="editarNro" class="form-control" name="editarNro" readonly style="background-color: #f5f5f5;">
              </div>
              
              <!-- Comercio(s) -->
              <div class="form-group">
                <label><i class="fa fa-building"></i> Comercio(s) <small class="text-muted">(buscar por empresa)</small></label>
                <div id="comerciosContainerEditar">
                  <div class="comercio-item" style="margin-bottom: 8px;">
                    <select class="form-control select2-comercio-editar" name="editarComercios[]" style="width: calc(100% - 45px); display: inline-block;">
                    </select>
                    <button type="button" class="btn btn-danger btn-sm btn-quitar-comercio-editar" style="display: none;" title="Quitar">
                      <i class="fa fa-minus"></i>
                    </button>
                  </div>
                </div>
                <button type="button" class="btn btn-success btn-xs" id="btnAgregarComercioEditar" style="margin-top: 5px;">
                  <i class="fa fa-plus"></i> Agregar otro comercio
                </button>
              </div>
              
              <!-- Nombre Contador -->
              <div class="form-group">
                <label><i class="fa fa-user"></i> Nombre Contador</label>
                <input id="editarNomContador" class="form-control" name="editarNomContador" placeholder="Nombre completo del contador" autocomplete="off">
              </div>
              
              <!-- Nombre grabado en el celular -->
              <div class="form-group">
                <label><i class="fa fa-address-book"></i> Nombre grabado en el celular</label>
                <input id="editarTitularTlf" class="form-control" name="editarTitularTlf" placeholder="Nombre como aparece en el celular" autocomplete="off">
              </div>
            </div>
            
            <!-- Columna derecha -->
            <div class="col-md-6">
              <!-- Teléfono -->
              <div class="form-group">
                <label><i class="fa fa-phone"></i> Teléfono</label>
                <input id="editarTelefono" class="form-control" name="editarTelefono" placeholder="Número de teléfono" autocomplete="off">
              </div>
              
              <!-- Link -->
              <div class="form-group">
                <label><i class="fa fa-link"></i> Link</label>
                <input type="text" id="editarLink" class="form-control" name="editarLink" placeholder="ejemplo.dominio.com" autocomplete="off" pattern="^(https?:\/\/)?[a-zA-Z0-9][a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}(\/.*)?$|^$" title="Ingrese un dominio válido (ej: ejemplo.com o https://ejemplo.com)">
              </div>
              
              <!-- Usuario -->
              <div class="form-group">
                <label><i class="fa fa-user-circle"></i> Usuario</label>
                <input id="editarUsuario" class="form-control" name="editarUsuario" placeholder="Usuario de acceso" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
              </div>
              
              <!-- Contraseña -->
              <div class="form-group">
                <label><i class="fa fa-lock"></i> Contraseña</label>
                <input id="editarContrasena" class="form-control" name="editarContrasena" type="text" placeholder="Contraseña de acceso" autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false" data-lpignore="true" data-form-type="other">
              </div>
              
              <!-- Servidor -->
              <div class="form-group">
                <label><i class="fa fa-server"></i> Servidor</label>
                <select class="form-control" id="editarServidorContador" name="editarServidorContador">
                  <option value="">Seleccionar servidor...</option>
                  <option value="LORITO">LORITO</option>
                  <option value="ATLANTIS FAST">ATLANTIS FAST</option>
                  <option value="ATLANTIS POS">ATLANTIS POS</option>
                  <option value="ATLANTIS ONLINE">ATLANTIS ONLINE</option>
                  <option value="ATLANTIS VIP">ATLANTIS VIP</option>
                </select>
              </div>
            </div>
          </div>
          
          <!-- Campo oculto para IDs de comercios -->
          <input type="hidden" name="comercioIdsEditar" id="comercioIdsEditar">
        </div>
        <div class="modal-footer" style="background-color: #f5f5f5;">
          <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancelar</button>
          <button type="submit" class="btn btn-warning"><i class="fa fa-save"></i> Actualizar</button>
        </div>
        <?php
          $editar = new ControladorContador();
          $editar->ctrEditarContador();
        ?>
      </form>
    </div>
  </div>
</div>

<script src="<?php echo BASE_URL; ?>/vistas/js/contadores.js"></script>
