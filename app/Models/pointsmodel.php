<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PointsModel extends Model
{
    protected $table      = 'points';
    protected $guarded    = [];
    public    $timestamps = false;
    protected $fillable   = ['name', 'description', 'geom', 'image'];

    // GeoJSON semua titik
    public function setGeomAttribute($value)
    {
        $this->attributes['geom'] = DB::raw("ST_GeomFromText('{$value}', 4326)");
    }
    
    public function getGeoJsonPoints()
    {
        $points = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson')
        )->get();

        $features = $points->map(function ($point) {
            $geojsonDecoded = json_decode($point->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($point->geojson),
                'properties' => [
                    'id'          => $point->id,
                    'name'        => $point->name,
                    'description' => $point->description,
                    'image'       => $point->image,
                    // geom dikirim sebagai array PHP, bukan string,
                    // agar JS tidak perlu double-parse
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }

    // GeoJSON satu titik berdasarkan ID
    public function getGeoJsonPointById($id)
    {
        $points = $this->select(
            'id', 'name', 'description', 'image',
            DB::raw('ST_AsGeoJSON(geom) as geojson')
        )->where('id', $id)->get();

        $features = $points->map(function ($point) {
            $geojsonDecoded = json_decode($point->geojson, true);
            return [
                'type'     => 'Feature',
                'geometry' => json_decode($point->geojson),
                'properties' => [
                    'id'          => $point->id,
                    'name'        => $point->name,
                    'description' => $point->description,
                    'image'       => $point->image,
                    'geom'        => $geojsonDecoded,
                ],
            ];
        });

        return ['type' => 'FeatureCollection', 'features' => $features];
    }
}
