<?php

namespace Satellite\Nodes;

use Satellite\Nodes\Parser;
use Symfony\Component\Yaml\Yaml;

class Repository implements \ArrayAccess
{
    protected $nodes = [];
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function getParent(Node $node)
    {
        return array_key_exists($node->getParent(), $this->nodes)
            ? $this->nodes[$node->getParent()]
            : null;
    }

    public function find(string $pagePath)
    {
        if (!$pagePath) {
            throw new \RuntimeException("Missing pages/ folder");
        }

        $mapping = [];
        $nodes = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($pagePath),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        while ($iterator->valid()) {
            /** @var $iterator \SplFileInfo */
            if ($iterator->isDot()) {
                $iterator->next();
                continue;
            }

            // echo "<hr />";
            // \var_dump($iterator->getRealPath());
            //\var_dump(dirname($iterator->getRealPath()));


            // /** @var $iterator \SplFileInfo */
            // if ($iterator->isDot() || $iterator->isDir() || ($iterator->isFile() && stripos($iterator->getBasename(), ".html.twig") === false)) {
            //     $iterator->next();
            //     continue;
            // }

            // // $parent = dirname($iterator->getRealPath());

            $node = new Node($iterator->current());
            $this->parser->parse($node);

            if ($iterator->isDir()) {
                $mapping[$iterator->getRealPath()] = $node->getId();
            }

            $parent = dirname($node->getRealPath());
            $node->setParent(isset($mapping[$parent]) ? $mapping[$parent] : null);

            // // if (!isset($mapping[$parent])) {
            // //     $mapping[$parent] = [$node->getId()];
            // // } else {
            // //     $isVariant = false;
            // //     foreach ($mapping[$parent] as $siblingId) {
            // //         $sibling = $this->nodes[$siblingId];
            // //         var_dump($sibling->getName() . " === " . $node->getName());
            // //         if ($sibling->getName() === $node->getName()) {
            // //             var_dump("Add variant: " . $node->getLocale());
            // //             $sibling->addVariant($node->getLocale(), $node);
            // //             $isVariant = true;
            // //             break;
            // //         }
            // //     }
            // //     if ($isVariant) {
            // //         $iterator->next();
            // //         continue;
            // //     } else {
            // //         $node->setParent(reset($mapping[$parent]));
            // //         $mapping[$parent][] = $node->getId();
            // //     }
            // // }

            // // var_dump($node, $parent);

            // // // if ($iterator->isFile() && $node->getName() === "index") {
            // // //     $this->nodes[$node->getParent()]->merge($node);
            // // //     $iterator->next();
            // // //     continue;
            // // // }

            $nodes[$node->getId()] = $node;
            $iterator->next();
        }

        foreach ($nodes as $node) {
            if (!$node->getFile()->isFile() || $node->getName() !== "index") {
                continue;
            }
            $nodes[$node->getParent()]->addVariant($node->getIdentifier(), $node);
            $nodes[$node->getParent()]->setFlag("routable", true);
            unset($nodes[$node->getId()]);
        }

        $this->nodes = $this->nodes + $nodes;
    }

    public function offsetUnset($id)
    {
        unset($this->nodes[$id]);
    }

    public function offsetSet($id, $node)
    {
        $this->nodes[$id] = $node;
    }

    public function offsetGet($id)
    {
        return $this->nodes[$id];
    }

    public function offsetExists($id)
    {
        return array_key_exists($this->nodes, $id);
    }
}
