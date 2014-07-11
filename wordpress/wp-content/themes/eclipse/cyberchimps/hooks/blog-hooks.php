<?php
/**
 * Title: Blog Hooks
 *
 * Description: Defines actions/hooks for blog page.
 *
 * Please do not edit this file. This file is part of the Cyber Chimps Framework and all modifications
 * should be made in a child theme.
 *
 * @category Cyber Chimps Framework
 * @package  Framework
 * @since    1.0
 * @author   CyberChimps
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v3.0 (or later)
 * @link     http://www.cyberchimps.com/
 */

function cyberchimps_blog_section_order_action() {
	global $post;

	$defaults = array();
	$default  = apply_filters( 'cyberchimps_elements_draganddrop_defaults', array(
		'slider_lite'    => __( 'Slider Lite', 'cyberchimps_core' ),
		'boxes_lite'     => __( 'Boxes', 'cyberchimps_core' ),
		'blog_post_page' => __( 'Post Page', 'cyberchimps_core' )
	) );
	foreach( $default as $key => $val ) {
		$defaults[] = $key;
	}

	$blog_section_order = cyberchimps_get_option( 'blog_section_order', $defaults );
	//select default in case options are empty
	$blog_section_order = ( $blog_section_order == '' ) ? array( 'blog_post_page' ) : $blog_section_order;
	$slider_size        = cyberchimps_get_option( 'blog_slider_size', 'full' );
	if( is_array( $blog_section_order ) ) {

		// Check if both of slider and blog post were active
		if( in_array( 'page_slider', $blog_section_order ) && in_array( 'blog_post_page', $blog_section_order ) ) {

			// Get position of slider and blog post page in the active elements list.
			$position_slider    = array_search( 'page_slider', $blog_section_order );
			$position_blog_post = array_search( 'blog_post_page', $blog_section_order );

			$slider_order = $position_slider > $position_blog_post ? 'after' : 'before';
			cyberchimps_add_half_slider_action( $slider_order );
		}

		foreach( $blog_section_order as $func ) {
			// checks if slider is selected at half size, if it is it removes it so we can display it above blog content
			if( $func == 'page_slider' && $slider_size == 'half' ) {
				$func = '';
			}
			else {
				?>
				<div class="container-full-width" id="<?php echo $func; ?>_section">
					<div class="container">
						<div class="container-fluid">
							<?php
							do_action( $func );
							?>
						</div>
						<!-- .container-fluid-->
					</div>
					<!-- .container -->
				</div>    <!-- .container-full-width -->
			<?php
			}
		}
	}
}

add_action( 'cyberchimps_blog_content', 'cyberchimps_blog_section_order_action' );

function cyberchimps_post() {

	?>
	<div id="container" <?php cyberchimps_filter_container_class(); ?>>

		<?php do_action( 'cyberchimps_before_content_container' ); ?>

		<div id="content" <?php cyberchimps_filter_content_class(); ?>>

			<?php do_action( 'cyberchimps_before_content' ); ?>

			<?php if( have_posts() ) : ?>

				<?php while( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

			<?php elseif( current_user_can( 'edit_posts' ) ) : ?>

				<?php get_template_part( 'no-results', 'index' ); ?>

			<?php endif; ?>

			<?php do_action( 'cyberchimps_after_content' ); ?>

		</div>
		<!-- #content -->
		<?php do_action( 'cyberchimps_after_content_container' ); ?>

	</div><!-- #container -->
<?php
}

add_action( 'blog_post_page', 'cyberchimps_post' );

/**
 * Create the blog page title and hook it in
 *
 * @hook cyberchimps_before_content
 */
function cyberchimps_blog_title() {
	if( is_home() ) {
		// Add blog title if toggle is on.
		$title_toggle = cyberchimps_get_option( 'blog_title', false );
		if( $title_toggle ) {
			$title_text = cyberchimps_get_option( 'blog_title_text', __( 'Our Blog', 'cyberchimps_core' ) );
			echo apply_filters( 'cyberchimps_blog_title_html', '
        <div id="cyberchimps_blog_title" class="row-fluid">
            <header class="page-header">
                <h1 class="page-title">' . $title_text . '</h1>
            </header>
        </div>' );
		}
	}
}

add_action( 'cyberchimps_before_content', 'cyberchimps_blog_title' );
