<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class polygonsModel extends Model
{
    protected $table = 'polygons_tables';
    protected $guarded = ['id'];

    public function getGeoJsonPolygons()
    {
        $polygons = $this->select(
            'id',
            'name',
            'description',
            'image',                                                        // ← ditambahkan
            DB::raw('ST_AsGeoJSON(geom) as geojson'),
            DB::raw('ST_Area(geom::geography) / 10000 as area_hektar')
        )->get();

        $features = $polygons->map(function ($polygon) {
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($polygon->geojson),
                'properties' => [
                    'id'          => $polygon->id,
                    'name'        => $polygon->name,
                    'description' => $polygon->description,
                    'image'       => $polygon->image,                       // ← ditambahkan
                    'area_ha'     => round($polygon->area_hektar, 2) . ' Hektar',
                ]
            ];
        });

        return [
            'type'     => 'FeatureCollection',
            'features' => $features
        ];
    }
    protected $fillable = ['name', 'description', 'geom', 'image'];
}
