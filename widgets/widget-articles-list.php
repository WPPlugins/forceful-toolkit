<?php

class Forceful_Toolkit_Widget_Articles_List extends Kopa_Widget {

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

		$this->widget_cssclass    = 'kopa-latest-post-widget';
		$this->widget_description = esc_html__( 'Display Latest Articles Widget.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_articles_list';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Articles List', 'forceful-toolkit' );
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
            ?>
                <li>
                    <span class="entry-date"><?php the_time( get_option( 'date_format' ) ); ?></span>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </li>
            <?php }
            } ?>
        </ul>

        <?php
        wp_reset_postdata();

		echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Articles_List' );