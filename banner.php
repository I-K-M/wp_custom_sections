<?php
$banner_bg = get_field('banner_background');
$banner_title = get_field('banner_title');
$banner_p     = get_field('banner_paragraph');
?>
<style>
.banner h2 {color:#fff;font-size:100px;}
.banner p {color:#fff;margin-bottom:3rem;}
.banner .container {flex-direction:row;height:500px;}
.banner .col {width:50%;display: flex; align-items: flex-start; justify-content: center; align-content: flex-start; flex-direction: column; flex-wrap: wrap;}
</style>
<section class="section banner" style="<?php
    $bg = $banner_bg['url'] ?? get_the_post_thumbnail_url(null,'full');
    if ($bg) echo 'background-image:url(\''.esc_url($bg).'\')';
  ?>">
    <div class="container">
    <div class="col">
      <?php if($banner_title): ?><h2 class="h-sub"><?php echo $banner_title; ?></h2><?php endif; ?>
      <?php if($banner_p):     ?><p class="banner-copy mt-8"><?php echo wp_kses_post($banner_p); ?></p><?php endif; ?>
      <a>
        <button>BOOK NOW</button>
      </a>
    </div>
    <div class="col"></div>
    </div>
  </section>