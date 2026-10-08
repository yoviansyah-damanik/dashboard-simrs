<?php

namespace App\Repository;

use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PharmacyStockRepository
{
    const KONEKSI = 'simrs';
    const LIMIT_DEFAULT = 25;

    /**
     * Mengambil ringkasan statistik stok obat (Total obat, stok fisik, nilai aset, obat habis, menipis/akan habis).
     */
    public static function getSummary(?string $depo = null, ?string $kategori = null): array
    {
        $conn = DB::connection(self::KONEKSI);

        // Ambil semua obat aktif beserta stok agregatnya
        $query = $conn->table('databarang as db')
            ->leftJoin('gudangbarang as gb', 'db.kode_brng', '=', 'gb.kode_brng')
            ->where('db.status', '1');

        if ($depo && $depo !== 'semua') {
            $query->where('gb.kd_bangsal', $depo);
        }

        if ($kategori && $kategori !== 'semua') {
            $query->where('db.kode_kategori', $kategori);
        }

        $medicines = $query->selectRaw("
                db.kode_brng,
                db.nama_brng,
                db.stokminimal,
                db.h_beli,
                db.expire,
                coalesce(sum(gb.stok), 0) as total_stok
            ")
            ->groupBy('db.kode_brng', 'db.nama_brng', 'db.stokminimal', 'db.h_beli', 'db.expire')
            ->get();

        $totalItems = $medicines->count();
        $totalFisik = 0;
        $totalNilaiAset = 0;
        $totalHabis = 0;
        $totalMenipis = 0;
        $totalAman = 0;
        $totalExpired = 0;
        $totalNearExpired = 0;

        $now = Carbon::now()->startOfDay();
        $nearLimit = Carbon::now()->addDays(90)->endOfDay();

        foreach ($medicines as $m) {
            $stok = (float) $m->total_stok;
            $min = (float) $m->stokminimal > 0 ? (float) $m->stokminimal : 10;
            $hBeli = (float) $m->h_beli;

            $totalFisik += $stok;
            $totalNilaiAset += ($stok * $hBeli);

            if ($stok <= 0) {
                $totalHabis++;
            } elseif ($stok <= $min) {
                $totalMenipis++;
            } else {
                $totalAman++;
            }

            if (!empty($m->expire) && $m->expire !== '0000-00-00') {
                $exp = Carbon::parse($m->expire);
                if ($exp->lessThanOrEqualTo($now)) {
                    $totalExpired++;
                } elseif ($exp->lessThanOrEqualTo($nearLimit)) {
                    $totalNearExpired++;
                }
            }
        }

        // Daftar depo / gudang farmasi (hanya untuk obat aktif db.status = '1')
        $depos = $conn->table('gudangbarang as gb')
            ->join('databarang as db', 'gb.kode_brng', '=', 'db.kode_brng')
            ->leftJoin('bangsal as b', 'gb.kd_bangsal', '=', 'b.kd_bangsal')
            ->where('db.status', '1')
            ->selectRaw('gb.kd_bangsal, coalesce(b.nm_bangsal, gb.kd_bangsal) as nama_depo, count(distinct gb.kode_brng) as total_obat, sum(gb.stok) as total_unit')
            ->groupBy('gb.kd_bangsal', 'b.nm_bangsal')
            ->orderByDesc('total_unit')
            ->get();

        // Daftar kategori obat
        $kategoriList = $conn->table('kategori_barang as kb')
            ->join('databarang as db', 'kb.kode', '=', 'db.kode_kategori')
            ->where('db.status', '1')
            ->select('kb.kode', 'kb.nama')
            ->distinct()
            ->orderBy('kb.nama')
            ->get();

        return [
            'total_items' => $totalItems,
            'total_fisik' => $totalFisik,
            'total_nilai_aset' => $totalNilaiAset,
            'total_habis' => $totalHabis,
            'total_menipis' => $totalMenipis,
            'total_aman' => $totalAman,
            'total_expired' => $totalExpired,
            'total_near_expired' => $totalNearExpired,
            'depos' => $depos,
            'kategori_list' => $kategoriList,
        ];
    }

    /**
     * Mengambil data stok obat terpaginasi dengan filter status, depo, pencarian, dan penanda stok menipis.
     */
    public static function getPaginated(
        array $filters = [],
        int $limit = self::LIMIT_DEFAULT,
        string $sortField = 'stok',
        string $sortDirection = 'asc'
    ): LengthAwarePaginator {
        $conn = DB::connection(self::KONEKSI);

        $search = $filters['search'] ?? null;
        $statusStok = $filters['status_stok'] ?? 'semua'; // 'semua', 'menipis', 'habis', 'aman', 'expired', 'near_expired'
        $depo = $filters['depo'] ?? 'semua';
        $kategori = $filters['kategori'] ?? 'semua';

        $query = $conn->table('databarang as db')
            ->leftJoin('gudangbarang as gb', 'db.kode_brng', '=', 'gb.kode_brng')
            ->leftJoin('kategori_barang as kb', 'db.kode_kategori', '=', 'kb.kode')
            ->where('db.status', '1');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('db.nama_brng', 'like', "%{$search}%")
                    ->orWhere('db.kode_brng', 'like', "%{$search}%")
                    ->orWhere('db.letak_barang', 'like', "%{$search}%");
            });
        }

        if ($kategori && $kategori !== 'semua') {
            $query->where('db.kode_kategori', $kategori);
        }

        // Select agregat
        $query->selectRaw("
            db.kode_brng,
            db.nama_brng,
            db.kode_sat,
            db.letak_barang,
            db.stokminimal,
            db.h_beli,
            db.ralan,
            db.expire,
            db.kode_kategori,
            kb.nama as nama_kategori,
            coalesce(sum(gb.stok), 0) as total_stok,
            coalesce(sum(case when gb.kd_bangsal = 'AP' then gb.stok else 0 end), 0) as stok_apotek,
            coalesce(sum(case when gb.kd_bangsal = 'GD 1' then gb.stok else 0 end), 0) as stok_gudang,
            coalesce(sum(case when gb.kd_bangsal not in ('AP', 'GD 1') then gb.stok else 0 end), 0) as stok_lainnya
        ")
        ->groupBy(
            'db.kode_brng',
            'db.nama_brng',
            'db.kode_sat',
            'db.letak_barang',
            'db.stokminimal',
            'db.h_beli',
            'db.ralan',
            'db.expire',
            'db.kode_kategori',
            'kb.nama'
        );

        // Filter berdasarkan status stok menggunakan HAVING
        if ($statusStok === 'habis') {
            $query->havingRaw('total_stok <= 0');
        } elseif ($statusStok === 'menipis') {
            $query->havingRaw('total_stok > 0 AND total_stok <= case when db.stokminimal > 0 then db.stokminimal else 10 end');
        } elseif ($statusStok === 'aman') {
            $query->havingRaw('total_stok > case when db.stokminimal > 0 then db.stokminimal else 10 end');
        } elseif ($statusStok === 'expired') {
            $query->whereRaw("db.expire is not null and db.expire != '0000-00-00' and db.expire <= curdate()");
        } elseif ($statusStok === 'near_expired') {
            $query->whereRaw("db.expire is not null and db.expire != '0000-00-00' and db.expire > curdate() and db.expire <= date_add(curdate(), interval 90 day)");
        }

        if ($depo && $depo !== 'semua') {
            if ($depo === 'AP') {
                $query->havingRaw('stok_apotek > 0');
            } elseif ($depo === 'GD 1') {
                $query->havingRaw('stok_gudang > 0');
            } else {
                $query->havingRaw('stok_lainnya > 0');
            }
        }

        // Sorting
        switch ($sortField) {
            case 'nama_brng':
                $query->orderBy('db.nama_brng', $sortDirection);
                break;
            case 'nilai_aset':
                $query->orderByRaw("(total_stok * db.h_beli) $sortDirection");
                break;
            case 'stokminimal':
                $query->orderBy('db.stokminimal', $sortDirection);
                break;
            case 'expire':
                $query->orderBy('db.expire', $sortDirection);
                break;
            case 'stok':
            default:
                $query->orderBy('total_stok', $sortDirection);
                break;
        }

        $paginator = $query->paginate($limit);

        $now = Carbon::now()->startOfDay();
        $nearLimit = Carbon::now()->addDays(90)->endOfDay();

        // Transform collection untuk menandai status obat secara presisi
        $paginator->getCollection()->transform(function ($item) use ($now, $nearLimit) {
            $stok = (float) $item->total_stok;
            $min = (float) $item->stokminimal > 0 ? (float) $item->stokminimal : 10;
            $nilaiAset = $stok * (float) $item->h_beli;

            $statusText = 'aman';
            $statusBadge = 'Aman';
            $statusColor = 'emerald';

            if ($stok <= 0) {
                $statusText = 'habis';
                $statusBadge = 'Stok Kosong';
                $statusColor = 'rose';
            } elseif ($stok <= $min) {
                $statusText = 'menipis';
                $statusBadge = 'Akan Habis';
                $statusColor = 'amber';
            }

            $isExpired = false;
            $isNearExpired = false;
            $daysToExpire = null;

            if (!empty($item->expire) && $item->expire !== '0000-00-00') {
                $exp = Carbon::parse($item->expire);
                $daysToExpire = (int) $now->diffInDays($exp, false);

                if ($exp->lessThanOrEqualTo($now)) {
                    $isExpired = true;
                } elseif ($exp->lessThanOrEqualTo($nearLimit)) {
                    $isNearExpired = true;
                }
            }

            return (object) [
                'kode_brng' => $item->kode_brng,
                'nama_brng' => $item->nama_brng,
                'kode_sat' => $item->kode_sat ?? 'PCS',
                'letak_barang' => $item->letak_barang ?? '-',
                'stokminimal' => (float) $item->stokminimal,
                'ambang_kritis' => $min,
                'h_beli' => (float) $item->h_beli,
                'ralan' => (float) $item->ralan,
                'expire' => $item->expire,
                'nama_kategori' => $item->nama_kategori ?? 'Umum',
                'total_stok' => $stok,
                'stok_apotek' => (float) $item->stok_apotek,
                'stok_gudang' => (float) $item->stok_gudang,
                'stok_lainnya' => (float) $item->stok_lainnya,
                'nilai_aset' => $nilaiAset,
                'status_text' => $statusText,
                'status_badge' => $statusBadge,
                'status_color' => $statusColor,
                'is_akan_habis' => ($statusText === 'menipis'),
                'is_habis' => ($statusText === 'habis'),
                'is_expired' => $isExpired,
                'is_near_expired' => $isNearExpired,
                'days_to_expire' => $daysToExpire,
            ];
        });

        return $paginator;
    }

    /**
     * Mengambil detail stok per batch dan depo untuk obat tertentu.
     */
    public static function getDetailByKode(string $kodeBrng): ?array
    {
        $conn = DB::connection(self::KONEKSI);

        $obat = $conn->table('databarang as db')
            ->leftJoin('kategori_barang as kb', 'db.kode_kategori', '=', 'kb.kode')
            ->leftJoin('jenis as j', 'db.kdjns', '=', 'j.kdjns')
            ->select('db.*', 'kb.nama as nama_kategori', 'j.nama as nama_jenis')
            ->where('db.kode_brng', $kodeBrng)
            ->where('db.status', '1')
            ->first();

        if (!$obat) return null;

        $gudang = $conn->table('gudangbarang as gb')
            ->join('databarang as db', 'gb.kode_brng', '=', 'db.kode_brng')
            ->leftJoin('bangsal as b', 'gb.kd_bangsal', '=', 'b.kd_bangsal')
            ->selectRaw('gb.kd_bangsal, coalesce(b.nm_bangsal, gb.kd_bangsal) as nama_depo, gb.stok, gb.no_batch, gb.no_faktur')
            ->where('gb.kode_brng', $kodeBrng)
            ->where('db.status', '1')
            ->get();

        return [
            'obat' => $obat,
            'gudang' => $gudang,
            'total_stok' => (float) $gudang->sum('stok'),
        ];
    }
}
