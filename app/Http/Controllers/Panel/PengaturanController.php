<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    private const BIDANG = [
        'nama_puskesmas' => 'Nama puskesmas',
        'alamat' => 'Alamat',
        'telepon' => 'Telepon',
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'jam_operasional' => 'Teks jam operasional',
        'jam_buka' => 'Jam buka',
        'jam_tutup' => 'Jam tutup',
        'hari_kerja' => 'Hari kerja',
        'kepala_nama' => 'Nama kepala puskesmas',
        'kepala_nip' => 'NIP kepala puskesmas',
        'visi' => 'Visi',
        'misi' => 'Misi (satu per baris)',
        'sambutan' => 'Sambutan singkat',
    ];

    public function index()
    {
        $nilai = [];
        foreach (array_keys(self::BIDANG) as $kunci) {
            $nilai[$kunci] = Pengaturan::get($kunci, $this->bawaan($kunci));
        }

        return view('panel.pengaturan', [
            'nilai' => $nilai,
            'bidang' => self::BIDANG,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'nama_puskesmas' => ['required', 'string', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:300'],
            'telepon' => ['nullable', 'string', 'max:40'],
            'whatsapp' => ['nullable', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:100'],
            'jam_operasional' => ['nullable', 'string', 'max:100'],
            'jam_buka' => ['required', 'date_format:H:i'],
            'jam_tutup' => ['required', 'date_format:H:i', 'after:jam_buka'],
            'hari_kerja' => ['required', 'string', 'max:20'],
            'kepala_nama' => ['nullable', 'string', 'max:100'],
            'kepala_nip' => ['nullable', 'string', 'max:40'],
            'visi' => ['nullable', 'string', 'max:500'],
            'misi' => ['nullable', 'string', 'max:2000'],
            'sambutan' => ['nullable', 'string', 'max:1500'],
        ], [], [
            'nama_puskesmas' => 'nama puskesmas', 'jam_buka' => 'jam buka',
            'jam_tutup' => 'jam tutup', 'hari_kerja' => 'hari kerja',
        ]);

        foreach ($data as $kunci => $nilai) {
            Pengaturan::set($kunci, $nilai, in_array($kunci, ['jam_buka', 'jam_tutup', 'hari_kerja', 'jam_operasional'], true) ? 'operasional' : 'umum');
        }

        ActivityLog::catat('pengaturan.ubah', 'Pengaturan', 'Memperbarui pengaturan situs');

        return back()->with('sukses', 'Pengaturan disimpan.');
    }

    private function bawaan(string $kunci)
    {
        return [
            'nama_puskesmas' => 'Puskesmas Umban Sari',
            'alamat' => 'Jl. Umban Sari, Rumbai, Pekanbaru, Riau',
            'telepon' => '(0761) 000000',
            'whatsapp' => '081200000000',
            'email' => 'puskesmas.umbansari@pku.go.id',
            'jam_operasional' => 'Senin–Sabtu, 08.00–14.00',
            'jam_buka' => '08:00',
            'jam_tutup' => '14:00',
            'hari_kerja' => '1,2,3,4,5,6',
            'kepala_nama' => 'dr. Nama Kepala Puskesmas',
            'kepala_nip' => '19800101 200801 1 001',
            'visi' => 'Terwujudnya masyarakat Umban Sari yang sehat, mandiri, dan produktif.',
            'misi' => "Meningkatkan mutu pelayanan kesehatan dasar yang merata dan terjangkau.\nMemperkuat upaya promotif dan preventif di tingkat kelurahan.\nMengembangkan tata kelola puskesmas yang transparan dan akuntabel.",
            'sambutan' => 'Selamat datang di Puskesmas Umban Sari. Kami melayani dengan sepenuh hati untuk kesehatan keluarga Anda.',
        ][$kunci] ?? null;
    }
}
