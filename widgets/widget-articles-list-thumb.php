<?php

class Forceful_Toolkit_Widget_Articles_List_Thumb extends Kopa_Widget {

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

		$this->widget_cssclass    = 'kopa-popular-post-widget';
		$this->widget_description = esc_html__( 'Display Latest Articles with Thumbnails.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_articles_list_thumbnails';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Articles List With Thumbnails', 'forceful-toolkit' );
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
            echo sprintf( '%s', $before_title . $title . ' <span>' . $subtitle . '</span>' . $after_title );
        ?>

        <ul>

            <?php if ( $posts->have_posts() ) {
                while ( $posts->have_posts() ) {
                    $posts->the_post();

                    if ( 'video' == get_post_format() ) {
                        $data_icon = 'video'; // icon-film-2
                    } elseif ( 'gallery' == get_post_format() ) {
                        $data_icon = 'images'; // icon-images
                    } elseif ( 'audio' == get_post_format() ) {
                        $data_icon = 'music'; // icon-music
                    } else {
                        $data_icon = 'pencil'; // icon-pencil
                    }

                    $has_printed_thumbnail = false;
            ?>
                <li>
                    <article class="entry-item clearfix">
                        <div class="entry-thumb">
                        <?php
                        if ( has_post_thumbnail() ) {
                            the_post_thumbnail( 'kopa-image-size-2' ); // 451x259
                            $has_printed_thumbnail = true;
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

                                $has_printed_thumbnail = true;
                            }
                        } elseif ( 'gallery' == get_post_format() ) {
                            $gallery_ids = forceful_lite_content_get_gallery_attachment_ids( get_the_content() );

                            if ( ! empty( $gallery_ids ) ) {
                                foreach ( $gallery_ids as $id ) {
                                    if ( wp_attachment_is_image( $id ) ) {
                                        echo wp_get_attachment_image( $id, 'kopa-image-size-2' ); // 451 x 259
                                        $has_printed_thumbnail = true;
                                        break;
                                    }
                                }
                            }
                        } // endif has_post_thumbnail
                        ?>

                        <?php if ( $has_printed_thumbnail ) { ?>
                            <a href="<?php the_permalink(); ?>" ><?php echo Forceful_Lite_Icon::getIcon('long-arrow-right'); ?></a>
                        <?php } // endif ?>
                        </div>
                        <div class="entry-content">
                            <header>
                                <span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
                                <h4 class="entry-title clearfix"><?php echo Forceful_Lite_Icon::getIcon($data_icon, 'span'); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                <?php the_excerpt(); ?>
                            </header>
                        </div>
                    </article>
                </li>
            <?php } 
            } ?>
        </ul>

        <?php
        wp_reset_postdata();

		echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Articles_List_Thumb' );