<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class TransaksiController extends Controller
{
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
}
