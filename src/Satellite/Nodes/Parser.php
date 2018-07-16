<?php

namespace Satellite\Nodes;

use Satellite\Nodes\Types\Controller;
use Satellite\Nodes\Types\Data;
use Satellite\Nodes\Types\Directory;
use Satellite\Nodes\Types\File;
use Satellite\Nodes\Types\Template;
use SplFileInfo;

class Parser
{
    /**
     * @param SplFileInfo $file
     * @return Node
     */
    public function parse($siteId, SplFileInfo $file)
    {
        $node = $this->createNode($file);
        $node->setSiteId($siteId);

        $name = $node->getName();

        // TODO: Compare locales against a list of valid locales
        if (preg_match('/.+\.(\w{2}(\_\w{2})?)$/i', $name, $localeMatches)) {
            if (!method_exists($node, "setLocale")) {
                throw new \RuntimeException("File '{$file->getRealPath()}' cannot be localized");
            }
            $node->setLocale($localeMatches[1]);
            $name = substr($name, 0, (strlen($localeMatches[1]) + 1) * -1);
        }
        if (preg_match('/^(\d+\.).+/', $name, $orderMatches)) {
            // $node->setOrder(intval($orderMatches[1]));
            $name = substr($name, strlen($orderMatches[1]));
        }

        $node->setName($name);

        return $node;
    }

    /**
     * @param SplFileInfo $file
     * @return Satellite\Nodes\Types\Node
     */
    protected function createNode(SplFileInfo $file)
    {
        if ($file->isDir()) {
            return new Directory($file);
        }

        switch ($file->getExtension()) {
            case "twig":
                $node = new Template($file);
                return $node;
            case "yaml":
            case "yml":
                $node = new Data($file);
                return $node;
            case "php":
                $node = new Controller($file);
                return $node;
            default:
                $node = new File($file);
                return $node;
        }
    }
}
