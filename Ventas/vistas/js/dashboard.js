$(document).ready(function() {
    // Variables to store Chart instances
    let chartClientes = null;
    let chartEvolucion = null;

    // Inicializar dashboard
    inicializarDashboard();

    // Función para inicializar el dashboard
    function inicializarDashboard() {
        cargarIndicadoresClave();
        cargarGraficoClientes();
        cargarReunionesSemana();
        cargarEvolucionMensual();
        cargarTotalesDashboard();

        // Actualizar cada 5 minutos
        setInterval(function() {
            cargarIndicadoresClave();
            cargarReunionesSemana();
            cargarTotalesDashboard();
        }, 300000);
    }

    // Cargar totales para la zona inferior del dashboard
    function cargarTotalesDashboard() {
        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { action: 'getTotalesDashboard' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    $('#resumen-prospectos').text(response.data.total_prospectos);
                    $('#resumen-clientes').text(response.data.total_clientes);
                    $('#resumen-reuniones').text(response.data.total_reuniones);
                } else {
                    console.error('Error al cargar totales:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX al cargar totales:', error);
            }
        });
    }

    // Cargar indicadores clave
    function cargarIndicadoresClave() {
        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { action: 'getIndicadoresClave' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    actualizarIndicadores(response.data);
                } else {
                    console.error('Error al cargar indicadores:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
                console.error('Respuesta completa:', xhr.responseText);
            }
        });
    }

    // Actualizar indicadores en la interfaz
    function actualizarIndicadores(data) {
        // Clientes ganados
        $('#indicador-clientes-ganados .indicador-valor').text(data.clientes_ganados);

        // Prospectos
        $('#indicador-prospectos .indicador-valor').text(data.prospectos_actual);
        const variacionProspectos = $('#indicador-prospectos .indicador-variacion');
        variacionProspectos.text(data.variacion_prospectos + '%');

        if (data.variacion_prospectos > 0) {
            variacionProspectos.removeClass('text-danger').addClass('text-success');
            variacionProspectos.html('<i class="fa fa-arrow-up"></i> ' + data.variacion_prospectos + '%');
        } else if (data.variacion_prospectos < 0) {
            variacionProspectos.removeClass('text-success').addClass('text-danger');
            variacionProspectos.html('<i class="fa fa-arrow-down"></i> ' + Math.abs(data.variacion_prospectos) + '%');
        } else {
            variacionProspectos.removeClass('text-success text-danger').addClass('text-muted');
            variacionProspectos.html('0%');
        }

        // Clientes perdidos
        $('#indicador-clientes-perdidos .indicador-valor').text(data.clientes_perdidos);

        // Reuniones semana
        $('#indicador-reuniones .indicador-valor').text(data.reuniones_semana);
    }

    // Cargar gráfico de clientes por estado
    function cargarGraficoClientes() {
        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { 
                action: 'getMetricasClientes',
                periodo: 'mensual'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    crearGraficoClientes(response.data);
                } else {
                    console.error('Error al cargar métricas:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
            }
        });
    }

    // Crear gráfico de clientes
    function crearGraficoClientes(data) {
        const canvas = document.getElementById('grafico-clientes');

        // Destruir gráfico previo si existe
        if (chartClientes) {
            try {
                chartClientes.destroy();
                chartClientes = null;
            } catch (error) {
                console.warn('Error destroying previous chartClientes:', error);
            }
        }

        // Limpiar canvas completamente
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // Procesar datos para el gráfico
        const estados = {
            '0': 0, // Prospectos
            '1': 0, // Seguimiento
            '2': 0, // Clientes
            '3': 0, // No clientes
            '4': 0  // Zona espera
        };

        data.forEach(item => {
            estados[item.estado] = item.total;
        });

        const chartData = {
            labels: ['Prospectos', 'Seguimiento', 'Clientes', 'No Clientes', 'Zona Espera'],
            datasets: [{
                data: [
                    estados['0'],
                    estados['1'],
                    estados['2'],
                    estados['3'],
                    estados['4']
                ],
                backgroundColor: [
                    '#FF6384', // Rojo - Prospectos
                    '#36A2EB', // Azul - Seguimiento
                    '#4BC0C0', // Verde - Clientes
                    '#FFCD56', // Amarillo - No clientes
                    '#9966FF'  // Púrpura - Zona espera
                ],
                hoverBackgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#4BC0C0',
                    '#FFCD56',
                    '#9966FF'
                ]
            }]
        };

        try {
            chartClientes = new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const dataset = context.dataset;
                                    const total = dataset.data.reduce((sum, value) => sum + value, 0);
                                    const currentValue = dataset.data[context.dataIndex];
                                    const percentage = ((currentValue / total) * 100).toFixed(2);
                                    return context.label + ': ' + currentValue + ' (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartClientes:', error);
        }
    }

    // Cargar reuniones de la semana
    function cargarReunionesSemana() {
        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { action: 'getReunionesSemana' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    actualizarReuniones(response.data);
                } else {
                    console.error('Error al cargar reuniones:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
            }
        });
    }

    // Actualizar lista de reuniones
    function actualizarReuniones(reuniones) {
        const lista = $('#lista-reuniones');
        lista.empty();

        if (reuniones.length === 0) {
            lista.append('<li class="list-group-item text-center text-muted">No hay reuniones programadas esta semana</li>');
            return;
        }

        reuniones.forEach(reunion => {
            const fecha = new Date(reunion.fecha + 'T' + reunion.hora_inicio);
            const fechaFormateada = fecha.toLocaleDateString('es-ES', { 
                weekday: 'short', 
                day: 'numeric', 
                month: 'short' 
            });
            const horaFormateada = fecha.toLocaleTimeString('es-ES', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });

            const item = `
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">${reunion.titulo}</h5>
                            <small class="text-muted">${reunion.cliente_nombre || 'Sin cliente'}</small>
                        </div>
                        <div class="text-right">
                            <span class="badge bg-primary">${fechaFormateada}</span>
                            <br>
                            <small class="text-muted">${horaFormateada}</small>
                        </div>
                    </div>
                    ${reunion.ubicacion ? `<small class="text-muted"><i class="fa fa-map-marker"></i> ${reunion.ubicacion}</small>` : ''}
                </li>
            `;

            lista.append(item);
        });
    }

    // Cargar evolución mensual (gráfico de líneas)
    function cargarEvolucionMensual() {
        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { action: 'getEvolucionMensual' },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    crearGraficoEvolucion(response.data);
                } else {
                    console.error('Error al cargar evolución:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error AJAX:', error);
            }
        });
    }

    // Crear gráfico de evolución mensual
    function crearGraficoEvolucion(data) {
        const canvas = document.getElementById('grafico-evolucion');

        // Destruir gráfico previo si existe
        if (chartEvolucion) {
            try {
                chartEvolucion.destroy();
                chartEvolucion = null;
            } catch (error) {
                console.warn('Error destroying previous chartEvolucion:', error);
            }
        }

        // Limpiar canvas completamente
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const meses = data.map(item => {
            const [year, month] = item.mes.split('-');
            return new Date(year, month - 1).toLocaleDateString('es-ES', {
                month: 'short',
                year: 'numeric'
            });
        });

        const chartData = {
            labels: meses,
            datasets: [
                {
                    label: 'Prospectos',
                    data: data.map(item => item.prospectos),
                    borderColor: '#FF6384',
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    fill: true
                },
                {
                    label: 'Seguimiento',
                    data: data.map(item => item.seguimiento),
                    borderColor: '#36A2EB',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    fill: true
                },
                {
                    label: 'Clientes',
                    data: data.map(item => item.clientes),
                    borderColor: '#4BC0C0',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    fill: true
                }
            ]
        };

        try {
            chartEvolucion = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating chartEvolucion:', error);
        }
    }

    // Cambiar período del gráfico
    $(document).on('click', '.btn-periodo', function() {
        const periodo = $(this).data('periodo');
        $('.btn-periodo').removeClass('active');
        $(this).addClass('active');

        $.ajax({
            url: 'ajax/dashboard.ajax.php',
            type: 'POST',
            data: { 
                action: 'getMetricasClientes',
                periodo: periodo
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Recargar gráfico con nuevos datos
                    crearGraficoClientes(response.data);
                }
            }
        });
    });
});