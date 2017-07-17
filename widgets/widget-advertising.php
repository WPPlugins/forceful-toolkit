<?php

class Forceful_Toolkit_Widget_Advertising extends Kopa_Widget {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->widget_cssclass    = 'kopa-adv-widget';
		$this->widget_description = esc_html__( 'Display one 300x300 advertising image.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_advertising';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Advertising', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' )
			),
            'image_src'  => array(
                'type'  => 'upload',
                'std'   => '',
                'label' => esc_html__( 'Image Source:', 'forceful-toolkit' ),
                'mimes' => 'image',
            ),
            'image_url'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Url:', 'forceful-toolkit' )
            )
		);
		parent::__construct();
	}

	/**
	* widget function.
	*
	* @see WP_Widget
	* @access public
	* @param array $args
	* @param array $instance
	* @return void
	*/
	public function widget( $args, $instance ) {

		extract( $args );

		$title       = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

        extract( $instance );

        echo wp_kses_post( $before_widget );
        if ( ! empty( $title ) )
            echo wp_kses_post( $before_title . $title . $after_title );
        ?>

        <div class="kopa-banner-300">
            <?php if ( $image_url ) { ?>
                <a href="<?php echo esc_url($image_url) ?>"><img src="<?php echo esc_url($image_src); ?>" alt=""></a>
            <?php } ?>
        </div><!--kopa-banner-300-->

        <?php
        echo wp_kses_post( $after_widget );
	}
}

register_widget( 'Forceful_Toolkit_Widget_Advertising' );