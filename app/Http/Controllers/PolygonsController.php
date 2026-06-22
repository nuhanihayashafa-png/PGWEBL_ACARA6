<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolygonsModel; // FIX: huruf kapital P, fatal error di Linux kalau lowercase

class PolygonsController extends Controller
{
    protected PolygonsModel $polygons; // FIX: tipe diseragamkan

    public function __construct(PolygonsModel $polygons) // FIX: pakai dependency injection
    {
        $this->polygons = $polygons;
    }

    // ════════════════════════════════════════════════════
    // Kembalikan satu area sebagai JSON
    // ════════════════════════════════════════════════════
    public function show(string $id)
    {
        $polygon = $this->polygons->find($id);
        return response()->json($polygon);
    }

    // ════════════════════════════════════════════════════
    // Simpan data baru
    // ════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'geometry_polygon' => 'required',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        $name_image = null;
        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $name_image = time() . '_polygon.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polygon,
            'image'       => $name_image,
        ];

        if ($this->polygons->create($data)) {
            return redirect()->route('map')->with('success', 'Data area berhasil disimpan.');
        }

        return redirect()->route('map')->with('error', 'Gagal menyimpan data area.');
    }

    // ════════════════════════════════════════════════════
    // Hapus data area
    // ════════════════════════════════════════════════════
    public function destroy(string $id)
    {
        $item = $this->polygons->find($id);

        if ($item && $item->image) {
            $path = public_path('storage/images/' . $item->image);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        if ($this->polygons->destroy($id)) {
            return redirect()->route('map')->with('success', 'Data area berhasil dihapus.');
        }

        return redirect()->route('map')->with('error', 'Gagal menghapus data area.');
    }

    // ════════════════════════════════════════════════════
    // Perbarui data area
    // ════════════════════════════════════════════════════

    public function edit(string $id)
    {
        $polygon = $this->polygons
            ->select(
                'id', 'name', 'description', 'image',
                DB::raw('ST_AsGeoJSON(geom) as geom')
            )
            ->find($id);

        if (!$polygon) {
            return redirect()->route('map')->with('error', 'Data area tidak ditemukan.');
        }

        return view('map-edit-polygons', compact('polygon'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'geometry_polygon' => 'required',
            'name'             => 'required|string|max:255',
            'description'      => 'nullable|string',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $item = $this->polygons->find($id);

        if (!$item) {
            return redirect()->route('map')->with('error', 'Data area tidak ditemukan.');
        }

        $name_image = $item->image;

        if ($request->hasFile('image')) {
            if ($item->image) {
                $oldPath = public_path('storage/images/' . $item->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image      = $request->file('image');
            $name_image = time() . '_polygon.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polygon,
            'image'       => $name_image,
        ];

        if ($item->update($data)) {
            return redirect()->route('map')->with('success', 'Data area berhasil diperbarui.');
        }

        return redirect()->route('map')->with('error', 'Gagal memperbarui data area.');
    }
}
