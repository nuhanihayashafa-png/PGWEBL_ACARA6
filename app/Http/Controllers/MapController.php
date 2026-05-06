<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\pointsModel;
use App\Models\polylinesModel;
use App\Models\polygonsModel;

class MapController extends Controller
{
    public function map()
    {
        return view('map', ['title' => 'Peta Interaktif']);
    }

    public function table()
    {
        $points = pointsModel::select('id', 'name', 'description', 'image')->get();
        return view('table', ['title' => 'Tabel Data Lokasi', 'points' => $points]);
    }

    public function getMapData()
    {
        $points   = pointsModel::select('id', 'name', 'description', 'image',
                        DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();
        $lines    = polylinesModel::select('id', 'name', 'description', 'image',
                        DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();
        $polygons = polygonsModel::select('id', 'name', 'description', 'image',
                        DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();

        $features = $points->concat($lines)->concat($polygons)->map(function ($item) {
            return [
                'type'       => 'Feature',
                'geometry'   => json_decode($item->geojson),
                'properties' => [
                    'id'          => $item->id,
                    'name'        => $item->name,
                    'description' => $item->description,
                    'image'       => $item->image,
                ],
            ];
        });

        return response()->json(['type' => 'FeatureCollection', 'features' => $features]);
    }

    // ── GeoJSON untuk map.blade.php ($.getJSON) ──

    public function geojsonPoints()
    {
        $items = pointsModel::select('id', 'name', 'description', 'image', 'created_at',
                    DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $items->map(fn($i) => [
                'type'       => 'Feature',
                'geometry'   => json_decode($i->geojson),
                'properties' => [
                    'id'          => $i->id,
                    'name'        => $i->name,
                    'description' => $i->description,
                    'image'       => $i->image,
                    'created_at'  => $i->created_at,
                ],
            ]),
        ]);
    }

    public function geojsonPolylines()
    {
        $items = polylinesModel::select('id', 'name', 'description', 'image', 'created_at',
                    DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $items->map(fn($i) => [
                'type'       => 'Feature',
                'geometry'   => json_decode($i->geojson),
                'properties' => [
                    'id'          => $i->id,
                    'name'        => $i->name,
                    'description' => $i->description,
                    'image'       => $i->image,
                    'created_at'  => $i->created_at,
                ],
            ]),
        ]);
    }

    public function geojsonPolygons()
    {
        $items = polygonsModel::select('id', 'name', 'description', 'image', 'created_at',
                    DB::raw('ST_AsGeoJSON(geom::geometry) as geojson'))->get();

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $items->map(fn($i) => [
                'type'       => 'Feature',
                'geometry'   => json_decode($i->geojson),
                'properties' => [
                    'id'          => $i->id,
                    'name'        => $i->name,
                    'description' => $i->description,
                    'image'       => $i->image,
                    'created_at'  => $i->created_at,
                ],
            ]),
        ]);
    }
}
