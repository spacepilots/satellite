<?php

namespace Satellite;

use Symfony\Component\Yaml\Yaml;

class Nodes implements \ArrayAccess
{
    protected $nodes;

    public function __construct(array $nodes)
    {
        $this->nodes = $nodes;
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

    public function parse(Node $node)
    {
        // TODO: Add caching.
        $content = file_get_contents($node->getPath());

        $pattern = '/^[\s\r\n]?---[\s\r\n]?$/sm';
        $parts = preg_split($pattern, PHP_EOL . ltrim($content));

        if (count($parts) < 3) {
            $matter = [];
            $body = $content;
        } else {
            $matter = Yaml::parse(trim($parts[1]));
            $body = implode(PHP_EOL . '---' . PHP_EOL, array_slice($parts, 2));
        }

        $node->setData($matter);
        $node->setTemplate($body);
    }

    public function parseAll()
    {
        foreach ($this->nodes as $node) {
            $this->parse($node);
        }
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
