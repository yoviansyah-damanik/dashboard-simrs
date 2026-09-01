<?php

namespace App\Livewire\Sirs;

use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        $formulir = [
            'dasar' => [
                ['kode' => 'RL 1.1', 'judul' => 'Data Dasar RS', 'route' => 'sirs.rl11', 'desc' => 'Identitas dan profil rumah sakit'],
                ['kode' => 'RL 1.2', 'judul' => 'Indikator Pelayanan', 'route' => 'sirs.rl12', 'desc' => 'BOR, ALOS, TOI, dst tahunan seluruh RS'],
                ['kode' => 'RL 1.3', 'judul' => 'Fasilitas Tempat Tidur', 'route' => 'sirs.rl13', 'desc' => 'Jumlah tempat tidur per kelas perawatan'],
                ['kode' => 'RL 2', 'judul' => 'Ketenagaan', 'route' => 'sirs.rl2', 'desc' => 'Rekap SDM per jenis tenaga & status kepegawaian'],
            ],
            'bulanan' => [
                ['kode' => 'RL 3.1', 'judul' => 'Indikator Pelayanan', 'route' => 'sirs.rl31', 'desc' => 'BOR, ALOS, BTO, TOI, NDR, GDR'],
                ['kode' => 'RL 3.2', 'judul' => 'Rawat Inap', 'route' => 'sirs.rl32', 'desc' => 'Rekapitulasi kegiatan pelayanan rawat inap'],
                ['kode' => 'RL 3.3', 'judul' => 'Rawat Darurat', 'route' => 'sirs.rl33', 'desc' => 'Pelayanan rawat darurat/IGD'],
                ['kode' => 'RL 3.4', 'judul' => 'Pengunjung', 'route' => 'sirs.rl34', 'desc' => 'Rekapitulasi pengunjung rumah sakit'],
                ['kode' => 'RL 3.5', 'judul' => 'Kunjungan Rajal', 'route' => 'sirs.rl35', 'desc' => 'Kunjungan rawat jalan per poliklinik'],
                ['kode' => 'RL 3.6', 'judul' => 'Kebidanan', 'route' => 'sirs.rl36', 'desc' => 'Pelayanan kebidanan'],
                ['kode' => 'RL 3.7', 'judul' => 'Neonatal', 'route' => 'sirs.rl37', 'desc' => 'Neonatal/Bayi/Balita'],
                ['kode' => 'RL 3.8', 'judul' => 'Laboratorium', 'route' => 'sirs.rl38', 'desc' => 'Pemeriksaan laboratorium'],
                ['kode' => 'RL 3.9', 'judul' => 'Radiologi', 'route' => 'sirs.rl39', 'desc' => 'Pemeriksaan radiologi'],
                ['kode' => 'RL 3.10', 'judul' => 'Rujukan', 'route' => 'sirs.rl310', 'desc' => 'Rujukan masuk & keluar'],
                ['kode' => 'RL 3.12', 'judul' => 'Pembedahan', 'route' => 'sirs.rl312', 'desc' => 'Pelayanan pembedahan'],
                ['kode' => 'RL 3.14', 'judul' => 'Pelayanan Khusus', 'route' => 'sirs.rl314', 'desc' => 'Hemodialisa & operasi'],
            ],
            'tahunan' => [
                ['kode' => 'RL 3.11', 'judul' => 'Gigi & Mulut', 'route' => 'sirs.rl311', 'desc' => 'Pelayanan gigi dan mulut'],
                ['kode' => 'RL 3.13', 'judul' => 'Rehab Medik', 'route' => 'sirs.rl313', 'desc' => 'Rehabilitasi medik'],
                ['kode' => 'RL 3.15', 'judul' => 'Kesehatan Jiwa', 'route' => 'sirs.rl315', 'desc' => 'Pelayanan kesehatan jiwa'],
                ['kode' => 'RL 3.16', 'judul' => 'Keluarga Berencana', 'route' => 'sirs.rl316', 'desc' => 'Pelayanan KB'],
                ['kode' => 'RL 3.17', 'judul' => 'Farmasi Pengadaan', 'route' => 'sirs.rl317', 'desc' => 'Pengadaan obat'],
                ['kode' => 'RL 3.18', 'judul' => 'Farmasi Resep', 'route' => 'sirs.rl318', 'desc' => 'Resep obat'],
                ['kode' => 'RL 3.19', 'judul' => 'Cara Bayar', 'route' => 'sirs.rl319', 'desc' => 'Cara bayar pasien'],
            ],
            'penyakit' => [
                ['kode' => 'RL 4.1', 'judul' => 'Morbiditas Ranap', 'route' => 'sirs.rl41', 'desc' => 'Penyakit rawat inap per kelompok umur'],
                ['kode' => 'RL 4.2', 'judul' => '10 Besar Ranap', 'route' => 'sirs.rl42', 'desc' => '10 besar penyakit rawat inap'],
                ['kode' => 'RL 4.3', 'judul' => '10 Besar Kematian', 'route' => 'sirs.rl43', 'desc' => '10 besar penyebab kematian'],
                ['kode' => 'RL 5.1', 'judul' => 'Morbiditas Rajal', 'route' => 'sirs.rl51', 'desc' => 'Penyakit rawat jalan per kelompok umur'],
                ['kode' => 'RL 5.2', 'judul' => '10 Besar Rajal', 'route' => 'sirs.rl52', 'desc' => '10 besar penyakit rawat jalan kasus baru'],
                ['kode' => 'RL 5.3', 'judul' => '10 Besar Kunjungan', 'route' => 'sirs.rl53', 'desc' => '10 besar kunjungan rawat jalan'],
            ],
        ];

        return view('pages.sirs.index', compact('formulir'))->title('SIRS Online');
    }
}
