<?php namespace Goetas\XML\XSDReader\Utils;

/**
 * Class Uri
 * @package Goetas\XML\XSDReader\Utils
 *
 * Implementation from symfony/dom-crawler:~2.5, LICENSE:
 *
 * Copyright (c) 2004-2014 Fabien Potencier
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class Uri {

    /**
     * @var string
     */
    protected $currentUri;

    /**
     * @var string
     */
    protected $nextUri;

    /**
     * @param string $currentUri
     * @param string $nextUri
     *
     * @throws \InvalidArgumentException if the currentUri is not valid
     */
    public function __construct($currentUri, $nextUri)
    {
        if (!in_array(strtolower(substr($currentUri, 0, 4)), array('http', 'file'))) {
            throw new \InvalidArgumentException(sprintf('Current URI must be an absolute URL ("%s").', $currentUri));
        }

        $this->currentUri = $currentUri;
        $this->nextUri = $nextUri;
    }

    public function getUri()
    {
        $uri = trim($this->getRawUri());

        // empty URI
        if (!$uri) {
            return $this->currentUri;
        }

        // absolute URL?
        if (null !== parse_url($uri, PHP_URL_SCHEME)) {
            return $uri;
        }

        // an anchor
        if ('#' === $uri[0]) {
            return $this->cleanupAnchor($this->currentUri).$uri;
        }

        $baseUri = $this->cleanupUri($this->currentUri);

        if ('?' === $uri[0]) {
            return $baseUri.$uri;
        }

        // absolute URL with relative schema
        if (0 === strpos($uri, '//')) {
            return preg_replace('#^([^/]*)//.*$#', '$1', $baseUri).$uri;
        }

        $baseUri = preg_replace('#^(.*?//[^/]*)(?:\/.*)?$#', '$1', $baseUri);

        // absolute path
        if ('/' === $uri[0]) {
            return $baseUri.$uri;
        }

        // relative path
        $path = parse_url(substr($this->currentUri, strlen($baseUri)), PHP_URL_PATH);
        $path = $this->canonicalizePath(substr($path, 0, strrpos($path, '/')).'/'.$uri);

        return $baseUri.('' === $path || '/' !== $path[0] ? '/' : '').$path;
    }

    /**
     * Returns raw URI data.
     *
     * @return string
     */
    protected function getRawUri()
    {
        return $this->nextUri;
    }

    /**
     * Returns the canonicalized URI path (see RFC 3986, section 5.2.4)
     *
     * @param string $path URI path
     *
     * @return string
     */
    protected function canonicalizePath($path)
    {
        if ('' === $path || '/' === $path) {
            return $path;
        }

        if ('.' === substr($path, -1)) {
            $path = $path.'/';
        }

        $output = array();

        foreach (explode('/', $path) as $segment) {
            if ('..' === $segment) {
                array_pop($output);
            } elseif ('.' !== $segment) {
                array_push($output, $segment);
            }
        }

        return implode('/', $output);
    }

    /**
     * Removes the query string and the anchor from the given uri.
     *
     * @param string $uri The uri to clean
     *
     * @return string
     */
    private function cleanupUri($uri)
    {
        return $this->cleanupQuery($this->cleanupAnchor($uri));
    }

    /**
     * Remove the query string from the uri.
     *
     * @param string $uri
     *
     * @return string
     */
    private function cleanupQuery($uri)
    {
        if (false !== $pos = strpos($uri, '?')) {
            return substr($uri, 0, $pos);
        }

        return $uri;
    }

    /**
     * Remove the anchor from the uri.
     *
     * @param string $uri
     *
     * @return string
     */
    private function cleanupAnchor($uri)
    {
        if (false !== $pos = strpos($uri, '#')) {
            return substr($uri, 0, $pos);
        }

        return $uri;
    }

}
