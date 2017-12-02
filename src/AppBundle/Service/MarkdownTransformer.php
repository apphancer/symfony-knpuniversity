<?php


namespace AppBundle\Service;


use function strtoupper;

class MarkdownTransformer
{

    private $markdownParser;

    public function __construct($markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
        return $this->markdownParser
            ->transform($str);
    }

}