<?php
namespace Goetas\XML\XSDReader\Utils;

class UrlUtils
{

    /**
     * @param string $base
     * @param string $rel
     * @return string
     */
    public static function resolveRelativeUrl($base, $rel)
    {
        $uri = new Uri($base, $rel);

        return $uri->getUri();
    }

}
