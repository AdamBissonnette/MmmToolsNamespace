<?php

require_once( ABSPATH . WPINC . '/class-wp-customize-control.php' );

class Mmm_Color_Control extends WP_Customize_Control {
    public $type = 'text';

    public function render_content() {
        ob_start(); //Since there isn't a nice way to get this link content we have to use the output buffer
        $this->link();
        $link = ob_get_contents();
        ob_end_clean();

        echo createFormField($this->id, "", $this->value(), "color", array("title" => $this->label, "link" => $link));
    }
}