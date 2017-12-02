<?php


namespace AppBundle\Service;


use function strtoupper;

class MarkdownTransformer
{
    public function parse($str)
    {
        return strtoupper($str);
    }

}