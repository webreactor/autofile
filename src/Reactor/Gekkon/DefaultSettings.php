<?php

namespace Reactor\Gekkon;

class DefaultSettings {

    static function get() {
        return array(
            'display_errors' => true,
            'auto_escape' => false,
            'force_compile' => false,
            'tag_systems' => array(
                'Reactor\\Gekkon\\Tags\\SimpleEcho\\TagSystem' => array(
                    'open' => '{',
                    'close' => '}',
                ),
                'Reactor\\Gekkon\\Tags\\Common\\TagSystem' => array(
                    'open' => '{',
                    'close' => '}',
                ),
                'Reactor\\Gekkon\\Tags\\HtmlSpecialChars\\TagSystem' => array(
                    'open' => '{{',
                    'close' => '}}',
                ),
                'Reactor\\Gekkon\\Tags\\Comment\\TagSystem' => array(
                    'open' => '{#',
                    'close' => '#}',
                ),
            ),
        );
    }

}
