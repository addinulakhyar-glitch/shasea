<?php
/**
 * SHASEA — Contact Page
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$pageTitle = 'Hubungi Kami | Shasea';
$pageDesc  = 'Hubungi Shasea melalui WhatsApp, email, atau form kontak. Kami siap membantu pertanyaan dan pesananmu.';
$extraCSS  = ['pages.css'];
include 'includes/header.php';
include 'includes/navbar.php';

$waNumber  = getSiteContent('whatsapp_number', '6281234567890');
$instagram = getSiteContent('instagram_handle', '@shasea.official');
$address   = getSiteContent('address', 'Bandung, Jawa Barat');
?>

<div class="page-content">

    <div class="page-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span class="sep">›</span>
                <span class="current">Hubungi Kami</span>
            </div>
            <h1 class="page-hero-title">Hubungi <em style="font-style:italic;color:var(--gold-primary);">Kami</em></h1>
            <p class="page-hero-count">Kami siap membantu kamu — respon cepat via WhatsApp</p>
        </div>
    </div>

    <div class="container">
        <div class="contact-grid">

            <!-- Left: Contact Info -->
            <div>
                <div class="reveal">
                    <h2 class="contact-info-title">Senang Mendengar<br>dari <em style="font-style:italic;color:var(--gold-primary);">Kamu</em></h2>
                    <p class="contact-info-desc">
                        Ada pertanyaan tentang produk, ukuran, atau pengiriman? Jangan ragu untuk menghubungi kami. Tim Shasea siap membantu dengan cepat dan ramah.
                    </p>
                </div>

                <div class="contact-items">
                    <!-- WhatsApp -->
                    <div class="contact-item reveal">
                        <div class="contact-item-icon">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.533 5.858L.054 23.25c-.073.244.009.505.209.655a.56.56 0 00.537.071l5.602-1.946A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.818 9.818 0 01-5.012-1.374l-.358-.213-3.716 1.29 1.242-3.62-.234-.373A9.818 9.818 0 112 12c0-5.414 4.405-9.818 9.818-9.818s9.819 4.404 9.819 9.818c0 5.414-4.404 9.818-9.819 9.818z" fill-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="contact-item-text">
                            <strong>WhatsApp (Paling Cepat)</strong>
                            <p>Senin – Minggu, 08.00 – 21.00 WIB</p>
                            <a href="https://wa.me/<?= $waNumber ?>?text=<?= urlencode('Halo Shasea! Saya ingin bertanya...') ?>"
                               target="_blank"
                               style="color:var(--gold-primary);font-weight:600;font-size:1rem;margin-top:4px;display:inline-block;">
                                +<?= $waNumber ?>
                            </a>
                        </div>
                    </div>

                    <!-- Instagram -->
                    <div class="contact-item reveal reveal-delay-1">
                        <div class="contact-item-icon">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </div>
                        <div class="contact-item-text">
                            <strong>Instagram</strong>
                            <p>Ikuti koleksi & promo terbaru</p>
                            <a href="https://instagram.com/<?= ltrim($instagram, '@') ?>" target="_blank">
                                <?= htmlspecialchars($instagram) ?>
                            </a>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="contact-item reveal reveal-delay-2">
                        <div class="contact-item-icon">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="contact-item-text">
                            <strong>Email</strong>
                            <p>Untuk pertanyaan bisnis & kerjasama</p>
                            <a href="mailto:hello@shasea.id">hello@shasea.id</a>
                        </div>
                    </div>

                    <!-- Address -->
                    <div class="contact-item reveal reveal-delay-3">
                        <div class="contact-item-icon">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <div class="contact-item-text">
                            <strong>Lokasi</strong>
                            <p><?= htmlspecialchars($address) ?></p>
                            <p style="margin-top:2px;font-size:0.8rem;color:var(--text-faint);">(Khusus pengiriman — belum ada toko offline)</p>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="brand-value reveal" style="padding:var(--space-lg);">
                    <div style="font-family:var(--font-display);font-size:0.75rem;letter-spacing:0.25em;color:var(--gold-primary);text-transform:uppercase;margin-bottom:var(--space-md);">Jam Operasional</div>
                    <?php
                    $hours = [
                        'Senin – Jumat' => '08.00 – 21.00 WIB',
                        'Sabtu'         => '08.00 – 22.00 WIB',
                        'Minggu'        => '09.00 – 21.00 WIB',
                    ];
                    foreach ($hours as $day => $time): ?>
                    <div style="display:flex;justify-content:space-between;font-size:0.85rem;padding:6px 0;border-bottom:1px solid var(--border-color);">
                        <span style="color:var(--text-secondary);"><?= $day ?></span>
                        <span style="color:var(--gold-primary);font-weight:500;"><?= $time ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: Contact Form -->
            <div class="reveal reveal-delay-1">
                <div class="contact-form-card">
                    <h3 class="contact-form-title">Kirim Pesan</h3>

                    <div class="form-success-msg" id="formSuccess">
                        ✓ Pesan berhasil dikirim! Kami akan segera menghubungi kamu.
                    </div>

                    <form id="contactForm" novalidate>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="name" class="form-input"
                                       placeholder="Nama kamu" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor WA / HP *</label>
                                <input type="tel" name="phone" class="form-input"
                                       placeholder="08xxxxxxxxxx" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input"
                                   placeholder="email@kamu.com">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Topik Pesan *</label>
                            <select name="subject" class="form-select" required>
                                <option value="">— Pilih topik —</option>
                                <option value="Tanya Produk">Tanya Produk</option>
                                <option value="Status Pesanan">Status Pesanan</option>
                                <option value="Komplain / Return">Komplain / Return</option>
                                <option value="Kerjasama / Reseller">Kerjasama / Reseller</option>
                                <option value="Saran & Masukan">Saran & Masukan</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Pesan *</label>
                            <textarea name="message" class="form-textarea"
                                      placeholder="Tulis pesanmu di sini..."
                                      rows="5" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            Kirim Pesan
                        </button>

                        <p style="font-size:0.75rem;color:var(--text-muted);text-align:center;margin-top:var(--space-md);">
                            Atau langsung chat via
                            <a href="https://wa.me/<?= $waNumber ?>" target="_blank"
                               style="color:var(--gold-primary);">WhatsApp</a>
                            untuk respon lebih cepat.
                        </p>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>

<script>
document.getElementById('contactForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn  = this.querySelector('button[type="submit"]');
    const data = Object.fromEntries(new FormData(this));

    // Basic validation
    if (!data.name || !data.phone || !data.subject || !data.message) {
        window.showToast?.('Mohon lengkapi semua field yang wajib diisi', 'warning');
        return;
    }

    btn.innerHTML = '<span style="opacity:.7">Mengirim...</span>';
    btn.disabled  = true;

    try {
        const res  = await fetch('api/contact.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
            document.getElementById('formSuccess').classList.add('show');
            this.reset();
            window.showToast?.('Pesan berhasil dikirim!', 'success');
        } else {
            window.showToast?.(json.message || 'Gagal mengirim pesan', 'error');
        }
    } catch {
        window.showToast?.('Terjadi kesalahan. Coba lagi.', 'error');
    }

    btn.innerHTML = '<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg> Kirim Pesan';
    btn.disabled  = false;
});
</script>

<?php include 'includes/footer.php'; ?>
