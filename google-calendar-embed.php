<?php
/*
Plugin Name: Google Calendar Embed
Version: 1.0.0
Description: Embed Google calendars into your website
Author: Stephen Harris
Author: URI: http://www.stephenharris.info
License: GPL v2 or higher
*/


/**
 * Returns the HTML for the [googlecalendar] shortcode
 *
 * @since 1.0.0
 * @param $args array An array with keys
 * @param array $args {
 *     Settings array.
 *     @type string $src    The URL of the calendar to embed
 *     @type int    $width  The width of the embed
 *     @type int    $height The height of the embed
 *     @type string $style  Any styling attributes for the embed
 * }
 */
function google_calendar_embed_shortcode( $args ) {

    $defaults = array( 'src' => null, 'width' => 800, 'height' => 600, 'style' => null );

    //Merge in defaults, and whitelist keys
    $args = array_merge( $defaults, $args );
    $args = array_intersect_key( $args, $defaults );

    $arguments = '';
    foreach ( $args as $key => $value ) {
        if ( is_null( $value ) ) {
            continue;
        }
        switch ( $key ) {

            case 'height':
            case 'width':
                $value = (int) $value;
                break;

            case 'style':
                $value = esc_attr( $value );
                break;

            case 'src':
                //We're creating a generic iframe, make sure it is a google calendar URL
                if ( ! preg_match( '#^https://calendar.google.com/calendar/embed\?([^\.]*)group.calendar.google.com(.*)$#i', $value) ) {
                    return '';
                }
                $value = esc_url( $value );
                break;

            default:
                continue;
        }
        $arguments .= "$key=\"$value\"";
    }
    return "<iframe {$arguments} frameborder='0' scrolling='no'></iframe>";
}
add_shortcode( 'googlecalendar' , 'google_calendar_embed_shortcode' );


/**
 * Parses iframes before they a stripped.
 * If one is identified as a Google calendar iframe, then its turned into a shortcode.
 * This is here as a server-side fallback. Usually the Visual editor will convert the Google calendar iframe to
 * a shortcode, but that might not be enabled or used by the user.
 * @access private
 * @hooked content_save_pre
 */
function google_calendar_embed_iframe_parser( $content ) {
    $content = preg_replace(
        '/(<iframe) ([^\]><]*src=\\\"https:\/\/calendar.google.com\/calendar\/embed\?[^\]><]*)(><\/iframe>)/i',
        '[googlecalendar $2]',
        $content
    );
    return $content;
}
add_filter( 'content_save_pre', 'google_calendar_embed_iframe_parser', 5 );


/**
 * Registers our TinyMCE plugin for handling pasting of iframe codes for Google calendar
 * @access private
 * @hooked init
 */
function google_calendar_embed_tinymce_plugin_init() {
    //Add a callback to regiser our tinymce plugin
    add_filter( 'mce_external_plugins', function( $plugin_array ) {
        $plugin_array['googlecalendarembed'] = plugin_dir_url( __FILE__ ) . 'google-calendar-embed.js';
        return $plugin_array;
    });
}
add_action( 'init', 'google_calendar_embed_tinymce_plugin_init' );
