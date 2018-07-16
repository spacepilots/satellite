<?php

namespace Satellite\Nodes\Traits;

use Satellite\Nodes\Types\AbstractNode;

trait Localizable
{
    /**
     * @var array
     */
    protected $locales = [];

    /**
     * @param AbstractNode $node
     * @return void
     */
    public function addLocale(AbstractNode $node)
    {
        $locale = $node->getLocale() ?: "default";
        $this->locales[$locale] = $node;
    }

    /**
     * @param string $locale
     * @param boolean $strict
     * @return AbstractNode|null
     */
    public function getLocale($locale, $strict = true)
    {
        if (!$strict) {
            throw new \BadMethodCallException("Not implemented yet.");
        }
        return isset($this->locales[$locale]) ? $this->locales[$locale] : null;
    }
}
