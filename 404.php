<?php get_header(); ?>
<article id="post-0" class="post not-found">
  <div class="header">
    <h1 class="entry__title" itemprop="name"><?php esc_html_e( 'Not Found', 'blankslate' ); ?></h1>
  </div>
  <div class="entry__content" itemprop="mainContentOfPage">
    <p><?php esc_html_e( 'Nothing found for the requested page. Try a search instead?', 'blankslate' ); ?></p>
    <?php get_search_form(); ?>
  </div>
</article>
<?php get_footer(); ?>