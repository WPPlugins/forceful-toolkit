<?php
function forceful_toolkit_widget_posttype_build_query( $instance ) {
    $default_query_args = array(
        'post_type'      => 'post',
        'posts_per_page' => -1,
        'post__not_in'   => array(),
        'ignore_sticky_posts' => 1,
        'categories'     => array(),
        'tags'           => array(),
        'relation'       => 'OR',
        'orderby'        => 'lastest',
        'cat_name'       => 'category',
        'tag_name'       => 'post_tag'
    );

    $instance = wp_parse_args( $instance, $default_query_args );

    $args = array(
        'post_type'           => $instance['post_type'],
        'posts_per_page'      => $instance['posts_per_page'],
        'post__not_in'        => $instance['post__not_in'],
        'ignore_sticky_posts' => $instance['ignore_sticky_posts']
    );

    $tax_query = array();

    if ( $instance['categories'] ) {
    	if($instance['categories'][0] == '')
			unset($instance['categories'][0]);

		if ( $instance['categories'] ) {
	        $tax_query[] = array(
	            'taxonomy' => $instance['cat_name'],
	            'field'    => 'slug',
	            'terms'    => $instance['categories']
	        );
	    }
    }

    if ( $instance['tags'] ) {
    	if($instance['tags'][0] == '')
			unset($instance['tags'][0]);

		if ( $instance['tags'] ) {
	        $tax_query[] = array(
	            'taxonomy' => $instance['tag_name'],
	            'field'    => 'slug',
	            'terms'    => $instance['tags']
	        );
	    }
    }

    if ( $instance['relation'] && count( $tax_query ) == 2 )
        $tax_query['relation'] = $instance['relation'];

    if ( $tax_query ) {
        $args['tax_query'] = $tax_query;
    }

    switch ( $instance['orderby'] ) {
    case 'popular':
        $args['meta_key'] = 'forceful_lite_total_view';
        $args['orderby'] = 'meta_value_num';
        break;
    case 'most_comment':
        $args['orderby'] = 'comment_count';
        break;
    case 'random':
        $args['orderby'] = 'rand';
        break;
    default:
        $args['orderby'] = 'date';
        break;
    }
    
    return new WP_Query( $args );
}

function forceful_toolkit_get_shortcode($content, $enable_multi = false, $shortcodes = array()) {
    
    $codes         = array();
    $regex_matches = '';
    $regex_pattern = get_shortcode_regex();
    
    preg_match_all('/' . $regex_pattern . '/s', $content, $regex_matches);

    foreach ($regex_matches[0] as $shortcode) {
        $regex_matches_new = '';
        preg_match('/' . $regex_pattern . '/s', $shortcode, $regex_matches_new);

        if (in_array($regex_matches_new[2], $shortcodes)) :
            $codes[] = array(
                'shortcode' => $regex_matches_new[0],
                'type'      => $regex_matches_new[2],
                'content'   => $regex_matches_new[5],
                'atts'      => shortcode_parse_atts($regex_matches_new[3])
            );

            if (false == $enable_multi) {
                break;
            }
        endif;
    }

    return $codes;
}