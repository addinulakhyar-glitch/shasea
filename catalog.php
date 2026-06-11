<?php
/**
 * SHASEA — Catalog Page
 */
define('IN_SHASEA', true);
require_once __DIR__ . '/config/database.php';

$db = getDB();

// Active categories for sidebar
$categories = $db->query(
    "SELECT c.*, COUNT(p.id) as product_count
     FROM categories c
     LEFT JOIN products p ON c.id = p.category_id AND p.status='active'
     WHERE c.is_active=1
     GROUP BY c.id
     ORDER BY c.sort_order"
)->fetchAll();

// Active category filter
$activeCat = $_GET['category'] ?? '';
$filter    = $_GET['filter']   ?? '';

// Determine page heading
$pageHeading = 'Semua Koleksi';
if ($activeCat) {
    $catRow = $db->prepare("SELECT name FROM categories WHERE slug = ?");
    $catRow->execute([$activeCat]);
    $catRow = $catRow->fetch();
    if ($catRow) $pageHeading = $catRow['name'];
}
if ($filter === 'new')  $pageHeading = 'New Arrivals';
if ($filter === 'sale') $pageHeading = 'Sale';

$pageTitle = $pageHeading . ' | Shasea';
$pageDesc  = 'Temukan koleksi busana muslimah premium Shasea — ' . strtolower($pageHeading) . '.';
$extraCSS  = ['catalog.css'];
$extraJS   = ['catalog.js'];
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="page-content">

    <!-- Page Hero -->
    <div class="page-hero">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a>
                <span class="sep">›</span>
                <a href="catalog.php">Katalog</a>
                <?php if ($activeCat || $filter): ?>
                <span class="sep">›</span>
                <span class="current"><?= htmlspecialchars($pageHeading) ?></span>
                <?php endif; ?>
            </div>
            <div class="page-hero-inner">
                <div>
                    <h1 class="page-hero-title">
                        <?= htmlspecialchars($pageHeading) ?><em>.</em>
                    </h1>
                    <p class="page-hero-count" id="productCount">Memuat produk...</p>
                </div>
                <div style="display:flex;gap:var(--space-sm);align-items:center;">
                    <button id="filterToggle" class="btn btn-ghost btn-sm"
                            style="display:none;">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-width="2" d="M3 4h18M7 12h10M11 20h2"/>
                        </svg>
                        Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Catalog Layout -->
    <div class="container">
        <div class="catalog-layout">

            <!-- ── Filter Sidebar ───────────────────────────────── -->
            <aside class="filter-sidebar" id="filterSidebar">

                <div class="filter-header">
                    <h3>Filter</h3>
                    <button class="filter-reset" onclick="window.resetFilters()">Reset</button>
                    <button id="filterClose" style="display:none;background:none;border:none;cursor:pointer;color:var(--text-muted);margin-left:8px;">✕</button>
                </div>

                <form id="filterForm">

                    <!-- Hidden fields for URL params -->
                    <?php if ($activeCat): ?>
                    <input type="hidden" name="_cat" value="<?= htmlspecialchars($activeCat) ?>">
                    <?php endif; ?>
                    <?php if ($filter): ?>
                    <input type="hidden" name="_filter" value="<?= htmlspecialchars($filter) ?>">
                    <?php endif; ?>

                    <!-- Category -->
                    <div class="filter-group open">
                        <div class="filter-group-header">
                            <span class="filter-group-title">Kategori</span>
                            <span class="filter-group-icon">▾</span>
                        </div>
                        <div class="filter-group-body">
                            <?php foreach ($categories as $cat): ?>
                            <label class="filter-check">
                                <input type="checkbox" name="category[]"
                                       value="<?= htmlspecialchars($cat['slug']) ?>"
                                       <?= ($activeCat === $cat['slug']) ? 'checked' : '' ?>>
                                <span><?= htmlspecialchars($cat['name']) ?></span>
                                <span class="filter-check-count"><?= $cat['product_count'] ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-group open">
                        <div class="filter-group-header">
                            <span class="filter-group-title">Harga</span>
                            <span class="filter-group-icon">▾</span>
                        </div>
                        <div class="filter-group-body">
                            <div class="price-range-wrap">
                                <div class="price-range-labels">
                                    Min: <span id="priceMinLabel">Rp 0</span>
                                </div>
                                <input type="range" id="priceMin" name="price_min"
                                       min="0" max="1000000" step="25000" value="0">
                                <div class="price-range-labels" style="margin-top:var(--space-sm);">
                                    Max: <span id="priceMaxLabel">Rp 1.000.000</span>
                                </div>
                                <input type="range" id="priceMax" name="price_max"
                                       min="0" max="1000000" step="25000" value="1000000">
                            </div>
                        </div>
                    </div>

                    <!-- Size -->
                    <div class="filter-group open">
                        <div class="filter-group-header">
                            <span class="filter-group-title">Ukuran</span>
                            <span class="filter-group-icon">▾</span>
                        </div>
                        <div class="filter-group-body">
                            <div class="size-grid">
                                <?php foreach (['S','M','L','XL','XXL','All Size'] as $s): ?>
                                <!-- Hidden checkbox synced with chip -->
                                <input type="checkbox" name="size[]" value="<?= $s ?>" style="display:none" id="size_<?= $s ?>">
                                <span class="size-chip" data-size="<?= $s ?>"
                                      onclick="document.getElementById('size_<?= $s ?>').checked = !document.getElementById('size_<?= $s ?>').checked;this.classList.toggle('active');">
                                    <?= $s ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="filter-group">
                        <div class="filter-group-header">
                            <span class="filter-group-title">Ketersediaan</span>
                            <span class="filter-group-icon">▾</span>
                        </div>
                        <div class="filter-group-body">
                            <label class="filter-check">
                                <input type="checkbox" name="in_stock" value="1">
                                <span>Stok tersedia</span>
                            </label>
                            <label class="filter-check">
                                <input type="checkbox" name="is_new" value="1" <?= $filter === 'new' ? 'checked' : '' ?>>
                                <span>New Arrival</span>
                            </label>
                            <label class="filter-check">
                                <input type="checkbox" name="on_sale" value="1" <?= $filter === 'sale' ? 'checked' : '' ?>>
                                <span>Sedang Diskon</span>
                            </label>
                        </div>
                    </div>

                </form>
            </aside>

            <!-- ── Products Area ────────────────────────────────── -->
            <div>
                <!-- Toolbar -->
                <div class="catalog-toolbar">
                    <div class="catalog-search">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" id="catalogSearch" placeholder="Cari produk...">
                    </div>

                    <div class="catalog-toolbar-right">
                        <select class="sort-select" id="sortSelect">
                            <option value="newest">Terbaru</option>
                            <option value="popular">Terpopuler</option>
                            <option value="price_asc">Harga: Terendah</option>
                            <option value="price_desc">Harga: Tertinggi</option>
                            <option value="name_asc">A – Z</option>
                        </select>

                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" title="Grid">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                                    <rect x="0" y="0" width="7" height="7"/><rect x="9" y="0" width="7" height="7"/>
                                    <rect x="0" y="9" width="7" height="7"/><rect x="9" y="9" width="7" height="7"/>
                                </svg>
                            </button>
                            <button class="view-btn" data-view="list" title="List">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Active Filter Tags -->
                <div id="activeFilters" class="active-filters"></div>

                <!-- Grid -->
                <div class="catalog-grid" id="catalogGrid"></div>

                <!-- Pagination -->
                <div id="paginationWrap" class="pagination"></div>
            </div>

        </div>
    </div>

</div>

<script>
// Pass URL params to catalog.js filters on load
document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const category  = urlParams.get('category');
    const filter    = urlParams.get('filter');

    if (category) {
        const cb = document.querySelector(`input[name="category[]"][value="${category}"]`);
        if (cb) cb.checked = true;
    }
    if (filter === 'new') {
        const cb = document.querySelector('input[name="is_new"]');
        if (cb) cb.checked = true;
    }
    if (filter === 'sale') {
        const cb = document.querySelector('input[name="on_sale"]');
        if (cb) cb.checked = true;
    }

    // Show filter toggle on mobile
    if (window.innerWidth <= 768) {
        document.getElementById('filterToggle').style.display = 'flex';
        document.getElementById('filterClose').style.display  = 'block';
    }
});
</script>

<?php include 'includes/footer.php'; ?>
