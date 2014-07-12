<?php while (have_posts()) : the_post(); ?>
  <article <?php post_class(); ?>>
    <?php $wistia = get_post_meta( $post->ID, 'wistia', true ); ?>
    <script>
      $("#wistia").attr('id', 'wistia_<?php echo $wistia ?>').show();
      wistiaEmbed = Wistia.embed("<?php echo $wistia ?>", {
        videoFoam: true
      });
      wistiaEmbed.bind("secondchange", function (s) {
        if(s > 60*10 && !user) {
          $('.wistia_embed').empty().append('<h2 style="color:#fff">Please sign in to watch the rest of the video.</h2><h3 style="color:#fff">Contact your hospital or educational institution for login details.</h3><a href="/" class="btn white fat" style="margin-top:25px">Back to front page</a>').attr('style', 'height: 653px;text-align: center;padding-top: 200px;border: 3px solid #eee;');
        }
        $('.vtime-item').removeClass('done').removeClass('current');
        $('.vtime-item').each(function(index){
          if($(this).data('time') < s)
          {
            $(this).addClass('done');
          }
          else
          {
            $('.vtime-item:nth-child('+index+')').addClass('current');
            return false;
          }
        });
      });
    </script>
    <header>
      <h1 class="entry-title"><?php the_title(); ?></h1>
      <?php get_template_part('templates/entry-meta'); ?>
      <ul class="nav nav-tabs" role="tablist">
        <li class="active"><a href="#main">Main Text</a></li>
        <li><a href="#outline">Procedure Outline</a></li>
      </ul>
    </header>
    <div class="entry-content">
      <div class="tab-content">
        <div class="tab-pane active" id="main"><?php the_content(); ?></div>
        <div class="tab-pane" id="outline"><?php the_block( 'Procedure Outline' ); ?></div>
      </div>
    </div>
    <div>
      <h3>Citations</h3>
      <?php the_block( 'Citations' ); ?>
    </div>

    <script>
      $('.nav-tabs a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
      });
     $(document).ready(function(){
        $('.widget_search, .widget_categories').hide();
        $('section.video').each(function(){
          $('#chapters ul').append('<li class="vtime-item" data-time="'+$(this).attr('time')+'"><a href="#video" onclick="wistiaEmbed.time('+$(this).attr('time')+').play();">'+$(this).attr('name')+'</a></li>');
        });
        $('#chapters').show();
     });
    </script>
    <footer>
      <?php wp_link_pages(array('before' => '<nav class="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
    </footer>
  </article>
<?php endwhile; ?>