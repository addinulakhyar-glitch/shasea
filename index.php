<?php
/**
 * SHASEA — Homepage
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$db = getDB();

// Featured products
$featured = $db->query(
    "SELECT p.*, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.is_featured = 1 AND p.status = 'active'
     ORDER BY p.created_at DESC
     LIMIT 8"
)->fetchAll();

// New arrivals
$newArrivals = $db->query(
    "SELECT p.*, c.name as category_name
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     WHERE p.is_new = 1 AND p.status = 'active'
     ORDER BY p.created_at DESC
     LIMIT 8"
)->fetchAll();

// Active categories
$categories = $db->query(
    "SELECT c.*, COUNT(p.id) as product_count
     FROM categories c
     LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
     WHERE c.is_active = 1 AND c.parent_id IS NULL
     GROUP BY c.id
     ORDER BY c.sort_order
     LIMIT 6"
)->fetchAll();

// Active banners
$banners = $db->query(
    "SELECT * FROM banners WHERE is_active=1 ORDER BY sort_order LIMIT 3"
)->fetchAll();

// Testimonials (static for now)
$testimonials = [
    ['text' => 'Kualitasnya luar biasa, bahan adem dan jahitannya rapi banget!', 'author' => 'Rina A.'],
    ['text' => 'Pengiriman cepat, packing aman, produknya persis foto.', 'author' => 'Dewi S.'],
    ['text' => 'Gamis Naura beneran cantik banget, udah beli 3 warna!', 'author' => 'Fathia R.'],
    ['text' => 'CS ramah dan responsif, belanja jadi nyaman.', 'author' => 'Nurul H.'],
    ['text' => 'Outer Kiara-nya elegan banget, cocok buat kondangan.', 'author' => 'Sari M.'],
    ['text' => 'Ukurannya pas sesuai size guide, satisfied banget!', 'author' => 'Laila K.'],
    ['text' => 'Warna baju sama persis kayak foto, gak kecewa sama sekali.', 'author' => 'Aisyah P.'],
    ['text' => 'Bahan premium tapi harga terjangkau, worth it!', 'author' => 'Winda F.'],
];

function buildCard($p, $source = 'homepage') {
    $imgs = json_decode($p['images'] ?? '[]', true);
    $img  = $imgs[0] ?? '';
    $disc = ($p['original_price'] > $p['price'])
          ? round((1 - $p['price'] / $p['original_price']) * 100) : 0;
    return sprintf(
        '<a href="product-detail.php?slug=%s&src=%s"
            class="product-card reveal"
            data-product-id="%d"
            data-track-source="%s">
          <div class="product-card-img">
            %s
            <div class="product-badge">
              %s%s
            </div>
          </div>
          <div class="product-card-body">
            <div class="product-category">%s</div>
            <div class="product-name">%s</div>
            <div class="product-price">
              <span class="price-current">%s</span>
              %s%s
            </div>
          </div>
        </a>',
        htmlspecialchars($p['slug']), $source, $p['id'], $source,
        $img
            ? '<img src="'.htmlspecialchars($img).'" alt="'.htmlspecialchars($p['name']).'" loading="lazy">'
            : '<div class="img-placeholder"><svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"/></svg><span>Shasea</span></div>',
        $p['is_new']  ? '<span class="badge badge-new">New</span>'     : '',
        $disc > 0     ? '<span class="badge badge-sale">-'.$disc.'%</span>' : '',
        htmlspecialchars($p['category_name'] ?? ''),
        htmlspecialchars($p['name']),
        formatRupiah($p['price']),
        $p['original_price'] > $p['price']
            ? '<span class="price-original">'.formatRupiah($p['original_price']).'</span>' : '',
        $disc > 0
            ? '<span class="price-discount">Hemat '.$disc.'%</span>' : ''
    );
}

$heroPage  = true;
$pageTitle = 'Shasea | Busana Muslimah Premium';
$pageDesc  = 'Shasea — Koleksi busana muslimah premium yang elegan, berkualitas, dan nyaman untuk perempuan modern.';
$extraCSS  = ['home.css'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="page-content" style="padding-top:0">

    <!-- ══════════════════════ HERO ══════════════════════════════ -->
    <section class="hero">
        <div class="hero-bg">
            <?php if (!empty($banners)): ?>
            <img src="<?= htmlspecialchars($banners[0]['image']) ?>"
                 alt="Shasea Collection"
                 onerror="this.parentElement.style.background='var(--bg-surface)'">
            <?php else: ?>
            <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--bg-secondary),var(--bg-surface))"></div>
            <?php endif; ?>
        </div>
        <div class="hero-bg-pattern"></div>

        <div class="hero-content">
            <div class="hero-label">
                <?= htmlspecialchars($banners[0]['badge_text'] ?? 'New Collection 2025') ?>
            </div>
            <h1 class="hero-title">
                <?= nl2br(htmlspecialchars(getSiteContent('hero_tagline', 'Elegance in\nEvery Thread'))) ?>
            </h1>
            <p class="hero-desc">
                <?= htmlspecialchars(getSiteContent('hero_subtitle', 'Koleksi busana muslimah premium yang menggabungkan keanggunan dan kenyamanan dalam setiap detail.')) ?>
            </p>
            <div class="hero-actions">
                <a href="catalog.php" class="btn btn-primary btn-lg">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Shop Now
                </a>
                <a href="about.php" class="btn btn-outline btn-lg">Our Story</a>
            </div>
        </div>

        <!-- Stats -->
        <div class="hero-stats" style="max-width:1280px;margin:0 auto;right:var(--space-xl);">
            <div class="hero-stat">
                <div class="hero-stat-num">500+</div>
                <div class="hero-stat-label">Produk</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">10K+</div>
                <div class="hero-stat-label">Pelanggan</div>
            </div>
            <div class="hero-stat">
                <div class="hero-stat-num">98%</div>
                <div class="hero-stat-label">Satisfied</div>
            </div>
        </div>

        <div class="hero-scroll-indicator">
            <div class="scroll-dot"></div>
            <span>Scroll</span>
        </div>
    </section>

    <!-- ══════════════════════ CATEGORIES STRIP ══════════════════ -->
    <section class="categories-strip">
        <div class="categories-scroll">
            <a href="catalog.php" class="cat-chip active">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                Semua
            </a>
            <?php foreach ($categories as $cat): ?>
            <a href="catalog.php?category=<?= urlencode($cat['slug']) ?>" class="cat-chip">
                <?= htmlspecialchars($cat['name']) ?>
                <span style="font-size:0.7rem;color:var(--gold-dark);"><?= $cat['product_count'] ?></span>
            </a>
            <?php endforeach; ?>
            <a href="catalog.php?filter=new" class="cat-chip" style="border-color:var(--border-gold);color:var(--gold-primary);">
                ✦ New Arrival
            </a>
        </div>
    </section>

    <!-- ══════════════════════ FEATURED PRODUCTS ═════════════════ -->
    <?php if (!empty($featured)): ?>
    <section class="section-pad">
        <div class="container">
            <div class="section-header">
                <div class="section-badge reveal">Featured Collection</div>
                <h2 class="section-title reveal">Pilihan <em>Terbaik</em> Kami</h2>
                <p class="section-desc reveal">Koleksi unggulan yang dipilih dengan cermat untuk tampilan yang anggun dan berkelas.</p>
            </div>

            <div class="products-grid">
                <?php foreach ($featured as $p): ?>
                <?= buildCard($p, 'homepage') ?>
                <?php endforeach; ?>
            </div>

            <div style="text-align:center;margin-top:var(--space-2xl);" class="reveal">
                <a href="catalog.php" class="btn btn-outline btn-lg">
                    Lihat Semua Koleksi
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ══════════════════════ COLLECTIONS BENTO ═════════════════ -->
    <?php if (!empty($categories)): ?>
    <section class="section-pad" style="background:var(--bg-secondary);">
        <div class="container">
            <div class="section-header">
                <div class="section-badge reveal">Kategori</div>
                <h2 class="section-title reveal">Jelajahi <em>Koleksi</em></h2>
            </div>

            <div class="collections-bento reveal">
                <?php foreach (array_slice($categories, 0, 5) as $i => $cat): ?>
                <a href="catalog.php?category=<?= urlencode($cat['slug']) ?>" class="collection-card">
                    <?php if (!empty($cat['image'])): ?>
                    <img src="<?= htmlspecialchars($cat['image']) ?>" alt="<?= htmlspecialchars($cat['name']) ?>" loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,var(--bg-surface) 0%,var(--bg-card) 100%)"></div>
                    <?php endif; ?>
                    <div class="collection-info">
                        <div class="collection-cat">Koleksi</div>
                        <div class="collection-name"><?= htmlspecialchars($cat['name']) ?></div>
                        <div class="collection-count"><?= $cat['product_count'] ?> Produk</div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ══════════════════════ NEW ARRIVALS ══════════════════════ -->
    <?php if (!empty($newArrivals)): ?>
    <section class="section-pad">
        <div class="container">
            <div class="section-header" style="display:flex;justify-content:space-between;align-items:flex-end;text-align:left;">
                <div>
                    <div class="section-badge">New Arrival</div>
                    <h2 class="section-title" style="margin-bottom:0;">Terbaru dari <em>Shasea</em></h2>
                </div>
                <a href="catalog.php?filter=new" class="btn btn-ghost" style="flex-shrink:0;">
                    Lihat Semua
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>

            <div class="arrivals-scroll-wrapper" style="margin-top:var(--space-xl);">
                <div class="arrivals-scroll">
                    <?php foreach ($newArrivals as $p): ?>
                    <?= buildCard($p, 'homepage') ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ══════════════════════ BRAND STORY ═══════════════════════ -->
    <section class="brand-story section-pad">
        <div class="container">
            <div class="brand-story-grid">
                <div class="brand-story-img reveal">
                    <img src="assets/images/brand-story.jpg" alt="Shasea Story"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,var(--bg-surface),var(--bg-card))'">
                    <div class="img-badge">
                        <div style="font-family:var(--font-display);font-size:1.8rem;color:var(--gold-primary);font-weight:600;line-height:1;">2020</div>
                        <div style="font-size:0.7rem;color:var(--text-muted);letter-spacing:0.1em;">Berdiri sejak</div>
                    </div>
                </div>

                <div class="reveal reveal-delay-2">
                    <div class="section-badge" style="margin-bottom:var(--space-lg);">Our Story</div>
                    <h2 class="section-title" style="text-align:left;">Diciptakan dengan <em>Cinta</em> untuk Muslimah</h2>
                    <p style="color:var(--text-muted);line-height:2;margin:var(--space-lg) 0;">
                        <?= htmlspecialchars(getSiteContent('about_short', 'Shasea hadir untuk perempuan muslimah modern yang menginginkan busana berkualitas tinggi dengan desain yang timeless dan elegan.')) ?>
                    </p>

                    <div class="brand-values">
                        <?php
                        $values = [
                            ['icon'=>'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'title'=>'Premium Quality', 'desc'=>'Bahan pilihan, jahitan presisi'],
                            ['icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'title'=>'Modest Fashion', 'desc'=>'Elegan, anggun, penuh makna'],
                            ['icon'=>'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'title'=>'Pengiriman Cepat', 'desc'=>'Same-day & next-day delivery'],
                            ['icon'=>'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'title'=>'Harga Terjangkau', 'desc'=>'Premium dengan harga fair'],
                        ];
                        foreach ($values as $v): ?>
                        <div class="brand-value">
                            <div class="brand-value-icon">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?= $v['icon'] ?>"/>
                                </svg>
                            </div>
                            <h4><?= $v['title'] ?></h4>
                            <p><?= $v['desc'] ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <a href="about.php" class="btn btn-primary" style="margin-top:var(--space-xl);">
                        Kenali Shasea Lebih Dalam
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══════════════════════ TESTIMONIALS TICKER ═══════════════ -->
    <section style="background:var(--bg-secondary);border-top:1px solid var(--border-color);border-bottom:1px solid var(--border-color);padding:var(--space-lg) 0;overflow:hidden;">
        <div class="testimonial-ticker">
            <div class="testimonial-track">
                <?php foreach (array_merge($testimonials, $testimonials) as $t): ?>
                <div class="testimonial-item">
                    <div class="testimonial-stars">★★★★★</div>
                    <span class="testimonial-text">"<?= htmlspecialchars($t['text']) ?>"</span>
                    <span class="testimonial-author">— <?= htmlspecialchars($t['author']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══════════════════════ NEWSLETTER ═══════════════════════ -->
    <section class="newsletter-section">
        <div class="container container-sm">
            <div class="section-badge reveal">Newsletter</div>
            <h2 class="section-title reveal">Jangan Lewatkan <em>Koleksi Terbaru</em></h2>
            <p class="section-desc reveal">
                Subscribe newsletter kami dan dapatkan notifikasi koleksi terbaru, promo spesial, dan konten eksklusif.
            </p>
            <form class="newsletter-form" id="newsletterForm" style="margin-top:var(--space-xl);">
                <input type="email" name="email" placeholder="Masukkan email kamu..." required>
                <button type="submit">Daftar</button>
            </form>
        </div>
    </section>

</div>

<?php include 'includes/footer.php'; ?>
