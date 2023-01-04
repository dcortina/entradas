<?php

use App\Http\Controllers\nuevaEntradaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\nuevoProductoController;
use App\Http\Controllers\nuevoProvedorController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsumosController;
use App\Http\Controllers\LineasController;
use App\Http\Controllers\PlanMedicamentoController;
use App\Http\Controllers\EstadisticasController;
use App\Http\Controllers\ComprobarProductos;
use App\Http\Controllers\ConduceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/registro', [UserController::class, 'registro']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/usuarios',[UserController::class,'showUser']);
Route::put('/usuarios/{id}',[UserController::class,'update']);
Route::delete('/usuarios/{id}',[UserController::class, 'destroy']);


Route::post('/entradas/productos', [nuevaEntradaController::class, 'obtenerProductos']);
Route::get('/entradas/pendientes', [nuevaEntradaController::class, 'PendienteArribo']);
Route::post('/entradas/agrega/pendientes', [nuevaEntradaController::class, 'agregaPendienteArribo']);

Route::post('/consumos/unidad', [ConsumosController::class, 'obtenerConsumos']);

Route::get('/consumos/masVendido', [ConsumosController::class, 'productoMasVendido']);

Route::get('/consumos/masVendidos', [ConsumosController::class, 'productosMasVendidos']);
Route::get('/consumos/productosMasImporte', [ConsumosController::class, 'productosMasimporte']);
Route::get('/consumos/importeTotal', [ConsumosController::class, 'importeTotal']);

Route::get('/consumos/mayorImporte', [ConsumosController::class, 'productoConMayorImporte']);
Route::get('/consumos/unidadMasActiva', [ConsumosController::class, 'unidadMasActiva']);
Route::get('/consumos/unidadMasCompra', [ConsumosController::class, 'unidadConMayorImporte']);
Route::get('/consumos/lentoMovimiento', [ConsumosController::class, 'productosLentoMovimiento']);

Route::post('/lineas/productos', [LineasController::class, 'ObtenerProductosPorLinea']);
Route::post('/lineas/actualizarPlan', [LineasController::class, 'actualizarPlan']);


Route::get('/provedores/mistral', [nuevoProvedorController::class, 'provedoresMistral']);

Route::get('/productos/mistral', [nuevoProductoController::class, 'productosMistral']);

Route::get('/productos/exportar', [nuevoProductoController::class, 'exportExcel']);

Route::get('/consumos/codigo', [ConsumosController::class, 'obtenerConsumosPorCodigo']);

Route::get('/estadisticas/planLinea', [EstadisticasController::class, 'acumuladoPorLinea']);
Route::get('/estadisticas/ultimasEntradas', [EstadisticasController::class, 'ultimasEntradas']);
Route::get('/estadisticas/diasAbastecidos', [EstadisticasController::class, 'diasAbastecidos']);
Route::get('/estadisticas/situacion', [EstadisticasController::class, 'porcientoSituacion']);

Route::get('/stock/precioPublico', [ComprobarProductos::class, 'buscarDatosDiferentesPrecios']);
Route::get('/stock/recargo', [ComprobarProductos::class, 'buscarDatosDiferenteMargen']);
Route::get('/stock/conformado', [ComprobarProductos::class, 'buscarDatosDiferenteConformado']);
Route::get('/stock/donativo', [ComprobarProductos::class, 'buscarDatosDiferenteDonativos']);
Route::get('/stock/contador', [ComprobarProductos::class, 'contador']);
Route::post('/stock/comprobarProductos', [ComprobarProductos::class, 'guardarDatos']);




Route::resource('/entradas', nuevaEntradaController::class);

Route::resource('/provedores', nuevoProvedorController::class);

Route::resource('/consumos', ConsumosController::class);

Route::resource('/demanda/plan', PlanMedicamentoController::class);

Route::resource('/productos', nuevoProductoController::class);

Route::resource('/lineas', LineasController::class);

Route::resource('/conduce', ConduceController::class);
Route::get('/conduce/productos/show', [ConduceController::class,'productosConduce']);
Route::get('/clientes', [ConduceController::class,'clientes']);
Route::get('/chofer', [ConduceController::class,'chofer']);




