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
        $fechaFin = $request->input('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        // Consulta principal de atenciones
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
        // DEBUG: Logging mejorado
        \Log::info('Dashboard Licencias - Debug Info:', [
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin,
            'total_atenciones' => $atenciones->count(),
            'primera_atencion' => $atenciones->first(),
            'sample_query' => "SELECT COUNT(*) FROM Solicitudes WHERE CAST(Fecha_Solicitud AS DATE) BETWEEN '$fechaInicio' AND '$fechaFin'"
        ]);

        // DEBUG: Verificar si hay datos
        if ($atenciones->isEmpty()) {
            \Log::info('No se encontraron atenciones para el período: ' . $fechaInicio . ' a ' . $fechaFin);
        } else {
            \Log::info('Atenciones encontradas: ' . $atenciones->count());
        }

        // KPI 1: Resumen General
        $totalSolicitudes = $atenciones->count();
        $ingresoTotal = $atenciones->sum('Total_Giro');
        $promedioValor = $totalSolicitudes > 0 ? $ingresoTotal / $totalSolicitudes : 0;

        // KPI 2: Distribución por Sexo
        $distribucionSexo = $atenciones
            ->groupBy('Sexo')
            ->map(function ($group) {
                return $group->count();
            });

        // KPI 3: Distribución por Comuna
        $distribucionComuna = $atenciones
            ->groupBy('Comuna')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc()
            ->take(10);

        // KPI 4: Solicitudes por Día de la Semana
        $solicitudesPorDia = $atenciones
            ->groupBy(function ($item) {
                return Carbon::parse($item->Fecha_Solicitud)->format('l');
            })
            ->map(function ($group) {
                return $group->count();
            });

        // KPI 5: Solicitudes por Hora
        $solicitudesPorHora = $atenciones
            ->groupBy('Hora')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortKeys();

        // KPI 6: Distribución por Tipo de Solicitud (Glosa)
        $distribucionGlosa = $atenciones
            ->groupBy('Glosa')
            ->map(function ($group) {
                return $group->count();
            })
            ->sortDesc();

        // KPI 7: Ingresos Diarios
        $ingresosDiarios = $atenciones
            ->groupBy(function ($item) {
                return Carbon::parse($item->Fecha_Solicitud)->format('Y-m-d');
            })
            ->map(function ($group) {
                return $group->sum('Total_Giro');
            });

        // KPI 8: Ingresos por Tipo de Solicitud
        $ingresosPorGlosa = $atenciones
            ->groupBy('Glosa')
            ->map(function ($group) {
                return $group->sum('Total_Giro');
            })
            ->sortDesc();

        // KPI 9: Distribución por Edad
        $distribucionEdad = $atenciones
            ->map(function ($item) {
                if (!$item->Fecha_Nacimiento) return null;
                return Carbon::parse($item->Fecha_Nacimiento)->age;
            })
            ->filter()
            ->groupBy(function ($edad) {
                if ($edad < 25) return '18-24';
                else if ($edad < 35) return '25-34';
                else if ($edad < 45) return '35-44';
                else if ($edad < 55) return '45-54';
                else if ($edad < 65) return '55-64';
                else return '65+';
            })
            ->map(function ($group) {
                return $group->count();
            });

        // Calcular período anterior de manera más intuitiva
        $fechaInicioCarbon = Carbon::parse($fechaInicio);
        $fechaFinCarbon = Carbon::parse($fechaFin);
        $diasPeriodo = $fechaInicioCarbon->diffInDays($fechaFinCarbon) + 1;
        
        // El período anterior termina un día antes del inicio del período actual
        $periodoAnteriorFin = $fechaInicioCarbon->copy()->subDay();
        // El período anterior inicia los mismos días hacia atrás
        $periodoAnteriorInicio = $periodoAnteriorFin->copy()->subDays($diasPeriodo - 1);
        
        $periodoAnteriorInicioStr = $periodoAnteriorInicio->format('Y-m-d');
        $periodoAnteriorFinStr = $periodoAnteriorFin->format('Y-m-d');

        $atencionesAnterior = DB::connection('sqlsrv')
            ->table('Solicitudes as S')
            ->leftJoin('Personas as P', 'S.Rut', '=', 'P.Rut')
            ->leftJoin('Giros as G', 'S.Folio_Solicitud', '=', 'G.Folio_Solicitud')
            ->whereBetween('S.Fecha_Solicitud', [$periodoAnteriorInicioStr . ' 00:00:00', $periodoAnteriorFinStr . ' 23:59:59'])
            ->count();
            
        $ingresosAnterior = DB::connection('sqlsrv')
            ->table('Solicitudes as S')
            ->leftJoin('Giros as G', 'S.Folio_Solicitud', '=', 'G.Folio_Solicitud')
            ->whereBetween('S.Fecha_Solicitud', [$periodoAnteriorInicioStr . ' 00:00:00', $periodoAnteriorFinStr . ' 23:59:59'])
            ->sum('G.Total_Giro');

        $cambioSolicitudes = $atencionesAnterior > 0 ? (($totalSolicitudes - $atencionesAnterior) / $atencionesAnterior) * 100 : 0;
        $cambioIngresos = $ingresosAnterior > 0 ? (($ingresoTotal - $ingresosAnterior) / $ingresosAnterior) * 100 : 0;

        return view('LicenciasConducir.index', [
            'atenciones' => $atenciones,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'totalSolicitudes' => $totalSolicitudes,
            'ingresoTotal' => $ingresoTotal,
            'promedioValor' => $promedioValor,
            'distribucionSexo' => $distribucionSexo,
            'distribucionComuna' => $distribucionComuna,
            'solicitudesPorDia' => $solicitudesPorDia,
            'solicitudesPorHora' => $solicitudesPorHora,
            'distribucionGlosa' => $distribucionGlosa,
            'ingresosDiarios' => $ingresosDiarios,
            'ingresosPorGlosa' => $ingresosPorGlosa,
            'distribucionEdad' => $distribucionEdad,
            'cambioSolicitudes' => $cambioSolicitudes,
            'cambioIngresos' => $cambioIngresos,
            'periodoAnteriorInicio' => $periodoAnteriorInicioStr,
            'periodoAnteriorFin' => $periodoAnteriorFinStr,
            'diasPeriodo' => $diasPeriodo,
        ]);
    }
}