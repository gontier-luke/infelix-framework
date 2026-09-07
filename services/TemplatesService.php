<?php

namespace Services;

class TemplatesService {

    const TEMPLATES_DIR = BASE_PATH . '/templates/';
    public static function renderTemplate(string $template, array $data = []): string {
        $template = self::TEMPLATES_DIR . $template;

        foreach ($data as $key => $value) {
            $$key = $value;
        }

        ob_start();
        include $template;
        $output = ob_get_contents();
        ob_end_clean();

        return $output;
    }

}