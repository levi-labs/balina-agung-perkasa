<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $title = 'Data Users';
        $data = User::all();
        return view('pages.user.index', compact('title', 'data'));
    }

    public function create()
    {
        $title = 'Form Data Users';
        return view('pages.user.create', compact('title'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required',
            'level' => 'required',
            'password' => 'required',
        ]);
        try {
            User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'level' => $request->level,
                'password' => bcrypt($request->password),
            ]);
            return redirect()->route('user')->with('success', 'Data User berhasil ditambahkan');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function edit(User $user)
    {
        $title = 'Form Data Users';
        return view('pages.user.edit', compact('title', 'user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'nama' => 'required',
            'username' => 'required',
            'level' => 'required',
        ]);
        try {
            User::where('id', $user->id)->update([
                'nama' => $request->nama,
                'username' => $request->username,
                'level' => $request->level,
            ]);
            return redirect()->route('user')->with('success', 'Data User berhasil diupdate');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }

    public function destroy(User $user)
    {
        try {
            User::destroy($user->id);
            return redirect()->route('user')->with('success', 'Data User berhasil dihapus');
        } catch (\Throwable $th) {
            return back()->with('error', $th->getMessage());
        }
    }
}
