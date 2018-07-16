<?php

namespace Satellite\Nodes\Interfaces;

use Satellite\Nodes\Types\AbstractNode;

interface Localizable
{
    /**
     * @param AbstractNode $node
     * @return void
     */
    public function addLocale(AbstractNode $node);

    /**
     * @param string $locale
     * @param boolean $strict
     * @return AbstractNode|null
     */
    public function getLocale($locale, $strict = true);
}
