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
}
