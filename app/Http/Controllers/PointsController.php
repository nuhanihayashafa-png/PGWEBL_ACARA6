<?php

namespace App\Http\Controllers;

use App\Models\PointsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // FIX: tambah import DB untuk ST_AsText

class PointsController extends Controller
{
    protected PointsModel $points;

    public function __construct(PointsModel $points)
    {
        $this->points = $points;
    }

    // ════════════════════════════════════════════════════
    // Simpan data baru
    // ════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'geometry_point' => 'required',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        $image_old = null;
        if ($request->hasFile('image')) {
            $image     = $request->file('image');
            $image_old = time() . '_point.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $image_old);
        }

        $data = [
            'geom'        => $request->geometry_point,
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $image_old,
        ];

        if ($this->points->create($data)) {
            return redirect()->route('map')->with('success', 'Data titik berhasil disimpan.');
        }

        return redirect()->route('map')->with('error', 'Gagal menyimpan data titik.');
    }

    // ════════════════════════════════════════════════════
    // FIX: Halaman edit tersendiri (map-edit-point.blade.php)
    // Mengembalikan geom dalam format WKT agar JS bisa parse koordinatnya
    // ════════════════════════════════════════════════════
    public function edit(string $id)
    {
        $point = $this->points
            ->select(
                'id', 'name', 'description', 'image',
                DB::raw('ST_AsText(geom) as geom') // WKT: POINT(lng lat)
            )
            ->find($id);

        if (!$point) {
            return redirect()->route('map')->with('error', 'Data titik tidak ditemukan.');
        }

        return view('map-edit-point', compact('point'));
    }

    // ════════════════════════════════════════════════════
    // Kembalikan satu titik sebagai JSON
    // FIX: pakai ST_AsText agar tidak mengembalikan hex WKB mentah
    // ════════════════════════════════════════════════════
    public function show($id)
    {
        $point = $this->points
            ->select(
                'id', 'name', 'description', 'image',
                DB::raw('ST_AsText(geom) as geom')
            )
            ->find($id);

        return response()->json($point);
    }

    // ════════════════════════════════════════════════════
    // Perbarui data titik
    // ════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $request->validate([
            'geometry_point' => 'required',
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $existingPoint = $this->points->find($id);

        if (!$existingPoint) {
            return redirect()->route('map')->with('error', 'Data titik tidak ditemukan.');
        }

        $image_old = $existingPoint->image;

        if ($request->hasFile('image')) {
            if ($existingPoint->image) {
                $oldPath = public_path('storage/images/' . $existingPoint->image);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            $image     = $request->file('image');
            $image_old = time() . '_point.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $image_old);
        }

        $data = [
            'geom'        => $request->geometry_point,
            'name'        => $request->name,
            'description' => $request->description,
            'image'       => $image_old,
        ];

        if ($existingPoint->update($data)) {
            return redirect()->route('map')->with('success', 'Data titik berhasil diperbarui.');
        }

        return redirect()->route('map')->with('error', 'Gagal memperbarui data titik.');
    }

    // ════════════════════════════════════════════════════
    // Hapus data titik
    // ════════════════════════════════════════════════════
    public function destroy(string $id)
    {
        $point = $this->points->find($id);

        if ($point && $point->image) {
            $imagePath = public_path('storage/images/' . $point->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        if ($this->points->destroy($id)) {
            return redirect()->route('map')->with('success', 'Data titik berhasil dihapus.');
        }

        return redirect()->route('map')->with('error', 'Gagal menghapus data titik.');
    }
}
