<?php

class Forceful_Toolkit_Widget_Combo extends Kopa_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'kopa-article-tab-widget';
		$this->widget_description = esc_html__( 'Display your latest posts, popular view posts and popular comment posts.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_combo';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Combo', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' ),
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

		$orderbys = array( 'lastest', 'popular', 'most_comment' );

		echo wp_kses_post( $before_widget );

        if ( ! empty( $title ) ) {
            echo sprintf( '%s', $before_title . $title . $after_title );
        }
        ?>

        <div class="list-container-2">
            <ul class="tabs-2 clearfix">
                <li class="active"><a href="#<?php echo $this->get_field_id( 'tab' ) . '-lastest'; ?>"><?php esc_html_e( 'Latest', 'forceful-toolkit'); ?></a></li>
                <li><a href="#<?php echo $this->get_field_id( 'tab' ) . '-popular'; ?>"><?php esc_html_e( 'Popular', 'forceful-toolkit'); ?></a></li>
                <li><a href="#<?php echo $this->get_field_id( 'tab' ) . '-most_comment'; ?>"><?php esc_html_e( 'Comments', 'forceful-toolkit'); ?></a></li>
            </ul><!--tabs-2-->
        </div>

        <div class="tab-container-2">

        <?php
        foreach ( $orderbys as $orderby ) {
            $instance['orderby'] = $orderby;

            $posts = forceful_toolkit_widget_posttype_build_query( $instance );
            ?>

            <div class="tab-content-2" id="<?php echo esc_attr( $this->get_field_id( 'tab' ) . '-' . $orderby ); ?>">
                <ul>
                <?php
                if ( $posts->have_posts() ) {
                    while ( $posts->have_posts() ) {
                        $posts->the_post();
                        ?>

                        <li>
                            <article class="entry-item clearfix">
                                <div class="entry-thumb">
                                    <?php
                                    if ( has_post_thumbnail() ) {
                                        the_post_thumbnail( 'kopa-image-size-4' ); // 81 x 81
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
                                                    echo wp_get_attachment_image( $id, 'kopa-image-size-4' ); // 81 x 81
                                                    break;
                                                }
                                            }
                                        }
                                    } // endif has_post_thumbnail
                                    ?>
                                </div>
                                <div class="entry-content">
                                    <h4 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                                    <div class="meta-box">
                                        <span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
                                        <span class="entry-author"><?php esc_html_e( 'By', 'forceful-toolkit'); ?> <?php the_author_posts_link(); ?></span>
                                    </div>
                                </div>
                            </article>
                        </li>

                        <?php
                    } // endwhile
                } // endif
                ?>
                </ul>
            </div>

            <?php
            wp_reset_postdata();
        } // endforeach
        ?>

        </div>

        <?php
        echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Combo' );