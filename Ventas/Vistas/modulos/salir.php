<?php
session_destroy();
ModeloUsuarios::mdlActualizarUsuario("usuarios", "estado", 1, "id", $_SESSION["id"]);
echo '<script>
      window.location = "login";
 </script>';