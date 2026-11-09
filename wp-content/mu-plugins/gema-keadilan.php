<?php
/**
 * Plugin Name: Gema Keadilan Jawa Barat Design System
 * Description: Styling & assets for Gema Keadilan Jawa Barat landing page, articles & mobile drawer.
 * Version: 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'gk_enqueue_styles', 99);
add_action('elementor/editor/after_enqueue_styles', 'gk_enqueue_styles', 99);
add_action('elementor/preview/enqueue_styles', 'gk_enqueue_styles', 99);

function gk_enqueue_styles() {
    // Google Fonts: Anton & Plus Jakarta Sans
    wp_enqueue_style(
        'gk-google-fonts',
        'https://fonts.googleapis.com/css2?family=Anton&family=Plus+Jakarta+Sans:wght@400;500;700;800&display=swap',
        array(),
        null
    );

    // Gema Keadilan stylesheet
    wp_enqueue_style(
        'gk-main-design',
        content_url('mu-plugins/gema-keadilan.css'),
        array('gk-google-fonts'),
        '1.2.0'
    );
}

// Single post custom template
add_filter('template_include', function($template) {
    if (is_single()) {
        $custom_single = __DIR__ . '/templates/single-post.php';
        if (file_exists($custom_single)) {
            return $custom_single;
        }
    }
    return $template;
});

/**
 * Shortcode: [gk_artikel_count]
 * Menampilkan jumlah artikel rilis secara dinamis
 */
add_shortcode('gk_artikel_count', function($atts) {
    $atts = shortcode_atts([
        'category' => '',
    ], $atts, 'gk_artikel_count');

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ];
    if (!empty($atts['category'])) {
        if (is_numeric($atts['category'])) {
            $args['cat'] = intval($atts['category']);
        } else {
            $args['category_name'] = sanitize_title($atts['category']);
        }
    }
    $posts = get_posts($args);
    $count = count($posts);

    return sprintf(
        '<span class="chip" style="background:var(--ink); color:var(--paper);">%d Artikel Rilis</span>',
        $count
    );
});

/**
 * Shortcode: [gk_artikel_list]
 * Menampilkan daftar artikel dinamis dengan pagination bernuansa Gema Keadilan
 * Parameter:
 * - per_page: jumlah artikel per halaman (default: 6)
 * - category: slug atau ID kategori (opsional)
 * - orderby: field pengurutan (default: date)
 * - order: ASC / DESC (default: DESC)
 * - show_header: yes/no, tampilkan judul section pilihan & counter (default: no)
 */
add_shortcode('gk_artikel_list', function($atts) {
    $atts = shortcode_atts([
        'per_page'    => 6,
        'category'    => '',
        'orderby'     => 'date',
        'order'       => 'DESC',
        'show_header' => 'no',
    ], $atts, 'gk_artikel_list');

    // Deteksi nomor halaman aktif
    $paged = 1;
    if (get_query_var('paged')) {
        $paged = absint(get_query_var('paged'));
    } elseif (get_query_var('page')) {
        $paged = absint(get_query_var('page'));
    } elseif (isset($_GET['paged'])) {
        $paged = absint($_GET['paged']);
    } elseif (isset($_GET['page'])) {
        $paged = absint($_GET['page']);
    } elseif (preg_match('#/page/([0-9]+)/?#', $_SERVER['REQUEST_URI'] ?? '', $m)) {
        $paged = absint($m[1]);
    }
    if ($paged < 1) {
        $paged = 1;
    }

    // Tangkap URL halaman kanonikal sebelum query berjalan
    $current_page_id = get_the_ID();
    $current_url = $current_page_id ? get_permalink($current_page_id) : '';
    if (!$current_url && !empty($GLOBALS['post'])) {
        $current_url = get_permalink($GLOBALS['post']);
    }
    if (!$current_url) {
        $current_url = home_url($GLOBALS['wp']->request ?? '');
    }
    $current_url = remove_query_arg(['preview', 'preview_id', 'preview_nonce', 'elementor-preview'], $current_url);

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => intval($atts['per_page']),
        'paged'          => $paged,
        'orderby'        => sanitize_key($atts['orderby']),
        'order'          => strtoupper($atts['order']) === 'ASC' ? 'ASC' : 'DESC',
    ];

    if (!empty($atts['category'])) {
        if (is_numeric($atts['category'])) {
            $args['cat'] = intval($atts['category']);
        } else {
            $args['category_name'] = sanitize_title($atts['category']);
        }
    }

    $query = new WP_Query($args);
    $site_url = get_site_url();
    $default_fallback_img = $site_url . '/wp-content/uploads/hero-silaturahmi.jpg';

    ob_start();

    if (strtolower($atts['show_header']) === 'yes' || strtolower($atts['show_header']) === 'true') {
        ?>
        <div style="display:flex; justify-content:space-between; align-items:flex-end; border-bottom:2px solid var(--ink); padding-bottom:16px; margin-bottom:20px;">
          <div>
            <div class="kicker" style="margin-bottom:4px;">Publikasi Terbaru</div>
            <h2 class="display" style="font-size:clamp(32px, 4vw, 54px);">Tulisan Pilihan</h2>
          </div>
          <span class="chip" style="background:var(--ink); color:var(--paper);"><?php echo $query->found_posts; ?> Artikel Rilis</span>
        </div>
        <?php
    }

    if ($query->have_posts()) {
        echo '<div class="article-grid">';
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $permalink = get_permalink();
            $categories = get_the_category();
            $cat_name = !empty($categories) ? $categories[0]->name : 'Gema Keadilan';

            $thumb_url = get_the_post_thumbnail_url($post_id, 'large');
            if (!$thumb_url) {
                $thumb_url = $default_fallback_img;
            }

            $excerpt = get_the_excerpt();
            if (empty($excerpt)) {
                $excerpt = wp_strip_all_tags(get_the_content());
            }
            $excerpt = wp_trim_words($excerpt, 20, '...');
            ?>
            <article class="article-card">
              <div class="article-card-thumb">
                <a href="<?php echo esc_url($permalink); ?>">
                  <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php the_title_attribute(); ?>">
                </a>
              </div>
              <div class="article-card-body">
                <div class="article-card-meta">
                  <span class="chip" style="font-size:11px; padding:3px 10px; background:var(--pink);"><?php echo esc_html($cat_name); ?></span>
                  <span><?php echo get_the_date('j F Y'); ?></span>
                </div>
                <h3 class="article-card-title">
                  <a href="<?php echo esc_url($permalink); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="article-card-excerpt"><?php echo esc_html($excerpt); ?></p>
                <div class="article-card-footer">
                  <a class="btn-readmore" href="<?php echo esc_url($permalink); ?>">Baca Selengkapnya &rarr;</a>
                </div>
              </div>
            </article>
            <?php
        }
        echo '</div>';
        wp_reset_postdata();

        // Render Pagination
        if ($query->max_num_pages > 1) {
            global $wp_rewrite;

            if ($wp_rewrite && $wp_rewrite->using_permalinks()) {
                $base = user_trailingslashit(trailingslashit($current_url) . 'page/%#%');
            } else {
                $base = add_query_arg('paged', '%#%', $current_url);
            }

            // Filter agar parameter preview tidak terbawa ke link pagination mana pun
            $clean_link_fn = function($link) {
                return remove_query_arg(['preview', 'preview_id', 'preview_nonce', 'elementor-preview'], $link);
            };
            add_filter('paginate_links', $clean_link_fn, 99);

            $paginate = paginate_links([
                'base'      => $base,
                'format'    => '',
                'current'   => max(1, $paged),
                'total'     => $query->max_num_pages,
                'type'      => 'plain',
                'prev_text' => '&larr; Sebelumnya',
                'next_text' => 'Selanjutnya &rarr;',
                'end_size'  => 2,
                'mid_size'  => 2,
            ]);

            remove_filter('paginate_links', $clean_link_fn, 99);

            // Bersihkan /page/1/ agar link halaman pertama selalu ke URL kanonikal bersih
            if ($paginate) {
                $paginate = str_replace('/page/1/', '/', $paginate);
                $paginate = str_replace(['?paged=1"', '&amp;paged=1"'], '"', $paginate);
                ?>
                <nav class="gk-pagination-wrapper" aria-label="Navigasi Halaman Artikel">
                  <div class="gk-pagination">
                    <?php echo $paginate; ?>
                  </div>
                </nav>
                <?php
            }
        }
    } else {
        ?>
        <div style="padding: 40px; text-align: center; border: 2px dashed var(--ink); background: var(--paper); margin-top: 30px;">
          <h3 class="display" style="font-size: 24px; margin-bottom: 8px;">Belum Ada Artikel</h3>
          <p style="color: var(--brown);">Belum ada artikel yang dipublikasikan pada kategori ini.</p>
        </div>
        <?php
    }

    wp_reset_postdata();

    return ob_get_clean();
});

/**
 * Shortcode: [gk_kabar_terbaru]
 * Menampilkan 1 artikel rilis paling baru dengan layout spotlight di Beranda
 * Parameter:
 * - category: slug atau ID kategori (opsional)
 * - button_text: teks tombol CTA (default: "Baca Artikel Lengkap &rarr;")
 */
add_shortcode('gk_kabar_terbaru', function($atts) {
    $atts = shortcode_atts([
        'category'    => '',
        'button_text' => 'Baca Artikel Lengkap &rarr;',
    ], $atts, 'gk_kabar_terbaru');

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];

    if (!empty($atts['category'])) {
        if (is_numeric($atts['category'])) {
            $args['cat'] = intval($atts['category']);
        } else {
            $args['category_name'] = sanitize_title($atts['category']);
        }
    }

    $posts = get_posts($args);
    if (empty($posts)) {
        return '';
    }

    $p = $posts[0];
    $permalink = get_permalink($p->ID);
    $site_url = get_site_url();
    $thumb_url = get_the_post_thumbnail_url($p->ID, 'large');
    if (!$thumb_url) {
        $thumb_url = $site_url . '/wp-content/uploads/hero-silaturahmi.jpg';
    }

    $categories = get_the_category($p->ID);
    $cat_name = !empty($categories) ? $categories[0]->name : 'Kegiatan & Berita';

    $excerpt = !empty($p->post_excerpt) ? $p->post_excerpt : wp_trim_words(strip_tags($p->post_content), 24, '...');

    $author_name = get_the_author_meta('display_name', $p->post_author);
    if (empty($author_name)) {
        $author_name = 'Gema Keadilan Jabar';
    }

    $title_parts = explode(':', $p->post_title, 2);
    if (count($title_parts) === 2) {
        $main_title = esc_html($title_parts[0]);
        $sub_title = '<small style="display:block; font-size:0.42em; color:var(--brown); font-family:\'Plus Jakarta Sans\', sans-serif; font-weight:700; text-transform:none; margin-top:10px; line-height:1.35;">' . esc_html(trim($title_parts[1])) . '</small>';
    } else {
        $main_title = esc_html($p->post_title);
        $sub_title = '';
    }

    ob_start();
    ?>
    <div class="grid">
      <figure data-img>
        <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($p->post_title); ?>">
          <img src="<?php echo esc_url($thumb_url); ?>" alt="<?php echo esc_attr($p->post_title); ?>">
        </a>
        <div class="ph"><?php echo esc_html($p->post_title); ?></div>
      </figure>
      <div class="event">
        <span class="chip" style="font-size:12px; padding:4px 12px; background:var(--pink); margin-bottom:12px; display:inline-block;"><?php echo esc_html($cat_name); ?></span>
        <h3 class="display" style="margin-top:8px;">
          <a href="<?php echo esc_url($permalink); ?>">
            <?php echo $main_title; ?>
          </a>
          <?php echo $sub_title; ?>
        </h3>
        <p style="margin-top:14px; font-size:17px; line-height:1.6;"><?php echo esc_html($excerpt); ?></p>
        <dl class="facts">
          <div><dt>Hari / Tanggal</dt><dd><?php echo get_the_date('l, j F Y', $p->ID); ?></dd></div>
          <div><dt>Kategori</dt><dd><?php echo esc_html($cat_name); ?></dd></div>
          <div><dt>Penulis</dt><dd><?php echo esc_html($author_name); ?></dd></div>
        </dl>
        <div style="margin-top:24px;">
          <a class="btn" href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($atts['button_text']); ?></a>
        </div>
      </div>
    </div>
    <?php
    return ob_get_clean();
});

// Izinkan shortcode berjalan di dalam widget HTML Elementor
add_filter('elementor/widget/render_content', function($content, $widget) {
    if ('html' === $widget->get_name()) {
        $content = do_shortcode($content);
    }
    return $content;
}, 10, 2);

// Nonaktifkan Elementor Element Cache pada halaman artikel & beranda
// agar update artikel terbaru dan nomor halaman selalu tersaji dinamis
add_filter('get_post_metadata', function($value, $object_id, $meta_key, $single) {
    if ('_elementor_element_cache' === $meta_key) {
        if ($object_id == 11 || $object_id == 16 || is_page(['artikel', 'beranda']) || is_paged() || get_query_var('paged') > 1 || preg_match('#/page/[0-9]+#', $_SERVER['REQUEST_URI'] ?? '')) {
            return '';
        }
    }
    return $value;
}, 10, 4);

add_filter('update_post_metadata', function($check, $object_id, $meta_key, $meta_value) {
    if ('_elementor_element_cache' === $meta_key) {
        if ($object_id == 11 || $object_id == 16 || is_page(['artikel', 'beranda']) || is_paged() || get_query_var('paged') > 1 || preg_match('#/page/[0-9]+#', $_SERVER['REQUEST_URI'] ?? '')) {
            return false;
        }
    }
    return $check;
}, 10, 4);

// Otomatis bersihkan cache Elementor saat artikel baru dipublikasikan atau diperbarui
add_action('save_post', function($post_id) {
    if (get_post_type($post_id) === 'post') {
        delete_post_meta(11, '_elementor_element_cache');
        delete_post_meta(16, '_elementor_element_cache');
    }
});

// Mobile Drawer & Fallback Script
add_action('wp_footer', function() {
    $site_url = get_site_url();
    ?>
    <!-- Mobile Drawer Backdrop & Menu -->
    <div class="gk-drawer-backdrop" id="gkDrawerBackdrop"></div>
    <div class="gk-drawer" id="gkDrawer" aria-hidden="true">
      <div class="gk-drawer-head">
        <span class="display" style="font-size:22px; color:var(--ink);">Menu Navigasi</span>
        <button class="gk-drawer-close" id="gkDrawerClose" aria-label="Tutup Menu">&times;</button>
      </div>
      <nav class="gk-drawer-nav">
        <a href="<?php echo esc_url($site_url); ?>/">Beranda</a>
        <a href="<?php echo esc_url($site_url); ?>/#tentang">Tentang Kami</a>
        <a href="<?php echo esc_url($site_url); ?>/#dirgahayu">21 Tahun</a>
        <a href="<?php echo esc_url($site_url); ?>/#kegiatan">Kegiatan</a>
        <a href="<?php echo esc_url($site_url); ?>/#pimpinan">Pimpinan</a>
        <a href="<?php echo esc_url($site_url); ?>/artikel/">Artikel</a>
        <div style="margin-top:20px;">
          <a class="btn" style="display:block; text-align:center;" href="<?php echo esc_url($site_url); ?>/#gabung">Gabung Pemuda</a>
        </div>
      </nav>
      <div style="margin-top:auto; padding-top:20px; border-top:1px solid #ddd; font-size:12px; color:var(--brown);">
        <strong>Gema Keadilan Jawa Barat</strong><br>
        Muda Berkarya, Bangsa Sejahtera!
      </div>
    </div>

    <script>
    (function(){
      // Dynamic header offset
      function updateHeaderOffset() {
        var header = document.querySelector('header.top');
        if (header) {
          var adminBar = document.getElementById('wpadminbar');
          var adminBarHeight = adminBar ? adminBar.offsetHeight : 0;
          var headerHeight = header.offsetHeight;
          var totalOffset = headerHeight + adminBarHeight;
          document.body.style.setProperty('padding-top', totalOffset + 'px', 'important');
          document.documentElement.style.scrollPaddingTop = (totalOffset + 16) + 'px';
        }
      }
      window.addEventListener('DOMContentLoaded', updateHeaderOffset);
      window.addEventListener('load', updateHeaderOffset);
      window.addEventListener('resize', updateHeaderOffset);
      updateHeaderOffset();

      // Insert hamburger button into header
      var topWrap = document.querySelector('header.top .wrap');
      if (topWrap && !document.getElementById('gkHamburgerBtn')) {
        var btn = document.createElement('button');
        btn.id = 'gkHamburgerBtn';
        btn.className = 'gk-hamburger-btn';
        btn.setAttribute('aria-label', 'Buka Menu Navigasi');
        btn.innerHTML = '<span style="font-size:16px;">&#9776;</span> <span>MENU</span>';
        
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          document.getElementById('gkDrawer').classList.add('active');
          document.getElementById('gkDrawerBackdrop').classList.add('active');
          document.getElementById('gkDrawer').setAttribute('aria-hidden', 'false');
        });
        topWrap.appendChild(btn);
      }

      // Close drawer function
      function closeDrawer() {
        var drawer = document.getElementById('gkDrawer');
        var backdrop = document.getElementById('gkDrawerBackdrop');
        if (drawer) {
          drawer.classList.remove('active');
          drawer.setAttribute('aria-hidden', 'true');
        }
        if (backdrop) {
          backdrop.classList.remove('active');
        }
      }

      var closeBtn = document.getElementById('gkDrawerClose');
      if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

      var backdrop = document.getElementById('gkDrawerBackdrop');
      if (backdrop) backdrop.addEventListener('click', closeDrawer);

      // Close drawer on link click
      var navLinks = document.querySelectorAll('.gk-drawer-nav a');
      navLinks.forEach(function(link){
        link.addEventListener('click', closeDrawer);
      });

      // Fallback for broken images
      document.querySelectorAll('img[data-fallback]').forEach(function(img){
        img.addEventListener('error', function(){
          if(img.dataset.fallback === 'brand'){
            var brand = img.closest('.brand');
            if(brand) brand.classList.add('nologo');
          } else {
            var dataImg = img.closest('[data-img]');
            if(dataImg) dataImg.classList.add('noimg');
          }
        });
      });
    })();
    </script>
    <?php
});
