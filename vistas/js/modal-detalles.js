(function(window, $){
    'use strict';

    // Normaliza el nombre de la columna para mostrar en el título
    function humanizeColumnName(name) {
        if (!name) return '';
        // Mapeo de columnas conocidas a nombres más amigables
        var map = {
            seguimiento: 'Seguimiento',
            oportunidad: 'Oportunidad',
            cierre: 'Cierre',
            prospectos: 'Prospectos',
            pendientes: 'Pendientes'
        };

        name = String(name).trim();
        var lower = name.toLowerCase();
        if (map[lower]) return map[lower];

        // Reemplaza guiones/underscores por espacios y capitaliza palabras
        var s = name.replace(/[-_]+/g, ' ');
        return s.replace(/\w\S*/g, function(txt){
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }

    // Maneja la apertura del modal y actualiza el título
    function setupModalTitle() {
        $('#modal-detalles-oportunidad').on('show.bs.modal', function (e) {
            var button = $(e.relatedTarget);
            var column = null;

            if (button && button.length) {
                column = button.data('column') || button.attr('data-column');
            }

            if (!column) {
                var contenido = $('#detalles-oportunidad-contenido');
                column = contenido.data('column-name') || contenido.attr('data-column-name');
            }

            var $title = $('#modal-detalles-oportunidad-label');
            if (column) {
                column = humanizeColumnName(column);
                $title.text('Detalles ' + column);
            } else {
                $title.text('Detalles de la Oportunidad');
            }
        });
    }

    // Inicialización cuando el DOM esté listo
    $(function(){
        if ($ && $.fn && $.fn.modal) {
            setupModalTitle();
        }
    });

})(window, jQuery);
