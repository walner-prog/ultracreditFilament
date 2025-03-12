<?php

use App\Models\Person;
use Illuminate\Support\Facades\Route;
use Vormkracht10\FilamentMails\Facades\FilamentMails;


Route::get('/', function () {
    return redirect('/admin');
});


use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Blade;
use App\Models\Role; // Asegúrate de que este modelo existe y representa los roles

Route::get('/admin/pdf-roles', function () {
    $roles = Role::all(); // Obtener todos los roles

    $pdf = Pdf::loadHTML(Blade::render('pdf-roles', ['records' => $roles]));

    return $pdf->stream('roles.pdf');
})->name('admin.pdf-roles');

use App\Http\Controllers\AbonoExportController;

Route::get('/abonos/export', [AbonoExportController::class, 'export'])->name('abonos.export');

Route::get('/abonos/exportsemana', [AbonoExportController::class, 'exportSemana'])->name('abonos.exportsemana');
