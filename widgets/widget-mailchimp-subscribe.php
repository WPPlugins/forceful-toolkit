<?php

class Forceful_Toolkit_Widget_Mailchimp_Subscribe extends Kopa_Widget {

	/**
	* Constructor
	*/
	public function __construct() {
		$this->widget_cssclass    = 'kopa-newsletter-widget';
		$this->widget_description = esc_html__( 'Display mailchimp newsletter subscription form.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_mailchimp_subscribe';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Mailchimp Subscribe', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' )
			),
            'mailchimp_form_action'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Mailchimp Form Action:', 'forceful-toolkit' )
            ),
            'mailchimp_enable_popup'  => array(
                'type'  => 'checkbox',
                'std'   => '',
                'label' => esc_html__( 'Enable popup mode:', 'forceful-toolkit' )
            ),
            'description'  => array(
                'type'  => 'textarea',
                'std'   => '',
                'label' => esc_html__( 'Description:', 'forceful-toolkit' ),
                'rows' 	=> 5,
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

        <form action="<?php echo esc_url( $mailchimp_form_action ); ?>" method="post" class="newsletter-form clearfix" <?php echo esc_attr( $mailchimp_enable_popup ? 'target=_blank' : '' ); ?>>
            <p class="input-email clearfix">
                <input type="text" onfocus="if(this.value==this.defaultValue)this.value='';" onblur="if(this.value=='')this.value=this.defaultValue;" name="EMAIL" value="<?php esc_html_e( 'Your email here...', 'forceful-toolkit' ); ?>" class="email" size="40">
                <input type="submit" value="" class="submit">
            </p>
        </form>
        <p><?php echo wp_kses_post( $description ); ?></p>

        <?php
        echo wp_kses_post( $after_widget );
	}
}

register_widget( 'Forceful_Toolkit_Widget_Mailchimp_Subscribe' );