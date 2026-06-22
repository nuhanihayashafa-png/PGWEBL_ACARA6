<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PolylinesModel; // FIX: huruf kapital P, fatal error di Linux kalau lowercase

class PolylinesController extends Controller
{
    protected PolylinesModel $polylines; // FIX: tipe diseragamkan

    public function __construct(PolylinesModel $polylines) // FIX: pakai dependency injection
    {
        $this->polylines = $polylines;
    }

    // ════════════════════════════════════════════════════
    // Ambil semua garis (JSON)
    // ════════════════════════════════════════════════════
    public function index()
    {
        $data = $this->polylines->all();
        return response()->json($data);
    }

    // ════════════════════════════════════════════════════
    // Simpan data baru
    // ════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'geometry_polylines' => 'required',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if (!is_dir('storage/images')) {
            mkdir('./storage/images', 0777, true);
        }

        $name_image = null;
        if ($request->hasFile('image')) {
            $image      = $request->file('image');
            $name_image = time() . '_polyline.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polylines,
            'image'       => $name_image,
        ];

        if ($this->polylines->create($data)) {
            return redirect()->route('map')->with('success', 'Data garis berhasil disimpan.');
        }

        return redirect()->route('map')->with('error', 'Gagal menyimpan data garis.');
    }

    // ════════════════════════════════════════════════════
    // Halaman edit tersendiri (map-edit-polylines.blade.php)
    // ════════════════════════════════════════════════════
    public function edit(string $id)
    {
        $polylines = $this->polylines->find($id);

        if (!$polylines) {
            return redirect()->route('map')->with('error', 'Data tidak ditemukan.');
        }

        return view('map-edit-polylines', compact('polylines'));
    }

    // ════════════════════════════════════════════════════
    // Kembalikan satu garis sebagai JSON
    // ════════════════════════════════════════════════════
    public function show(string $id)
    {
        $polylines = $this->polylines->find($id);
        return response()->json($polylines);
    }

    // ════════════════════════════════════════════════════
    // Perbarui data garis
    // ════════════════════════════════════════════════════
    public function update(Request $request, string $id)
    {
        $request->validate([
            'geometry_polylines' => 'required',
            'name'               => 'required|string|max:255',
            'description'        => 'nullable|string',
            'image'              => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $item = $this->polylines->find($id);

        if (!$item) {
            return redirect()->route('map')->with('error', 'Data tidak ditemukan.');
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
            $name_image = time() . '_polyline.' . strtolower($image->getClientOriginalExtension());
            $image->move('storage/images', $name_image);
        }

        $data = [
            'name'        => $request->name,
            'description' => $request->description,
            'geom'        => $request->geometry_polylines,
            'image'       => $name_image,
        ];

        if ($item->update($data)) {
            return redirect()->route('map')->with('success', 'Data garis berhasil diperbarui.');
        }

        return redirect()->route('map')->with('error', 'Gagal memperbarui data garis.');
    }

    // ════════════════════════════════════════════════════
    // Hapus data garis
    // ════════════════════════════════════════════════════
    public function destroy(string $id)
    {
        $item = $this->polylines->find($id);

        if (!$item) {
            return redirect()->route('map')->with('error', 'Data tidak ditemukan.');
        }

        if ($item->image) {
            $oldPath = public_path('storage/images/' . $item->image);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        if ($item->delete()) {
            return redirect()->route('map')->with('success', 'Data garis berhasil dihapus.');
        }

        return redirect()->route('map')->with('error', 'Gagal menghapus data garis.');
    }
}
