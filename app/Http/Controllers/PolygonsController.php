<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\polygonsModel;

class PolygonsController extends Controller
{
    protected $polygons;

    public function __construct()
    {
        $this->polygons = new polygonsModel();
    }

    public function store(Request $request)
    {
        $request->validate([
            'geometry_polygons' => 'required',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $name_image = time() . '_polygon.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polygons,
            'image'       => $name_image,
        ];

        if ($this->polygons->create($data)) {
            return redirect()->route('map')->with('success', 'Data area berhasil disimpan.');
        }

        return redirect()->route('map')->with('error', 'Gagal menyimpan data area.');
    }
}
