<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

use function Ramsey\Uuid\v1;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = User::with('roles')->get();

            return DataTables::of($user)
                              ->addIndexColumn()
                              ->addColumn('action', 'user.action')
                              ->toJson();
        }

        $role = Role::all();
        return view('user.index', compact('role'));
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
            'name'  => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'role'  => 'required',
            'password' => 'required|min:6',
        ]);

        $validate['password'] = bcrypt($validate['password']);

        $user = User::create($validate);
        $user->assignRole($request->role);

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil simpan user',
            'data' => []
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return response()->json($user->with('roles')->findOrFail($user->id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $validate = $request->validate([
            'name'  => 'required',
            'email' => 'required|email:rfc,dns|unique:users,email,'.$user->id.',id',
            'role'  => 'required',

        ]);

        if ($request->password) {
            $request->validate(['password' => 'required|min:6',]);

            $validate['password'] = bcrypt($validate['password']);
        }

        DB::beginTransaction();
        try {

            $user->roles()->detach($user->id);
            $user->update($validate);
            $user->assignRole($request->role);

            DB::commit();
            return response()->json([
                'code' => 200,
                'message' => 'Berhasil rubah user',
                'data' => []
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();
            return response()->json([
                'code' => 200,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->roles()->detach($user->id);
        $user->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Berhasil menghapus user',
            'data' => []
        ]);

    }
}
