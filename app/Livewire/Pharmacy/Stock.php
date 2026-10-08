<?php

namespace App\Livewire\Pharmacy;

use App\Helpers\SirsHelper;
use App\Repository\PharmacyStockRepository;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Stock extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusStok = 'semua'; // 'semua' | 'menipis' | 'habis' | 'aman' | 'near_expired' | 'expired'

    #[Url]
    public string $depo = 'semua';

    #[Url]
    public string $kategori = 'semua';

    #[Url]
    public string $sortField = 'stok';

    #[Url]
    public string $sortDirection = 'asc';

    public int $perPage = 25;

    public ?string $selectedDrugKode = null;
    public ?array $selectedDrugDetail = null;
    public bool $detailModalOpen = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatusStok(): void
    {
        $this->resetPage();
    }

    public function updatedDepo(): void
    {
        $this->resetPage();
    }

    public function updatedKategori(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function setStatusFilter(string $status): void
    {
        $this->statusStok = $status;
        $this->resetPage();
    }

    public function openDetail(string $kode): void
    {
        $detail = PharmacyStockRepository::getDetailByKode($kode);
        if (!$detail) {
            $this->closeDetail();
            return;
        }

        $this->selectedDrugKode = $kode;
        $this->selectedDrugDetail = $detail;
        $this->detailModalOpen = true;
    }

    public function closeDetail(): void
    {
        $this->detailModalOpen = false;
        $this->selectedDrugKode = null;
        $this->selectedDrugDetail = null;
    }

    public function render()
    {
        $filters = [
            'search' => $this->search,
            'status_stok' => $this->statusStok,
            'depo' => $this->depo,
            'kategori' => $this->kategori,
        ];

        $summary = PharmacyStockRepository::getSummary($this->depo, $this->kategori);
        $stockList = PharmacyStockRepository::getPaginated(
            $filters,
            $this->perPage,
            $this->sortField,
            $this->sortDirection
        );
        $profil = SirsHelper::getProfilRS();

        return view('pages.pharmacy.stock', [
            'summary' => $summary,
            'stockList' => $stockList,
            'profil' => $profil,
        ])->title('Rekap Stok Obat Farmasi');
    }

    /**
     * Ekspor rekapitulasi stok obat ke format PDF resmi
     */
    public function exportPdf()
    {
        set_time_limit(0);

        $filters = [
            'search' => $this->search,
            'status_stok' => $this->statusStok,
            'depo' => $this->depo,
            'kategori' => $this->kategori,
        ];

        $summary = PharmacyStockRepository::getSummary($this->depo, $this->kategori);
        // Ambil data untuk laporan PDF (maksimal 200 item)
        $items = PharmacyStockRepository::getPaginated(
            $filters,
            200,
            $this->sortField,
            $this->sortDirection
        );
        $profil = SirsHelper::getProfilRS();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.pharmacy-stock-pdf', [
            'summary' => $summary,
            'items' => $items,
            'profil' => $profil,
            'statusStok' => $this->statusStok,
            'depo' => $this->depo,
            'printedAt' => now()->translatedFormat('d F Y H:i'),
            'printedBy' => auth()->user()->name ?? 'Petugas Farmasi SIMRS',
        ])->setPaper('a4', 'portrait');

        $filename = 'rekap-stok-obat-' . now()->format('YmdHis') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $filename);
    }
}
