<?php
/**
 * SHASEA — About Page
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Tentang Kami | Shasea';
$pageDesc  = 'Kenali Shasea lebih dalam — brand busana muslimah premium yang hadir untuk perempuan modern.';
$extraCSS  = ['pages.css'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="page-content">

    <!-- About Hero -->
    <section class="about-hero">
        <div class="container">
            <div class="about-hero-grid">
                <div class="reveal">
                    <div class="section-badge" style="margin-bottom:var(--space-lg);">Our Story</div>
                    <h1 class="about-tagline">
                        Diciptakan dengan<br>
                        <em>Cinta</em> untuk<br>
                        Muslimah Modern
                    </h1>
                    <p style="color:var(--text-muted);margin-top:var(--space-lg);font-size:0.95rem;line-height:2;max-width:460px;">
                        <?= htmlspecialchars(getSiteContent('about_short', 'Shasea hadir untuk perempuan muslimah modern yang menginginkan busana berkualitas tinggi dengan desain yang timeless dan elegan.')) ?>
                    </p>
                    <div style="display:flex;gap:var(--space-md);margin-top:var(--space-xl);">
                        <a href="catalog.php" class="btn btn-primary">Lihat Koleksi</a>
                        <a href="contact.php" class="btn btn-outline">Hubungi Kami</a>
                    </div>
                </div>
                <div class="about-hero-img reveal reveal-delay-2">
                    <img src="assets/images/about-hero.jpg" alt="Shasea About"
                         onerror="this.parentElement.style.background='linear-gradient(135deg,var(--bg-surface),var(--bg-card))'">
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item reveal">
                    <div class="stat-number" data-count="10000" data-suffix="+">0+</div>
                    <div class="stat-label">Happy Customers</div>
                </div>
                <div class="stat-item reveal reveal-delay-1">
                    <div class="stat-number" data-count="500" data-suffix="+">0+</div>
                    <div class="stat-label">Produk Tersedia</div>
                </div>
                <div class="stat-item reveal reveal-delay-2">
                    <div class="stat-number" data-count="34" data-suffix="">0</div>
                    <div class="stat-label">Provinsi Terjangkau</div>
                </div>
                <div class="stat-item reveal reveal-delay-3">
                    <div class="stat-number" data-count="5" data-suffix=" Tahun">0 Tahun</div>
                    <div class="stat-label">Berdiri Sejak 2020</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="about-mission">
        <div class="container">
            <div class="about-mission-grid">
                <div class="reveal">
                    <div class="about-mission-label">Misi Kami</div>
                    <h2>Menghadirkan <em style="font-style:italic;color:var(--gold-primary);">Keanggunan</em><br>dalam Setiap Detail</h2>
                    <div class="divider-gold" style="margin:var(--space-lg) 0;"><span>✦</span></div>
                </div>
                <div class="reveal reveal-delay-1">
                    <p class="about-mission-text">
                        Shasea lahir dari keyakinan bahwa setiap perempuan muslimah berhak tampil anggun, percaya diri, dan nyaman dalam berbusana. Kami tidak hanya menjual pakaian — kami menghadirkan pengalaman berpakaian yang bermakna.
                    </p>
                    <p class="about-mission-text">
                        Setiap produk Shasea dirancang dengan cermat memperhatikan kualitas bahan, keindahan desain, dan kenyamanan pemakainya. Dari pemilihan kain premium hingga detail jahitan terakhir, kami berkomitmen pada kesempurnaan.
                    </p>
                    <p class="about-mission-text">
                        Dengan lebih dari 10.000 pelanggan setia di seluruh Indonesia, Shasea terus berkembang sebagai brand muslimah fashion yang dipercaya dan dicintai.
                    </p>

                    <!-- Timeline -->
                    <div style="margin-top:var(--space-xl);">
                        <?php
                        $milestones = [
                            ['year'=>'2020','event'=>'Shasea didirikan dengan koleksi perdana 20 produk'],
                            ['year'=>'2021','event'=>'Membuka official store dan mencapai 1.000 pelanggan'],
                            ['year'=>'2022','event'=>'Ekspansi ke marketplace dan meluncurkan website resmi'],
                            ['year'=>'2023','event'=>'Koleksi Ramadan terlaris dengan 5.000+ pesanan'],
                            ['year'=>'2025','event'=>'10.000+ pelanggan aktif, 500+ koleksi produk'],
                        ];
                        foreach ($milestones as $m): ?>
                        <div style="display:flex;gap:var(--space-md);margin-bottom:var(--space-md);align-items:flex-start;">
                            <div style="min-width:60px;font-family:var(--font-heading);font-size:1.1rem;color:var(--gold-primary);font-weight:500;padding-top:2px;"><?= $m['year'] ?></div>
                            <div style="flex:1;padding-top:4px;font-size:0.88rem;color:var(--text-muted);border-left:1px solid var(--border-color);padding-left:var(--space-md);"><?= $m['event'] ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="section-pad" style="background:var(--bg-secondary);border-top:1px solid var(--border-color);">
        <div class="container">
            <div class="section-header">
                <div class="section-badge reveal">Nilai Kami</div>
                <h2 class="section-title reveal">Mengapa <em>Memilih</em> Shasea?</h2>
            </div>

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-xl);">
                <?php
                $values = [
                    ['icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                      'title'=>'Kualitas Premium', 'desc'=>'Setiap produk menggunakan bahan pilihan berkualitas tinggi yang telah melalui seleksi ketat untuk memastikan kenyamanan dan ketahanan.'],
                    ['icon'=>'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                      'title'=>'Desain Bermakna', 'desc'=>'Setiap koleksi dirancang dengan memadukan estetika modern dan nilai-nilai modest fashion yang menonjolkan keindahan sejati.'],
                    ['icon'=>'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                      'title'=>'Harga Transparan', 'desc'=>'Kami berkomitmen memberikan produk premium dengan harga yang adil. Tidak ada hidden cost, semua transparan sejak awal.'],
                    ['icon'=>'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z',
                      'title'=>'Layanan Prima', 'desc'=>'Tim customer service kami siap membantu kapanpun kamu membutuhkan — dari pemilihan produk hingga after-sales support.'],
                    ['icon'=>'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
                      'title'=>'Packing Aman', 'desc'=>'Setiap pesanan dikemas dengan teliti menggunakan packaging premium agar produk sampai dalam kondisi sempurna.'],
                    ['icon'=>'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
                      'title'=>'Garansi Return', 'desc'=>'Kami percaya pada kualitas produk kami. Tidak puas? Kembalikan dalam 7 hari dan kami akan mencarikan solusi terbaik.'],
                ];
                foreach ($values as $i => $v): ?>
                <div class="brand-value reveal reveal-delay-<?= $i % 3 ?>" style="padding:var(--space-xl);">
                    <div class="brand-value-icon" style="width:48px;height:48px;margin-bottom:var(--space-md);">
                        <svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="<?= $v['icon'] ?>"/>
                        </svg>
                    </div>
                    <h4 style="font-family:var(--font-heading);font-size:1.2rem;font-weight:500;margin-bottom:8px;"><?= $v['title'] ?></h4>
                    <p style="font-size:0.88rem;color:var(--text-muted);line-height:1.8;"><?= $v['desc'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="section-pad" style="text-align:center;">
        <div class="container container-sm">
            <div class="section-badge reveal">Bergabunglah</div>
            <h2 class="section-title reveal">Mulai Perjalananmu<br>bersama <em>Shasea</em></h2>
            <p class="section-desc reveal">Temukan koleksi busana muslimah premium yang akan membuat kamu tampil percaya diri setiap hari.</p>
            <div style="display:flex;gap:var(--space-md);justify-content:center;margin-top:var(--space-xl);" class="reveal">
                <a href="catalog.php" class="btn btn-primary btn-lg">Belanja Sekarang</a>
                <a href="contact.php" class="btn btn-outline btn-lg">Hubungi Kami</a>
            </div>
        </div>
    </section>

</div>

<?php include 'includes/footer.php'; ?>
