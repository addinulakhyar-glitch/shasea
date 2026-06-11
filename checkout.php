<?php
/**
 * SHASEA — Checkout Page
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$cart      = getCart();
$cartTotal = getCartTotal();
$waNumber  = getSiteContent('whatsapp_number', '6281234567890');

if (empty($cart)) {
    header('Location: catalog.php');
    exit;
}

// Handle order submission
$orderSuccess = false;
$orderNumber  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db   = getDB();
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    $name       = trim($data['name']     ?? '');
    $phone      = trim($data['phone']    ?? '');
    $email      = trim($data['email']    ?? '');
    $address    = trim($data['address']  ?? '');
    $city       = trim($data['city']     ?? '');
    $province   = trim($data['province'] ?? '');
    $postal     = trim($data['postal']   ?? '');
    $payment    = trim($data['payment']  ?? 'transfer_bank');
    $courier    = trim($data['courier']  ?? '');
    $notes      = trim($data['notes']    ?? '');

    if ($name && $phone && $address) {
        $shipping    = 25000; // flat for demo
        $discount    = 0;
        $total       = $cartTotal + $shipping - $discount;
        $orderNo     = 'SHA' . strtoupper(date('ymd')) . mt_rand(1000, 9999);

        header('Content-Type: application/json');

        try {
            $db->beginTransaction();

            $stmt = $db->prepare(
                "INSERT INTO orders
                 (order_number, customer_name, customer_email, customer_phone,
                  customer_address, customer_city, customer_province, customer_postal_code,
                  subtotal, shipping_cost, discount, total_amount,
                  payment_method, shipping_courier, notes, status)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,'pending')"
            );
            $stmt->execute([
                $orderNo, $name, $email, $phone,
                $address, $city, $province, $postal,
                $cartTotal, $shipping, $discount, $total,
                $payment, $courier, $notes
            ]);
            $orderId = $db->lastInsertId();

            foreach ($cart as $item) {
                $itemStmt = $db->prepare(
                    "INSERT INTO order_items
                     (order_id, product_id, product_name, product_image, price, quantity, size, color, subtotal)
                     VALUES (?,?,?,?,?,?,?,?,?)"
                );
                $itemStmt->execute([
                    $orderId, $item['product_id'], $item['name'], $item['image'],
                    $item['price'], $item['qty'], $item['size'], $item['color'],
                    $item['price'] * $item['qty']
                ]);

                // Decrease stock
                $db->prepare("UPDATE products SET total_sold=total_sold+?, stock=GREATEST(0,stock-?) WHERE id=?")
                   ->execute([$item['qty'], $item['qty'], $item['product_id']]);
            }

            $db->commit();
            $_SESSION['cart'] = [];

            echo json_encode([
                'success'      => true,
                'order_number' => $orderNo,
                'total_fmt'    => formatRupiah($total),
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success'=>false,'message'=>'Gagal membuat pesanan. Coba lagi.']);
        }
        exit;
    }

    header('Content-Type: application/json');
    echo json_encode(['success'=>false,'message'=>'Mohon lengkapi data pengiriman']);
    exit;
}

$pageTitle = 'Checkout | Shasea';
$pageDesc  = 'Selesaikan pembelian produk busana muslimah Shasea.';
$extraCSS  = ['pages.css'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="page-content">
    <div class="page-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a><span class="sep">›</span>
                <a href="catalog.php">Katalog</a><span class="sep">›</span>
                <span class="current">Checkout</span>
            </div>
            <h1 class="page-hero-title">Checkout<em style="color:var(--gold-primary);">.</em></h1>
        </div>
    </div>

    <div class="container">
        <div class="checkout-grid">

            <!-- ── Form ─────────────────────────────────────────── -->
            <div>
                <!-- Shipping Info -->
                <div class="checkout-section">
                    <div class="checkout-section-title">Informasi Pengiriman</div>
                    <form id="checkoutForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-input" placeholder="Nama penerima" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor WhatsApp *</label>
                                <input type="tel" name="phone" class="form-input" placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input" placeholder="email@kamu.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Alamat Lengkap *</label>
                            <textarea name="address" class="form-textarea" rows="3"
                                      placeholder="Nama jalan, nomor rumah, RT/RW, kelurahan, kecamatan" required></textarea>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Kota / Kabupaten *</label>
                                <input type="text" name="city" class="form-input" placeholder="Kota" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Provinsi *</label>
                                <input type="text" name="province" class="form-input" placeholder="Provinsi" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Kode Pos</label>
                                <input type="text" name="postal" class="form-input" placeholder="Kode pos">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Kurir</label>
                                <select name="courier" class="form-select">
                                    <option value="JNE">JNE</option>
                                    <option value="J&T">J&T Express</option>
                                    <option value="SiCepat">SiCepat</option>
                                    <option value="AnterAja">AnterAja</option>
                                    <option value="Pos Indonesia">Pos Indonesia</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Payment Method -->
                <div class="checkout-section">
                    <div class="checkout-section-title">Metode Pembayaran</div>
                    <div class="payment-options">
                        <?php
                        $payments = [
                            ['value'=>'transfer_bank','label'=>'Transfer Bank','desc'=>'BCA, BNI, Mandiri, BRI'],
                            ['value'=>'qris',        'label'=>'QRIS',         'desc'=>'Scan QR dari semua e-wallet'],
                            ['value'=>'gopay',       'label'=>'GoPay',        'desc'=>'Bayar via aplikasi Gojek'],
                            ['value'=>'ovo',         'label'=>'OVO',          'desc'=>'Bayar via aplikasi OVO'],
                            ['value'=>'dana',        'label'=>'DANA',         'desc'=>'Bayar via aplikasi DANA'],
                            ['value'=>'cod',         'label'=>'COD',          'desc'=>'Bayar di tempat (area tertentu)'],
                        ];
                        foreach ($payments as $i => $pm): ?>
                        <label class="payment-option <?= $i===0?'selected':'' ?>"
                               onclick="selectPayment(this,'<?= $pm['value'] ?>')">
                            <input type="radio" name="payment" value="<?= $pm['value'] ?>" <?= $i===0?'checked':'' ?>>
                            <div class="payment-radio-indicator"></div>
                            <div>
                                <strong style="font-size:0.9rem;color:var(--text-primary);"><?= $pm['label'] ?></strong>
                                <p style="font-size:0.78rem;color:var(--text-muted);margin-top:2px;"><?= $pm['desc'] ?></p>
                            </div>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Notes -->
                <div class="checkout-section">
                    <div class="checkout-section-title">Catatan Pesanan</div>
                    <textarea name="notes" form="checkoutForm" class="form-textarea" rows="3"
                              placeholder="Catatan untuk penjual (opsional)..."></textarea>
                </div>
            </div>

            <!-- ── Order Summary ─────────────────────────────────── -->
            <div>
                <div class="order-summary-card">
                    <div class="checkout-section-title">Ringkasan Pesanan</div>

                    <!-- Items -->
                    <div style="margin-bottom:var(--space-lg);">
                        <?php foreach ($cart as $key => $item): ?>
                        <div style="display:flex;gap:12px;padding:10px 0;border-bottom:1px solid var(--border-color);">
                            <div style="width:60px;height:75px;border-radius:var(--radius-sm);overflow:hidden;background:var(--bg-surface);flex-shrink:0;">
                                <?php if ($item['image']): ?>
                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                     alt="<?= htmlspecialchars($item['name']) ?>"
                                     style="width:100%;height:100%;object-fit:cover;">
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <div style="font-size:0.88rem;font-family:var(--font-heading);margin-bottom:3px;">
                                    <?= htmlspecialchars($item['name']) ?>
                                </div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">
                                    <?= htmlspecialchars($item['size']) ?> · <?= htmlspecialchars($item['color']) ?>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-top:4px;">
                                    <span style="font-size:0.78rem;color:var(--text-muted);">x<?= $item['qty'] ?></span>
                                    <span style="font-size:0.88rem;color:var(--gold-primary);font-weight:600;">
                                        <?= formatRupiah($item['price'] * $item['qty']) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Totals -->
                    <div class="order-line">
                        <span class="order-line-label">Subtotal</span>
                        <span><?= formatRupiah($cartTotal) ?></span>
                    </div>
                    <div class="order-line">
                        <span class="order-line-label">Ongkir (estimasi)</span>
                        <span><?= formatRupiah(25000) ?></span>
                    </div>
                    <?php if ($cartTotal >= (float)getSiteContent('free_shipping_min','500000')): ?>
                    <div class="order-line" style="color:var(--success);">
                        <span>Free Ongkir</span>
                        <span>- <?= formatRupiah(25000) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="order-line total">
                        <span>Total</span>
                        <span id="grandTotal">
                            <?php
                            $ship = $cartTotal >= (float)getSiteContent('free_shipping_min','500000') ? 0 : 25000;
                            echo formatRupiah($cartTotal + $ship);
                            ?>
                        </span>
                    </div>

                    <button class="btn btn-primary" id="placeOrderBtn"
                            style="width:100%;justify-content:center;margin-top:var(--space-lg);height:52px;">
                        Buat Pesanan
                    </button>

                    <a href="https://wa.me/<?= $waNumber ?>?text=<?= urlencode('Halo Shasea! Saya ingin order manual.') ?>"
                       target="_blank"
                       class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px;">
                        Order via WhatsApp
                    </a>

                    <p style="font-size:0.72rem;color:var(--text-muted);text-align:center;margin-top:var(--space-md);line-height:1.6;">
                        Dengan memesan, kamu menyetujui syarat & ketentuan Shasea.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.8);z-index:var(--z-modal);display:none;align-items:center;justify-content:center;padding:var(--space-xl);">
    <div style="background:var(--bg-card);border:1px solid var(--border-color);border-radius:var(--radius-xl);padding:var(--space-2xl);max-width:480px;width:100%;text-align:center;">
        <div style="width:72px;height:72px;background:rgba(122,170,122,0.15);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto var(--space-lg);">
            <svg width="36" height="36" fill="none" viewBox="0 0 24 24" stroke="var(--success)">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h2 style="font-family:var(--font-heading);font-size:2rem;margin-bottom:var(--space-sm);">Pesanan Berhasil!</h2>
        <p style="color:var(--text-muted);margin-bottom:var(--space-sm);">Nomor pesanan kamu:</p>
        <div id="orderNumDisplay" style="font-family:var(--font-display);font-size:1.3rem;color:var(--gold-primary);letter-spacing:0.15em;margin-bottom:var(--space-md);"></div>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-bottom:var(--space-xl);line-height:1.7;">
            Tim Shasea akan segera menghubungi kamu via WhatsApp untuk konfirmasi pesanan dan pembayaran.
        </p>
        <div style="display:flex;gap:var(--space-sm);">
            <a id="waConfirmBtn" href="#" target="_blank" class="btn btn-primary" style="flex:1;justify-content:center;">
                Konfirmasi di WA
            </a>
            <a href="index.php" class="btn btn-ghost" style="flex:1;justify-content:center;">Lanjut Belanja</a>
        </div>
    </div>
</div>

<script>
let selectedPayment = 'transfer_bank';

function selectPayment(el, val) {
    document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    selectedPayment = val;
}

document.getElementById('placeOrderBtn')?.addEventListener('click', async function() {
    const form   = document.getElementById('checkoutForm');
    const data   = Object.fromEntries(new FormData(form));
    data.payment = selectedPayment;
    data.notes   = document.querySelector('textarea[name="notes"]')?.value || '';

    if (!data.name || !data.phone || !data.address || !data.city) {
        window.showToast?.('Mohon lengkapi data pengiriman', 'warning'); return;
    }

    this.innerHTML = 'Memproses...';
    this.disabled  = true;

    const res  = await fetch(window.location.href, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json' },
        body:    JSON.stringify(data)
    });
    const json = await res.json();

    if (json.success) {
        const modal = document.getElementById('successModal');
        modal.style.display   = 'flex';
        document.getElementById('orderNumDisplay').textContent = json.order_number;
        const waMsg = encodeURIComponent(`Halo Shasea! Saya sudah order dengan nomor *${json.order_number}* total *${json.total_fmt}*. Mohon dikonfirmasi ya 🙏`);
        document.getElementById('waConfirmBtn').href = `https://wa.me/<?= $waNumber ?>?text=${waMsg}`;
    } else {
        window.showToast?.(json.message || 'Gagal membuat pesanan', 'error');
        this.innerHTML = 'Buat Pesanan';
        this.disabled  = false;
    }
});
</script>

<?php include 'includes/footer.php'; ?>
