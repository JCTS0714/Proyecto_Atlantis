<!DOCTYPE html>
<html>
<head>
    <title>Test AJAX</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Test AJAX - Eliminar Oportunidad</h1>
    <button id="testBtn">Hacer llamada AJAX</button>
    <div id="result"></div>

    <script>
        $(document).ready(function() {
            $("#testBtn").click(function() {
                $.ajax({
                    url: 'ajax/oportunidades.ajax.php',
                    method: 'POST',
                    data: { action: 'eliminarOportunidad', id: 999 },
                    dataType: 'json',
                    success: function(response) {
                        $("#result").html("<p style='color: green;'>✓ Éxito: " + JSON.stringify(response) + "</p>");
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        $("#result").html("<p style='color: red;'>✗ Error: " + textStatus + " - " + errorThrown + "</p>");
                        console.log("Response Text:", jqXHR.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
