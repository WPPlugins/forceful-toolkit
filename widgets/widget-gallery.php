<?php

class Forceful_Toolkit_Widget_Gallery extends Kopa_Widget {

	public function __construct() {
		$gallery_posts = new WP_Query( array(
            'tax_query' => array(
                array(
                  'taxonomy' => 'post_format',
                  'field' => 'slug',
                  'terms' => 'post-format-gallery'
                )
            )
        ) );

		$galleries = array('' => esc_html__('-- None --', 'forceful-toolkit'));
        if ( $gallery_posts->have_posts() ) {
            while ( $gallery_posts->have_posts() ) {
                $gallery_posts->the_post();
                $galleries[ get_the_id() ] = get_the_title();
            }
        }

        wp_reset_postdata();

		$this->widget_cssclass    = 'kopa-gallery-widget';
		$this->widget_description = esc_html__( 'Display a carousel slider of all images in one gallery format post.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_gallery';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Gallery', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' ),
			),
            'subtitle'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Sub Title:', 'forceful-toolkit' ),
            ),
			'post_id'    => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Select One Gallery Post:', 'forceful-toolkit' ),
				'std'     => 'OR',
				'options' => $galleries,
			),
			'scroll_items'    => array(
				'type'    => 'number',
				'label'   => esc_html__( 'Scroll Items:', 'forceful-toolkit' ),
				'std'     => '5'
			),
		);
		parent::__construct();
	}

	public function widget( $args, $instance ) {

		extract( $args );

		extract( $instance );

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		echo wp_kses_post( $before_widget );

        if ( ! empty( $title ) ) {
            echo sprintf( '%s', $before_title . $title . ' <span>' . $subtitle . '</span>' . $after_title );
        }

		$gallery_post = get_post( $post_id );

        $gallery_ids = forceful_lite_content_get_gallery_attachment_ids( $gallery_post->post_content );

        if ( ! empty( $gallery_ids ) ) {
        ?>

        <div class="list-carousel responsive">
            <ul class="kopa-gallery-carousel owl-carousel" data-scroll-items="<?php echo esc_attr( $scroll_items ); ?>">
                <?php foreach ( $gallery_ids as $id ) {

                    if ( ! wp_attachment_is_image( $id ) ) {
                        continue;
                    }

                    $full_image_src = wp_get_attachment_image_src( $id, 'full' );
                    $thumbnail_image = wp_get_attachment_image( $id, 'kopa-image-size-5' ); // 276 x 202

                ?>
                    <li class="item">
                        <a rel="prettyPhoto[<?php echo esc_attr( $this->get_field_id( 'kp-gallery' ) ); ?>]" href="<?php echo esc_url( $full_image_src[0] ); ?>" title="<?php echo get_post_field( 'post_excerpt', $id ); ?>"><?php echo $thumbnail_image; ?></a>
                    </li>
                <?php } ?>
            </ul><!--kopa-featured-news-carousel-->

        </div><!--list-carousel-->

        <?php
        } // endif

		echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Gallery' );