<?php

class Forceful_Toolkit_Widget_Flexslider extends Kopa_Widget {

	public function __construct() {

		$all_cats = get_categories();
		$categories = array('' => esc_html__('-- None --', 'forceful-toolkit'));
		foreach ( $all_cats as $cat ) {
			$categories[ $cat->slug ] = $cat->name;
		}

		$all_tags = get_tags();
		$tags = array('' => esc_html__('-- None --', 'forceful-toolkit'));
		foreach( $all_tags as $tag ) {
			$tags[ $tag->slug ] = $tag->name;
		}

		$this->widget_cssclass    = 'kopa-home-slider-widget';
		$this->widget_description = esc_html__( 'A Posts Slider Widget.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_flexslider';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Flexslider', 'forceful-toolkit' );
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
			'categories' => array(
				'type'    => 'multiselect',
				'std'     => '',
				'label'   => esc_html__( 'Categories:', 'forceful-toolkit' ),
				'options' => $categories,
				'size'    => '5',
			),
			'relation'    => array(
				'type'    => 'select',
				'label'   => esc_html__( 'Relation:', 'forceful-toolkit' ),
				'std'     => 'OR',
				'options' => array(
					'AND' => esc_html__( 'AND', 'forceful-toolkit' ),
					'OR'  => esc_html__( 'OR', 'forceful-toolkit' ),
				),
			),
			'tags' => array(
				'type'    => 'multiselect',
				'std'     => '',
				'label'   => esc_html__( 'Tags:', 'forceful-toolkit' ),
				'options' => $tags,
				'size'    => '5',
			),
			'orderby' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => esc_html__( 'Orderby:', 'forceful-toolkit' ),
				'options' => array(
					'date'         => esc_html__( 'Date', 'forceful-toolkit' ),
					'random'       => esc_html__( 'Random', 'forceful-toolkit' ),
					'most_comment' => esc_html__( 'Number of comments', 'forceful-toolkit' ),
				),
			),
			'posts_per_page' => array(
				'type'    => 'number',
				'std'     => '5',
				'label'   => esc_html__( 'Number of posts:', 'forceful-toolkit' ),
				'min'     => '1',
			),
            'animation' => array(
                'type'  => 'select',
                'std'   => 'slide',
                'label' => esc_html__( 'Animation:', 'forceful-toolkit' ),
                'options' => array(
                    'slide'         => esc_html__( 'Slide', 'forceful-toolkit' )
                ),
            ),
            'direction' => array(
                'type'  => 'select',
                'std'   => 'horizontal',
                'label' => esc_html__( 'Direction:', 'forceful-toolkit' ),
                'options' => array(
                    'horizontal'         => esc_html__( 'Horizontal', 'forceful-toolkit' )
                ),
            ),
            'slideshow_speed' => array(
                'type'  => 'number',
                'std'   => '7000',
                'label' => esc_html__( 'Slideshow Speed:', 'forceful-toolkit' )
            ),
            'animation_speed' => array(
                'type'  => 'number',
                'std'   => '600',
                'label' => esc_html__( 'Animation Speed:', 'forceful-toolkit' )
            ),
            'is_auto_play' => array(
                'type'  => 'select',
                'std'   => 'true',
                'label' => esc_html__( 'Auto Play:', 'forceful-toolkit' ),
                'options' => array(
                    'true'  => esc_html__( 'True', 'forceful-toolkit' ),
                    'false' => esc_html__( 'False', 'forceful-toolkit' ),
                )
            ),
		);
		parent::__construct();
	}

	public function widget( $args, $instance ) {

		extract( $args );

		extract( $instance );

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		$posts = forceful_toolkit_widget_posttype_build_query($instance);

		echo wp_kses_post( $before_widget );

		if ( ! empty ( $title ) )
            echo sprintf( '%s', $before_title . '<span data-icon="&#xf040;"></span>' . $title . $after_title );

        if ( $posts->have_posts() ) : ?>

            <div class="home-slider flexslider loading" data-animation="<?php echo esc_attr( $animation ); ?>" data-direction="<?php echo esc_attr( $direction ); ?>" data-slideshow_speed="<?php echo esc_attr( $slideshow_speed ); ?>" data-animation_speed="<?php echo esc_attr( $animation_speed ); ?>" data-autoplay="<?php echo esc_attr( $is_auto_play ); ?>">
                <ul class="slides">
                    <?php while ( $posts->have_posts() ) : $posts->the_post(); 
                        if ( 'video' == get_post_format() ) {
                            $data_icon = 'film'; // icon-film-2
                        } elseif ( 'gallery' == get_post_format() ) {
                            $data_icon = 'images'; // icon-images
                        } elseif ( 'audio' == get_post_format() ) {
                            $data_icon = 'music'; // icon-music
                        } else {
                            $data_icon = 'pencil'; // icon-pencil
                        }
                    ?>
                    <li>
                        <article class="entry-item standard-post">
                            <div class="entry-thumb">
                            <?php
                            if ( has_post_thumbnail() ) {
                                the_post_thumbnail( 'kopa-image-size-0' ); // 579x 382
                            } elseif ( 'video' == get_post_format() ) {
                                $video = forceful_lite_content_get_video( get_the_content() );

                                if ( isset( $video[0] ) ) {
                                    $video = $video[0];
                                } else {
                                    $video = '';
                                }

                                if ( isset( $video['type'] ) && isset( $video['url'] ) ) {
                                    $video_thumbnail_url = forceful_lite_get_video_thumbnails_url( $video['type'], $video['url'] );
                                    echo '<img src="'.esc_url( $video_thumbnail_url ).'" alt="'.get_the_title().'">';
                                }
                            } elseif ( 'gallery' == get_post_format() ) {
                                $gallery_ids = forceful_lite_content_get_gallery_attachment_ids( get_the_content() );

                                if ( ! empty( $gallery_ids ) ) {
                                    foreach ( $gallery_ids as $id ) {
                                        if ( wp_attachment_is_image( $id ) ) {
                                            echo wp_get_attachment_image( $id, 'kopa-image-size-0' ); // 579 x 382
                                            break;
                                        }
                                    }
                                }
                            } // endif has_post_thumbnail
                            ?>

                                <a href="<?php the_permalink(); ?>"><?php echo Forceful_Lite_Icon::getIcon('long-arrow-right'); ?></a>
                            </div>
                            <div class="entry-content">
                                <header>
                                    <span class="entry-categories"><?php echo Forceful_Lite_Icon::getIcon('star', 'span'); ?><?php the_category(', '); ?></span>
                                    <h4 class="entry-title clearfix"><?php echo Forceful_Lite_Icon::getIcon($data_icon, 'span'); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <div class="meta-box">
                                        <span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
                                        <?php if(comments_open()){ ?>
                                        <span class="entry-comments"><?php echo Forceful_Lite_Icon::getIcon('comment', 'span'); ?><?php comments_popup_link( '0', '1', '%' ); ?></span>
                                        <?php } ?>
                                        <?php if ( 'show' == get_theme_mod('forceful_lite_options_post_view_count_status') && true == get_post_meta( get_the_ID(), 'forceful_lite_total_view', true ) ) { ?>
                                        <span class="entry-view"><?php echo Forceful_Lite_Icon::getIcon('view', 'span'); ?><?php echo get_post_meta( get_the_ID(), 'forceful_lite_total_view', true ); ?></span>
                                        <?php } ?>
                                    </div>
                                    <!-- meta-box -->
                                    <?php
                                    $post_rating = round( get_post_meta( get_the_ID(), 'forceful_toolkit_editor_user_total_all_rating', true ) );

                                    if ( ! empty( $post_rating ) ) {
                                    ?>
                                        <ul class="kopa-rating clearfix">
                                            <?php
                                            for ( $i = 0; $i < $post_rating; $i++ ) {
                                                echo '<li>'.Forceful_Lite_Icon::getIcon('star', 'span').'</li>';
                                            }
                                            for ( $i = 0; $i < 5 - $post_rating; $i++ ) {
                                                echo '<li>'.Forceful_Lite_Icon::getIcon('star2', 'span').'</li>';
                                            }
                                            ?>
                                        </ul>
                                    <?php } ?>
                                    <div class="clear"></div>
                                </header>
                                <?php the_excerpt(); ?>
                            </div>
                        </article>
                        <!-- entry-item -->
                    </li>
                    <?php endwhile; ?>
                </ul><!--slides-->
            </div><!--home-slider-->

        <?php
        endif; // endif $posts->have_posts()

        wp_reset_postdata();

		echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Flexslider' );