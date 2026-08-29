<?php

use App\Http\Controllers\Admin\ReportPeriodController;
use App\Http\Controllers\Api\HermesInboundController;
use App\Http\Controllers\Api\ImportPreviewController;
use App\Http\Controllers\Api\InsightsApiController;
use App\Http\Controllers\Api\N8n\RatioExportController;
use App\Http\Controllers\Api\N8n\ReportDeliveryController;
use App\Http\Controllers\Api\N8n\SapSyncController;
use App\Http\Controllers\Api\N8n\TaxFilingRadarController;
use App\Http\Controllers\Api\PnlDataController;
use App\Http\Controllers\Intelligence\InsightsController;
use App\Http\Controllers\Reports\ApprovalStepController;
use App\Http\Controllers\Reports\ReportPackageController;
use Illuminate\Support\Facades\Route;

Route::post('/hermes/inbound', [HermesInboundController::class, 'handle'])
    ->middleware('signature.hermes')
    ->name('api.hermes.inbound');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/pnl/sites/{projectSite}/{reportPeriod}', [PnlDataController::class, 'site'])
        ->middleware('permission:pnl.view-own-site|pnl.view-all-sites')
        ->name('api.pnl.site');

    Route::get('/pnl/consolidated/{reportPeriod}', [PnlDataController::class, 'consolidated'])
        ->middleware('permission:pnl.view-all-sites')
        ->name('api.pnl.consolidated');

    Route::post('/imports/{importBatch}/preview', [ImportPreviewController::class, 'show'])
        ->name('api.imports.preview');

    Route::get('/insights/{reportPeriod}', [InsightsApiController::class, 'index'])
        ->name('api.insights');

    Route::middleware('ability:n8n')->prefix('n8n')->group(function () {
        Route::get('/tax-filings/upcoming', [TaxFilingRadarController::class, 'index'])
            ->name('api.n8n.tax.upcoming');

        Route::post('/sap-sync', [SapSyncController::class, 'trigger'])
            ->name('api.n8n.sap-sync');

        Route::post('/report-packages/{reportPackage}/deliver', [ReportDeliveryController::class, 'deliver'])
            ->name('api.n8n.reports.deliver');

        Route::get('/ratios/{reportPeriod}', [RatioExportController::class, 'index'])
            ->name('api.n8n.ratios');
    });
});
