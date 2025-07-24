<?php
require_once '../controladores/ControladorOportunidad.php';
require_once '../modelos/ModeloCRM.php';

header('Content-Type: application/json');
echo json_encode(ControladorOportunidad::ctrMostrarOportunidades());
?>
