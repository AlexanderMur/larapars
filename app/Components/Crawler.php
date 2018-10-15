<?php

namespace App\Components;

use Symfony\Component\DomCrawler\Crawler as BaseCrawler;

/**
 * Class Crawler
 * @package App\Components
 * @method $this filterXpath($xpath)
 */
class Crawler extends BaseCrawler
{
    public function index()
    {
        $i = 0;
        foreach ($this->parents()->children() as $child) {
            if ($child->isSameNode($this->getNode(0))) {
                break;
            }
            $i++;
        }
        return $i;
    }

    public function map(\Closure $closure)
    {
        return $this->each($closure);
    }

    /**
     * @param $selector
     * @return $this|BaseCrawler
     */
    public function query($selector){
        if(!$selector){
            return $this->eq(-1);
        }
        if(strpos($selector,'/') !== false){
            return parent::filterXPath($selector);
        }
        return parent::filter($selector);
    }
    /**
     * @param string $selector
     * @return $this|BaseCrawler
     */
    public function filter($selector)
    {
        if (is_string($selector)) {
            return parent::filter($selector);
        }
        if(is_callable($selector)){
            return $this->reduce($selector);
        }
        return null;
    }

    /**
     * @param null $default
     * @return null|string
     */
    public function getText($default = null){
        if($this->count()){
            return trim($this->text());
        }
        return null;
    }
}