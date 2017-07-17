<?php

class Forceful_Toolkit_Widget_Flickr extends Kopa_Widget {

	/**
	* Constructor
	*/
	public function __construct() {
		$this->widget_cssclass    = 'kopa-widget-flickr';
		$this->widget_description = esc_html__( 'Display your latest photos on Flickr.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_flickr';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Flickr', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title', 'forceful-toolkit' )
			),
            'flickr_id'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Flickr ID', 'forceful-toolkit' ),
				'desc'  => esc_html__( 'http://idgettr.com/ ', 'forceful-toolkit' ),
            ),
            'limit'  => array(
                'type'  => 'text',
                'std'   => 9,
                'label' => esc_html__( 'Limit the number of items', 'forceful-toolkit' )
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

        if ( empty( $flickr_id ) ) {
            echo wp_kses_post( $after_widget );
            return;
        }
        ?>

        <div class="flickr-wrap clearfix" data-flickr_id="<?php echo esc_attr( $flickr_id ); ?>" data-limit="<?php echo esc_attr( $limit ); ?>">
            <ul class="kopa-flickr-widget clearfix"></ul>
        </div><!--flickr-wrap-->

        <?php
        echo wp_kses_post( $after_widget );
	}
}

register_widget( 'Forceful_Toolkit_Widget_Flickr' );