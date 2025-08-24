<?php
/*
Template Name: Generic News (ACF Grouped)
Description: Reusable, client-agnostic News template using grouped ACF fields. Includes Swiper for brand/logo slider.
*/

get_header();
?>

<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<?php
  /**
   * ACF structure:
   * blog (group)
   *  - banner (group)
   *      - background_image (image or url)
   *      - title (text)
   *      - title_in_highlight (text)
   *      - subtitle (text)
   *      - brand_logo_left (image/url)
   *      - brand_logos (repeater)
   *          - logo (image/url)
   *  - posts_section_title (text)
   *  - cta_section (group)
   *      - bg_image (image/url)
   *      - title (text)
   *      - items (repeater)
   *          - item (text)
   *      - buttons (repeater)
   *          - label (text)
   *          - url (url)
   *  - newsletter_section (group)
   *      - title (text)
   *      - description (wysiwyg)
   *      - form_html (textarea)
   */

  $acf      = function_exists('get_field') ? get_field('news_page') : null;
  $banner   = $acf['banner'] ?? null;
  $posts_h  = $acf['posts_section_title'] ?? '';
  $cta      = $acf['cta_section'] ?? null;
  $nl       = $acf['newsletter_section'] ?? null;

  $bg = '';
  if (!empty($banner['background_image'])) {
    $bg = is_array($banner['background_image']) ? ($banner['background_image']['url'] ?? '') : $banner['background_image'];
    $bg = esc_url($bg);
  }
?>

<style>
  /* Section: Banner & Slider */
  .slider_section h2 { color:#191C18; }
  .slider_section .section-head > * { text-align:center; }
  .swiper { width:100%; height:100%; padding:1rem; }
  .swiper-slide { display:block; width:auto; height:auto; }
  .swiper-pagination { bottom:-5px; }
  .swiper-pagination-bullet { width:30px; border-radius:15px; }

  /* Cards */
  .news_wrap { border-radius:20px; }
  .news_wrap img { border-radius:20px 20px 0 0; }

  /* Tabs */
  .blog-tabs { justify-content:center; }
  .blog-tabs .nav-link { border-radius:50px; padding:6px 20px; border:1px solid #ddd; color:#333; transition:all .2s; }
  .blog-tabs .nav-link:hover { background:#f5f5f5; }
  .blog-tabs .nav-link.active { border:1px solid #e30613; background-color:#fff; color:#000; }

  /* CTA & Newsletter */
  .cta-banner { background-position:center; background-size:cover; }
  .cta-banner h2, .cta-banner p { color:#fff; }
  .cta-banner a { background-color:#b8140e; }
  .newsletter-form { display:flex; justify-content:center; align-items:stretch; max-width:800px; margin:0 auto; }
  .newsletter-form p { display:flex; }
  .newsletter-input { flex:1; border:1px solid #ddd; padding:20px 25px !important; font-size:16px; border-radius:50px 0 0 50px !important; outline:none !important; box-shadow:none !important; }
  .newsletter-btn { background:#b50d0d !important; color:#fff !important; border:none !important; padding:20px 40px !important; font-size:16px; font-weight:600; border-radius:0 50px 50px 0 !important; cursor:pointer; }
</style>

<?php if ($banner) : ?>
<section class="section slider_section main-banner blogs no-lazy-load" style="<?php if ($bg) echo 'background-image:url(\''.$bg.'\');'; ?> background-size:cover; background-position:center;">
  <div class="container">
    <header class="section-head mt-150">
      <?php if (!empty($banner['title'])) : ?>
        <h1 class="text-white">
          <?php echo esc_html($banner['title']); ?><br/>
          <?php if (!empty($banner['title_in_highlight'])) : ?>
            <span class="text-red" style="text-shadow:unset;"><?php echo esc_html($banner['title_in_highlight']); ?></span>
          <?php endif; ?>
        </h1>
      <?php endif; ?>
      <?php if (!empty($banner['subtitle'])) : ?>
        <p style="color:#fff; max-width:760px; margin:8px auto 0;"><?php echo esc_html($banner['subtitle']); ?></p>
      <?php endif; ?>
    </header>
  </div>

  <?php if (!empty($banner['brand_logo_left']) || !empty($banner['brand_logos'])) : ?>
    <div class="banner-brand">
      <div class="container">
        <div class="row align-items-center g-3">
          <div class="col-md-4">
            <?php if (!empty($banner['brand_logo_left'])) :
              $left = is_array($banner['brand_logo_left']) ? ($banner['brand_logo_left']['url'] ?? '') : $banner['brand_logo_left']; ?>
              <img src="<?php echo esc_url($left); ?>" class="img-fluid" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
            <?php endif; ?>
          </div>
          <div class="col-md-8">
            <?php $brand_logos = $banner['brand_logos'] ?? []; ?>
            <?php if (!empty($brand_logos)) : ?>
              <div class="swiper brand-swiper" data-swiper-initialized="0">
                <div class="swiper-wrapper">
                  <?php foreach ($brand_logos as $row) :
                    $logo = is_array($row['logo'] ?? null) ? ($row['logo']['url'] ?? '') : ($row['logo'] ?? '');
                    if (!$logo) continue; ?>
                    <div class="swiper-slide">
                      <div class="slider-item card border-0 shadow-sm p-3 text-center h-100">
                        <img src="<?php echo esc_url($logo); ?>" class="img-fluid" alt="Logo" loading="lazy">
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev" aria-label="Previous"></div>
                <div class="swiper-button-next" aria-label="Next"></div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <div class="back-to-top text-center mt-3">
    <a href="#catAnchor" aria-label="Scroll to posts"><i class="bi bi-arrow-down-circle"></i></a>
  </div>
</section>
<?php endif; ?>

<?php wp_reset_postdata(); ?>

<section class="relatives" id="catAnchor">
  <div class="container">
    <?php
      // Category filter via ?cat=<id>
      $current_cat = isset($_GET['cat']) ? (int) $_GET['cat'] : 0;
      $categories = get_categories(['hide_empty' => true]);
    ?>

    <div class="row my-4">
      <div class="col">
        <ul class="nav nav-pills blog-tabs gap-2 flex-wrap">
          <li class="nav-item">
            <a class="nav-link <?php echo $current_cat ? '' : 'active'; ?>" href="<?php echo esc_url( get_permalink() . '/#catAnchor' ); ?>"><?php esc_html_e('All', 'theme'); ?></a>
          </li>
          <?php foreach ($categories as $cat) : ?>
            <li class="nav-item">
              <a class="nav-link <?php echo ($current_cat === (int) $cat->term_id) ? 'active' : ''; ?>" href="<?php echo esc_url( add_query_arg('cat', $cat->term_id, get_permalink()) . '/#catAnchor' ); ?>"><?php echo esc_html($cat->name); ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <?php if (!$posts_h) { $posts_h = __('Latest Articles', 'theme'); } ?>
        <h2 class="main-head mb-5 text-center"><?php echo esc_html($posts_h); ?></h2>
      </div>
    </div>

    <?php
      // Paginated query
      $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
      $args = [
        'post_type'      => 'post',
        'posts_per_page' => 9,
        'paged'          => $paged,
      ];
      if ($current_cat) { $args['cat'] = $current_cat; }
      $blog_posts = new WP_Query($args);
    ?>

    <div class="row">
      <?php if ($blog_posts->have_posts()) : while ($blog_posts->have_posts()) : $blog_posts->the_post(); ?>
        <div class="col-sm-6 col-lg-4 mb-4">
          <div class="card shadow-sm news_wrap h-100">
            <a href="<?php the_permalink(); ?>" class="d-block">
              <?php if (has_post_thumbnail()) :
                the_post_thumbnail('large', ['class' => 'img-fluid', 'title' => esc_attr(get_the_title()), 'loading' => 'lazy', 'alt' => esc_attr(get_the_title())]);
              else : ?>
                <img class="img-fluid" src="<?php echo esc_url(get_template_directory_uri() . '/assets/img/placeholder-16x9.png'); ?>" alt="" loading="lazy">
              <?php endif; ?>
            </a>
            <div class="card-body blog_posts d-flex flex-column">
              <p class="mb-2">
                <?php
                  $post_cats = get_the_category();
                  if (!empty($post_cats)) {
                    $pieces = [];
                    foreach ($post_cats as $category) {
                      $pieces[] = '<a href="' . esc_url(get_category_link($category->term_id)) . '" class="text-decoration-none"><small class="color-red">' . esc_html($category->name) . '</small></a>';
                    }
                    echo implode(' ', $pieces);
                  }
                ?>
              </p>
              <h4 class="mb-2"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
              <div class="mb-3 text-muted small"><?php echo esc_html( wp_strip_all_tags( get_the_excerpt() ) ); ?></div>
              <div class="mt-auto d-flex align-items-center gap-2">
                <div class="avatar rounded-circle bg-secondary flex-shrink-0" style="width:40px; height:40px;"></div>
                <div class="meta small">
                  <span class="d-block text-danger fw-semibold"><?php echo esc_html( get_bloginfo('name') ); ?></span>
                  <span class="text-muted">
                    <?php echo esc_html(get_the_date(get_option('date_format'))); ?> •
                    <?php
                      $words = str_word_count( wp_strip_all_tags( get_the_content() ) );
                      $reading_time = max(1, ceil($words / 200));
                      echo esc_html($reading_time . ' ' . __('min read', 'theme'));
                    ?>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; wp_reset_postdata(); else: ?>
        <div class="col"><p><?php esc_html_e('No posts found.', 'theme'); ?></p></div>
      <?php endif; ?>
    </div>

    <?php if ($blog_posts->max_num_pages > 1) : ?>
      <div class="row">
        <div class="col">
          <nav class="pagination-wrapper mt-4">
            <?php
              echo paginate_links([
                'total'     => $blog_posts->max_num_pages,
                'current'   => $paged,
                'prev_text' => __('« Prev', 'theme'),
                'next_text' => __('Next »', 'theme'),
              ]);
            ?>
          </nav>
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>

<?php if ($cta) :
  $cta_bg = '';
  if (!empty($cta['bg_image'])) {
    $cta_bg = is_array($cta['bg_image']) ? ($cta['bg_image']['url'] ?? '') : $cta['bg_image'];
    $cta_bg = $cta_bg ? 'style="background-image:url(\'' . esc_url($cta_bg) . '\');"' : '';
  }
?>
<section class="cta-banner" <?php echo $cta_bg; ?>>
  <div class="container">
    <div class="row row-cols-1 row-cols-lg-2">
      <div class="col mb-5">
        <?php if (!empty($cta['title'])) : ?>
          <h2 class="main-head mb-4"><?php echo esc_html($cta['title']); ?></h2>
        <?php endif; ?>
        <?php if (!empty($cta['items'])) : foreach ($cta['items'] as $it) : if (empty($it['item'])) continue; ?>
          <div class="d-flex align-items-center mb-2"><p class="mb-0"><?php echo esc_html($it['item']); ?></p></div>
        <?php endforeach; endif; ?>
        <?php if (!empty($cta['buttons'])) : ?>
          <div class="d-flex gap-3 mt-4 flex-wrap">
            <?php foreach ($cta['buttons'] as $btn) :
              $label = !empty($btn['label']) ? esc_html($btn['label']) : '';
              $url   = !empty($btn['url'])   ? esc_url($btn['url'])   : '';
              if (!$label || !$url) continue; ?>
              <a href="<?php echo $url; ?>" class="btn rounded-pill px-4"><?php echo $label; ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<?php if ($nl) : ?>
<section class="newsletter-section" style="background:#f9f9f9; padding:80px 0;">
  <div class="container text-center">
    <?php if (!empty($nl['title'])) : ?>
      <h2 style="font-size:42px; font-weight:600; margin-bottom:20px; color:#333;"><?php echo esc_html($nl['title']); ?></h2>
    <?php endif; ?>
    <?php if (!empty($nl['description'])) : ?>
      <p style="max-width:700px; margin:0 auto 40px; font-size:16px; color:#555; line-height:1.6; "><?php echo wp_kses_post($nl['description']); ?></p>
    <?php endif; ?>
    <?php if (!empty($nl['form_html'])) : ?>
      <div class="newsletter-form-wrapper"><?php echo wp_kses_post($nl['form_html']); ?></div>
    <?php endif; ?>
    <small style="display:block; margin-top:20px; color:#666;">
      <?php echo esc_html__('By clicking Sign Up you\'re confirming that you agree with our', 'theme'); ?>
      <a href="<?php echo esc_url( home_url('/terms-and-conditions') ); ?>" style="text-decoration:underline;"><?php echo esc_html__('Terms and Conditions', 'theme'); ?></a>.
    </small>
  </div>
</section>
<?php endif; ?>

<?php endwhile; endif; ?>
<?php get_footer(); ?>

<?php 
/*
 * functions.php — enqueue Swiper
 */ 
// add to functions.php slider script import
add_action('wp_enqueue_scripts', function () {
  // Swiper assets
  wp_enqueue_style(
    'swiper',
    'https://unpkg.com/swiper@11/swiper-bundle.min.css',
    [],
    null
  );
  wp_enqueue_script(
    'swiper',
    'https://unpkg.com/swiper@11/swiper-bundle.min.js',
    [],
    null,
    true
  );

  wp_add_inline_script('swiper', <<<JS
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.brand-swiper').forEach(function (el) {
      if (el.dataset.swiperInitialized === '1') return;
      if (el.swiper && typeof el.swiper.destroy === 'function') { el.swiper.destroy(true, true); }

      var slidesCount = el.querySelectorAll('.swiper-slide').length;
      var canLoop = slidesCount > 3; // loop only if enough logos

      new Swiper(el, {
        slidesPerView: 2,
        spaceBetween: 16,
        slidesPerGroup: 1,
        breakpoints: {
          640:  { slidesPerView: 3 },
          1024: { slidesPerView: 4 }
        },
        pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
        navigation: {
          nextEl: el.querySelector('.swiper-button-next'),
          prevEl: el.querySelector('.swiper-button-prev')
        },
        loop: canLoop,
        rewind: !canLoop,
        watchOverflow: true
      });

      el.dataset.swiperInitialized = '1';
    });
  });
JS
  );
});
