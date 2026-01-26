@extends('peserta.partials.app')

@section('content')
    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>KONFIRMASI</h1>
                <p>Konfirmasi pesanan Anda sebelum diproses.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.cart') }}">KERANJANG</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.checkout.payment') }}">PEMBAYARAN</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">KONFIRMASI</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="shop_section w-100 float-left padding-top padding-bottom" id="confirm_section">
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
                .confirm-card {
                    border-radius: 18px;
                    overflow: hidden;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                }
                .confirm-card__header {
                    padding: 16px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
                .confirm-item {
                    padding: 12px 16px;
                    border-top: 1px solid rgba(0, 0, 0, 0.06);
                }
            </style>

            <div class="mb-4">
                <div class="checkout-steps" aria-label="Status checkout">
                    <div class="checkout-step">
                        <span class="checkout-step__dot">1</span>
                        <span class="checkout-step__label">Keranjang</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">2</span>
                        <span class="checkout-step__label">Pembayaran</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step__dot">3</span>
                        <span class="checkout-step__label">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="row" style="row-gap: 18px;">
                <div class="col-12 col-lg-7">
                    <div class="confirm-card">
                        <div class="confirm-card__header">
                            <div class="fw-semibold text-black">Detail pesanan</div>
                            <div class="text-muted small">Pastikan sudah sesuai.</div>
                        </div>
                        <div id="confirm-items"></div>
                        <div id="confirm-empty" class="p-4" style="display:none;">
                            <div class="border rounded p-4 bg-light">
                                <div class="fw-semibold text-black">Keranjang kosong</div>
                                <div class="text-muted">Kembali ke shop untuk memilih paket.</div>
                                <div class="mt-3">
                                    <a href="{{ route('peserta.shop') }}#shop_section" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                        Ke Shop
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="confirm-card">
                        <div class="confirm-card__header">
                            <div class="fw-semibold text-black">Total</div>
                            <div class="text-muted small">Ringkasan pembayaran.</div>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="text-muted fw-semibold">Subtotal</div>
                                <div class="text-black fw-bold" id="sum-subtotal">Rp 0</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom">
                                <div class="text-muted fw-semibold">Diskon</div>
                                <div class="text-black fw-bold" id="sum-discount">Rp 0</div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between py-2">
                                <div class="text-muted fw-semibold">Total</div>
                                <div class="text-black fw-bold" id="sum-total">Rp 0</div>
                            </div>

                            <div class="mt-3 d-grid gap-2">
                                <button id="btn-finish" type="button" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                    Konfirmasi Pesanan
                                </button>
                                <a href="{{ route('peserta.cart') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                    Kembali ke Keranjang
                                </a>
                            </div>

                            <div class="text-muted small mt-3">
                                Setelah konfirmasi, Anda akan diarahkan untuk menghubungi admin jika diperlukan.
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
                const itemsEl = document.getElementById('confirm-items');
                const emptyEl = document.getElementById('confirm-empty');
                const subtotalEl = document.getElementById('sum-subtotal');
                const discountEl = document.getElementById('sum-discount');
                const totalEl = document.getElementById('sum-total');
                const finishBtn = document.getElementById('btn-finish');
                const contactUrl = @json(route('peserta.contact'));

                function safeParse(raw) {
                    try { return JSON.parse(raw); } catch (e) { return null; }
                }
                function readCart() {
                    const data = safeParse(localStorage.getItem(storageKey));
                    if (!data || typeof data !== 'object') return { items: [] };
                    if (!Array.isArray(data.items)) return { items: [] };
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

                const cart = readCart();
                const hasItems = cart.items.length > 0;
                emptyEl.style.display = hasItems ? 'none' : 'block';
                itemsEl.style.display = hasItems ? 'block' : 'none';

                const subtotal = cart.items.reduce((acc, it) => acc + (Number(it.harga || 0) * Number(it.qty || 1)), 0);
                const discount = 0;
                const total = subtotal - discount;
                subtotalEl.textContent = formatRupiah(subtotal);
                discountEl.textContent = formatRupiah(discount);
                totalEl.textContent = formatRupiah(total);

                if (hasItems) {
                    itemsEl.innerHTML = cart.items.map((it) => {
                        const event = it.event || {};
                        const qty = Math.max(1, parseInt(it.qty || 1, 10));
                        const harga = Number(it.harga || 0);
                        const lineTotal = harga * qty;
                        return `
                            <div class="confirm-item">
                                <div class="d-flex align-items-start justify-content-between" style="gap: 12px;">
                                    <div style="min-width: 0;">
                                        <div class="fw-bold text-black">${(event.judul || 'Event').toString()}</div>
                                        <div class="text-muted small mt-1">${(it.nama_paket || '').toString()}</div>
                                        <div class="text-muted small mt-2">Jumlah: ${qty}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-black">${formatRupiah(lineTotal)}</div>
                                        <div class="text-muted small">${formatRupiah(harga)} x ${qty}</div>
                                    </div>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    itemsEl.innerHTML = '';
                    finishBtn.disabled = true;
                }

                finishBtn.addEventListener('click', function () {
                    if (!hasItems) return;
                    writeCart({ items: [] });
                    if (typeof Swal === 'undefined') {
                        window.location.href = contactUrl;
                        return;
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Pesanan dikonfirmasi',
                        text: 'Silakan lanjut untuk mendapatkan instruksi pembayaran.',
                        confirmButtonText: 'OK',
                        allowOutsideClick: false,
                    }).then((res) => {
                        if (res.isConfirmed) window.location.href = contactUrl;
                    });
                });
            })();
        </script>
    @endpush
@endsection

