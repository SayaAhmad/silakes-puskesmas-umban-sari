<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index()
    {
        return view('panel.pengguna', [
            'pengguna' => User::orderBy('role')->orderBy('name')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('panel.pengguna-form', [
            'pengguna' => new User(['role' => 'petugas', 'is_active' => true]),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'jabatan' => ['nullable', 'string', 'max:80'],
            'role' => ['required', 'in:admin,petugas'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama', 'email' => 'email', 'role' => 'peran',
            'password' => 'kata sandi', 'jabatan' => 'jabatan',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        User::create($data);
        ActivityLog::catat('pengguna.buat', $data['email'], 'Menambah pengguna panel');

        return redirect()->route('panel.pengguna.index')->with('sukses', "Pengguna {$data['name']} ditambahkan.");
    }

    public function edit(User $pengguna)
    {
        return view('panel.pengguna-form', ['pengguna' => $pengguna]);
    }

    public function update(Request $request, User $pengguna)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($pengguna->id)],
            'jabatan' => ['nullable', 'string', 'max:80'],
            'role' => ['required', 'in:admin,petugas'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [], [
            'name' => 'nama', 'email' => 'email', 'role' => 'peran', 'password' => 'kata sandi',
        ]);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // Jangan sampai admin terakhir menonaktifkan/menurunkan dirinya sendiri
        if ($pengguna->id === auth()->id()) {
            $data['role'] = $pengguna->role;
            $data['is_active'] = true;
        } else {
            $data['is_active'] = $request->boolean('is_active');
        }

        $pengguna->update($data);
        ActivityLog::catat('pengguna.ubah', $pengguna->email, 'Memperbarui pengguna panel');

        return redirect()->route('panel.pengguna.index')->with('sukses', "Pengguna {$pengguna->name} diperbarui.");
    }

    public function destroy(User $pengguna)
    {
        if ($pengguna->id === auth()->id()) {
            return back()->with('info', 'Anda tidak dapat menghapus akun sendiri.');
        }

        if (User::where('role', 'admin')->count() <= 1 && $pengguna->isAdmin()) {
            return back()->with('info', 'Minimal harus ada satu administrator aktif.');
        }

        $email = $pengguna->email;
        $pengguna->delete();
        ActivityLog::catat('pengguna.hapus', $email, 'Menghapus pengguna panel');

        return back()->with('sukses', "Pengguna {$email} dihapus.");
    }
}
