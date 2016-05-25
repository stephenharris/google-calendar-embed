<?php

/**
 * Widget for embedding Google calendars
 */
class Google_Calendar_Embed_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname'   => 'google-calendar-embed-widget',
            'description' => 'Embed Google calendars into your website',
        );
        parent::__construct( 'google-calendar-embed-widget', 'Google Calendar Embed', $widget_ops );
    }

    /**
     * Outputs the content of the widget
     */
    public function widget( $args, $instance ) {
        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
        }
        echo do_shortcode( $instance['parsed'] );
        echo $args['after_widget'];
    }

    /**
     * Outputs the options form on admin
     */
    public function form( $instance ) {
        $title  = ! empty( $instance['title'] ) ? $instance['title'] : '';
        $iframe = ! empty( $instance['iframe'] ) ? $instance['iframe'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'iframe' ) ); ?>"><?php esc_attr_e( 'Iframe:' ); ?></label>
            <textarea class="widefat" rows="12" cols="20" id="<?php echo esc_attr( $this->get_field_id( 'iframe' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'iframe' ) ); ?>"><?php echo esc_textarea( $iframe ); ?></textarea>
        </p>
        <?php
    }

    /**
     * Processing widget options on save
     */
    public function update( $new_instance, $old_instance ) {
        $iframe = $new_instance['iframe'];
        $parsed = wp_filter_nohtml_kses( google_calendar_embed_iframe_parser( addslashes( $iframe ) ) );

        $validated = array(
            'title'  => sanitize_text_field( $new_instance['title'] ),
            'iframe' => $iframe,
            'parsed' => stripslashes( $parsed ),
        );
        return $validated;

    }
}