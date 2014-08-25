<?php
/*
Template Name: Articles
*/
?>

<?php #get_template_part('templates/page', 'header'); ?>

<?php
$type = 'article';
$args=array(
  'post_type' => $type,
  'post_status' => array('publish', 'preprint', 'coming_soon', 'in_production'),
  'posts_per_page' => -1,
  'caller_get_posts'=> 1
);
$my_query = new WP_Query($args);

$post_count = 0;
//global $article_count;
//$article_count = 0;
//$count = 0;

if (!$my_query->have_posts()) : ?>
  <div class="alert alert-warning">
    <?php _e('Sorry, no results were found.', 'roots'); ?>
  </div>
<?php endif; ?>

<div class='article-container'>
<?php while ($my_query->have_posts()) : 
  $my_query->the_post(); 
  //$count++; ?>
  <?php if($post_count == 0): ?>
    <!--div class='row'>
    <div class='col-sm-6'-->
    <?php get_template_part('templates/content', get_post_format()); ?>
    <?php $post_count++; ?>
    <!--/div-->
  <?php elseif($post_count == 1): ?>
    <!--div class='col-sm-6'-->
    <?php get_template_part('templates/content', get_post_format()); ?>
    <?php $post_count = 0; ?>
    <!--/div>
    </div-->
  <?php endif ?>
<?php endwhile; ?>

<?php if ($wp_query->max_num_pages > 1) : ?>
  <nav class="post-nav">
    <ul class="pager">
      <li class="previous"><?php next_posts_link(__('&larr; Older posts', 'roots')); ?></li>
      <li class="next"><?php previous_posts_link(__('Newer posts &rarr;', 'roots')); ?></li>
    </ul>
  </nav>
<?php endif; ?>

<?php
//if($count >= $article_count) {
//  $article_count = $count;
//}

?>

</div>

<script>
//console.log(<?php echo $article_count; ?>);
</script>