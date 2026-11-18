<?php

namespace App\Console\Commands;

use App\Models\Antrian;
use App\Models\Pengaturan;
use Illuminate\Console\Command;

class AnonimkanAntrian extends Command
{
    protected $signature = 'antrian:anonimkan
        {--hari= : Batas usia data dalam hari (default dari pengaturan retensi_hari)}
        {--dry-run : Hitung saja, jangan ubah data}';

    protected $description = 'Mengosongkan data pribadi pasien pada antrian yang lebih tua dari batas retensi';

    public function handle(): int
    {
        $hari = (int) ($this->option('hari') ?: Pengaturan::get('retensi_hari', 90));
        $batas = now()->subDays($hari)->toDateString();

        $query = Antrian::where('tanggal', '<', $batas)->whereNull('anonim_pada');

        if ($this->option('dry-run')) {
            $jumlah = (clone $query)->count();
            $this->info("{$jumlah} antrian lebih tua dari {$hari} hari akan dianonimkan.");

            return self::SUCCESS;
        }

        $diubah = $query->update([
            'nama_pasien' => 'Pasien',
            'nik' => null,
            'no_hp' => null,
            'keluhan' => null,
            'anonim_pada' => now(),
        ]);

        $this->info("{$diubah} antrian dianonimkan (batas {$hari} hari).");

        return self::SUCCESS;
    }
}
