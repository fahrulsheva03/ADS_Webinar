<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Paket;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class TransaksiController extends Controller
{
    public function laporan(Request $request)
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $paketId = (string) $request->query('paket_id', '');
        $statuses = $request->query('status', []);
        $metodes = $request->query('metode', []);
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = $this->buildLaporanQuery($request);

        $statsTotal = (clone $query)->count();
        $statsPaidCount = (clone $query)->where('status_pembayaran', 'paid')->count();
        $statsPaidNominal = (clone $query)->where('status_pembayaran', 'paid')->sum('total_bayar');
        $statsPendingCount = (clone $query)->where('status_pembayaran', 'pending')->count();
        $statsRevenue = (clone $query)->where('status_pembayaran', 'paid')->sum('total_bayar');

        $transaksi = (clone $query)
            ->with(['user', 'paket.event'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $events = Event::query()->orderBy('tanggal_mulai', 'desc')->get(['id', 'judul']);
        $paket = Paket::query()
            ->with('event')
            ->orderBy('event_id')
            ->orderBy('nama_paket')
            ->get(['id', 'event_id', 'nama_paket']);

        return view('admin.laporan.transaksi', [
            'transaksi' => $transaksi,
            'events' => $events,
            'paket' => $paket,
            'filters' => [
                'q' => $q,
                'event_id' => $eventId,
                'paket_id' => $paketId,
                'status' => is_array($statuses) ? array_values($statuses) : [$statuses],
                'metode' => is_array($metodes) ? array_values($metodes) : [$metodes],
                'from' => $from,
                'to' => $to,
            ],
            'stats' => [
                'total_transaksi' => (int) $statsTotal,
                'paid_count' => (int) $statsPaidCount,
                'paid_nominal' => (float) $statsPaidNominal,
                'pending_count' => (int) $statsPendingCount,
                'revenue' => (float) $statsRevenue,
            ],
        ]);
    }

    public function exportLaporan(Request $request, string $format): StreamedResponse
    {
        $format = strtolower($format);
        abort_unless(in_array($format, ['csv', 'xlsx'], true), 404);

        $query = $this->buildLaporanQuery($request)->with(['user', 'paket.event'])->orderBy('id');

        $filename = 'laporan-transaksi-'.now()->format('Ymd-His').'.'.$format;

        if ($format === 'csv') {
            return $this->exportCsv($query, $filename);
        }

        return $this->exportXlsx($query, $filename);
    }

    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $sort = (string) $request->query('sort', 'id');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'waktu' => 'waktu_bayar',
            'total' => 'total_bayar',
            'status' => 'status_pembayaran',
            'created_at' => 'created_at',
        ];

        $query = $this->buildQuery($request)->with(['user', 'paket.event']);

        $orderColumn = $sortable[$sort] ?? 'id';
        $query->orderBy($orderColumn, $dir);

        $transaksi = $query->paginate(10)->withQueryString();

        $statusOptions = [
            '' => 'Semua status',
            'pending' => 'Pending',
            'paid' => 'Paid',
            'expired' => 'Expired',
            'failed' => 'Failed',
        ];

        return view('admin.transaksi.index', [
            'transaksi' => $transaksi,
            'q' => $q,
            'status' => $status,
            'from' => $from,
            'to' => $to,
            'sort' => $sort,
            'dir' => $dir,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function update(Request $request, Pesanan $transaksi)
    {
        $data = $request->validate([
            'status_pembayaran' => 'required|in:pending,paid,expired,failed',
        ]);

        $transaksi->update([
            'status_pembayaran' => $data['status_pembayaran'],
        ]);

        return redirect()
            ->route('admin.transaksi.index')
            ->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Pesanan $transaksi)
    {
        try {
            $transaksi->delete();

            return redirect()
                ->route('admin.transaksi.index')
                ->with('success', 'Transaksi berhasil dihapus.');
        } catch (Throwable $e) {
            return redirect()
                ->route('admin.transaksi.index')
                ->with('error', 'Transaksi gagal dihapus.');
        }
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:delete,set_status',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:pesanan,id',
            'status_pembayaran' => 'nullable|required_if:action,set_status|in:pending,paid,expired,failed',
        ]);

        $ids = $data['ids'];

        DB::transaction(function () use ($data, $ids) {
            if ($data['action'] === 'delete') {
                Pesanan::query()->whereIn('id', $ids)->delete();

                return;
            }

            Pesanan::query()
                ->whereIn('id', $ids)
                ->update(['status_pembayaran' => $data['status_pembayaran']]);
        });

        $message = $data['action'] === 'delete'
            ? 'Transaksi terpilih berhasil dihapus.'
            : 'Status transaksi terpilih berhasil diperbarui.';

        return redirect()
            ->route('admin.transaksi.index')
            ->with('success', $message);
    }

    public function export(Request $request, string $format): StreamedResponse
    {
        $format = strtolower($format);
        abort_unless(in_array($format, ['csv', 'xls'], true), 404);

        $delimiter = $format === 'csv' ? ',' : "\t";
        $mime = $format === 'csv' ? 'text/csv' : 'application/vnd.ms-excel';
        $filename = 'transaksi-'.now()->format('Ymd-His').'.'.$format;

        $query = $this->buildQuery($request)->with(['user', 'paket.event'])->orderByDesc('id');

        $headers = [
            'ID',
            'Tanggal',
            'Nama Pelanggan',
            'Email',
            'Event',
            'Paket',
            'Total Pembayaran',
            'Status',
        ];

        return response()->streamDownload(function () use ($query, $headers, $delimiter) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers, $delimiter);

            $query->chunkById(1000, function ($rows) use ($out, $delimiter) {
                foreach ($rows as $trx) {
                    $tanggal = $trx->waktu_bayar ? $trx->waktu_bayar : $trx->created_at;
                    fputcsv($out, [
                        $trx->id,
                        optional($tanggal)->format('Y-m-d H:i:s'),
                        $trx->user?->nama,
                        $trx->user?->email,
                        $trx->paket?->event?->judul,
                        $trx->paket?->nama_paket,
                        (string) $trx->total_bayar,
                        $trx->status_pembayaran,
                    ], $delimiter);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => $mime.'; charset=UTF-8',
        ]);
    }

    private function buildQuery(Request $request): Builder
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = Pesanan::query();

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('kode_pesanan', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($userQuery) use ($q) {
                        $userQuery->where('nama', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('paket', function ($paketQuery) use ($q) {
                        $paketQuery->where('nama_paket', 'like', "%{$q}%");
                    });
            });
        }

        if ($status !== '') {
            $query->where('status_pembayaran', $status);
        }

        if ($from !== '') {
            $query->where(function ($sub) use ($from) {
                $sub->whereDate('waktu_bayar', '>=', $from)
                    ->orWhere(function ($sub2) use ($from) {
                        $sub2->whereNull('waktu_bayar')
                            ->whereDate('created_at', '>=', $from);
                    });
            });
        }

        if ($to !== '') {
            $query->where(function ($sub) use ($to) {
                $sub->whereDate('waktu_bayar', '<=', $to)
                    ->orWhere(function ($sub2) use ($to) {
                        $sub2->whereNull('waktu_bayar')
                            ->whereDate('created_at', '<=', $to);
                    });
            });
        }

        return $query;
    }

    private function buildLaporanQuery(Request $request): Builder
    {
        $q = (string) $request->query('q', '');
        $eventId = (string) $request->query('event_id', '');
        $paketId = (string) $request->query('paket_id', '');
        $statuses = $request->query('status', []);
        $metodes = $request->query('metode', []);
        $from = (string) $request->query('from', '');
        $to = (string) $request->query('to', '');

        $query = Pesanan::query();

        if ($q !== '') {
            $query->where(function (Builder $sub) use ($q) {
                $sub->where('kode_pesanan', 'like', "%{$q}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($q) {
                        $userQuery->where('email', 'like', "%{$q}%");
                    });
            });
        }

        if ($eventId !== '') {
            $query->whereHas('paket', fn (Builder $paketQuery) => $paketQuery->where('event_id', $eventId));
        }

        if ($paketId !== '') {
            $query->where('paket_id', $paketId);
        }

        if (is_array($statuses) && ! empty($statuses)) {
            $query->whereIn('status_pembayaran', $statuses);
        } elseif (is_string($statuses) && $statuses !== '') {
            $query->where('status_pembayaran', $statuses);
        }

        if (is_array($metodes) && ! empty($metodes)) {
            $query->whereIn('metode_pembayaran', $metodes);
        } elseif (is_string($metodes) && $metodes !== '') {
            $query->where('metode_pembayaran', $metodes);
        }

        if ($from !== '') {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to !== '') {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    private function exportCsv(Builder $query, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, [
                'Kode Pesanan',
                'Nama Peserta',
                'Email',
                'Event',
                'Paket',
                'Status Pembayaran',
                'Metode Pembayaran',
                'Total Bayar',
                'Waktu Pesan',
                'Waktu Bayar',
            ]);

            $query->chunkById(1000, function ($rows) use ($out) {
                foreach ($rows as $trx) {
                    $waktuPesan = $trx->created_at;
                    $waktuBayar = null;
                    if (! empty($trx->waktu_bayar)) {
                        try {
                            $waktuBayar = Carbon::parse($trx->waktu_bayar);
                        } catch (Throwable) {
                            $waktuBayar = null;
                        }
                    }
                    fputcsv($out, [
                        $trx->kode_pesanan ?: ('TRX-'.str_pad((string) $trx->id, 6, '0', STR_PAD_LEFT)),
                        $trx->user?->nama ?? '',
                        $trx->user?->email ?? '',
                        $trx->paket?->event?->judul ?? '',
                        $trx->paket?->nama_paket ?? '',
                        $trx->status_pembayaran ?? '',
                        $trx->metode_pembayaran ?? '',
                        (string) $trx->total_bayar,
                        optional($waktuPesan)->format('d-m-Y H:i'),
                        $waktuBayar ? $waktuBayar->format('d-m-Y H:i') : '',
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function exportXlsx(Builder $query, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query) {
            $tmp = tempnam(sys_get_temp_dir(), 'xlsx_');
            if ($tmp === false) {
                abort(500);
            }

            $zipPath = $tmp.'.xlsx';
            @unlink($tmp);

            $zip = new \ZipArchive;
            if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
                @unlink($zipPath);
                abort(500);
            }

            $sheetRows = [];
            $sheetRows[] = [
                'Kode Pesanan',
                'Nama Peserta',
                'Email',
                'Event',
                'Paket',
                'Status Pembayaran',
                'Metode Pembayaran',
                'Total Bayar',
                'Waktu Pesan',
                'Waktu Bayar',
            ];

            $query->chunkById(1000, function ($rows) use (&$sheetRows) {
                foreach ($rows as $trx) {
                    $waktuPesan = $trx->created_at;
                    $waktuBayar = null;
                    if (! empty($trx->waktu_bayar)) {
                        try {
                            $waktuBayar = Carbon::parse($trx->waktu_bayar);
                        } catch (Throwable) {
                            $waktuBayar = null;
                        }
                    }
                    $sheetRows[] = [
                        $trx->kode_pesanan ?: ('TRX-'.str_pad((string) $trx->id, 6, '0', STR_PAD_LEFT)),
                        $trx->user?->nama ?? '',
                        $trx->user?->email ?? '',
                        $trx->paket?->event?->judul ?? '',
                        $trx->paket?->nama_paket ?? '',
                        $trx->status_pembayaran ?? '',
                        $trx->metode_pembayaran ?? '',
                        (string) $trx->total_bayar,
                        optional($waktuPesan)->format('d-m-Y H:i'),
                        $waktuBayar ? $waktuBayar->format('d-m-Y H:i') : '',
                    ];
                }
            });

            $colLetter = function (int $index): string {
                $index += 1;
                $letters = '';
                while ($index > 0) {
                    $mod = ($index - 1) % 26;
                    $letters = chr(65 + $mod).$letters;
                    $index = intdiv($index - 1, 26);
                }

                return $letters;
            };

            $xmlEscape = function (string $v): string {
                return htmlspecialchars($v, ENT_XML1 | ENT_COMPAT, 'UTF-8');
            };

            $sheetData = '';
            foreach ($sheetRows as $r => $row) {
                $rowIndex = $r + 1;
                $cells = '';
                foreach (array_values($row) as $c => $value) {
                    $ref = $colLetter($c).$rowIndex;
                    $v = $xmlEscape((string) $value);
                    $cells .= '<c r="'.$ref.'" t="inlineStr"><is><t>'.$v.'</t></is></c>';
                }
                $sheetData .= '<row r="'.$rowIndex.'">'.$cells.'</row>';
            }

            $worksheet = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<sheetData>'.$sheetData.'</sheetData>'
                .'</worksheet>';

            $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
                .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
                .'<Default Extension="xml" ContentType="application/xml"/>'
                .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
                .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
                .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
                .'</Types>');

            $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
                .'</Relationships>');

            $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" '
                .'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
                .'<sheets>'
                .'<sheet name="Transaksi" sheetId="1" r:id="rId1"/>'
                .'</sheets>'
                .'</workbook>');

            $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
                .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
                .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
                .'</Relationships>');

            $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
                .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
                .'<fonts count="1"><font><sz val="11"/><name val="Calibri"/></font></fonts>'
                .'<fills count="1"><fill><patternFill patternType="none"/></fill></fills>'
                .'<borders count="1"><border/></borders>'
                .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
                .'<cellXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/></cellXfs>'
                .'</styleSheet>');

            $zip->addFromString('xl/worksheets/sheet1.xml', $worksheet);
            $zip->close();

            readfile($zipPath);
            @unlink($zipPath);
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
