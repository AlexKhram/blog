<?php

namespace App\Utils;

/**
 * Class Markdown
 * @package App\Utils
 */
class Markdown
{
    private $parser;
    private $purifier;

    public function __construct()
    {
        $this->parser   = new \Parsedown();
        $this->purifier = new \HTMLPurifier(\HTMLPurifier_Config::create([
            'Cache.DefinitionImpl' => null, // Disable caching
        ]));
    }

    public function toHtml(string $text): string
    {
        return $this->purifier->purify($this->parser->text($text));
    }
}