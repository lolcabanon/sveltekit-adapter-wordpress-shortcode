<?php

function SHORTCODE_PREFIX_shortcodeHead()
{
    return file_get_contents(plugin_dir_path(__FILE__) . 'svelte_kit_shortcode_head.html');
}

function SHORTCODE_PREFIX_shortcodeBody()
{
    return file_get_contents(plugin_dir_path(__FILE__) . 'svelte_kit_shortcode_body.html');
}

function SHORTCODE_PREFIX_shortcodeData($attributes, $content)
{
    $jsonAttributes = json_encode($attributes);
    return <<<HTML
            <script id="SHORTCODE_CODE-attributes" type="application/json">
                {$jsonAttributes}
            </script>
            <template id="SHORTCODE_CODE-content">
                {$content}
            </template>
        HTML;
}

add_shortcode('SHORTCODE_CODE', function ($attributes, $content) {
    $injection = SHORTCODE_PREFIX_shortcodeData($attributes, $content) . SHORTCODE_PREFIX_shortcodeBody();

    $result = '';

    if (SHORTCODE_SHADOW) {
        $injection .= SHORTCODE_PREFIX_shortcodeHead();
        $result     = <<<HTML
               <template id="SHORTCODE_CODE-template">
                   {$injection}
               </template>
               <div id="SHORTCODE_CODE-container"></div>
               <script>
                   document
                       .querySelector("#SHORTCODE_CODE-container")
                       .attachShadow({ mode: "open" })
                       .appendChild(document.querySelector("#SHORTCODE_CODE-template").content)
               </script>
            HTML;
    } else {
        $result = $injection;
    }

    if (APPEND_TO_BODY) {
        add_option('SHORTCODE_PREFIX_content', $result);
        return '';
    } else {
        return $result
    }
});

add_action('wp_head', function () {
    if (SHORTCODE_SHADOW) return;

    global $post;
    if (!has_shortcode($post->post_content, 'SHORTCODE_CODE')) return;

    echo SHORTCODE_PREFIX_shortcodeHead();
});

if (APPEND_TO_BODY) {
    // Étape 2 : Utiliser un hook pour insérer le contenu dans le <body>
    function render_SHORTCODE_PREFIX_content()
    {
        // Récupérez le contenu enregistré
        $content = get_option('SHORTCODE_PREFIX_content');
        
        if ($content) {
            echo $content;
            // Supprimez l'option après l'avoir utilisée
            delete_option('SHORTCODE_PREFIX_content');
        }
    }   
    add_action('wp_footer', 'render_SHORTCODE_PREFIX_content');
}

?>
