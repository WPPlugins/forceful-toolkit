<?php

// AWESOME WEATHER WIDGET, WIDGET CLASS, SO MANY WIDGETS
class Forceful_Toolkit_Widget_Awesome_Weather extends Kopa_Widget {

	/**
    * Constructor
    */
	public function __construct() {
		$this->widget_cssclass    = 'widget_awesomeweatherwidget clearfix';
		$this->widget_description = esc_html__( 'Display Weather Widget base on customer location automatically.', 'forceful-toolkit' );
		$this->widget_id          = 'kopa_custom_awesome_weather_widget';
		$this->widget_name        = esc_html__( '[FORCEFUL] - Awesome Weather', 'forceful-toolkit' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => esc_html__( 'Title', 'forceful-toolkit' ),
			),
            'appid'  => array(
                'type'  => 'text',
                'std'   => 'bd82977b86bf27fb59a04b61b657fb6f',
                'label' => esc_html__( 'API Key', 'forceful-toolkit' ),
                'desc' => esc_html__( 'API Key OpenWeatherMap', 'forceful-toolkit' ),
            ),
			'location'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Location', 'forceful-toolkit' ),
                'desc' => esc_html__( 'i.e: London,UK or New York City,NY', 'forceful-toolkit' ),
            ),
            'override_title'  => array(
                'type'  => 'text',
                'std'   => '',
                'label' => esc_html__( 'Override Title', 'forceful-toolkit' )
            ),
            'units'  => array(
                'type'  => 'select',
                'std'   => 'imperial',
                'label' => esc_html__( 'Units', 'forceful-toolkit' ),
                'options' => array(
					'imperial' => 'F',
					'metric' => 'C',
                )
            ),
            'forecast_days'  => array(
                'type'  => 'select',
                'std'   => '1',
                'label' => esc_html__( 'Forecast Days', 'forceful-toolkit' ),
                'options' => array(
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
                )
            ),
            'show_link'  => array(
                'type'  => 'checkbox',
                'std'   => '1',
                'label' => esc_html__( 'Link to OpenWeatherMap', 'forceful-toolkit' ),
            ),
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

        $location       = isset($instance['location']) ? $instance['location'] : false;
        $appid          = isset($instance['appid']) ? $instance['appid'] : false;
        $override_title = isset($instance['override_title']) ? $instance['override_title'] : false;
        $units          = isset($instance['units']) ? $instance['units'] : false;
        $size           = false;
        $forecast_days  = isset($instance['forecast_days']) ? $instance['forecast_days'] : false;
        $hide_stats     = 0;
        $show_link      = (isset($instance['show_link']) AND $instance['show_link'] == 1) ? 1 : 0;
        $background     = false;

        echo wp_kses_post( $before_widget );

        echo forceful_toolkit_custom_awesome_weather_logic( array( 'location' => $location, 'appid' => $appid, 'override_title' => $override_title, 'size' => $size, 'units' => $units, 'forecast_days' => $forecast_days, 'hide_stats' => $hide_stats, 'show_link' => $show_link, 'background' => $background ));

        echo wp_kses_post( $after_widget );

	}
}

register_widget( 'Forceful_Toolkit_Widget_Awesome_Weather' );

// THE LOGIC
function forceful_toolkit_custom_awesome_weather_logic( $atts ){
    $rtn            = "";
    $weather_data   = array();
    $location       = isset($atts['location']) ? $atts['location'] : false;
    $appid          = isset($atts['appid']) ? $atts['appid'] : false;
    $size           = (isset($atts['size']) AND $atts['size'] == "tall") ? 'tall' : 'wide';
    $units          = isset($atts['units']) ? $atts['units'] : 'imperial';
    $units_display  = $units == "metric" ? esc_html__('C', 'forceful-toolkit') : esc_html__('F', 'forceful-toolkit');
    $override_title = isset($atts['override_title']) ? $atts['override_title'] : false;
    $days_to_show   = isset($atts['forecast_days']) ? $atts['forecast_days'] : 4;
    $show_stats     = (isset($atts['hide_stats']) AND $atts['hide_stats'] == 1) ? 0 : 1;
    $show_link      = (isset($atts['show_link']) AND $atts['show_link'] == 1) ? 1 : 0;
    $background     = isset($atts['background']) ? $atts['background'] : false;
    $locale         = 'en';

    $sytem_locale = get_locale();
    $available_locales = array( 'en', 'sp', 'fr', 'it', 'de', 'pt', 'ro', 'pl', 'ru', 'ua', 'fi', 'nl', 'bg', 'se', 'tr', 'zh_tw', 'zh_cn' ); 

    
    // CHECK FOR LOCALE
    if( in_array( $sytem_locale , $available_locales ) )
    {
        $locale = $sytem_locale;
    }
    
    // CHECK FOR LOCALE BY FIRST TWO DIGITS
    if( in_array(substr($sytem_locale, 0, 2), $available_locales ) )
    {
        $locale = substr($sytem_locale, 0, 2);
    }


    // NO LOCATION, ABORT ABORT!!!1!
    if( !$location ) { return forceful_toolkit_custom_awesome_weather_error(); }
    
    
    //FIND AND CACHE CITY ID
    $city_name_slug                 = sanitize_title( $location );
    $weather_transient_name         = 'forceful-toolkit-custom-awesome-weather-' . $units . '-' . $city_name_slug . "-". $locale;


    // TWO APIS USED (VERSION 2.5)
    //http://api.openweathermap.org/data/2.5/weather?q=London,uk&units=metric&cnt=7&lang=fr
    //http://api.openweathermap.org/data/2.5/forecast/daily?q=London&units=metric&cnt=7&lang=fr

    
    
    // GET WEATHER DATA
    if( get_transient( $weather_transient_name ) )
    {
        $weather_data = get_transient( $weather_transient_name );
    }
    else
    {
        // NOW
        $now_ping = "http://api.openweathermap.org/data/2.5/weather?q=" . $city_name_slug . "&lang=" . $locale . "&units=" . $units."&appid=".$appid."";
        $now_ping_get = wp_remote_get( $now_ping );
    
        if( is_wp_error( $now_ping_get ) ) 
        {
            return forceful_toolkit_custom_awesome_weather_error( $now_ping_get->get_error_message()  ); 
        }   
    
        $city_data = json_decode( $now_ping_get['body'] );
        
        if( isset($city_data->cod) AND $city_data->cod == 404 )
        {
            return forceful_toolkit_custom_awesome_weather_error( $city_data->message ); 
        }
        else
        {
            $weather_data['now'] = $city_data;
        }
        
        // FORECAST
        if( $days_to_show != "hide" )
        {
            $forecast_ping = "http://api.openweathermap.org/data/2.5/forecast/daily?q=" . $city_name_slug . "&lang=" . $locale . "&units=" . $units ."&cnt=7&appid=".$appid."";

            $forecast_ping_get = wp_remote_get( $forecast_ping );
        
            if( is_wp_error( $forecast_ping_get ) ) 
            {
                return forceful_toolkit_custom_awesome_weather_error( $forecast_ping_get->get_error_message()  ); 
            }   
            
            $forecast_data = json_decode( $forecast_ping_get['body'] );
            
            if( isset($forecast_data->cod) AND $forecast_data->cod == 404 )
            {
                return forceful_toolkit_custom_awesome_weather_error( $forecast_data->message ); 
            }
            else
            {
                $weather_data['forecast'] = $forecast_data;
            }
        }   
        
        
        if($weather_data['now'] AND $weather_data['forecast'])
        {
            // SET THE TRANSIENT, CACHE FOR AN HOUR
            set_transient( $weather_transient_name, $weather_data, apply_filters( 'forceful_toolkit_custom_awesome_weather_cache', 3600 ) ); 
        }
    }



    // NO WEATHER
    if( !$weather_data OR !isset($weather_data['now'])) { return forceful_toolkit_custom_awesome_weather_error(); }
    
    
    // TODAYS TEMPS
    $today          = $weather_data['now'];
    $today_temp     = round($today->main->temp);
    $today_high     = round($today->main->temp_max);
    $today_low      = round($today->main->temp_min);
    
    
    // COLOR OF WIDGET
    $bg_color = "temp1";
    if($units_display == "F")
    {
        if($today_temp > 31 AND $today_temp < 40) $bg_color = "temp2";
        if($today_temp >= 40 AND $today_temp < 50) $bg_color = "temp3";
        if($today_temp >= 50 AND $today_temp < 60) $bg_color = "temp4";
        if($today_temp >= 60 AND $today_temp < 80) $bg_color = "temp5";
        if($today_temp >= 80 AND $today_temp < 90) $bg_color = "temp6";
        if($today_temp >= 90) $bg_color = "temp7";
    }
    else
    {
        if($today_temp > 1 AND $today_temp < 4) $bg_color = "temp2";
        if($today_temp >= 4 AND $today_temp < 10) $bg_color = "temp3";
        if($today_temp >= 10 AND $today_temp < 15) $bg_color = "temp4";
        if($today_temp >= 15 AND $today_temp < 26) $bg_color = "temp5";
        if($today_temp >= 26 AND $today_temp < 32) $bg_color = "temp6";
        if($today_temp >= 32) $bg_color = "temp7";
    }
    
    
    // DATA
    $header_title = $override_title ? $override_title : $today->name;
    
    $today->main->humidity      = round($today->main->humidity);
    $today->wind->speed         = round($today->wind->speed);
    
    $wind_label = array ( 
                            esc_html__('N', 'forceful-toolkit'),
                            esc_html__('NNE', 'forceful-toolkit'), 
                            esc_html__('NE', 'forceful-toolkit'),
                            esc_html__('ENE', 'forceful-toolkit'),
                            esc_html__('E', 'forceful-toolkit'),
                            esc_html__('ESE', 'forceful-toolkit'),
                            esc_html__('SE', 'forceful-toolkit'),
                            esc_html__('SSE', 'forceful-toolkit'),
                            esc_html__('S', 'forceful-toolkit'),
                            esc_html__('SSW', 'forceful-toolkit'),
                            esc_html__('SW', 'forceful-toolkit'),
                            esc_html__('WSW', 'forceful-toolkit'),
                            esc_html__('W', 'forceful-toolkit'),
                            esc_html__('WNW', 'forceful-toolkit'),
                            esc_html__('NW', 'forceful-toolkit'),
                            esc_html__('NNW', 'forceful-toolkit')
                        );
    if( isset($today->wind->deg) ){
        $wind_direction = $wind_label[ fmod((($today->wind->deg + 11) / 22.5),16) ];
    }else{
        $wind_direction = '';
    }

    $show_stats_class = ($show_stats) ? "awe_with_stats" : "awe_without_stats";

    if($background) $bg_color = "darken";
    $bg_color = 'temp6'; // force temp6

    // DISPLAY WIDGET
    $rtn .= "

        <div id=\"awesome-weather-{$city_name_slug}\" class=\"awesome-weather-wrap awecf {$bg_color} {$show_stats_class} awe_{$size}\">
    ";


    if($background)
    {
        $rtn .= "<div class=\"awesome-weather-cover\" style='background-image: url($background);'>";
        $rtn .= "<div class=\"awesome-weather-darken\">";
    }

    $rtn .= "
            <div class=\"awesome-weather-header\">{$header_title}</div>
            <div class=\"awesome-weather-left\">

            <div class=\"awesome-weather-current-temp\">
                $today_temp<sup>{$units_display}</sup>
            </div> <!-- /.awesome-weather-current-temp -->
    ";

    if($days_to_show != "hide")
    {
        $rtn .= "<div class=\"awesome-weather-forecast awe_days_{$days_to_show} awecf\">";
        $c = 1;
        $dt_today = date_i18n('Ymd');
        $forecast = $weather_data['forecast'];
        $days_to_show = (int) $days_to_show;

        foreach( (array) $forecast->list as $forecast )
        {
            if( $dt_today >= date_i18n('Ymd', $forecast->dt)) continue;

            $forecast->temp = (int) $forecast->temp->day;
            $day_of_week = date_i18n('D', $forecast->dt);
            $rtn .= "
                <div class=\"awesome-weather-forecast-day\">
                    <div class=\"awesome-weather-forecast-day-temp\">{$forecast->temp}<sup>{$units_display}</sup></div>
                    <div class=\"awesome-weather-forecast-day-abbr\">$day_of_week</div>
                </div>
            ";
            if($c == $days_to_show) break;
            $c++;
        }
        $rtn .= " </div> <!-- /.awesome-weather-forecast -->";
        $rtn .= " </div> <!-- /.awesome-weather-left -->";
    }

    if($show_stats)
    {
        $speed_text = ($units == "metric") ? esc_html__('km/h', 'forceful-toolkit') : esc_html__('mph', 'forceful-toolkit');


        $rtn .= "

                <div class=\"awesome-weather-todays-stats\">
                    <div class=\"awe_desc\">{$today->weather[0]->description}</div>
                    <div class=\"awe_humidty\">" . esc_html__('humidity:', 'forceful-toolkit') . " {$today->main->humidity}% </div>
                    <div class=\"awe_wind\">" . esc_html__('wind:', 'forceful-toolkit') . " {$today->wind->speed}" . $speed_text . " {$wind_direction}</div>
                    <div class=\"awe_highlow\"> "  .esc_html__('H', 'forceful-toolkit') . " {$today_high} &bull; " . esc_html__('L', 'forceful-toolkit') . " {$today_low} </div>
                </div> <!-- /.awesome-weather-todays-stats -->
        ";
    }

    if($show_link AND isset($today->id))
    {
        $show_link_text = apply_filters('kopa_awesome_weather_extended_forecast_text' , esc_html__('extended forecast', 'forceful-toolkit'));

        $rtn .= "<div class=\"awesome-weather-more-weather-link\">";
        $rtn .= "<a href=\"http://openweathermap.org/city/{$today->id}\" target=\"_blank\">{$show_link_text}</a>";
        $rtn .= "</div> <!-- /.awesome-weather-more-weather-link -->";
    }

    if($background)
    {
        $rtn .= "</div> <!-- /.awesome-weather-cover -->";
        $rtn .= "</div> <!-- /.awesome-weather-darken -->";
    }

    $rtn .= "</div> <!-- /.awesome-weather-wrap -->";
    return $rtn;
}


// RETURN ERROR
function forceful_toolkit_custom_awesome_weather_error( $msg = false )
{
    if(!$msg) $msg = esc_html__('No weather information available', 'awesome-weather');
    return apply_filters( 'forceful_toolkit_custom_awesome_weather_error', "<!-- AWESOME WEATHER ERROR: " . $msg . " -->" );
}

