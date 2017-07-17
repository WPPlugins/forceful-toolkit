<?php

class Forceful_Toolkit_Widget_Twitter extends Kopa_Widget {

	public function __construct() {
		$this->widget_cssclass    = 'kopa-twitter-widget';
		$this->widget_description = esc_html__( 'Display your latest twitter status.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_widget_twitter';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Twitter', 'forceful-toolkit' );
		$this->settings           = array(
            'title'  => array(
                'type'  => 'text',
                'std'   => __( 'Latest Tweets', 'beatmix-toolkit' ),
                'label' => __( 'Title:', 'beatmix-toolkit' )
            ),
            'twitter_id' => array(
                'type'    => 'text',
                'label'   => __('Twitter ID:', 'beatmix-toolkit')
            ),
            'tw_api_key' => array(
                'type'    => 'text',
                'label'   => __( 'Twitter API key:', 'beatmix-toolkit' )
            ),
            'tw_api_secret' => array(
                'type'    => 'text',
                'label'   => __( 'Twitter API secret:', 'beatmix-toolkit' ),
            ),
            'tw_access_token' => array(
                'type'  => 'text',
                'label' => __( 'Twitter Access token:', 'beatmix-toolkit' )
            ),
            'tw_access_token_secret' => array(
                'type'  => 'text',
                'label' => __( 'Twitter Access token secret:', 'beatmix-toolkit' )
            ),
            'limit' => array(
                'type'  => 'number',
                'label' => __( 'Number of item:', 'beatmix-toolkit' ),
                'std' => 3
            )
        );
		parent::__construct();
	}

	public function widget( $args, $instance ) {

		extract( $args );

		extract( $instance );

		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		echo wp_kses_post( $before_widget );

		if ( ! empty ( $title ) )
            echo sprintf( '%s', $before_title . $title . $after_title );
        ?>
        	<?php 
                require_once FT_PATH .'addon/TwitterAPIExchange.class.php';
                
                $settings = array(
                    'oauth_access_token'        => $instance['tw_access_token'],
                    'oauth_access_token_secret' => $instance['tw_access_token_secret'],
                    'consumer_key'              => $instance['tw_api_key'],
                    'consumer_secret'           => $instance['tw_api_secret']
                );

                $id            = $instance['twitter_id'];
                $limit         = $instance['limit'];
                $url           = "https://api.twitter.com/1.1/statuses/user_timeline.json";
                $requestMethod = "GET";
                $getfield      = "?screen_name=$id&count=$limit";
                $curl_enable   = function_exists('curl_version');

                if ($curl_enable){
                    $twitter = new TwitterAPIExchange($settings);
                    $string = json_decode($twitter->setGetfield($getfield)
                        ->buildOauth($url, $requestMethod)
                        ->performRequest(),$assoc = TRUE);
                    if(isset($string["errors"][0]["message"]) && $string["errors"][0]["message"] != ""){
                        _e($string["errors"][0]["message"]. '. Please read document to config it correct.', 'beatmix-toolkit');
                    }else{?>

                            <ul class="tweetList">
                                <?php if (!empty($string)) : ?>
                                <?php foreach($string as $items): ?>

                                    <li>
                                        <p><?php echo forceful_toolkit_convert_links($items['text']) ; ?></p>
                                        <span class="tweet-time"><?php echo forceful_toolkit_relative_time($items['created_at']); ?></span>
                                    </li>

                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                    <?php }
                } else{ ?>
                	<p><?php _e('Sorry, your server don\'t support curl extension to run this widget', 'beatmix-toolkit'); ?></p>       
            <?php } ?>            
        <?php 
		echo wp_kses_post( $after_widget );

	}

}
register_widget( 'Forceful_Toolkit_Widget_Twitter' );

//convert links to clickable format
if (!function_exists('forceful_toolkit_convert_links')) {
    function forceful_toolkit_convert_links($status,$targetBlank=true,$linkMaxLen=250){
     
        // the target
            $target=$targetBlank ? " target=\"_blank\" " : "";
         
        // convert link to url                              
            $status = preg_replace('/\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[A-Z0-9+&@#\/%=~_|]/i', '<a href="\0" target="_blank">\0</a>', $status);
         
        // convert @ to follow
            $status = preg_replace("/(@([_a-z0-9\-]+))/i","<a href=\"http://twitter.com/$2\" title=\"Follow $2\" $target >$1</a>",$status);
         
        // convert # to search
            $status = preg_replace("/(#([_a-z0-9\-]+))/i","<a href=\"https://twitter.com/search?q=$2\" title=\"Search $1\" $target >$1</a>",$status);
         
        // return the status
            return $status;
    }
}


//convert dates to readable format  
if (!function_exists('forceful_toolkit_relative_time')) {
    function forceful_toolkit_relative_time($a) {
        //get current timestampt
        $b = strtotime('now'); 
        //get timestamp when tweet created
        $c = strtotime($a);
        //get difference
        $d = $b - $c;
        //calculate different time values
        $minute = 60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $week = $day * 7;
            
        if(is_numeric($d) && $d > 0) {
            //if less then 3 seconds
            if($d < 3) return __('right now','beatmix-toolkit');
            //if less then minute
            if($d < $minute) return floor($d) . __(' seconds ago','beatmix-toolkit');
            //if less then 2 minutes
            if($d < $minute * 2) return __('about 1 minute ago','beatmix-toolkit');
            //if less then hour
            if($d < $hour) return floor($d / $minute) . __(' minutes ago','beatmix-toolkit');
            //if less then 2 hours
            if($d < $hour * 2) return __('about 1 hour ago','beatmix-toolkit');
            //if less then day
            if($d < $day) return floor($d / $hour) . __(' hours ago','beatmix-toolkit');
            //if more then day, but less then 2 days
            if($d > $day && $d < $day * 2) return __('yesterday','beatmix-toolkit');
            //if less then year
            if($d < $day * 365) return floor($d / $day) . __(' days ago','beatmix-toolkit');
            //else return more than a year
            return __('over a year ago','beatmix-toolkit');
        }
    }   
}