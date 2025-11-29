<div id="back"></div><!--ID PARA LLAMAR EN EL CSS AGREGAR UN FONDO AL LOGIN-->
    <div class="login-box">
        <div class="login-logo">
           <!-- <img src="vistas/img/plantilla/logo.png" class="img-responsive" style="padding:330px 100px 0px 100px">-->
            <a href="#"><b>Admin</b>LTE</a>
        </div>
        <div class="login-box-body">
            <p class="login-box-msg">Ingresar al Sistema</p>

        <form method="post" class="login-form">
            <div class="form-group has-feedback">
                <input type="text" class="form-control" placeholder="Usuario" name="ingUsuario" requerid>
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" class="form-control" placeholder="Contraseña" name="ingPassword" requerid>
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            </div>
            <div class="row">
            <div class="col-xs-8">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="remember_me" value="on"> Recordarme
                    </label>
                </div>
            </div>
            <div class="col-xs-4">
                <button type="submit" class="btn btn-primary btn-block btn-flat">Ingresar</button>
                </div>
            </div>
            <!--LLAMAR AL MÉTPDO PARA INICIAR SESIÓN-->
            <?php
            $login = new ControladorUsuarios ();
            $login->ctrIngresoUsuario();
            ?>
        </form>
    </div>
</div>
