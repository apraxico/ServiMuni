{{-- filepath: resources/views/LicenciasConducir/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Licencias de Conducir')
@section('page-title', 'Dashboard Licencias de Conducir')

@section('styles')
<style>
    .kpi-card {
        transition: all 0.3s;
    }
    .kpi-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    /* Estilos mejorados para las tablas */
.table-responsive {
    border-radius: 8px;
    box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

.data-table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}

.data-table thead th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    border: none;
    padding: 15px 12px;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table thead th:first-child {
    border-top-left-radius: 8px;
}

.data-table thead th:last-child {
    border-top-right-radius: 8px;
}

.data-table tbody tr {
    transition: all 0.3s ease;
}

.data-table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.data-table tbody td {
    padding: 12px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.action-btn {
    margin: 0 2px;
    transition: all 0.3s ease;
    border-radius: 6px;
    padding: 8px 10px;
}

.action-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.btn-view {
    background: linear-gradient(135deg, #17a2b8, #138496);
    border: none;
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-delete {
    background: linear-gradient(135deg, #dc3545, #c82333);
    border: none;
}

/* Modal mejorado */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px 12px 0 0;
    border-bottom: none;
}

.modal-body {
    padding: 25px;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    padding: 15px 25px;
}

/* Empty state */
.table-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.table-empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.table-empty-state-text {
    font-size: 1.1rem;
    margin-bottom: 20px;
}
</style>
@endsection

@section('content')
<div class="container-fluid px-4">
    <!-- DEBUG: Verificar datos disponibles -->
    <div class="alert alert-info" style="margin-bottom: 20px;">
        <strong>Debug Info:</strong><br>
        Total atenciones: {{ count($atenciones) }}<br>
        Fecha inicio: {{ $fechaInicio }}<br>
        Fecha fin: {{ $fechaFin }}<br>
        Total solicitudes: {{ $totalSolicitudes }}<br>
        Ingresos totales: {{ $ingresoTotal }}<br>
        @if(count($atenciones) > 0)
            Primera atención: {{ $atenciones[0]->Folio_Solicitud ?? 'Sin folio' }}
        @endif
    </div>
    <!-- Filtro de fechas -->
    <div class="row mb-4">
        <div class="col-12 col-md-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label for="fecha_inicio" class="form-label mb-1">Desde</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}">
                        </div>
                        <div class="col-md-5">
                            <label for="fecha_fin" class="form-label mb-1">Hasta</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="{{ $fechaFin }}">
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">Filtrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumen General -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3">Resumen General</h4>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card border-left-primary shadow-sm h-100 py-2 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Solicitudes</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalSolicitudes, 0, ',', '.') }}</div>
                                    @if($cambioSolicitudes != 0)
                                        <div class="mt-2 small {{ $cambioSolicitudes > 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-{{ $cambioSolicitudes > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                            {{ number_format(abs($cambioSolicitudes), 1, ',', '.') }}% vs período anterior
                                            <br><small class="text-muted">({{ Carbon\Carbon::parse($periodoAnteriorInicio)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($periodoAnteriorFin)->format('d/m/Y') }})</small>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-success shadow-sm h-100 py-2 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Ingresos Totales</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$ {{ number_format($ingresoTotal, 0, ',', '.') }}</div>
                                    @if($cambioIngresos != 0)
                                        <div class="mt-2 small {{ $cambioIngresos > 0 ? 'text-success' : 'text-danger' }}">
                                            <i class="fas fa-{{ $cambioIngresos > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                            {{ number_format(abs($cambioIngresos), 1, ',', '.') }}% respecto al período anterior
                                        </div>
                                    @endif
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-info shadow-sm h-100 py-2 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Promedio por Solicitud</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">$ {{ number_format($promedioValor, 0, ',', '.') }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="card border-left-warning shadow-sm h-100 py-2 kpi-card">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Período Analizado</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Carbon\Carbon::parse($fechaInicio)->diffInDays(Carbon\Carbon::parse($fechaFin)) + 1 }} días</div>
                                    <div class="mt-2 small text-muted">
                                        {{ Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos - Primera fila -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Solicitudes por Día de la Semana</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="solicitudesPorDiaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Ingresos Diarios</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ingresosDiariosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos - Segunda fila -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Distribución por Sexo</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionSexoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Solicitudes por Tipo</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionGlosaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">Distribución por Edad</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionEdadChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos - Tercera fila -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Top 10 Comunas</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionComunaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Ingresos por Tipo de Solicitud</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ingresosPorGlosaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de datos -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Detalle de Atenciones</h5>
            <button class="btn btn-sm btn-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTable" aria-expanded="false">
                <i class="fas fa-expand-arrows-alt"></i> Mostrar/Ocultar
            </button>
        </div>
        <div class="collapse" id="collapseTable">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="dataTable">
                <thead class="table-light">
                <tr>
                    <th>Folio</th>
                    <th>Fecha</th>
                    <th>RUT</th>
                    <th>Nombre Completo</th>
                    <th>Tipo Solicitud</th>
                    <th>Monto</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                    <tbody>
                            @forelse($atenciones as $atencion)
                        <tr>
                            <td>{{ $atencion->Folio_Solicitud }}</td>
                            <td>{{ \Carbon\Carbon::parse($atencion->Fecha_Solicitud)->format('d/m/Y') }}</td>
                            <td>{{ $atencion->Rut }}</td>
                            <td>{{ $atencion->Nombres }} {{ $atencion->Apellidos }}</td>
                            <td>{{ $atencion->Glosa }}</td>
                            <td>$ {{ number_format($atencion->Total_Giro, 0, ',', '.') }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="verDetalle('{{ $atencion->Folio_Solicitud }}')">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                            </td>
                        </tr>
                            @empty
                                <tr>
                                <td colspan="7" class="text-center text-muted">No hay atenciones en este rango de fechas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal para detalles -->
                <div class="modal fade" id="detalleModal" tabindex="-1" aria-labelledby="detalleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detalleModalLabel">Detalle de Atención</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="modalContent">
                                <!-- Contenido dinámico -->
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
<script>
// Verificar que Chart.js se cargó correctamente
if (typeof Chart === 'undefined') {
    console.error('Chart.js no se cargó correctamente');
    alert('Error: Chart.js no está disponible. Los gráficos no se mostrarán.');
}
</script>
<script>
// Verificar que Bootstrap esté disponible
if (typeof bootstrap === 'undefined') {
    console.error('Bootstrap no está disponible');
}

document.addEventListener('DOMContentLoaded', function() {
    // Debug: Verificar datos disponibles
    console.log('Datos de solicitudes por día:', @json($solicitudesPorDia));
    console.log('Datos de distribución por sexo:', @json($distribucionSexo));
    console.log('Datos de distribución por comuna:', @json($distribucionComuna));
    console.log('Datos de distribución por glosa:', @json($distribucionGlosa));
    console.log('Total de atenciones:', {{ $totalSolicitudes }});
    // Colores para gráficos
    const colorPalette = [
        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69',
        '#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#e02d1b', '#3a3b45'
    ];
    
    // Configuración común para todos los gráficos
    const commonOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    };
    
    // 1. Gráfico de Solicitudes por Día
    const solicitudesPorDiaCtx = document.getElementById('solicitudesPorDiaChart').getContext('2d');
    const diasOrdenados = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const diasEspanol = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
    
    const solicitudesPorDiaData = diasOrdenados.map(dia => {
        return @json($solicitudesPorDia)[dia] || 0;
    });
    
    new Chart(solicitudesPorDiaCtx, {
        type: 'bar',
        data: {
            labels: diasEspanol,
            datasets: [{
                label: 'Solicitudes',
                data: solicitudesPorDiaData,
                backgroundColor: colorPalette[0],
                borderColor: colorPalette[0],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // 2. Gráfico de Ingresos Diarios
    const ingresosDiariosCtx = document.getElementById('ingresosDiariosChart').getContext('2d');
    const ingresosDiariosData = @json($ingresosDiarios);
    const fechas = Object.keys(ingresosDiariosData).sort();
    const ingresos = fechas.map(fecha => ingresosDiariosData[fecha]);
    
    new Chart(ingresosDiariosCtx, {
        type: 'line',
        data: {
            labels: fechas.map(fecha => {
                return new Date(fecha).toLocaleDateString('es-CL');
            }),
            datasets: [{
                label: 'Ingresos ($)',
                data: ingresos,
                borderColor: colorPalette[1],
                backgroundColor: 'rgba(28, 200, 138, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            ...commonOptions
        }
    });
    
    // 3. Gráfico de Distribución por Sexo
    const distribucionSexoCtx = document.getElementById('distribucionSexoChart').getContext('2d');
    const distribucionSexoData = @json($distribucionSexo);
    
    new Chart(distribucionSexoCtx, {
        type: 'pie',
        data: {
            labels: Object.keys(distribucionSexoData).map(sexo => {
                switch(sexo) {
                    case 'M': return 'Masculino';
                    case 'F': return 'Femenino';
                    default: return 'No especificado';
                }
            }),
            datasets: [{
                data: Object.values(distribucionSexoData),
                backgroundColor: [colorPalette[0], colorPalette[1], colorPalette[2]],
                borderWidth: 1
            }]
        },
        options: commonOptions
    });
    
    // 4. Gráfico de Distribución por Glosa
    const distribucionGlosaCtx = document.getElementById('distribucionGlosaChart').getContext('2d');
    const distribucionGlosaData = @json($distribucionGlosa);
    
    new Chart(distribucionGlosaCtx, {
        type: 'doughnut',
        data: {
            labels: Object.keys(distribucionGlosaData),
            datasets: [{
                data: Object.values(distribucionGlosaData),
                backgroundColor: colorPalette,
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            plugins: {
                legend: {
                    position: 'right',
                    align: 'start'
                }
            }
        }
    });
    
    // 5. Gráfico de Distribución por Edad
    const distribucionEdadCtx = document.getElementById('distribucionEdadChart').getContext('2d');
    const distribucionEdadData = @json($distribucionEdad);
    
    new Chart(distribucionEdadCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(distribucionEdadData),
            datasets: [{
                label: 'Cantidad',
                data: Object.values(distribucionEdadData),
                backgroundColor: colorPalette[4],
                borderColor: colorPalette[4],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
    
    // 6. Gráfico de Distribución por Comuna
    const distribucionComunaCtx = document.getElementById('distribucionComunaChart').getContext('2d');
    const distribucionComunaData = @json($distribucionComuna);

    new Chart(distribucionComunaCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(distribucionComunaData),
            datasets: [{
                label: 'Solicitudes',
                data: Object.values(distribucionComunaData),
                backgroundColor: colorPalette.slice(0, Object.keys(distribucionComunaData).length),
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 10
                    }
                }
            }
        }
    });
    
    // 7. Gráfico de Ingresos por Glosa
    const ingresosPorGlosaCtx = document.getElementById('ingresosPorGlosaChart').getContext('2d');
    const ingresosPorGlosaData = @json($ingresosPorGlosa);
    
    new Chart(ingresosPorGlosaCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(ingresosPorGlosaData),
            datasets: [{
                label: 'Ingresos ($)',
                data: Object.values(ingresosPorGlosaData),
                backgroundColor: colorPalette[5],
                borderColor: colorPalette[5],
                borderWidth: 1
            }]
        },
        options: {
            ...commonOptions,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Inicializar DataTables para la tabla
    if (typeof $.fn.DataTable !== 'undefined') {
        $('#dataTable').DataTable({
        language: {
        url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
        pageLength: 25,
        order: [[1, 'desc']],
        responsive: true,
        columnDefs: [
        { targets: -1, orderable: false } // Columna de acciones no ordenable
            ]
        });
    }

});

// Función global para mostrar detalle en modal - DEBE ESTAR FUERA DEL DOMContentLoaded
function verDetalle(folio) {
    console.log('Buscando folio:', folio);
    const atenciones = @json($atenciones);
    console.log('Atenciones disponibles:', atenciones);
    
    const atencion = atenciones.find(a => String(a.Folio_Solicitud) === String(folio));
    console.log('Atención encontrada:', atencion);
    
    if (atencion) {
        const contenido = `
            <div class="row">
                <div class="col-md-6">
                    <h6 class="text-primary">Datos Personales</h6>
                    <p><strong>Folio:</strong> ${atencion.Folio_Solicitud || 'No disponible'}</p>
                    <p><strong>RUT:</strong> ${atencion.Rut || 'No disponible'}</p>
                    <p><strong>Nombres:</strong> ${atencion.Nombres || 'No disponible'}</p>
                    <p><strong>Apellidos:</strong> ${atencion.Apellidos || 'No disponible'}</p>
                    <p><strong>Sexo:</strong> ${atencion.Sexo || 'No disponible'}</p>
                    <p><strong>F. Nacimiento:</strong> ${atencion.Fecha_Nacimiento ? new Date(atencion.Fecha_Nacimiento).toLocaleDateString('es-CL') : 'No disponible'}</p>
                    <p><strong>Teléfono:</strong> ${atencion.Fono || 'No disponible'}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-success">Datos de la Solicitud</h6>
                    <p><strong>Fecha:</strong> ${new Date(atencion.Fecha_Solicitud).toLocaleDateString('es-CL')}</p>
                    <p><strong>Hora:</strong> ${atencion.Hora || 'No disponible'}</p>
                    <p><strong>Tipo:</strong> ${atencion.Glosa || 'No disponible'}</p>
                    <p><strong>Monto:</strong> $ ${atencion.Total_Giro ? new Intl.NumberFormat('es-CL').format(atencion.Total_Giro) : '0'}</p>
                    <h6 class="text-info mt-3">Ubicación</h6>
                    <p><strong>Dirección:</strong> ${atencion.Direccion || 'No disponible'}</p>
                    <p><strong>Comuna:</strong> ${atencion.Comuna || 'No disponible'}</p>
                    <p><strong>Profesión:</strong> ${atencion.Profesion || 'No disponible'}</p>
                </div>
            </div>
        `;
        
        document.getElementById('modalContent').innerHTML = contenido;
        
        // Verificar si Bootstrap está disponible
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('detalleModal'));
            modal.show();
        } else {
            // Fallback si Bootstrap no está disponible
            const modalElement = document.getElementById('detalleModal');
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
        }
    } else {
        console.error('No se encontró la atención con folio:', folio);
        alert('No se encontraron datos para este folio: ' + folio);
    }
}
</script>
@endsection