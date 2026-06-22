<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PolylinesModel extends Model
{
    protected $table    = 'polylines_tables';
    protected $guarded  = ['id'];
    protected $fillable = ['name', 'description', 'geom', 'image'];

    // GeoJSON semua garis
    public function getGeoJsonPolylines()
    {
        $polylines = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson')
        )->get();

        $features = $polylines->map(function ($polyline) {
            $geojsonDecoded = json_decode($polyline->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($polyline->geojson),
                'properties' => [
                    'id'          => $polyline->id,
                    'name'        => $polyline->name,
                    'description' => $polyline->description,
                    'image'       => $polyline->image,
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    // GeoJSON satu garis berdasarkan ID
    public function GeoJson_Polylines($id)
    {
        $polylines = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson')
        )->where('id', $id)->get();

        $features = $polylines->map(function ($polyline) {
            $geojsonDecoded = json_decode($polyline->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($polyline->geojson),
                'properties' => [
                    'id'          => $polyline->id,
                    'name'        => $polyline->name,
                    'description' => $polyline->description,
                    'image'       => $polyline->image,
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }
}
