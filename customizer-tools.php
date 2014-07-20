<?php

/* WP Customizer */

/**
 * Adds the individual sections, settings, and controls to the theme customizer
 */
function mmm_site_customizer( $wp_customize ) {
    global $customizer_sections;

    if ($customizer_sections != null)
    {
        foreach ($customizer_sections as $section)
        {
            add_customizer_section($wp_customize, $section["id"], $section["name"], $section["description"]);

            foreach ($section["settings"] as $setting)
            {
                add_section_setting($wp_customize, $section["id"], $setting["id"], $setting["name"], $setting["default"], $setting["type"]);
            }
            
        }
    }

    add_action( 'customize_controls_enqueue_scripts', 'admin_scripts' );

    if ($wp_customize->is_preview() && ! is_admin() ) {
        add_action('wp_footer', 'mmm_customize_preview', 21);
    }
}

function mmm_customize_preview() {
    global $customizer_sections;

    $customizeJS = "";

    if ($customizer_sections != null)
    {
        foreach ($customizer_sections as $section)
        {
            foreach ($section["settings"] as $setting)
            {
                switch ($setting["type"]) {
                    case "image":
                        $custom_js = gen_customize_js($setting["id"], $setting["hooks"], "'url(' + %s +')'");
                        break;
                    case "color":
                        $custom_js = gen_customize_js($setting["id"], $setting["hooks"]);
                        break;
                }

                $customizeJS .= $custom_js;
            }
        }
    }

    ?><script type="text/javascript">( function( $ ) {<?php echo $customizeJS; ?>} )( jQuery );</script><?php
}

function mmm_customize_css()
{
    global $customizer_sections;
    $customizeCSS = "";

    if ($customizer_sections != null)
    {
        foreach ($customizer_sections as $section)
        {
            foreach ($section["settings"] as $setting)
            {
                switch ($setting["type"]) {
                    case 'image':
                        $custom_css = gen_customize_css($setting["id"], $setting["hooks"], "url('%s')");
                        break;
                    default:
                        $custom_css = gen_customize_css($setting["id"], $setting["hooks"]);
                        //var_dump($custom_css);
                        break;
                }

                $customizeCSS .= $custom_css;
            }
        }
    }

    ?><style type="text/css"><?php echo $customizeCSS; ?>}</style>
    <?php
}

function add_customizer_section(WP_Customize_Manager $wp_customize, $id, $label, $description, $priority=35)
{
    $wp_customize->add_section(
        $id,
        array(
            'title' => $label,
            'description' => $description,
            'priority' => $priority,
        )
    );

    return $wp_customize;
}

function add_section_setting(WP_Customize_Manager $wp_customize, $section_id, $setting_id, $label, $default_value="", $type="text")
{
    switch ($type)
    {
        case 'text':
            $field = add_text_setting($wp_customize, $section_id, $setting_id, $label, $default_value);
        break;
        case 'color':
            $field = add_color_setting($wp_customize, $section_id, $setting_id, $label, $default_value);
        break;
        case 'image':
            $field = add_image_setting($wp_customize, $section_id, $setting_id, $label, $default_value);
        break;
    }
}

function add_text_setting(WP_Customize_Manager $wp_customize, $section_id, $setting_id, $label, $default_value="")
{
    $wp_customize->add_setting(
        $setting_id,
        array(
            'default' => $default_value,
            'transport' => 'postMessage'
        )
    );

    $wp_customize->add_control(
        $setting_id,
        array(
            'label' => $label,
            'section' => $section_id,
            'type' => 'text',
        )
    );
}

function add_color_setting(WP_Customize_Manager $wp_customize, $section_id, $setting_id, $label, $default_value="")
{
    $wp_customize->add_setting(
        $setting_id,
        array(
            'default' => $default_value,
            'sanitize_callback' => 'sanitize_hex_color',
            'transport' => 'postMessage'
        )
    );

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize,
            $setting_id,
            array(
                'label' => $label,
                'section' => $section_id,
                'settings' => $setting_id,
            )
        )
    );
}

function add_image_setting(WP_Customize_Manager $wp_customize, $section_id, $setting_id, $label, $default_value="")
{
    global $cur_setting_id;
    $cur_setting_id = $setting_id;

    $wp_customize->add_setting(
        $setting_id,
        array(
            'default' => $default_value,
            'transport' => 'postMessage'
        )
    );

    $control = new WP_Customize_Image_Control(
            $wp_customize,
            $setting_id,
            array(
                'label' => $label,
                'section' => $section_id,
                'settings' => $setting_id,
            )
        );

    $wp_customize->add_control($control);
    add_media_library_tab($control);
}

function add_media_library_tab($control)
{
    $control->add_tab('library', __('Media Library'), 'media_library_tab');
}

function media_library_tab() {
    global $cur_setting_id;

    $template = '<a class="btn open-media-library" href="#" data-controller="%s">Open Library</a>';
    echo sprintf($template, $cur_setting_id);
}


function gen_customize_js($customizeKey, $arrClassStyle, $value_wrapper = "%s")
{
    $hooks = "";
    $hook_template = "$('%s').css('%s', %s );";
    $content_template = "wp.customize('%s', function( value ) {value.bind(function(to) {%s});});\n";

    foreach ($arrClassStyle as $class => $style)
    {
        $arrStyleValue = explode(",", $style);

        foreach ($arrStyleValue as $stylevalue) {
            $stylevalue = explode("|", $stylevalue);
            $cur_style = $stylevalue[0];
            $value = sprintf($value_wrapper, "to");

            if (count($stylevalue) > 1)
            {
                $value = sprintf("'%s' + %s", $stylevalue[1], $value);
            }

            $hooks .= sprintf($hook_template, $class, $cur_style, $value);
        }
    }

    $content = sprintf($content_template, $customizeKey, $hooks);

    return $content;
}

function gen_customize_css($customizeKey, $arrClassStyle, $value_wrapper = "%s")
{
    $hooks = "";
    $hook_template = "%s: %s; ";
    $hook_template_wrapper = "%s {%s} ";
    $content_template = "/*%s*/ %s\n";

    foreach ($arrClassStyle as $class => $style)
    {
        $arrStyleValue = explode(",", $style);
        $inner_hooks = "";

        foreach ($arrStyleValue as $stylevalue) {
            $stylevalue = explode("|", $stylevalue);
            $cur_style = $stylevalue[0];
            $value = get_theme_mod($customizeKey);

            if (count($stylevalue) > 1)
            {
                $value = sprintf($stylevalue[1], $value);
            }

            $inner_hooks .= sprintf($hook_template, $cur_style, sprintf($value_wrapper, $value));
        }

        $hooks .= sprintf($hook_template_wrapper, $class, $inner_hooks);
    }

    $content = sprintf($content_template, $customizeKey, $hooks);

    return $content;
}
 
function admin_scripts() {
    wp_enqueue_media();
    wp_enqueue_script('shiba-media-manager', get_template_directory_uri().'/assets/admin/js/mmm-media-manager.js', array( ), '1.0', true);
}

?>