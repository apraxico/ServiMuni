{{-- filepath: resources/views/LicenciasConducir/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard Licencias de Conducir')
@section('page-title', 'Dashboard Licencias de Conducir')

@section('styles')
<style>
    /* Contenedores de gráficos mejorados */
    .chart-container {
        position: relative;
        height: 300px !important;
        width: 100%;
        padding: 20px 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Asegurar que los canvas sean responsivos */
    .chart-container canvas {
        max-height: 280px !important;
        max-width: 100% !important;
    }
    
    /* Mejorar las tarjetas */
    .card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    
    /* Headers de las tarjetas */
    .card-header {
        border-bottom: none;
        padding: 20px;
        font-weight: 600;
        background: linear-gradient(135deg, var(--header-color, #4e73df) 0%, var(--header-color-dark, #2e59d9) 100%);
    }
    
    .card-header h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .card-header i {
        font-size: 1.1rem;
    }
    
    /* KPI Cards mejoradas */
    .kpi-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        background: linear-gradient(135deg, #fff 0%, #f8f9fc 100%);
    }
    
    .kpi-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    .border-left-primary { border-left-color: #4e73df !important; }
    .border-left-success { border-left-color: #1cc88a !important; }
    .border-left-info { border-left-color: #36b9cc !important; }
    .border-left-warning { border-left-color: #f6c23e !important; }
    
    /* Mejorar la tabla */
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table thead th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        font-weight: 600;
        border: none;
        padding: 15px 12px;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
    }
    
    .table thead th:first-child {
        border-top-left-radius: 15px;
    }
    
    .table thead th:last-child {
        border-top-right-radius: 15px;
    }
    
    .table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #e9ecef;
    }
    
    .table tbody tr:hover {
        background: linear-gradient(135deg, rgba(78, 115, 223, 0.05) 0%, rgba(78, 115, 223, 0.02) 100%);
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .table tbody td {
        padding: 12px;
        vertical-align: middle;
        border-top: none;
    }
    
    /* Botones mejorados */
    .btn-info {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        transition: all 0.3s ease;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(23, 162, 184, 0.4);
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
    }
    
    /* Modal mejorado */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }
    
    .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
        padding: 20px 25px;
    }
    
    .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    
    .modal-body {
        padding: 30px 25px;
    }
    
    .modal-body table {
        font-size: 0.9rem;
    }
    
    .modal-body table td {
        padding: 8px 12px;
        border-top: 1px solid #e9ecef;
    }
    
    .modal-body table td:first-child {
        font-weight: 500;
        color: #495057;
        width: 35%;
    }
    
    /* Validaciones de formulario */
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    
    .invalid-feedback {
        display: block !important;
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        font-weight: 500;
    }
    
    /* Filtro de fechas mejorado */
    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s ease;
        padding: 10px 15px;
    }
    
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }
    
    .form-label {
        font-weight: 600;
        color: #5a5c69;
        margin-bottom: 8px;
    }
    
    /* Información de debug mejorada */
    .alert-info {
        background: linear-gradient(135deg, rgba(54, 185, 204, 0.1) 0%, rgba(54, 185, 204, 0.05) 100%);
        border: 1px solid rgba(54, 185, 204, 0.2);
        border-radius: 15px;
        color: #0c5460;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .chart-container {
            height: 250px !important;
            padding: 15px 5px;
        }
        
        .chart-container canvas {
            max-height: 220px !important;
        }
        
        .card-header {
            padding: 15px;
        }
        
        .card-header h5 {
            font-size: 0.9rem;
        }
        
        .modal-body {
            padding: 20px 15px;
        }
        
        .table thead th {
            font-size: 0.75rem;
            padding: 10px 8px;
        }
        
        .table tbody td {
            padding: 8px;
            font-size: 0.875rem;
        }
    }
    
    /* Animaciones sutiles */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .card {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .card:nth-child(1) { animation-delay: 0.1s; }
    .card:nth-child(2) { animation-delay: 0.2s; }
    .card:nth-child(3) { animation-delay: 0.3s; }
    .card:nth-child(4) { animation-delay: 0.4s; }
    
    /* Loading spinner para gráficos */
    .chart-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .chart-loading::before {
        content: "";
        width: 20px;
        height: 20px;
        border: 2px solid #e9ecef;
        border-top: 2px solid #4e73df;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-right: 10px;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Badges mejorados */
    .badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.8rem;
    }
    
    /* Mejoras para los números grandes */
    .h5.mb-0.font-weight-bold {
        font-size: 2rem !important;
        font-weight: 700 !important;
        color: #2c3e50;
    }
    
    .text-xs {
        font-size: 0.8rem !important;
        font-weight: 600 !important;
    }
    
    /* Estados de carga para prevenir flash */
    .chart-container:empty::before {
        content: "Cargando gráfico...";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #6c757d;
        font-size: 0.9rem;
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
                    <form method="GET" class="row g-3 align-items-end" id="filtroFechas">
                        <div class="col-md-5">
                            <label for="fecha_inicio" class="form-label mb-1">Desde</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" 
                                value="{{ $fechaInicio }}" 
                                max="{{ date('Y-m-d') }}"
                                required>
                            <div class="invalid-feedback" id="error-fecha-inicio"></div>
                        </div>
                        <div class="col-md-5">
                            <label for="fecha_fin" class="form-label mb-1">Hasta</label>
                            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" 
                                value="{{ $fechaFin }}" 
                                max="{{ date('Y-m-d') }}"
                                required>
                            <div class="invalid-feedback" id="error-fecha-fin"></div>
                        </div>
                        <div class="col-md-2 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
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

    <!-- Gráficos Principales - Rediseñados -->
    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-bar me-2"></i>
                        <h5 class="mb-0">Solicitudes por Día de la Semana</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="solicitudesPorDiaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-clock me-2"></i>
                        <h5 class="mb-0">Solicitudes por Hora</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="solicitudesPorHoraChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Segunda fila de gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line me-2"></i>
                        <h5 class="mb-0">Ingresos Diarios</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ingresosDiariosChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users me-2"></i>
                        <h5 class="mb-0">Distribución por Edad</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionEdadChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tercera fila de gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-purple text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-list-alt me-2"></i>
                        <h5 class="mb-0">Tipos de Solicitud</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="distribucionGlosaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-dollar-sign me-2"></i>
                        <h5 class="mb-0">Ingresos por Tipo</h5>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="ingresosPorGlosaChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de datos mejorada -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <i class="fas fa-table me-2"></i>
            <h5 class="mb-0">Detalle de Atenciones</h5>
            <span class="badge bg-light text-primary ms-2">{{ count($atenciones) }} registros</span>
        </div>
        <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTable" aria-expanded="false">
            <i class="fas fa-expand-arrows-alt me-1"></i> 
            <span class="d-none d-md-inline">Mostrar/Ocultar</span>
        </button>
    </div>
    
    <div class="collapse show" id="collapseTable">
        <div class="card-body p-0">
            @if(count($atenciones) > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="dataTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-1"></i>Folio</th>
                                <th><i class="fas fa-calendar me-1"></i>Fecha</th>
                                <th><i class="fas fa-id-card me-1"></i>RUT</th>
                                <th><i class="fas fa-user me-1"></i>Nombre Completo</th>
                                <th><i class="fas fa-clipboard-list me-1"></i>Tipo Solicitud</th>
                                <th><i class="fas fa-dollar-sign me-1"></i>Monto</th>
                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($atenciones as $index => $atencion)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary">{{ $atencion->Folio_Solicitud }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ \Carbon\Carbon::parse($atencion->Fecha_Solicitud)->format('d/m/Y') }}</span>
                                    @if($atencion->Hora)
                                        <br><small class="text-info">{{ $atencion->Hora }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="font-monospace">{{ $atencion->Rut }}</span>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ $atencion->Nombres }} {{ $atencion->Apellidos }}</span>
                                        @if($atencion->Fono)
                                            <br><small class="text-muted"><i class="fas fa-phone fa-xs me-1"></i>{{ $atencion->Fono }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $atencion->Glosa }}</span>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">$ {{ number_format($atencion->Total_Giro, 0, ',', '.') }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" onclick="verDetalle('{{ $atencion->Folio_Solicitud }}')" title="Ver detalles">
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-lg-inline ms-1">Ver</span>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación o información adicional -->
                <div class="card-footer bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Mostrando {{ count($atenciones) }} atenciones del período seleccionado
                            </small>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>
                                {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - 
                                {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @else
                <!-- Estado vacío mejorado -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-search fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="text-muted">No hay atenciones en este período</h5>
                    <p class="text-muted mb-4">
                        Intenta ajustar el rango de fechas para ver más resultados.
                    </p>
                    <button class="btn btn-primary" onclick="document.getElementById('fecha_inicio').focus()">
                        <i class="fas fa-filter me-1"></i>
                        Cambiar Filtro
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
<!-- Funcionalidades adicionales -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <div class="d-flex align-items-center">
                    <i class="fas fa-tools me-2"></i>
                    <h6 class="mb-0">Herramientas de Análisis</h6>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportarDatos()">
                                <i class="fas fa-file-excel me-1"></i>
                                Exportar a Excel
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-primary" onclick="imprimirReporte()">
                                <i class="fas fa-print me-1"></i>
                                Imprimir Reporte
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-grid">
                            <button class="btn btn-outline-info" onclick="actualizarDatos()">
                                <i class="fas fa-sync-alt me-1"></i>
                                Actualizar Datos
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Iniciando Dashboard de Licencias');
    
    // Verificar Chart.js
    if (typeof Chart === 'undefined') {
        console.error('❌ Chart.js no disponible');
        return;
    }
    
    // Datos del servidor
    const solicitudesPorDia = @json($solicitudesPorDia ?? []);
    const solicitudesPorHora = @json($solicitudesPorHora ?? []);
    const distribucionGlosa = @json($distribucionGlosa ?? []);
    const distribucionEdad = @json($distribucionEdad ?? []);
    const ingresosDiarios = @json($ingresosDiarios ?? []);
    const ingresosPorGlosa = @json($ingresosPorGlosa ?? []);
    
    console.log('📊 Datos recibidos:', {
        solicitudesPorDia,
        solicitudesPorHora,
        distribucionGlosa,
        distribucionEdad
    });
    
    // Colores
    const colores = {
        primary: '#4e73df',
        success: '#1cc88a', 
        info: '#36b9cc',
        warning: '#f6c23e',
        danger: '#e74a3b',
        purple: '#6f42c1'
    };
    
    // 1. Solicitudes por Día
    const ctx1 = document.getElementById('solicitudesPorDiaChart');
    if (ctx1) {
        const dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
        const diasEn = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const datos = diasEn.map(dia => solicitudesPorDia[dia] || 0);
        
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: dias,
                datasets: [{
                    label: 'Solicitudes',
                    data: datos,
                    backgroundColor: colores.primary,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('✅ Gráfico días creado');
    }
    
    // 2. Solicitudes por Hora
    const ctx2 = document.getElementById('solicitudesPorHoraChart');
    if (ctx2) {
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: Object.keys(solicitudesPorHora),
                datasets: [{
                    label: 'Solicitudes',
                    data: Object.values(solicitudesPorHora),
                    borderColor: colores.info,
                    backgroundColor: colores.info + '20',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('✅ Gráfico horas creado');
    }
    
    // 3. Ingresos Diarios
    const ctx3 = document.getElementById('ingresosDiariosChart');
    if (ctx3) {
        const fechas = Object.keys(ingresosDiarios).sort();
        const valores = fechas.map(f => ingresosDiarios[f]);
        
        new Chart(ctx3, {
            type: 'line',
            data: {
                labels: fechas.map(f => new Date(f).toLocaleDateString('es-CL')),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: valores,
                    borderColor: colores.success,
                    backgroundColor: colores.success + '20',
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
        console.log('✅ Gráfico ingresos diarios creado');
    }
    
    // 4. Distribución por Edad
    const ctx4 = document.getElementById('distribucionEdadChart');
    if (ctx4) {
        new Chart(ctx4, {
            type: 'doughnut',
            data: {
                labels: Object.keys(distribucionEdad),
                datasets: [{
                    data: Object.values(distribucionEdad),
                    backgroundColor: [
                        colores.primary,
                        colores.success,
                        colores.info,
                        colores.warning,
                        colores.danger,
                        colores.purple
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom' }
                }
            }
        });
        console.log('✅ Gráfico edad creado');
    }
    
    // 5. Tipos de Solicitud
    const ctx5 = document.getElementById('distribucionGlosaChart');
    if (ctx5) {
        new Chart(ctx5, {
            type: 'pie',
            data: {
                labels: Object.keys(distribucionGlosa),
                datasets: [{
                    data: Object.values(distribucionGlosa),
                    backgroundColor: [
                        colores.primary,
                        colores.success,
                        colores.warning,
                        colores.danger,
                        colores.info,
                        colores.purple
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { position: 'bottom' }
                }
            }
        });
        console.log('✅ Gráfico tipos creado');
    }
    
    // 6. Ingresos por Tipo
    const ctx6 = document.getElementById('ingresosPorGlosaChart');
    if (ctx6) {
        new Chart(ctx6, {
            type: 'bar',
            data: {
                labels: Object.keys(ingresosPorGlosa),
                datasets: [{
                    label: 'Ingresos ($)',
                    data: Object.values(ingresosPorGlosa),
                    backgroundColor: colores.success,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
        console.log('✅ Gráfico ingresos por tipo creado');
    }
    
    console.log('🎉 Todos los gráficos completados');
});

// Función Ver Detalle (mejorada)
function verDetalle(folio) {
    const atenciones = @json($atenciones);
    const atencion = atenciones.find(a => String(a.Folio_Solicitud) === String(folio));
    
    if (!atencion) {
        alert('No se encontraron datos para el folio: ' + folio);
        return;
    }
    
    const contenido = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary mb-3">📋 Información Personal</h6>
                <table class="table table-sm">
                    <tr><td><strong>Folio:</strong></td><td>${atencion.Folio_Solicitud}</td></tr>
                    <tr><td><strong>RUT:</strong></td><td>${atencion.Rut || 'N/A'}</td></tr>
                    <tr><td><strong>Nombre:</strong></td><td>${atencion.Nombres || ''} ${atencion.Apellidos || ''}</td></tr>
                    <tr><td><strong>Teléfono:</strong></td><td>${atencion.Fono || 'N/A'}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-success mb-3">📄 Detalles de Solicitud</h6>
                <table class="table table-sm">
                    <tr><td><strong>Fecha:</strong></td><td>${new Date(atencion.Fecha_Solicitud).toLocaleDateString('es-CL')}</td></tr>
                    <tr><td><strong>Hora:</strong></td><td>${atencion.Hora || 'N/A'}</td></tr>
                    <tr><td><strong>Tipo:</strong></td><td><span class="badge bg-primary">${atencion.Glosa || 'N/A'}</span></td></tr>
                    <tr><td><strong>Monto:</strong></td><td class="text-success fw-bold">$${atencion.Total_Giro ? new Intl.NumberFormat('es-CL').format(atencion.Total_Giro) : '0'}</td></tr>
                </table>
            </div>
        </div>
    `;
    
    document.getElementById('modalContent').innerHTML = contenido;
    
    if (typeof bootstrap !== 'undefined') {
        new bootstrap.Modal(document.getElementById('detalleModal')).show();
    }
}

// Validaciones de fecha
document.addEventListener('DOMContentLoaded', function() {
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');
    const hoy = new Date().toISOString().split('T')[0];
    
    function validarFechas() {
        const inicio = new Date(fechaInicio.value);
        const fin = new Date(fechaFin.value);
        
        fechaInicio.classList.remove('is-invalid');
        fechaFin.classList.remove('is-invalid');
        
        if (inicio > new Date(hoy)) {
            fechaInicio.classList.add('is-invalid');
            return false;
        }
        
        if (fin > new Date(hoy)) {
            fechaFin.classList.add('is-invalid');
            return false;
        }
        
        if (inicio > fin) {
            fechaInicio.classList.add('is-invalid');
            return false;
        }
        
        return true;
    }
    
    fechaInicio?.addEventListener('change', validarFechas);
    fechaFin?.addEventListener('change', validarFechas);
    
    document.getElementById('filtroFechas')?.addEventListener('submit', function(e) {
        if (!validarFechas()) {
            e.preventDefault();
            alert('Corrige los errores en las fechas');
        }
    });
});
// Funciones adicionales
function exportarDatos() {
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    
    // Simular descarga (aquí irías a tu endpoint de exportación)
    const url = `{{ route('licencias.dashboard') }}?fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}&export=excel`;
    
    // Crear elemento temporal para descarga
    const link = document.createElement('a');
    link.href = url;
    link.download = `licencias_${fechaInicio}_${fechaFin}.xlsx`;
    
    // Mostrar mensaje mientras se procesa
    showNotification('Preparando exportación...', 'info');
    
    // Simular descarga (reemplaza esto con tu lógica real)
    setTimeout(() => {
        showNotification('Exportación completada', 'success');
    }, 2000);
}

function imprimirReporte() {
    const contenidoOriginal = document.body.innerHTML;
    const contenidoImpresion = document.querySelector('.container-fluid').innerHTML;
    
    // Crear ventana de impresión
    const ventanaImpresion = window.open('', '', 'height=600,width=800');
    ventanaImpresion.document.write(`
        <html>
        <head>
            <title>Reporte Licencias de Conducir</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                @media print {
                    .chart-container { height: 200px !important; }
                    .btn, .collapse-toggle { display: none !important; }
                    .card { break-inside: avoid; }
                }
            </style>
        </head>
        <body>
            <div class="container-fluid">
                <h2 class="text-center mb-4">Reporte Licencias de Conducir</h2>
                ${contenidoImpresion}
            </div>
        </body>
        </html>
    `);
    
    ventanaImpresion.document.close();
    
    // Esperar a que se cargue y luego imprimir
    setTimeout(() => {
        ventanaImpresion.print();
        ventanaImpresion.close();
    }, 1000);
}

function actualizarDatos() {
    showNotification('Actualizando datos...', 'info');
    
    // Recargar la página con los mismos filtros
    const fechaInicio = document.getElementById('fecha_inicio').value;
    const fechaFin = document.getElementById('fecha_fin').value;
    
    const url = new URL(window.location);
    url.searchParams.set('fecha_inicio', fechaInicio);
    url.searchParams.set('fecha_fin', fechaFin);
    url.searchParams.set('refresh', Date.now()); // Para evitar caché
    
    window.location.href = url.toString();
}

function showNotification(mensaje, tipo = 'info') {
    // Crear contenedor de notificaciones si no existe
    let container = document.getElementById('notifications-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notifications-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 350px;
        `;
        document.body.appendChild(container);
    }
    
    // Crear notificación
    const notification = document.createElement('div');
    notification.className = `alert alert-${tipo} alert-dismissible fade show`;
    notification.style.cssText = `
        margin-bottom: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 10px;
    `;
    
    const iconos = {
        success: 'check-circle',
        error: 'exclamation-triangle', 
        warning: 'exclamation-circle',
        info: 'info-circle'
    };
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${iconos[tipo] || 'info-circle'} me-2"></i>
            <span>${mensaje}</span>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    container.appendChild(notification);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Mejorar la responsividad de los gráficos
window.addEventListener('resize', function() {
    // Re-renderizar gráficos si es necesario
    if (typeof Chart !== 'undefined') {
        Chart.helpers.each(Chart.instances, function(instance) {
            instance.resize();
        });
    }
});

// Loading states para gráficos
function addChartLoadingState() {
    const chartContainers = document.querySelectorAll('.chart-container');
    chartContainers.forEach(container => {
        if (!container.querySelector('canvas')) {
            container.innerHTML = '<div class="chart-loading">Cargando gráfico...</div>';
        }
    });
}

// Llamar al cargar la página
document.addEventListener('DOMContentLoaded', addChartLoadingState);
</script>
@endsection