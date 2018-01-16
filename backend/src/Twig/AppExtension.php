<?php

namespace App\Twig;

use App\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Class AppExtension
 * @package App\Twig
 */
class AppExtension extends AbstractExtension
{
    private $parser;

    public function __construct(Markdown $parser)
    {
        $this->parser = $parser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Transforms the given Markdown content into HTML content.
     * @param string $content
     * @return string
     */
    public function markdownToHtml(string $content): string
    {
        return $this->parser->toHtml($content);
    }
}