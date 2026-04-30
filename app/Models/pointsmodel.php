<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointsModel extends Model
{
    protected $table = 'points';
    protected $guarded = [];
    public $timestamps = false;

    public function getGeoJsonPoints()
    {
        $points = $this->select(
            'id',
            'name',
            'description',
            'image',          // ← ditambahkan
            DB::raw('ST_AsGeoJSON(geom) as geojson')
        )->get();

        $features = $points->map(function ($point) {
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($point->geojson),
                'properties' => [
                    'id'          => $point->id,
                    'name'        => $point->name,
                    'description' => $point->description,
                    'image'       => $point->image,  // ← ditambahkan
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
