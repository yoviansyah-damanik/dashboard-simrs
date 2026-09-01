<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Kolom sessions.user_id sempat dibuat sebagai bigint unsigned (foreignId()), padahal
     * users.id bertipe uuid. Pada koneksi default yang strict mode, ini membuat penulisan
     * sesi pengguna yang sedang login gagal/tidak konsisten (memicu masalah CSRF/session
     * setelah login). Migration ini memperbaiki tipe kolom tersebut agar sesuai dengan uuid.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }

        // Baris sesi lama dengan user_id bigint sudah tidak valid/berguna, kosongkan agar
        // tabel bisa diubah tipenya dengan aman. Pengguna yang sedang login akan diminta
        // login ulang, ini wajar dan diharapkan setelah perbaikan tipe kolom.
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->index()->after('id');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('sessions')) {
            return;
        }

        Schema::table('sessions', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::table('sessions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->index()->after('id');
        });
    }
};
