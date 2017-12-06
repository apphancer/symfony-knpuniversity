<?php


namespace AppBundle\Service;


use Doctrine\Common\Cache\Cache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{

    private $markdownParser;
    /**
     * @var Cache
     */
    private $cache;

    // when you need an object from inside a class, use dependency injection.
    // And when you add the __construct() argument, type-hint it with either the class you see in debug:container or an interface if you can find one.
    // Both totally work.
    // run bin/console debug:container {service_name}
    // search for console result in the Class row, use the Class name to search in the entire project
    // check if the Class implements another class and eventually use the latter for the type int
    // e.g:
    // bin/console debug:container doctrine_cache.providers.markdown_cache
    // Class = Doctrine\Common\Cache\ArrayCache
    // This class extends CacheProvider
    // CacheProvider implements Cache
    // thefore, typeint = Cache
    public function __construct(MarkdownParserInterface $markdownParser, Cache $cache)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
    }

    public function parse($str)
    {
        $cache = $this->cache;
        $key = md5($str);

        if ($cache->contains($key))
        {
            return $cache->fetch($key);
        }

        sleep(1);
        $str = $this->markdownParser
            ->transformMarkdown($str);

        $cache->save($key, $str);

        return $str;
    }

}