<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $status = (string) $request->query('status', '');

        $sort = (string) $request->query('sort', 'id');
        $dir = strtolower((string) $request->query('dir', 'desc')) === 'asc' ? 'asc' : 'desc';

        $sortable = [
            'id' => 'id',
            'nama' => 'nama',
            'email' => 'email',
            'status_akun' => 'status_akun',
            'created_at' => 'created_at',
        ];

        $baseQuery = User::query()->where('role', 'user');

        if ($q !== '') {
            $baseQuery->where(function ($sub) use ($q) {
                $sub->where('nama', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($status !== '') {
            $baseQuery->where('status_akun', $status);
        }

        $orderColumn = $sortable[$sort] ?? 'id';
        $baseQuery->orderBy($orderColumn, $dir);

        $users = $baseQuery->paginate(10)->withQueryString();

        $statsQuery = User::query()->where('role', 'user');
        $totalCount = (clone $statsQuery)->count();
        $aktifCount = (clone $statsQuery)->where('status_akun', 'aktif')->count();
        $nonaktifCount = $totalCount - $aktifCount;

        return view('admin.peserta.index', [
            'users' => $users,
            'totalCount' => $totalCount,
            'aktifCount' => $aktifCount,
            'nonaktifCount' => $nonaktifCount,
            'q' => $q,
            'status' => $status,
            'sort' => $sort,
            'dir' => $dir,
        ]);
    }

    public function show(User $user)
    {
        $paidOrders = Pesanan::query()
            ->with(['paket.event'])
            ->where('user_id', $user->id)
            ->where('status_pembayaran', 'paid')
            ->orderByDesc('waktu_bayar')
            ->get();

        $events = $paidOrders
            ->map(fn ($order) => $order->paket?->event)
            ->filter()
            ->unique('id')
            ->values();

        $eventRows = $events->map(function ($event) use ($paidOrders) {
            $ordersForEvent = $paidOrders
                ->filter(fn ($order) => (string) ($order->paket?->event_id) === (string) $event->id)
                ->values();

            $paketNames = $ordersForEvent
                ->map(fn ($order) => $order->paket?->nama_paket)
                ->filter()
                ->unique()
                ->values();

            return [
                'event' => $event,
                'paket_names' => $paketNames,
                'last_paid_at' => $ordersForEvent->first()?->waktu_bayar,
            ];
        });

        $totalPaid = $paidOrders->count();
        $totalEvents = $events->count();

        return view('admin.peserta.show', [
            'user' => $user,
            'paidOrders' => $paidOrders,
            'events' => $events,
            'eventRows' => $eventRows,
            'totalPaid' => $totalPaid,
            'totalEvents' => $totalEvents,
        ]);
    }
}
