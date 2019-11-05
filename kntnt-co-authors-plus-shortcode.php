<?php

/**
 * @wordpress-plugin
 * Plugin Name:       Kntnt Shortcode for Co-Authors Plus
 * Plugin URI:        https://github.com/kntnt/kntnt-co-authors-plus-shortcode
 * GitHub Plugin URI: https://github.com/kntnt/kntnt-co-authors-plus-shortcode
 * Description:       Provides shortcodes for the Co-Authors Plus plugin.
 * Version:           1.0.0
 * Author:            Thomas Barregren
 * Author URI:        https://www.kntnt.com/
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 */


namespace Kntnt\Co_Author_Pro_Shortcode;

defined( 'WPINC' ) && new Plugin;

class Plugin {

    // Allowed arguments
    private static $defaults = [
        'field' => 'display_names',
        'between' => null,
        'betweenLast' => null,
        'before' => null,
        'after' => null,
    ];

    // Allowed fields
    private static $fields = [
        'display_names',
        'posts_links',
        'posts_links_single',
        'firstnames',
        'lastnames',
        'nicknames',
        'nicenames',
        'links',
        'emails',
        'links_single',
        'ids',
    ];

    public function __construct() {
        if ( is_callable( 'coauthors__echo' ) ) {
            add_shortcode( 'coauthors', [ $this, 'coauthors_shortcode' ] );
        }
    }

    public function coauthors_shortcode( $atts ) {
        $atts = $this->shortcode_atts( self::$defaults, $atts );
        if ( 'nicenames' == $atts['field'] ) {
            $function = [ $this, 'coauthors_nicenames' ];
        }
        else if ( 'display_names' == $atts['field'] ) {
            $function = 'coauthors';
        }
        else {
            $function = "coauthors_{$atts['field']}";
        }
        return call_user_func( $function, $atts['between'], $atts['betweenLast'], $atts['before'], $atts['after'], false );
    }

    private function coauthors_nicenames( $between, $betweenLast, $before, $after, $echo ) {
        return coauthors__echo( 'get_the_author_meta', 'tag', [
            'between' => $between,
            'betweenLast' => $betweenLast,
            'before' => $before,
            'after' => $after,
        ], 'nicename', $echo );
    }

    // A more forgiving version of WP's shortcode_atts().
    private function shortcode_atts( $pairs, $atts, $shortcode = '' ) {

        $atts = (array) $atts;
        $out = [];
        $pos = 0;
        while ( $name = key( $pairs ) ) {
            $default = array_shift( $pairs );
            if ( array_key_exists( $name, $atts ) ) {
                $out[ $name ] = $atts[ $name ];
            }
            else if ( array_key_exists( $pos, $atts ) && $atts[ $pos ] ) {
                $out[ $name ] = $atts[ $pos ];
                ++ $pos;
            }
            else {
                $out[ $name ] = $default;
            }
        }

        if ( $shortcode ) {
            $out = apply_filters( "shortcode_atts_{$shortcode}", $out, $pairs, $atts, $shortcode );
        }

        return $out;

    }

}
