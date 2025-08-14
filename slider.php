<?php
$ss_title = get_field('slider_section_title');
$ss_bg = get_field('slider_section_background');
$ss_sub   = get_field('slider_section_subtitle');
$ss_p     = get_field('slider_section_paragraph');
?>
<style>
.slider_section h2 {color:#191C18;}
.slider_section .section-head > * {text-align:center;}
.swiper {width:100%; height: 100%;padding:1rem;}
.swiper-slide { display:block; width:calc((100%-16px)/2); height:auto;}
.swiper-pagination {bottom:-5px;}
.swiper-pagination-bullet {width: 30px;border-radius: 15px;}
</style>
 <section class="section slider_section" style="<?php
    $bg = $ss_bg['url'] ?? get_the_post_thumbnail_url(null,'full');
    if ($bg) echo 'background-image:url(\''.esc_url($bg).'\')';
  ?>">
    <div class="container">
      <header class="section-head">
        <?php if($ss_title): ?><h2 class="h-sub"><?php echo $ss_title; ?></h2><?php endif; ?>
        <?php if($ss_sub):   ?><div class="kicker"><?php echo esc_html($mem_sub); ?></strong></div><?php endif; ?>
        <?php if($ss_p):     ?><p><?php echo wp_kses_post($mem_p); ?></p><?php endif; ?>
      </header>

      <?php if ( have_rows('slider_section') ): ?>
      <div class="swiper">
        <div class="swiper-wrapper">
          <?php while ( have_rows('slider_section') ): the_row();
            $img = get_sub_field('image');
            $lnk = get_sub_field('link');
            $url = ''; $target = '';
            if (is_array($lnk) && !empty($lnk['url'])) { $url = $lnk['url']; $target = !empty($lnk['target']) ? $lnk['target'] : ''; }
            elseif ($lnk && is_string($lnk)) { $url = $lnk; }
          ?>
            <div class="swiper-slide">
              <div class="slider-item card">
                <?php if($url): ?><a href="<?php echo esc_url($url); ?>" <?php if($target) echo 'target="'.esc_attr($target).'" rel="noopener"'; ?>><?php endif; ?>
                  <?php if($img): ?>
                    <img src="<?php echo esc_url($img['url']); ?>" alt="<?php echo esc_attr($img['alt'] ?? ''); ?>" loading="lazy">
                  <?php endif; ?>
                <?php if($url): ?></a><?php endif; ?>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev" aria-label="Précédent"></div>
        <div class="swiper-button-next" aria-label="Suivant"></div>
      </div>
    <?php endif; ?>
    </div>
  </section>