<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Services\ActivityLogService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function loadLog(Request $request)
    {
        $logAktivitas = ActivityLogService::getAllLogs();
        $barang = Barang::all();

        $convertLog = collect($logAktivitas)->values()->map(function($res) use ($barang) {
            $res->barang_id   = $res->data->barang_id;
            $res->action      = $res->data->action;
            $res->nama_barang      = $res->data->nama_barang;
            $res->timestamp  = Carbon::parse($res->timestamp)->format('d-m-Y H:i:s');
            return $res;
        });

        if ($request->ajax()) {
            return DataTables::of($convertLog)
                               ->addIndexColumn()
                               ->toJson();
        }
    }

    public function guest()
    {
        $barang = Barang::all();
        return view('welcome', compact('barang'));
    }
}
