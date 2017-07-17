<?php

class Forceful_Toolkit_Widget_Lastest_Comments extends Kopa_Widget {

	public function __construct() {

		$this->widget_cssclass    = 'kopa-latest-comments';
		$this->widget_description = esc_html__( 'The most recent comments.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_lastest_comments';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Recent Comments', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title:', 'forceful-toolkit' ),
			),
            'number'  => array(
                'type'  => 'number',
                'std'   => 5,
                'label' => esc_html__( 'Number of comments to show:', 'forceful-toolkit' ),
            ),
		);
		parent::__construct();
	}

	public function widget( $args, $instance ) {

		extract( $args );

		extract( $instance );

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

        $comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) ) );

		$output = '';

        $output .= $before_widget;
        if ( $title )
            $output .= $before_title . $title . $after_title;

        $output .= '<ul id="recentcomments">';
        if ( $comments ) {

            foreach ( (array) $comments as $comment) {

                $output .= '<li>
                                <article class="entry-item">
                                    <header>
                                        <a href="'.esc_url( get_comment_author_url($comment->comment_ID) ).'" class="commenter-name">'.get_comment_author($comment->comment_ID).'</a>
                                        <a href="'.esc_url( get_comment_link($comment->comment_ID) ).'" class="entry-title">'.get_the_title($comment->comment_post_ID).'</a>
                                    </header>
                                    <div class="entry-thumb">
                                        <a href="'.esc_url( get_comment_link($comment->comment_ID) ).'">'.get_avatar( $comment, 50 ).'</a>
                                    </div>
                                    <div class="entry-content">
                                        '.get_comment_excerpt($comment->comment_ID).'
                                    </div>
                                    <div class="clear"></div>
                                </article>
                            </li>';
            }
        }
        $output .= '</ul>';
        $output .= $after_widget;

        echo sprintf( '%s', $output );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Lastest_Comments' );