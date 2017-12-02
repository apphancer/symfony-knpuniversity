<?php


namespace AppBundle\Service;


use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;
use function strtoupper;

class MarkdownTransformer
{

    private $markdownParser;

    // when you need an object from inside a class, use dependency injection.
    // And when you add the __construct() argument, type-hint it with either the class you see in debug:container or an interface if you can find one.
    // Both totally work.
    public function __construct(MarkdownParserInterface $markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
        return $this->markdownParser
            ->transformMarkdown($str);
    }

}