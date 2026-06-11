<?php
/**
 * SHASEA — Product Detail Page
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$db   = getDB();
$slug = trim($_GET['slug'] ?? '');
$src  = htmlspecialchars($_GET['src'] ?? 'direct');

if (!$slug) {
    header('Location: catalog.php');
    exit;
}

$stmt = $db->prepare(
    "SELECT p.*, c.name as category_name, c.slug as category_slug
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.slug = ? AND p.status = 'active'"
);
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('HTTP/1.0 404 Not Found');
    include 'pages/404.php';
    exit;
}

$images  = json_decode($product['images'] ?? '[]', true) ?: [];
$sizes   = json_decode($product['sizes']  ?? '[]', true) ?: [];
$colors  = json_decode($product['colors'] ?? '[]', true) ?: [];
$disc    = ($product['original_price'] > $product['price'])
         ? round((1 - $product['price'] / $product['original_price']) * 100) : 0;
$waNum   = getSiteContent('whatsapp_number', '6281234567890');
$waMsg   = urlencode("Halo Shasea! Saya tertarik dengan produk: *{$product['name']}* (Rp " . number_format($product['price'], 0, ',', '.') . ")\n\nLink: " . BASE_URL . "/product-detail.php?slug={$slug}");

// Related products
$related = $db->prepare(
    "SELECT p.*, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'
     ORDER BY RAND()
     LIMIT 4"
);
$related->execute([$product['category_id'], $product['id']]);
$related = $related->fetchAll();

$pageTitle = $product['name'] . ' | Shasea';
$pageDesc  = mb_substr(strip_tags($product['description'] ?? ''), 0, 160);
$pageImage = !empty($images) ? BASE_URL . '/' . $images[0] : '';
$extraCSS  = ['catalog.css'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="page-content" id="productDetailPage"
     data-product-id="<?= $product['id'] ?>"
     data-source="<?= $src ?>">

    <div class="container">

        <!-- Breadcrumb -->
        <div class="breadcrumb">
            <a href="index.php">Home</a>
            <span class="sep">›</span>
            <a href="catalog.php">Katalog</a>
            <?php if ($product['category_name']): ?>
            <span class="sep">›</span>
            <a href="catalog.php?category=<?= urlencode($product['category_slug']) ?>">
                <?= htmlspecialchars($product['category_name']) ?>
            </a>
            <?php endif; ?>
            <span class="sep">›</span>
            <span class="current"><?= htmlspecialchars($product['name']) ?></span>
        </div>

        <!-- Product Detail Grid -->
        <div class="product-detail-grid">

            <!-- ── Gallery ──────────────────────────────────────── -->
            <div class="product-gallery">
                <div class="gallery-main" id="galleryMain">
                    <?php if (!empty($images)): ?>
                    <img src="<?= htmlspecialchars($images[0]) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         id="mainImage">
                    <?php else: ?>
                    <div class="img-placeholder" style="height:100%;aspect-ratio:3/4;">
                        <svg width="60" height="60" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span style="color:var(--gold-dark);">Shasea</span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (count($images) > 1): ?>
                <div class="gallery-thumbs">
                    <?php foreach ($images as $i => $img): ?>
                    <div class="gallery-thumb <?= $i === 0 ? 'active' : '' ?>"
                         onclick="switchImage('<?= htmlspecialchars($img) ?>', this)">
                        <img src="<?= htmlspecialchars($img) ?>"
                             alt="<?= htmlspecialchars($product['name']) ?> <?= $i+1 ?>"
                             loading="lazy">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- ── Product Info ──────────────────────────────────── -->
            <div class="product-info">

                <div class="product-info-category">
                    <?= htmlspecialchars($product['category_name'] ?? '') ?>
                    <?php if ($product['is_new']): ?>
                    <span class="badge badge-new" style="margin-left:8px;">New</span>
                    <?php endif; ?>
                </div>

                <h1 class="product-info-title"><?= htmlspecialchars($product['name']) ?></h1>

                <!-- Price -->
                <div class="product-info-price">
                    <span class="price-main"><?= formatRupiah($product['price']) ?></span>
                    <?php if ($product['original_price'] > $product['price']): ?>
                    <span class="price-original"><?= formatRupiah($product['original_price']) ?></span>
                    <span class="price-off">Hemat <?= $disc ?>%</span>
                    <?php endif; ?>
                </div>

                <!-- Stock indicator -->
                <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                <div style="font-size:0.82rem;color:var(--danger);margin-bottom:var(--space-md);">
                    ⚠ Stok tersisa <?= $product['stock'] ?> item
                </div>
                <?php elseif ($product['stock'] === 0): ?>
                <div style="font-size:0.82rem;color:var(--text-muted);margin-bottom:var(--space-md);">
                    ✕ Stok habis — hubungi CS untuk pre-order
                </div>
                <?php endif; ?>

                <!-- Color Selector -->
                <?php if (!empty($colors)): ?>
                <div class="variant-section">
                    <div class="variant-label">
                        Warna <span id="selectedColorName"><?= htmlspecialchars($colors[0]['name']) ?></span>
                    </div>
                    <div class="color-options">
                        <?php foreach ($colors as $i => $color): ?>
                        <div class="color-option <?= $i === 0 ? 'selected' : '' ?>"
                             style="background:<?= htmlspecialchars($color['hex']) ?>;"
                             data-color="<?= htmlspecialchars($color['name']) ?>"
                             onclick="selectColor(this)"
                             title="<?= htmlspecialchars($color['name']) ?>">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Size Selector -->
                <?php if (!empty($sizes)): ?>
                <div class="variant-section">
                    <div class="variant-label">
                        Ukuran <span id="selectedSizeName">—</span>
                        <a href="#" style="font-size:0.75rem;color:var(--gold-primary);" onclick="event.preventDefault();document.getElementById('sizeGuide').style.display='block'">
                            Panduan Ukuran
                        </a>
                    </div>
                    <div class="size-options">
                        <?php foreach ($sizes as $size): ?>
                        <div class="size-option"
                             data-size="<?= htmlspecialchars($size) ?>"
                             onclick="selectSize(this)">
                            <?= htmlspecialchars($size) ?>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Size Guide (hidden) -->
                    <div id="sizeGuide" style="display:none;margin-top:var(--space-md);background:var(--bg-surface);border:1px solid var(--border-color);border-radius:var(--radius-md);padding:var(--space-md);">
                        <div style="display:flex;justify-content:space-between;margin-bottom:8px;">
                            <span style="font-size:0.82rem;font-weight:600;color:var(--text-secondary);">Panduan Ukuran</span>
                            <button onclick="document.getElementById('sizeGuide').style.display='none'"
                                    style="background:none;border:none;cursor:pointer;color:var(--text-muted);">✕</button>
                        </div>
                        <table style="width:100%;font-size:0.78rem;color:var(--text-muted);border-collapse:collapse;">
                            <thead>
                                <tr style="color:var(--gold-primary);border-bottom:1px solid var(--border-color);">
                                    <th style="padding:6px;text-align:left;">Size</th>
                                    <th style="padding:6px;">Lingkar Dada</th>
                                    <th style="padding:6px;">Pinggang</th>
                                    <th style="padding:6px;">Panjang</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $guide = ['S'=>['88','66','130'],'M'=>['92','70','132'],'L'=>['96','74','134'],'XL'=>['100','78','136'],'XXL'=>['104','82','138']];
                                foreach ($guide as $sz => $m): if (in_array($sz, $sizes)): ?>
                                <tr style="border-bottom:1px solid var(--border-color);">
                                    <td style="padding:6px;font-weight:600;"><?= $sz ?></td>
                                    <td style="padding:6px;text-align:center;"><?= $m[0] ?> cm</td>
                                    <td style="padding:6px;text-align:center;"><?= $m[1] ?> cm</td>
                                    <td style="padding:6px;text-align:center;"><?= $m[2] ?> cm</td>
                                </tr>
                                <?php endif; endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quantity + Add to Cart -->
                <div class="purchase-row">
                    <div class="qty-control">
                        <button class="qty-btn" onclick="changeQty(-1)">−</button>
                        <input class="qty-input" type="number" id="qtyInput" value="1" min="1" max="<?= $product['stock'] ?: 99 ?>">
                        <button class="qty-btn" onclick="changeQty(1)">+</button>
                    </div>
                    <button class="btn btn-primary add-to-cart-btn"
                            onclick="handleAddToCart()"
                            <?= $product['stock'] === 0 ? 'disabled' : '' ?>>
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <?= $product['stock'] === 0 ? 'Stok Habis' : 'Tambah ke Keranjang' ?>
                    </button>
                </div>

                <!-- Buy Now / WA -->
                <button class="buy-now-btn" onclick="handleBuyNow()">Beli Sekarang</button>

                <a href="https://wa.me/<?= $waNum ?>?text=<?= $waMsg ?>"
                   target="_blank"
                   class="btn btn-ghost" style="width:100%;justify-content:center;margin-top:8px;">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.858L.054 23.25c-.073.244.009.505.209.655a.56.56 0 00.537.071l5.602-1.946A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.012-1.374l-.358-.213-3.716 1.29 1.242-3.62-.234-.373A9.818 9.818 0 112 12c0-5.414 4.405-9.818 9.818-9.818s9.819 4.404 9.819 9.818c0 5.414-4.404 9.818-9.819 9.818z" fill-rule="evenodd"/>
                    </svg>
                    Tanya via WhatsApp
                </a>

                <!-- Accordion: Description, Material, Shipping -->
                <div class="product-accordion">
                    <div class="accordion-item open">
                        <div class="accordion-header">
                            <h4>Deskripsi Produk</h4>
                            <svg class="accordion-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="accordion-body">
                            <?= nl2br(htmlspecialchars($product['description'] ?? '')) ?>
                        </div>
                    </div>

                    <?php if ($product['material']): ?>
                    <div class="accordion-item">
                        <div class="accordion-header">
                            <h4>Material & Perawatan</h4>
                            <svg class="accordion-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="accordion-body">
                            <p><strong>Bahan:</strong> <?= htmlspecialchars($product['material']) ?></p>
                            <p style="margin-top:8px;">Cuci dengan air dingin, jangan diperas, angin-anginkan.</p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="accordion-item">
                        <div class="accordion-header">
                            <h4>Pengiriman & Return</h4>
                            <svg class="accordion-icon" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="accordion-body">
                            <p><?= htmlspecialchars(getSiteContent('shipping_info', 'Pengiriman ke seluruh Indonesia.')) ?></p>
                            <p style="margin-top:8px;"><?= htmlspecialchars(getSiteContent('return_policy', '7 hari return policy.')) ?></p>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
        <div class="related-section">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-lg);">
                <h2 style="font-family:var(--font-heading);font-size:2rem;font-weight:400;">
                    Produk <em style="font-style:italic;color:var(--gold-primary);">Terkait</em>
                </h2>
                <a href="catalog.php?category=<?= urlencode($product['category_slug'] ?? '') ?>"
                   class="btn btn-ghost btn-sm">Lihat Semua</a>
            </div>
            <div class="related-grid">
                <?php foreach ($related as $rp):
                    $rImgs  = json_decode($rp['images'] ?? '[]', true) ?: [];
                    $rImg   = $rImgs[0] ?? '';
                    $rDisc  = ($rp['original_price'] > $rp['price'])
                            ? round((1 - $rp['price'] / $rp['original_price']) * 100) : 0;
                ?>
                <a href="product-detail.php?slug=<?= urlencode($rp['slug']) ?>&src=related"
                   class="product-card reveal"
                   data-product-id="<?= $rp['id'] ?>"
                   data-track-source="related">
                    <div class="product-card-img">
                        <?php if ($rImg): ?>
                        <img src="<?= htmlspecialchars($rImg) ?>" alt="<?= htmlspecialchars($rp['name']) ?>" loading="lazy">
                        <?php else: ?>
                        <div class="img-placeholder"><svg width="32" height="32" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg></div>
                        <?php endif; ?>
                        <?php if ($rp['is_new']): ?>
                        <div class="product-badge"><span class="badge badge-new">New</span></div>
                        <?php endif; ?>
                    </div>
                    <div class="product-card-body">
                        <div class="product-category"><?= htmlspecialchars($rp['category_name'] ?? '') ?></div>
                        <div class="product-name"><?= htmlspecialchars($rp['name']) ?></div>
                        <div class="product-price">
                            <span class="price-current"><?= formatRupiah($rp['price']) ?></span>
                            <?php if ($rDisc > 0): ?>
                            <span class="price-discount">-<?= $rDisc ?>%</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
const PRODUCT_ID = <?= $product['id'] ?>;
let selectedSize  = '';
let selectedColor = '<?= !empty($colors) ? htmlspecialchars($colors[0]['name'], ENT_QUOTES) : '' ?>';

function switchImage(src, thumb) {
    document.getElementById('mainImage').src = src;
    document.querySelectorAll('.gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

function selectSize(el) {
    document.querySelectorAll('.size-option').forEach(s => s.classList.remove('selected'));
    el.classList.add('selected');
    selectedSize = el.dataset.size;
    document.getElementById('selectedSizeName').textContent = selectedSize;
}

function selectColor(el) {
    document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
    el.classList.add('selected');
    selectedColor = el.dataset.color;
    document.getElementById('selectedColorName').textContent = selectedColor;
}

function changeQty(delta) {
    const input = document.getElementById('qtyInput');
    const val   = Math.max(1, parseInt(input.value) + delta);
    input.value = val;
}

function handleAddToCart() {
    const qty = parseInt(document.getElementById('qtyInput').value) || 1;
    <?php if (!empty($sizes)): ?>
    if (!selectedSize) { window.showToast?.('Pilih ukuran terlebih dahulu', 'warning'); return; }
    <?php endif; ?>
    window.addToCart?.(PRODUCT_ID, selectedSize || 'All Size', selectedColor || 'Default', qty);
    window.openCart?.();
}

function handleBuyNow() {
    const qty = parseInt(document.getElementById('qtyInput').value) || 1;
    <?php if (!empty($sizes)): ?>
    if (!selectedSize) { window.showToast?.('Pilih ukuran terlebih dahulu', 'warning'); return; }
    <?php endif; ?>
    // Add to cart then redirect to checkout
    fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'add', product_id: PRODUCT_ID, size: selectedSize || 'All Size', color: selectedColor || 'Default', qty })
    }).then(() => { window.location.href = 'checkout.php'; });
}
</script>

<?php include 'includes/footer.php'; ?>
