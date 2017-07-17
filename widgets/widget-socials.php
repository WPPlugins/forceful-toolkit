<?php

class Forceful_Toolkit_Widget_Socials extends Kopa_Widget {

	/**
    * Constructor
    */
	public function __construct() {
		$this->widget_cssclass    = 'kopa-social-widget';
		$this->widget_description = esc_html__( 'Socials Widget', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_socials';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Socials', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title', 'forceful-toolkit' )
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

        extract( $instance );
        
        $title        = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
        $dribbble_url = get_theme_mod( 'forceful_lite_options_social_links_dribbble_url' );
        $gplus_url    = get_theme_mod( 'forceful_lite_options_social_links_google_plus_url' );
        $facebook_url = get_theme_mod( 'forceful_lite_options_social_links_facebook_url' );
        $twitter_url  = get_theme_mod( 'forceful_lite_options_social_links_twitter_url' );
        $rss_url      = get_theme_mod( 'forceful_lite_options_social_links_rss_url' );
        $flickr_url   = get_theme_mod( 'forceful_lite_options_social_links_flickr_url' );
        $youtube_url  = get_theme_mod( 'forceful_lite_options_social_links_youtube_url' );
        $social_link_target       = get_theme_mod( 'forceful_lite_options_social_links_target' , '_self');

        echo wp_kses_post( $before_widget );

        if ( ! empty( $title ) )
            echo wp_kses_post( $before_title . $title . $after_title );
        ?>

        <ul class="clearfix">
            <!-- dribbble -->
            <?php if ( ! empty ( $dribbble ) ) { ?>
            <li><a href="<?php echo esc_url( $dribbble_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('dribbble'); ?></a></li>
            <?php } ?>

            <!-- google plus -->
            <?php if ( ! empty ( $gplus_url ) ) { ?>
                <li><a href="<?php echo esc_url( $gplus_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('google-plus'); ?></a></li>
            <?php } ?>

            <!-- facebook -->
            <?php if ( ! empty ( $facebook_url ) ) { ?>
                <li><a href="<?php echo esc_url( $facebook_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('facebook'); ?></a></li>
            <?php } ?>

            <!-- twitter -->
            <?php if ( ! empty ( $twitter_url ) ) { ?>
            <li><a href="<?php echo esc_url( $twitter_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('twitter'); ?></a></li>
            <?php } ?>

            <!-- rss -->
            <?php if ( $rss_url != 'HIDE' ) {
                if ( empty( $rss_url ) ) {
                    $rss_url = get_bloginfo( 'rss2_url' );
                } else {
                    $rss_url = esc_url( $rss_url );
                }
            ?>
                <li><a href="<?php echo esc_url( $rss_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('rss'); ?></a></li>
            <?php } // endif ?>

            <!-- flickr -->
            <?php if ( ! empty ( $flickr_url ) ) { ?>
                <li><a href="<?php echo esc_url( $flickr_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('flickr'); ?></a></li>
            <?php } ?>

            <!-- youtube -->
            <?php if ( ! empty ( $youtube_url ) ) { ?>
                <li><a href="<?php echo esc_url( $youtube_url ); ?>" target="<?php echo esc_attr( $social_link_target ); ?>"><?php echo Forceful_Lite_Icon::getIcon('youtube'); ?></a></li>
            <?php } ?>
        </ul>

        <?php
        echo wp_kses_post( $after_widget );
	}
}

register_widget( 'Forceful_Toolkit_Widget_Socials' );