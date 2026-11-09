<?php
/**
 * Single Post Template for Gema Keadilan Jawa Barat
 */

$site_url = get_site_url();
$logo_url = $site_url . '/wp-content/uploads/logo-gema-keadilan.png';

$categories = get_the_category();
$category_name = !empty($categories) ? esc_html($categories[0]->name) : 'Gema Keadilan';

$featured_img_url = get_the_post_thumbnail_url(get_the_ID(), 'full');
if (!$featured_img_url) {
    $featured_img_url = $site_url . '/wp-content/uploads/hero-silaturahmi.jpg';
}

$post_url = get_permalink();
$post_title = get_the_title();
$encoded_url = rawurlencode($post_url);
$encoded_title = rawurlencode($post_title);

$wa_share_url = 'https://api.whatsapp.com/send?text=' . $encoded_title . '%20' . $encoded_url;
$fb_share_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $encoded_url;
$x_share_url  = 'https://twitter.com/intent/tweet?text=' . $encoded_title . '&url=' . $encoded_url;
$tg_share_url = 'https://t.me/share/url?url=' . $encoded_url . '&text=' . $encoded_title;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?php the_title(); ?> &ndash; Gema Keadilan Jawa Barat</title>
  <?php wp_head(); ?>
</head>
<body <?php body_class('single-gema-post'); ?>>

<!-- HEADER -->
<header class="top">
  <div class="wrap">
    <a class="brand" href="<?php echo esc_url($site_url); ?>/">
      <img src="<?php echo esc_url($logo_url); ?>" alt="Logo Gema Keadilan Provinsi Jawa Barat" data-fallback="brand">
      <span class="mark">GK</span>
      <span><b>Gema Keadilan</b><small>Provinsi Jawa Barat</small></span>
    </a>
    <nav class="top-nav" aria-label="Navigasi utama">
      <ul>
        <li><a href="<?php echo esc_url($site_url); ?>/">Beranda</a></li>
        <li><a href="<?php echo esc_url($site_url); ?>/#tentang">Tentang</a></li>
        <li><a href="<?php echo esc_url($site_url); ?>/#dirgahayu">21 Tahun</a></li>
        <li><a href="<?php echo esc_url($site_url); ?>/#kegiatan">Kegiatan</a></li>
        <li><a href="<?php echo esc_url($site_url); ?>/#pimpinan">Pimpinan</a></li>
        <li><a href="<?php echo esc_url($site_url); ?>/artikel/" style="color:var(--orange);">Artikel</a></li>
        <li><a class="btn" href="<?php echo esc_url($site_url); ?>/#gabung">Gabung</a></li>
      </ul>
    </nav>
  </div>
</header>

<!-- MAIN ARTICLE CONTENT -->
<main class="single-article-main">
  <div class="wrap" style="max-width: 860px; margin-top: 48px; margin-bottom: 80px;">
    
    <div style="margin-bottom: 24px;">
      <a href="<?php echo esc_url($site_url); ?>/artikel/" class="back-link">
        &larr; Kembali ke Semua Artikel
      </a>
    </div>

    <header class="article-header">
      <div class="article-meta-tags">
        <span class="chip" style="background:var(--pink); font-size:12px; padding:4px 12px;"><?php echo $category_name; ?></span>
        <span style="font-weight:700; font-size:14px; color:var(--brown); margin-left:12px;">
          <?php echo get_the_date('j F Y'); ?>
        </span>
        <span style="font-weight:700; font-size:14px; color:var(--stone); margin-left:8px;">
          &bull; Oleh <?php the_author(); ?>
        </span>
      </div>

      <h1 class="display article-title">
        <?php the_title(); ?>
      </h1>
    </header>

    <?php if ($featured_img_url): ?>
      <figure class="article-featured-figure">
        <img src="<?php echo esc_url($featured_img_url); ?>" alt="<?php the_title_attribute(); ?>">
      </figure>
    <?php endif; ?>

    <div class="article-body-content">
      <?php
      while (have_posts()) :
          the_post();
          the_content();
      endwhile;
      ?>
    </div>

    <!-- SOCIAL SHARE SECTION -->
    <div class="article-share-section">
      <div class="share-header">
        <span class="share-title">
          <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92c0-1.61-1.31-2.92-2.92-2.92z"/></svg>
          Bagikan Artikel Ini:
        </span>
        <button type="button" class="share-copy-btn" id="gkCopyLinkBtn" data-url="<?php echo esc_url($post_url); ?>" aria-label="Salin Tautan Artikel">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
          <span id="gkCopyBtnText">Salin Tautan</span>
        </button>
      </div>

      <div class="share-buttons">
        <!-- WhatsApp -->
        <a href="<?php echo esc_url($wa_share_url); ?>" class="share-btn share-wa" target="_blank" rel="noopener" aria-label="Bagikan ke WhatsApp">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.816 9.816 0 0 0 12.04 2m.01 1.67c2.2 0 4.26.86 5.82 2.42a8.225 8.225 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24-1.48 0-2.93-.4-4.2-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.196 8.196 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24m4.52 11.66c-.19-.09-1.1-.54-1.27-.6-.17-.06-.3-.09-.42.09-.12.19-.48.6-.59.72-.11.12-.22.14-.4.05-.19-.09-.79-.29-1.5-1.01-.56-.55-.93-1.23-1.04-1.42-.11-.19-.01-.29.08-.38.08-.08.19-.22.28-.33.09-.11.12-.19.19-.31.06-.12.03-.23-.01-.33-.05-.09-.42-1.01-.58-1.39-.15-.36-.31-.31-.42-.32h-.36c-.12 0-.33.05-.5.23-.17.19-.66.65-.66 1.58 0 .93.68 1.83.77 1.96.1.12 1.34 2.05 3.25 2.87.45.2.81.31 1.09.4.46.15.87.13 1.2.08.37-.06 1.1-.45 1.25-.88.16-.44.16-.81.11-.89-.05-.07-.17-.12-.36-.21"/></svg>
          <span>WhatsApp</span>
        </a>

        <!-- Facebook -->
        <a href="<?php echo esc_url($fb_share_url); ?>" class="share-btn share-fb" target="_blank" rel="noopener" aria-label="Bagikan ke Facebook">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
          <span>Facebook</span>
        </a>

        <!-- Twitter / X -->
        <a href="<?php echo esc_url($x_share_url); ?>" class="share-btn share-x" target="_blank" rel="noopener" aria-label="Bagikan ke X / Twitter">
          <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
          <span>Twitter / X</span>
        </a>

        <!-- Telegram -->
        <a href="<?php echo esc_url($tg_share_url); ?>" class="share-btn share-tg" target="_blank" rel="noopener" aria-label="Bagikan ke Telegram">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/></svg>
          <span>Telegram</span>
        </a>

        <!-- Instagram -->
        <button type="button" class="share-btn share-ig" id="gkInstagramShareBtn" data-url="<?php echo esc_url($post_url); ?>" aria-label="Bagikan ke Instagram">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
          <span>Instagram</span>
        </button>
      </div>

      <div id="gkShareToast" class="gk-share-toast" role="status" aria-live="polite">
        Tautan artikel berhasil disalin!
      </div>
    </div>

    <!-- SHARE & CTA BOX -->
    <div class="article-cta-box">
      <div class="kicker">Generasi Muda Keadilan</div>
      <h3 class="display" style="font-size:28px; margin-bottom:12px; color:var(--ink);">Muda Berkarya, Bangsa Sejahtera!</h3>
      <p style="margin-bottom:20px; font-size:16px;">Tertarik untuk berkontribusi atau berdiskusi lebih lanjut tentang isu kepemudaan di Jawa Barat?</p>
      <div style="display:flex; gap:12px; flex-wrap:wrap;">
        <a class="btn" href="https://www.instagram.com/gemakeadilanjawabarat/" target="_blank" rel="noopener">Hubungi via Instagram</a>
        <a class="btn alt" href="<?php echo esc_url($site_url); ?>/artikel/">Baca Artikel Lainnya</a>
      </div>
    </div>

  </div>
</main>

<!-- FOOTER -->
<footer>
  <div class="wrap">
    <a class="brand" href="<?php echo esc_url($site_url); ?>/" style="border:0">
      <img src="<?php echo esc_url($logo_url); ?>" alt="Logo Gema Keadilan" data-fallback="brand">
      <span class="mark">GK</span>
      <span><b>Gema Keadilan</b><small>Provinsi Jawa Barat</small></span>
    </a>
    <a href="https://www.instagram.com/gemakeadilanjawabarat/" target="_blank" rel="noopener">@gemakeadilanjawabarat</a>
    <div class="copy">Allahu Akbar! Merdeka! &middot; Muda Berkarya, Bangsa Sejahtera!</div>
  </div>
</footer>

<script>
(function(){
  function copyTextToClipboard(text, successMsg) {
    if (navigator.clipboard && window.isSecureContext) {
      navigator.clipboard.writeText(text).then(function(){
        showToast(successMsg);
      }).catch(function(){
        fallbackCopy(text, successMsg);
      });
    } else {
      fallbackCopy(text, successMsg);
    }
  }

  function fallbackCopy(text, successMsg) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    try {
      document.execCommand('copy');
      showToast(successMsg);
    } catch(err){}
    document.body.removeChild(ta);
  }

  var toastTimer;
  function showToast(msg) {
    var toast = document.getElementById('gkShareToast');
    if (!toast) return;
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function(){
      toast.classList.remove('show');
    }, 3200);
  }

  // Tombol Salin Tautan
  var copyBtn = document.getElementById('gkCopyLinkBtn');
  if (copyBtn) {
    copyBtn.addEventListener('click', function(e){
      e.preventDefault();
      var url = this.getAttribute('data-url') || window.location.href;
      copyTextToClipboard(url, 'Tautan artikel berhasil disalin!');
      var btnText = document.getElementById('gkCopyBtnText');
      if (btnText) {
        var original = btnText.textContent;
        btnText.textContent = 'Tersalin!';
        setTimeout(function(){ btnText.textContent = original; }, 2000);
      }
    });
  }

  // Tombol Instagram: Salin tautan dan arahkan ke Instagram
  var igBtn = document.getElementById('gkInstagramShareBtn');
  if (igBtn) {
    igBtn.addEventListener('click', function(e){
      e.preventDefault();
      var url = this.getAttribute('data-url') || window.location.href;
      copyTextToClipboard(url, 'Tautan artikel disalin! Membuka Instagram...');
      setTimeout(function(){
        window.open('https://www.instagram.com/', '_blank', 'noopener');
      }, 700);
    });
  }
})();
</script>

<?php wp_footer(); ?>
</body>
</html>

