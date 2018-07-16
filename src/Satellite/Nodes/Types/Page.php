<?php

namespace Satellite\Nodes\Types;

use Satellite\Nodes\Interfaces\Databag as DatabagInterface;
use Satellite\Nodes\Interfaces\Localizable as LocalizableInterface;
use Satellite\Nodes\Traits\Databag;
use Satellite\Nodes\Traits\Localizable;

class Page extends AbstractNode implements DatabagInterface, LocalizableInterface
{
    use Localizable, Databag;

    /**
     * @param Template[] $nodes
     * @return void
     */
    public static function fromTemplates(array $nodes)
    {
        $node = reset($nodes);

        $page = new static();
        $page->setName($node->getName());
        $page->setParentId($node->getParentId());
        $page->setPath($node->getPath());
        $page->setSiteId($node->getSiteId());

        foreach ($nodes as $node) {
            $page->addLocale($node);
        }

        return $page;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return "page";
    }
}
