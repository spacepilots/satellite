<?php

namespace Satellite\Nodes;

use Symfony\Component\Yaml\Yaml;

class Parser
{
    protected static $protectedNames = ["404"];

    public function parse(Node $node)
    {
        $this->parseFrontMatter($node);
        $this->parseName($node);
        $node->setFlag("parsed", true);
    }

    protected function parseName(Node $node)
    {
        $file = $node->getFile();

        $dirname = dirname($file->getRealPath());
        $basename = $file->getBasename(".html.twig");

        if (preg_match('/.+\.(\w{2}(\_\w{2})?)$/i', $basename, $localeMatches)) {
            $node->setLocale($localeMatches[1]);
            $basename = substr($basename, 0, (strlen($localeMatches[1]) + 1) * -1);
        }
        if (preg_match('/^(\d+\.).+/', $basename, $orderMatches)) {
            $node->setOrder($node->getParent(), intval($orderMatches[1]));
            $basename = substr($basename, strlen($orderMatches[1]));
        }

        if (in_array($basename , self::$protectedNames)) {
            $node->setFlag("routable", false);
        }

        $node->setName($basename);
    }

    protected function parseFrontMatter(Node $node)
    {
        if (!$node->getFile()->isFile()) {
            return;
        }

        // TODO: Add caching.
        $content = file_get_contents($node->getRealPath());

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
        // $node->setTemplate($body);
    }
}
