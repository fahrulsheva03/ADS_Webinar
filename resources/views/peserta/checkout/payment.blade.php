@extends('peserta.partials.app')

@section('content')
    <section class="sub-banner-main-section event-banner-section w-100 float-left">
        <div class="container">
            <div class="sub-banner-inner-con">
                <h1>PEMBAYARAN</h1>
                <p>Selesaikan pembayaran untuk mengaktifkan akses event Anda.</p>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb d-inline-block mb-0">
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.index') }}">HOME</a></li>
                        <li class="breadcrumb-item d-inline-block"><a href="{{ route('peserta.cart') }}">KERANJANG</a></li>
                        <li class="breadcrumb-item active d-inline-block" aria-current="page">PEMBAYARAN</li>
                    </ol>
                </nav>
            </div>
        </div>
    </section>

    <section class="shop_section w-100 float-left padding-top padding-bottom" id="payment_section">
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
                .pay-card {
                    border-radius: 18px;
                    overflow: hidden;
                    border: 1px solid rgba(0, 0, 0, 0.08);
                    background: #fff;
                }
                .pay-card__header {
                    padding: 16px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
                }
            </style>

            <div class="mb-4">
                <div class="checkout-steps" aria-label="Status checkout">
                    <div class="checkout-step">
                        <span class="checkout-step__dot">1</span>
                        <span class="checkout-step__label">Keranjang</span>
                    </div>
                    <div class="checkout-step is-active">
                        <span class="checkout-step__dot">2</span>
                        <span class="checkout-step__label">Pembayaran</span>
                    </div>
                    <div class="checkout-step">
                        <span class="checkout-step__dot">3</span>
                        <span class="checkout-step__label">Konfirmasi</span>
                    </div>
                </div>
            </div>

            <div class="row" style="row-gap: 18px;">
                <div class="col-12 col-lg-7">
                    <div class="pay-card">
                        <div class="pay-card__header">
                            <div class="fw-semibold text-black">Metode pembayaran</div>
                            <div class="text-muted small">Pilih metode, lalu lanjut konfirmasi.</div>
                        </div>
                        <div class="p-3 p-md-4">
                            <div class="border rounded p-3 bg-light">
                                <div class="fw-semibold text-black">Pembayaran belum terintegrasi otomatis</div>
                                <div class="text-muted mt-1">
                                    Untuk saat ini, silakan lanjut ke konfirmasi dan hubungi admin untuk instruksi pembayaran.
                                </div>
                                <div class="mt-3 d-flex flex-wrap" style="gap: 10px;">
                                    <a href="{{ route('peserta.contact') }}" class="btn btn-outline-secondary" style="border-radius: 12px; font-weight: 900;">
                                        Hubungi Admin
                                    </a>
                                    <a href="{{ route('peserta.checkout.confirm') }}" class="btn btn-primary" style="border-radius: 12px; font-weight: 900;">
                                        Lanjut Konfirmasi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-5">
                    <div class="pay-card">
                        <div class="pay-card__header">
                            <div class="fw-semibold text-black">Ringkasan</div>
                            <div class="text-muted small">Diambil dari keranjang Anda.</div>
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
                            <div class="text-muted small mt-3">
                                Pastikan item di keranjang sudah sesuai sebelum melanjutkan.
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
                const subtotalEl = document.getElementById('sum-subtotal');
                const discountEl = document.getElementById('sum-discount');
                const totalEl = document.getElementById('sum-total');

                function safeParse(raw) {
                    try { return JSON.parse(raw); } catch (e) { return null; }
                }
                function readCart() {
                    const data = safeParse(localStorage.getItem(storageKey));
                    if (!data || typeof data !== 'object') return { items: [] };
                    if (!Array.isArray(data.items)) return { items: [] };
                    return data;
                }
                function formatRupiah(n) {
                    const num = Number(n || 0);
                    return 'Rp ' + (isFinite(num) ? num.toLocaleString('id-ID', { maximumFractionDigits: 0 }) : '0');
                }
                const cart = readCart();
                const subtotal = cart.items.reduce((acc, it) => acc + (Number(it.harga || 0) * Number(it.qty || 1)), 0);
                const discount = 0;
                const total = subtotal - discount;
                subtotalEl.textContent = formatRupiah(subtotal);
                discountEl.textContent = formatRupiah(discount);
                totalEl.textContent = formatRupiah(total);
            })();
        </script>
    @endpush
@endsection

