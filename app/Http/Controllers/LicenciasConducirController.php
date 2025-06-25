<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LicenciasConducirController extends Controller
{
    public function index(Request $request)
    {
        // Fechas por defecto (mes actual)
        $fechaInicio = $request->input('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', now()->format('Y-m-d'));

        // Consulta principal con mejor estructura
        $atenciones = DB::connection('sqlsrv')
            ->table('Solicitudes as S')
            ->leftJoin('Personas as P', 'S.Rut', '=', 'P.Rut')
            ->leftJoin('Giros as G', 'S.Folio_Solicitud', '=', 'G.Folio_Solicitud')
            ->select([
                'S.Folio_Solicitud',
                'S.Fecha_Solicitud', 
                'S.Rut',
                'P.Nombres',
                'P.Apellidos',
                'S.Glosa',
                'S.Hora',
                'P.Direccion',
                'P.Comuna', 
                'P.Profesion',
                'P.Sexo',
                'P.Fecha_Nacimiento',
                'P.Fono',
                'G.Total_Giro'
            ])
            ->whereDate('S.Fecha_Solicitud', '>=', $fechaInicio)
            ->whereDate('S.Fecha_Solicitud', '<=', $fechaFin)
            ->whereNotNull('S.Folio_Solicitud')
            ->orderBy('S.Fecha_Solicitud', 'desc')
            ->get();

        // KPIs básicos
        $totalSolicitudes = $atenciones->count();
        $ingresoTotal = $atenciones->sum('Total_Giro') ?? 0;
        $promedioValor = $totalSolicitudes > 0 ? $ingresoTotal / $totalSolicitudes : 0;

        // PROCESAMIENTO MEJORADO DE DATOS PARA GRÁFICOS
        
        // 1. Solicitudes por día de la semana (formato correcto)
        $solicitudesPorDia = [];
        $diasSemana = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
        
        foreach ($atenciones as $atencion) {
            $diaSemana = Carbon::parse($atencion->Fecha_Solicitud)->format('l'); // Monday, Tuesday, etc.
            if (isset($diasSemana[$diaSemana])) {
                $diasSemana[$diaSemana]++;
            }
        }
        $solicitudesPorDia = $diasSemana;

        // 2. Ingresos diarios
        $ingresosDiarios = [];
        foreach ($atenciones as $atencion) {
            $fecha = Carbon::parse($atencion->Fecha_Solicitud)->format('Y-m-d');
            if (!isset($ingresosDiarios[$fecha])) {
                $ingresosDiarios[$fecha] = 0;
            }
            $ingresosDiarios[$fecha] += $atencion->Total_Giro ?? 0;
        }
        ksort($ingresosDiarios);

        // 3. Distribución por tipo de solicitud (más útil que sexo)
        $distribucionGlosa = [];
        foreach ($atenciones as $atencion) {
            $glosa = $atencion->Glosa ?? 'Sin especificar';
            if (!isset($distribucionGlosa[$glosa])) {
                $distribucionGlosa[$glosa] = 0;
            }
            $distribucionGlosa[$glosa]++;
        }
        arsort($distribucionGlosa);

        // 4. Ingresos por tipo de solicitud
        $ingresosPorGlosa = [];
        foreach ($atenciones as $atencion) {
            $glosa = $atencion->Glosa ?? 'Sin especificar';
            if (!isset($ingresosPorGlosa[$glosa])) {
                $ingresosPorGlosa[$glosa] = 0;
            }
            $ingresosPorGlosa[$glosa] += $atencion->Total_Giro ?? 0;
        }
        arsort($ingresosPorGlosa);

        // 5. Distribución por rango de edad (más útil)
        $distribucionEdad = ['18-25' => 0, '26-35' => 0, '36-45' => 0, '46-55' => 0, '56-65' => 0, '65+' => 0];
        foreach ($atenciones as $atencion) {
            if ($atencion->Fecha_Nacimiento) {
                $edad = Carbon::parse($atencion->Fecha_Nacimiento)->age;
                if ($edad >= 18 && $edad <= 25) $distribucionEdad['18-25']++;
                elseif ($edad >= 26 && $edad <= 35) $distribucionEdad['26-35']++;
                elseif ($edad >= 36 && $edad <= 45) $distribucionEdad['36-45']++;
                elseif ($edad >= 46 && $edad <= 55) $distribucionEdad['46-55']++;
                elseif ($edad >= 56 && $edad <= 65) $distribucionEdad['56-65']++;
                elseif ($edad > 65) $distribucionEdad['65+']++;
            }
        }

        // 6. Solicitudes por hora (más útil que comunas)
        $solicitudesPorHora = [];
        for ($i = 8; $i <= 18; $i++) {
            $solicitudesPorHora[sprintf('%02d:00', $i)] = 0;
        }
        
        foreach ($atenciones as $atencion) {
            if ($atencion->Hora) {
                $hora = substr($atencion->Hora, 0, 2) . ':00';
                if (isset($solicitudesPorHora[$hora])) {
                    $solicitudesPorHora[$hora]++;
                }
            }
        }

        // Cálculo de período anterior
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        $fechaFinCarbon = Carbon::parse($fechaFin);
        $diasPeriodo = $fechaInicioCarbon->diffInDays($fechaFinCarbon) + 1;
        
        $periodoAnteriorFin = $fechaInicioCarbon->copy()->subDay();
        $periodoAnteriorInicio = $periodoAnteriorFin->copy()->subDays($diasPeriodo - 1);
        
        $atencionesAnterior = DB::connection('sqlsrv')
            ->table('Solicitudes as S')
            ->whereDate('S.Fecha_Solicitud', '>=', $periodoAnteriorInicio->format('Y-m-d'))
            ->whereDate('S.Fecha_Solicitud', '<=', $periodoAnteriorFin->format('Y-m-d'))
            ->count();
            
        $ingresosAnterior = DB::connection('sqlsrv')
            ->table('Solicitudes as S')
            ->leftJoin('Giros as G', 'S.Folio_Solicitud', '=', 'G.Folio_Solicitud')
            ->whereDate('S.Fecha_Solicitud', '>=', $periodoAnteriorInicio->format('Y-m-d'))
            ->whereDate('S.Fecha_Solicitud', '<=', $periodoAnteriorFin->format('Y-m-d'))
            ->sum('G.Total_Giro') ?? 0;

        $cambioSolicitudes = $atencionesAnterior > 0 ? (($totalSolicitudes - $atencionesAnterior) / $atencionesAnterior) * 100 : 0;
        $cambioIngresos = $ingresosAnterior > 0 ? (($ingresoTotal - $ingresosAnterior) / $ingresosAnterior) * 100 : 0;

        // DEBUG mejorado
        \Log::info('Dashboard Licencias - Datos procesados:', [
            'total_atenciones' => $totalSolicitudes,
            'solicitudes_por_dia' => $solicitudesPorDia,
            'distribucion_glosa' => $distribucionGlosa,
            'ingresos_diarios_count' => count($ingresosDiarios),
            'distribucion_edad' => $distribucionEdad
        ]);

        return view('LicenciasConducir.index', compact(
            'atenciones',
            'fechaInicio',
            'fechaFin', 
            'totalSolicitudes',
            'ingresoTotal',
            'promedioValor',
            'solicitudesPorDia',
            'solicitudesPorHora',
            'distribucionGlosa',
            'ingresosDiarios',
            'ingresosPorGlosa',
            'distribucionEdad',
            'cambioSolicitudes',
            'cambioIngresos',
            'periodoAnteriorInicio',
            'periodoAnteriorFin',
            'diasPeriodo'
        ));
    }
}