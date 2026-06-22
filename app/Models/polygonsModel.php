<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PolygonsModel extends Model
{
    protected $table    = 'polygons_tables';
    protected $guarded  = ['id'];
    protected $fillable = ['name', 'description', 'geom', 'image'];

    public function setGeomAttribute($value)
    {
        $this->attributes['geom'] = DB::raw("ST_GeomFromText('{$value}', 4326)");
    }

    // GeoJSON semua area
    public function getGeoJsonPolygons()
    {
        $polygons = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson'),
            DB::raw('ST_Area(geom::geography) / 10000 as area_hektar')
        )->get();

        $features = $polygons->map(function ($polygon) {
            $geojsonDecoded = json_decode($polygon->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($polygon->geojson),
                'properties' => [
                    'id'          => $polygon->id,
                    'name'        => $polygon->name,
                    'description' => $polygon->description,
                    'image'       => $polygon->image,
                    'area_ha'     => round($polygon->area_hektar, 2) . ' Hektar',
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    // GeoJSON satu area berdasarkan ID
    public function getGeoJsonPolygon($id)
    {
        $polygons = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson'),
            DB::raw('ST_Area(geom::geography) / 10000 as area_hektar')
        )->where('id', $id)->get();

        $features = $polygons->map(function ($polygon) {
            $geojsonDecoded = json_decode($polygon->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($polygon->geojson),
                'properties' => [
                    'id'          => $polygon->id,
                    'name'        => $polygon->name,
                    'description' => $polygon->description,
                    'image'       => $polygon->image,
                    'area_ha'     => round($polygon->area_hektar, 2) . ' Hektar',
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }
}
