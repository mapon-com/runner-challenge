<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;
use Michelf\MarkdownExtra;

class TextService
{
    /** @var SettingsService */
    private $settings;

    public function __construct()
    {
        $this->settings = new SettingsService;
    }

    public function setRules(string $markdown)
    {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $html = MarkdownExtra::defaultTransform($markdown);

        $this->settings->set('rules_html', $purifier->purify($html));
        $this->settings->set('rules', $markdown);
    }

    public function getRules(): string
    {
        return $this->settings->get('rules', '');
    }

    public function getRulesHtml(): string
    {
        return $this->settings->get('rules_html', '');
    }
}