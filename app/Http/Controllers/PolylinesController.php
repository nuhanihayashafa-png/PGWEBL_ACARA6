<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\polylinesModel;

class PolylinesController extends Controller
{
    protected $polylines;

    public function __construct()
    {
        $this->polylines = new polylinesModel();
    }

    public function store(Request $request)
    {
        $request->validate([
            'geometry_polyline' => 'required',
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $name_image = time() . '_polyline.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        } else {
            $name_image = null;
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polyline,
            'image'       => $name_image,
        ];

        if ($this->polylines->create($data)) {
            return redirect()->route('map')->with('success', 'Data garis berhasil disimpan.');
        }

        return redirect()->route('map')->with('error', 'Gagal menyimpan data garis.');
    }

    public function destroy(string $id)
    {
        $item = $this->polylines->find($id);

        if ($item && $item->image) {
            $path = public_path('storage/images/' . $item->image);
            if (file_exists($path)) unlink($path);
        }

        if ($this->polylines->destroy($id)) {
            return redirect()->route('map')->with('success', 'Data garis berhasil dihapus.');
        }

        return redirect()->route('map')->with('error', 'Gagal menghapus data garis.');
    }
}
