<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\ReportController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas da API
// Gerenciamento de Estoque
Route::post('/inventory', [InventoryController::class, 'store']);
Route::get('/inventory', [InventoryController::class, 'index']);

// Processamento de Vendas
Route::post('/sales', [SaleController::class, 'store']);
Route::get('/sales/{id}', [SaleController::class, 'show']);

// Relatórios
Route::get('/reports/sales', [ReportController::class, 'sales']);
