<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- Tambahan pada tabel users bawaan Laravel ---
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('petugas')->after('email'); // admin | petugas
            $table->boolean('is_active')->default(true)->after('role');
            $table->string('jabatan')->nullable()->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('jabatan');
        });

        // --- Poli / unit layanan ---
        Schema::create('polis', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('ikon')->default('stethoscope');
            $table->string('warna')->default('#12796A');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // --- Layanan ---
        Schema::create('layanans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->nullable()->constrained('polis')->nullOnDelete();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->string('ringkasan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('persyaratan')->nullable();   // array string
            $table->unsignedSmallInteger('durasi_menit')->default(15);
            $table->boolean('butuh_rujukan')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        // --- Dokter ---
        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poli_id')->nullable()->constrained('polis')->nullOnDelete();
            $table->string('nama');
            $table->string('gelar')->nullable();
            $table->string('sip')->nullable();
            $table->text('bio')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();
        });

        // --- Jadwal praktik (hari: 0=Minggu .. 6=Sabtu) ---
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->foreignId('poli_id')->nullable()->constrained('polis')->nullOnDelete();
            $table->unsignedTinyInteger('hari');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->unsignedSmallInteger('kuota')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['hari', 'is_active']);
        });

        // --- Antrian ---
        Schema::create('antrians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('poli_id')->constrained('polis')->cascadeOnDelete();
            $table->unsignedSmallInteger('nomor');
            $table->string('kode', 12)->unique();
            $table->string('nama_pasien');
            $table->string('nik', 16)->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('jenis_kelamin', 1)->nullable(); // L | P
            $table->text('keluhan')->nullable();
            $table->string('status')->default('menunggu'); // menunggu|dipanggil|selesai|lewat|batal
            $table->string('sumber')->default('online');   // online | loket
            $table->timestamp('dipanggil_at')->nullable();
            $table->timestamp('selesai_at')->nullable();
            $table->timestamps();
            $table->unique(['tanggal', 'poli_id', 'nomor']);
            $table->index(['tanggal', 'status']);
        });

        // --- Pengumuman ---
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('kategori')->default('umum'); // umum|jadwal|kesehatan|layanan
            $table->string('ringkasan')->nullable();
            $table->longText('isi');
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // --- FAQ ---
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('pertanyaan');
            $table->text('jawaban');
            $table->string('kategori')->default('umum');
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // --- Pengaturan situs (key/value) ---
        Schema::create('pengaturans', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('grup')->default('umum');
            $table->timestamps();
        });

        // --- Log aktivitas ---
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('aksi');           // created | updated | deleted | login | called ...
            $table->string('subjek')->nullable();
            $table->string('deskripsi')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('pengaturans');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('pengumumans');
        Schema::dropIfExists('antrians');
        Schema::dropIfExists('jadwals');
        Schema::dropIfExists('dokters');
        Schema::dropIfExists('layanans');
        Schema::dropIfExists('polis');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active', 'jabatan']);
        });
    }
};
