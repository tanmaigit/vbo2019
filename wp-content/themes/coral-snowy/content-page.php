<?php
/**
 * The template used for displaying page content in page.php
 *
 * @package coral-snowy
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php 
			coral_snowy_post_thumbnail();
			the_content(); 

			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'coral-snowy' ),
				'after'  => '</div>',
			) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( __( 'Edit', 'coral-snowy' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
