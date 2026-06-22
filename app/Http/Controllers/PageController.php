<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\PointsModel;
use App\Models\PolylinesModel;
use App\Models\PolygonsModel;
use App\Models\User;

class PageController extends Controller
{
    public function home()
    {
        $jumlahPoint    = (new PointsModel)->count();
        $jumlahPolyline = (new PolylinesModel)->count();
        $jumlahPolygon  = (new PolygonsModel)->count();
        $jumlahUser     = User::count();

        return view('home', compact(
            'jumlahPoint',
            'jumlahPolyline',
            'jumlahPolygon',
            'jumlahUser'
        ));
    }

    public function map()
    {
        return view('map', ['title' => 'Peta']);
    }

    public function table()
    {
        $points = (new PointsModel)->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_X(geom::geometry) as longitude'),
            DB::raw('ST_Y(geom::geometry) as latitude')
        )->get();

        $polylines = (new PolylinesModel)->select(
            'id', 'name', 'description',
            DB::raw('ST_NPoints(geom::geometry) as jumlah_titik')
        )->get();

        $polygons = (new PolygonsModel)->select(
            'id', 'name', 'description',
            DB::raw('ST_NPoints(geom::geometry) as jumlah_titik')
        )->get();

        return view('table', [
            'title'     => 'Tabel Data Lokasi',
            'points'    => $points,
            'polylines' => $polylines,
            'polygons'  => $polygons,
        ]);
    }

    public function geojsonPoints()
    {
        return response()->json((new PointsModel)->getGeoJsonPoints());
    }

    public function geojsonPolylines()
    {
        return response()->json((new PolylinesModel)->getGeoJsonPolylines());
    }

    public function geojsonPolygons()
    {
        return response()->json((new PolygonsModel)->getGeoJsonPolygons());
    }
}
