@extends('peserta.partials.app')

@section('content')
    @php
        use App\Models\Paket;
        use Illuminate\Support\Facades\Schema;

        $seedItem = null;
        $seedPaketId = (int) request()->query('paket', 0);
        if ($seedPaketId > 0 && Schema::hasTable('paket')) {
            $paket = Paket::query()->with(['event', 'sesi'])->find($seedPaketId);
            if ($paket) {
                $seedItem = [
                    'paket_id' => (int) $paket->id,
                    'qty' => 1,
                    'nama_paket' => (string) ($paket->nama_paket ?? ''),
                    'harga' => (float) ($paket->harga ?? 0),
                    'event' => [
                        'id' => (int) ($paket->event?->id ?? 0),
                        'judul' => (string) ($paket->event?->judul ?? ''),
                    ],
                    'fitur' => [
                        'akses_live' => (bool) ($paket->akses_live ?? false),
                        'akses_rekaman' => (bool) ($paket->akses_rekaman ?? false),
                        'jumlah_sesi' => is_object($paket->sesi) ? $paket->sesi->count() : 0,
                        'kuota' => is_null($paket->kuota) ? null : (int) $paket->kuota,
                    ],
                ];
            }
        }
    @endphp

    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>KERANJANG</h1>
                <p>Periksa paket yang dipilih sebelum lanjut ke pembayaran.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.shop') }}">SHOP</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">KERANJANG</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="shop_section w-100 float-left padding-top padding-bottom" id="cart_section">
        <div class="container">
            <style>
                .checkout-steps {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 10px;
                    padding: 14px 14px;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                    border-radius: 16px;
                }
                .checkout-step {
                    flex: 1;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    padding: 10px 12px;
                    border-radius: 14px;
                    background: rgba(0, 0, 0, 0.03);
                    min-width: 0;
                }
                .checkout-step__dot {
                    width: 34px;
                    height: 34px;
                    border-radius: 50%;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 800;
                    background: rgba(0, 0, 0, 0.08);
                    color: #000;
                    flex: 0 0 auto;
                }
                .checkout-step__label {
                    font-weight: 800;
                    color: #000;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .checkout-step.is-active {
                    background: rgba(248, 0, 0, 0.08);
                    border: 1px solid rgba(248, 0, 0, 0.18);
                }
                .checkout-step.is-active .checkout-step__dot {
                    background: rgba(248, 0, 0, 0.16);
                    color: rgb(248, 0, 0);
                }
                .cart-card {
                    border-radius: 18px;
                    overflow: hidden;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                }
                .cart-card__header {
                    padding: 16px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                }
                .cart-item {
                    border-top: 1px solid rgba(0, 0, 0, 0.06);
                    padding: 14px 16px;
                    transition: opacity 180ms ease, transform 180ms ease;
                }
                .cart-item.is-enter {
                    opacity: 0;
                    transform: translateY(8px);
                }
                .cart-item.is-leave {
                    opacity: 0;
                    transform: translateY(-8px);
                }
                .cart-item__title {
                    font-weight: 900;
                    color: #000;
                    margin: 0;
                }
                .cart-item__subtitle {
                    margin: 6px 0 0;
                    color: rgba(0, 0, 0, 0.65);
                    font-size: 14px;
                    line-height: 20px;
                }
                .cart-summary__row {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: 12px;
                    padding: 10px 0;
                    border-bottom: 1px dashed rgba(0, 0, 0, 0.12);
                }
                .cart-summary__row:last-child {
                    border-bottom: 0;
                }
                .cart-summary__label {
                    color: rgba(0, 0, 0, 0.7);
                    font-weight: 700;
                }
                .cart-summary__value {
                    color: #000;
                    font-weight: 900;
                }
                .qty-input {
                    width: 86px;
                    border-radius: 12px;
                }
            </style>

            <div class="mb-4">
                <div class="checkout-steps" aria-label="Status checkout">
                    <div class="checkout-step is-active">
                        <span class="checkout-step__dot">1</span>
                        <span class="checkout-step__label">Pilih Paket</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">2</span>
                        <span class="checkout-step__label">Bayar</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">3</span>
                        <span class="checkout-step__label">Konfirmasi</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">4</span>
                        <span class="checkout-step__label">Selesai</span>
                    </div>
                </div>
            </div>

            <div class="row" style="row-gap: 18px;">
                <div class="col-12 col-lg-8">
                    <div class="cart-card">
                        <div class="cart-card__header">
                            <div>
                                <div class="fw-semibold text-black">Item di keranjang</div>
                                <div class="text-muted small">Anda bisa ubah jumlah atau hapus paket.</div>
                            </div>
                            <a href="{{ route('peserta.shop') }}#shop_section" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 800;">
                                Tambah paket
                            </a>
                        </div>
                        <div id="cart-items"></div>
                        <div id="cart-empty" class="p-4" style="display:none;">
                            <div class="border rounded p-4 bg-light">
                                <div class="fw-semibold text-black">Keranjang kosong</div>
                                <div class="text-muted">Pilih paket di Shop untuk mulai checkout.</div>
                                <div class="mt-3">
                                    <a href="{{ route('peserta.shop') }}#shop_section" class="btn btn-primary" style="border-radius: 12px; font-weight: 800;">
                                        Ke Shop
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-4">
                    <div class="cart-card">
                        <div class="cart-card__header">
                            <div>
                                <div class="fw-semibold text-black">Ringkasan pesanan</div>
                                <div class="text-muted small">Subtotal, diskon, dan total akhir.</div>
                            </div>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="cart-summary__row">
                                <div class="cart-summary__label">Subtotal</div>
                                <div class="cart-summary__value" id="sum-subtotal">Rp 0</div>
                            </div>
                            <div class="cart-summary__row">
                                <div class="cart-summary__label">Diskon</div>
                                <div class="cart-summary__value" id="sum-discount">Rp 0</div>
                            </div>
                            <div class="cart-summary__row">
                                <div class="cart-summary__label">Total</div>
                                <div class="cart-summary__value" id="sum-total">Rp 0</div>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                @auth
                                    <button id="btn-checkout" type="button" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                        Lanjutkan ke Pembayaran
                                    </button>
                                    <form id="checkout-form" action="{{ route('peserta.checkout.start') }}" method="post" class="d-none">
                                        @csrf
                                        <input type="hidden" name="paket_id" id="checkout-paket-id">
                                        <input type="hidden" name="qty" id="checkout-qty">
                                    </form>
                                @else
                                    <button id="btn-checkout-guest" type="button" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;" disabled>
                                        Lanjutkan ke Pembayaran
                                    </button>
                                    <a id="btn-login" href="{{ route('login', ['redirect' => '/cart']) }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                        Login untuk lanjut
                                    </a>
                                @endauth
                            </div>

                            <div class="text-muted small mt-3">
                                Keranjang tersimpan di perangkat ini, meskipun Anda belum login.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            (function () {
                const storageKey = 'peserta_cart_v1';
                const seed = @json($seedItem);
                const isLoggedIn = @json(auth()->check());
                const loginUrl = @json(route('login', ['redirect' => '/cart']));
                const itemsEl = document.getElementById('cart-items');
                const emptyEl = document.getElementById('cart-empty');
                const subtotalEl = document.getElementById('sum-subtotal');
                const discountEl = document.getElementById('sum-discount');
                const totalEl = document.getElementById('sum-total');

                function safeParse(raw) {
                    try {
                        return JSON.parse(raw);
                    } catch (e) {
                        return null;
                    }
                }

                function readCart() {
                    const data = safeParse(localStorage.getItem(storageKey));
                    if (!data || typeof data !== 'object') return { items: [], updated_at: null };
                    if (!Array.isArray(data.items)) return { items: [], updated_at: null };
                    return data;
                }

                function writeCart(cart) {
                    cart.updated_at = new Date().toISOString();
                    localStorage.setItem(storageKey, JSON.stringify(cart));
                }

                function formatRupiah(n) {
                    const num = Number(n || 0);
                    return 'Rp ' + (isFinite(num) ? num.toLocaleString('id-ID', { maximumFractionDigits: 0 }) : '0');
                }

                function upsertSeed() {
                    if (!seed || !seed.paket_id) return;
                    const cart = readCart();
                    const idx = cart.items.findIndex((i) => String(i.paket_id) === String(seed.paket_id));
                    if (idx >= 0) {
                        cart.items[idx].qty = Math.max(1, parseInt(cart.items[idx].qty || 1, 10));
                    } else {
                        cart.items.unshift(seed);
                    }
                    writeCart(cart);
                    const url = new URL(window.location.href);
                    url.searchParams.delete('paket');
                    window.history.replaceState({}, document.title, url.toString());
                }

                function calc(cart) {
                    const subtotal = cart.items.reduce((acc, it) => acc + (Number(it.harga || 0) * Number(it.qty || 1)), 0);
                    const discount = 0;
                    const total = subtotal - discount;
                    return { subtotal, discount, total };
                }

                function render() {
                    const cart = readCart();
                    const hasItems = cart.items.length > 0;
                    emptyEl.style.display = hasItems ? 'none' : 'block';
                    itemsEl.style.display = hasItems ? 'block' : 'none';

                    const sums = calc(cart);
                    subtotalEl.textContent = formatRupiah(sums.subtotal);
                    discountEl.textContent = formatRupiah(sums.discount);
                    totalEl.textContent = formatRupiah(sums.total);

                    if (!hasItems) {
                        itemsEl.innerHTML = '';
                        return;
                    }

                    itemsEl.innerHTML = cart.items.map((it) => {
                        const fitur = it.fitur || {};
                        const event = it.event || {};
                        const qty = Math.max(1, parseInt(it.qty || 1, 10));
                        const harga = Number(it.harga || 0);
                        const lineTotal = harga * qty;

                        const fiturText = [
                            `Live: ${fitur.akses_live ? 'Ya' : 'Tidak'}`,
                            `Rekaman: ${fitur.akses_rekaman ? 'Ya' : 'Tidak'}`,
                            `Sesi: ${parseInt(fitur.jumlah_sesi || 0, 10)}`,
                        ];
                        if (fitur.kuota !== null && fitur.kuota !== undefined && fitur.kuota !== '') fiturText.push(`Kuota: ${parseInt(fitur.kuota, 10)}`);

                        return `
                            <div class="cart-item" data-paket-id="${it.paket_id}">
                                <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                    <div class="me-2" style="min-width: 0;">
                                        <p class="cart-item__title">${(event.judul || 'Event').toString()}</p>
                                        <p class="cart-item__subtitle">${(it.nama_paket || '').toString()}</p>
                                        <div class="text-muted small mt-2">${fiturText.join(' • ')}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-black">${formatRupiah(harga)}</div>
                                        <div class="text-muted small">per paket</div>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap align-items-center justify-content-between mt-3" style="gap: 12px;">
                                    <div class="d-flex align-items-center" style="gap: 10px;">
                                        <label class="text-muted small mb-0">Jumlah</label>
                                        <input type="number" min="1" max="99" class="form-control qty-input js-qty" value="${qty}">
                                        <button type="button" class="btn btn-outline-danger btn-sm js-remove" style="border-radius: 10px; font-weight: 800;">
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="fw-bold text-black">
                                        ${formatRupiah(lineTotal)}
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                }

                function updateQty(paketId, qty) {
                    const cart = readCart();
                    const idx = cart.items.findIndex((i) => String(i.paket_id) === String(paketId));
                    if (idx < 0) return;
                    cart.items[idx].qty = Math.max(1, Math.min(99, parseInt(qty || 1, 10)));
                    writeCart(cart);
                    render();
                }

                function removeItem(paketId) {
                    const row = itemsEl.querySelector(`.cart-item[data-paket-id="${paketId}"]`);
                    const cart = readCart();
                    const nextItems = cart.items.filter((i) => String(i.paket_id) !== String(paketId));
                    cart.items = nextItems;
                    writeCart(cart);

                    if (!row) {
                        render();
                        return;
                    }

                    row.classList.add('is-leave');
                    if (window.jQuery) {
                        window.jQuery(row).slideUp(180, function () {
                            render();
                        });
                    } else {
                        setTimeout(render, 200);
                    }
                }

                document.addEventListener('input', function (e) {
                    const el = e.target;
                    if (!el.classList || !el.classList.contains('js-qty')) return;
                    const row = el.closest('.cart-item');
                    if (!row) return;
                    const paketId = row.getAttribute('data-paket-id');
                    updateQty(paketId, el.value);
                });

                document.addEventListener('click', function (e) {
                    const btn = e.target.closest && e.target.closest('.js-remove');
                    if (btn) {
                        const row = btn.closest('.cart-item');
                        if (!row) return;
                        removeItem(row.getAttribute('data-paket-id'));
                        return;
                    }

                    const checkoutBtn = e.target.closest && e.target.closest('#btn-checkout');
                    if (checkoutBtn) {
                        const cart = readCart();
                        if (!cart.items.length) return;
                        if (cart.items.length > 1 && typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'info',
                                title: 'Checkout 1 paket',
                                text: 'Saat ini checkout dilakukan untuk 1 paket. Paket pertama akan diproses.',
                                confirmButtonText: 'OK',
                                allowOutsideClick: false,
                            });
                        }
                        const first = cart.items[0] || {};
                        const paketId = parseInt(first.paket_id || 0, 10);
                        const qty = Math.max(1, parseInt(first.qty || 1, 10));
                        const form = document.getElementById('checkout-form');
                        const paketInput = document.getElementById('checkout-paket-id');
                        const qtyInput = document.getElementById('checkout-qty');
                        if (!form || !paketInput || !qtyInput) return;
                        if (!paketId) return;
                        paketInput.value = String(paketId);
                        qtyInput.value = String(qty);
                        form.submit();
                        return;
                    }

                    const checkoutGuest = e.target.closest && e.target.closest('#btn-checkout-guest');
                    if (checkoutGuest) {
                        if (typeof Swal === 'undefined') {
                            window.location.href = loginUrl;
                            return;
                        }
                        Swal.fire({
                            icon: 'warning',
                            title: 'Silahkan login terlebih dahulu',
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                        }).then((res) => {
                            if (res.isConfirmed) window.location.href = loginUrl;
                        });
                    }
                });

                upsertSeed();
                render();

                {
                    const cart = readCart();
                    const btn = document.getElementById(isLoggedIn ? 'btn-checkout' : 'btn-checkout-guest');
                    if (btn) btn.disabled = cart.items.length === 0;
                }

                const cart = readCart();
                if (cart.items.length > 0) {
                    const first = itemsEl.querySelector('.cart-item');
                    if (first) {
                        first.classList.add('is-enter');
                        requestAnimationFrame(() => {
                            first.classList.remove('is-enter');
                        });
                    }
                }
            })();
        </script>
    @endpush
@endsection
