<?php

use App\Http\Controllers\Api\HermesInboundController;
use Illuminate\Support\Facades\Route;

Route::post('/hermes/inbound', [HermesInboundController::class, 'handle'])
    ->name('api.hermes.inbound');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/n8n/sap-sync', fn () => response()->json(['status' => 'queued']))->name('api.n8n.sap-sync');
    Route::post('/n8n/report-packages/{id}/deliver', fn () => response()->json(['status' => 'queued']))->name('api.n8n.reports.deliver');
    Route::get('/n8n/tax-filings/upcoming', fn () => response()->json([]))->name('api.n8n.tax.upcoming');
    Route::get('/n8n/ratios/{reportPeriod}', fn () => response()->json([]))->name('api.n8n.ratios');
});
