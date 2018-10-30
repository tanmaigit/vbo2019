<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package coral-snowy
 */
?>

	</div><!-- #content -->	
  </div><!-- #gcontainer -->
  <footer id="colophon" class="site-footer grid-parent grid-100 tablet-grid-100 mobile-grid-100" role="contentinfo">
		<div class="grid-container footer-blocks">
			<div class="grid-100 tablet-grid-100 mobile-grid-100" style="padding-left: 0; padding-right: 0">
				<?php
				if(is_active_sidebar('footer-blocks')){ ?>
					<div class="top-footer wow fadeInLeft">
						<?php
						dynamic_sidebar('footer-blocks');
						?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<span id="designer" class="aligncenter" style="text-align:center">&copy2018 Ho Chi Minh City Open University</span>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
