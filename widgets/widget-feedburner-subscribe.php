<?php

class Forceful_Toolkit_Widget_Feedburner_Subscribe extends Kopa_Widget {

	/**
	* Constructor
	*/
	public function __construct() {
		$this->widget_cssclass    = 'kopa-newsletter-widget';
		$this->widget_description = esc_html__( 'Display Feedburner subscription form.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_feedburner_subscribe';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Feedburner Subscribe', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' )
			),
            'feedburner_id'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Feedburner ID:', 'forceful-toolkit' )
            ),
            'description'  => array(
                'type'  => 'textarea',
                'std'   => '',
                'label' => esc_html__( 'Description:', 'forceful-toolkit' ),
                'rows'	=> 5,
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

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );

        extract( $instance );

        echo wp_kses_post( $before_widget );
        if ( ! empty( $title ) )
            echo wp_kses_post( $before_title . $title . $after_title );
        ?>

        <form action="http://feedburner.google.com/fb/a/mailverify" method="post" class="newsletter-form clearfix" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo esc_attr( $feedburner_id ); ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">

            <input type="hidden" value="<?php echo esc_attr( $feedburner_id ); ?>" name="uri">

            <p class="input-email clearfix">
                <input type="text" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" name="email" value="<?php esc_html_e( 'Your email here...', 'forceful-toolkit' ); ?>" class="email" size="40">
                <input type="submit" value="" class="submit">
            </p>
        </form>

        <p><?php echo wp_kses_post( $description ); ?></p>

        <?php
        echo wp_kses_post( $after_widget );
	}
}

register_widget( 'Forceful_Toolkit_Widget_Feedburner_Subscribe' );