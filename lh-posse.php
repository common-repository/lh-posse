<?php
/*
Plugin Name: LH Posse
Plugin URI: https://lhero.org/plugins/lh-posse/
Description: Adds several feeds to Wordpress customised based on post format for posting to facebook and twitter via IFTTT
Author: Peter Shaw
Version: 1.03
Author URI: https://shawfactor.com/
License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2017  Peter Shaw  (email : pete@localhero.biz)
*/






include_once('includes/truncenator.php');
include_once("includes/tw-helpers.php");




if (!class_exists('LH_posse_plugin')) {

class LH_posse_plugin {
    
static function truncate_string($string,$min) {
    $text = trim(strip_tags($string));
    if(strlen($text)>$min) {
        $blank = strpos($text,' ');
        if($blank) {
            # limit plus last word
            $extra = strpos(substr($text,$min),' ');
            $max = $min+$extra;
            $r = substr($text,0,$max);
            if(strlen($text)>=$max) $r=trim($r,'.').'...';
        } else {
            # if there are no spaces
            $r = substr($text,0,$min).'...';
        }
    } else {
        # if original length is lower than limit
        $r = $text;
    }

$r = trim(preg_replace('/\s\s+/', ' ', $r));
    return $r;
}    
    
    
    
    
static function clean_excerpt($excerpt){

return preg_replace('#(<a[^>]*>).*?(</a>)#', '', $excerpt);

} 

static function get_link_url() {
	$content = get_the_content();
	$has_url = get_url_in_content( $content );

	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}


static function print_hashtags(){

$posttags = get_the_tags();
if ($posttags) {
  foreach($posttags as $tag) {
    echo ' #'.$tag->slug; 
  }
}


}

static function return_hashtags(){

$posttags = get_the_tags();

$tags = '';  
  
if ($posttags) {
$tags = '';  
  foreach($posttags as $tag) {
$tags .= ' #'.$tag->slug; 
}
  
return $tags;
  
} else {
  
  
return false;
  
}


}


    
    
    
public function fb_feed_xml() {

load_template(dirname(__FILE__) . '/templates/feed-lh-posse-fb.php');

}

public function tw_feed_xml() {

load_template(dirname(__FILE__) . '/templates/feed-lh-posse-tw.php');

}

public function attach_feed_xml() {

load_template(dirname(__FILE__) . '/templates/feed-lh-posse-attach.php');

}

public function add_feeds() {

add_feed('lh-posse-fb', array($this, 'fb_feed_xml'));
add_feed('lh-posse-tw', array($this, 'tw_feed_xml'));
add_feed('lh-posse-attach', array($this, 'attach_feed_xml'));


}
    
public function add_query_format_standard($query) {
    if (isset($query->query_vars['post_format']) && $query->query_vars['post_format'] == 'post-format-standard') {
        if (($post_formats = get_theme_support('post-formats')) &&
            is_array($post_formats[0]) && count($post_formats[0])) {
            $terms = array();
            foreach ($post_formats[0] as $format) {
                $terms[] = 'post-format-'.$format;
            }
            $query->is_tax = null;
            unset($query->query_vars['post_format']);
            unset($query->query_vars['taxonomy']);
            unset($query->query_vars['term']);
            unset($query->query['post_format']);
            $query->set('tax_query', array(
                'relation' => 'AND',
                array(
                    'taxonomy' => 'post_format',
                    'terms' => $terms,
                    'field' => 'slug',
                    'operator' => 'NOT IN'
                )
            ));
        }
    }
}
    
        public function __construct() {
            
        add_action('init', array($this, 'add_feeds'));

if (isset($_GET['feed'])){

remove_filter('template_redirect', 'redirect_canonical');

}
            
            
        add_action('pre_get_posts', array($this, 'add_query_format_standard'));
            
            
        }
    
}

$lh_posse_instance = new LH_posse_plugin();


}



?>