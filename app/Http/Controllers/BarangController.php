<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;

class BarangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $barang = Barang::all();
            return DataTables::of($barang)
                               ->addIndexColumn()
                               ->addColumn('foto_barang', function($q) {
                                    return '<img src="'.asset('storage/barang/'.$q->foto_barang).'" alt="" width="50px">';
                               })
                               ->addColumn('action', 'barang.action')
                               ->rawColumns(['action', 'foto_barang'])
                               ->toJson();

        }
        return view('barang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validate = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required|unique:barangs,nama_barang,id',
            'harga'       => 'required',
            'stok'        => 'required',
            'satuan'      => 'required',
            'foto_barang' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($request->hasFile('foto_barang')) {
            $foto = $request->file('foto_barang');
            $fileUpload = time().".".$foto->getClientOriginalExtension();
            $foto->storeAs('public/barang', $fileUpload);
        }

        DB::beginTransaction();
        try {
            $validate['foto_barang'] = $fileUpload;
            $validate['created_by']  = auth()->user()->id;

            $barang = Barang::create($validate);

            ActivityLogService::log('Barang dibuat', $barang->id, [
                'barang_id' => $barang->id,
                'nama_barang' => $request->nama_barang,
                'id_user'   => auth()->user()->id,
                'action'    => 'store',
            ]);

            DB::commit();

            return response()->json(
                [
                    'code'    => 200,
                    'message' => 'Berhasil Menyimpan Barang',
                    'data'    => []
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'code'    => 500,
                    'message' => $th->getMessage(),
                    'data'    => []
                ]
            );
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function show(Barang $barang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function edit(Barang $barang)
    {
        return response()->json($barang);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Barang $barang)
    {
        $validate = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required|unique:barangs,nama_barang,'.$barang->id.',id',
            'harga'       => 'required',
            'stok'        => 'required',
            'satuan'      => 'required',
        ]);

        if ($request->hasFile('foto_barang')) {
            $request->validate([
                'foto_barang' => 'required|image|mimes:jpeg,png,jpg|max:2048'
            ]);
        }

        $fileOld = $barang->foto_barang;

        if ($request->hasFile('foto_barang')) {
            $foto = $request->file('foto_barang');
            $fileUpload = time().".".$foto->getClientOriginalExtension();

            Storage::delete('public/barang/'.$fileOld);
            $foto->storeAs('public/barang', $fileUpload);
        }

        DB::beginTransaction();
        try {
            $validate['foto_barang'] = !$request->file('foto_barang') ? $fileOld : $fileUpload;
            $validate['updated_by']  = auth()->user()->id;

            $barang->update($validate);

            ActivityLogService::log('Barang dirubah', $barang->id, [
                'barang_id'   => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'id_user'   => auth()->user()->id,
                'action'    => 'update',
            ]);

            DB::commit();

            return response()->json(
                [
                    'code'    => 200,
                    'message' => 'Berhasil Menyimpan Barang',
                    'data'    => []
                ]
            );
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(
                [
                    'code'    => 500,
                    'message' => $th->getMessage(),
                    'data'    => []
                ]
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Barang  $barang
     * @return \Illuminate\Http\Response
     */
    public function destroy(Barang $barang)
    {
        DB::beginTransaction();
        try {

            Storage::delete('public/barang/'.$barang->foto_barang);

            ActivityLogService::log('Barang dihapus', $barang->id, [
                'barang_id' => $barang->id,
                'nama_barang' => $barang->nama_barang,
                'id_user'   => auth()->user()->id,
                'action'    => 'delete',
            ]);

            $barang->delete();


            DB::commit();

            return response()->json(
                [
                    'code'    => 200,
                    'message' => 'Berhasil Menyimpan Barang',
                    'data'    => []
                ]
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json(
                [
                    'code'    => 500,
                    'message' => $th->getMessage(),
                    'data'    => []
                ]
            );
        }
    }
}
