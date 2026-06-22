<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PointsModel;
use App\Models\PolylinesModel;
use App\Models\PolygonsModel;
use Illuminate\Routing\Controller;

class Apicontroller extends Controller
{
    // ── POINTS ──────────────────────────────────────────────────────────
    public function geojson_points()
    {
        $model = new PointsModel();
        return response()->json($model->getGeoJsonPoints());
    }

    public function geojson_point($id)
    {
        $model = new PointsModel();
        return response()->json($model->getGeoJsonPointById($id));
    }

    public function get_point($id)
    {
        $row = PointsModel::select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsText(geom) as geom')
        )->findOrFail($id);

        return response()->json($row);
    }

    // ── POLYLINES ────────────────────────────────────────────────────────
    public function geojson_polylines()
    {
        $model = new PolylinesModel();
        return response()->json($model->getGeoJsonPolylines());
    }

    public function geojson_polyline($id)
    {
        $model = new PolylinesModel();
        return response()->json($model->GeoJson_Polylines($id));
    }

    public function get_polyline($id)
    {
        $row = PolylinesModel::select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsText(geom) as geom')
        )->findOrFail($id);

        return response()->json($row);
    }

    // ── POLYGONS ─────────────────────────────────────────────────────────
    public function geojson_polygons()
    {
        $model = new PolygonsModel();
        return response()->json($model->getGeoJsonPolygons());
    }

    public function geojson_polygon($id)
    {
        $model = new PolygonsModel();
        // FIX: nama method sebelumnya getGeoJsonPolygonById() yang tidak ada di model
        //      yang benar dan terdefinisi adalah getGeoJsonPolygon()
        return response()->json($model->getGeoJsonPolygon($id));
    }

    public function get_polygon($id)
    {
        $row = PolygonsModel::select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsText(geom) as geom')
        )->findOrFail($id);

        return response()->json($row);
    }
}
