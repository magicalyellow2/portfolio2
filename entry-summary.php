<div class="entry__summary">
<?php if ( ( has_post_thumbnail() ) && ( !is_search() ) ) : ?>
<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_post_thumbnail(); ?></a>
<?php endif; ?>
<div itemprop="description"><?php the_excerpt(); ?></div>
<?php if ( is_search() ) { ?>
<div class="entry__links"><?php wp_link_pages(); ?></div>
<?php } ?>
</div>