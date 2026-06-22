<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Apicontroller;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ── POINTS ──────────────────────────────────────────────────────────
// FIX: nama route diubah jadi 'api.geojson.points' agar tidak konflik
//      dengan 'geojson.points' di web.php
Route::get('/points',     [Apicontroller::class, 'geojson_points'])->name('api.geojson.points');
Route::get('/point/{id}', [Apicontroller::class, 'geojson_point'])->name('api.geojson.point');
Route::get('/points/{id}',[Apicontroller::class, 'get_point']);

// ── POLYLINES ────────────────────────────────────────────────────────
Route::get('/polylines',      [Apicontroller::class, 'geojson_polylines'])->name('api.geojson.polylines');
Route::get('/polylines/{id}', [Apicontroller::class, 'get_polyline']);

// ── POLYGONS ─────────────────────────────────────────────────────────
Route::get('/polygons',      [Apicontroller::class, 'geojson_polygons'])->name('api.geojson.polygons');
Route::get('/polygons/{id}', [Apicontroller::class, 'get_polygon']);
