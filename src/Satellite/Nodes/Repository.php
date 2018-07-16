<?php

namespace Satellite\Nodes;

use Satellite\Nodes\Parser;
use Satellite\Nodes\Types\AbstractNode;
use Satellite\Nodes\Types\Page;
use SplFileInfo;

class Repository implements \ArrayAccess
{
    /**
     * @var \Satellite\Nodes\AbstractNode[]
     */
    protected $nodes = [];

    /**
     * @var \Satellite\Nodes\Parser
     */
    protected $parser;

    /**
     * @param \Satellite\Nodes\Parser $parser
     */
    public function __construct(Parser $parser = null)
    {
        $this->parser = $parser ?: new Parser();
    }

    /**
     * @return Node[]
     */
    public function getNodes()
    {
        return $this->nodes;
    }

    /**
     * @param \Satellite\Nodes\AbstractNode $node
     * @return \Satellite\Nodes\AbstractNode|null
     */
    public function getParent(AbstractNode $node)
    {
        return array_key_exists($node->getParentId(), $this->nodes)
        ? $this->nodes[$node->getParentId()]
        : null;
    }

    /**
     * @param string $id
     * @return void
     */
    public function offsetUnset($id)
    {
        unset($this->nodes[$id]);
    }

    /**
     * @param string $id
     * @param \Satellite\Nodes\AbstractNode
     * @return void
     */
    public function offsetSet($id, $object)
    {
        $this->nodes[$id] = $object;
    }

    /**
     * @param string $id
     * @return \Satellite\Nodes\AbstractNode
     */
    public function offsetGet($id)
    {
        return array_key_exists($id, $this->nodes) ? $this->nodes[$id] : null;
    }

    /**
     * @param string $id
     * @return void
     */
    public function offsetExists($id)
    {
        return array_key_exists($id, $this->nodes);
    }

    /**
     * @param string $pagePath
     * @return void
     */
    public function scan(string $pagePath)
    {
        $path = realpath($pagePath);
        if (!$path) {
            throw new \RuntimeException("Invalid path $pagePath");
        }

        $sites = glob($path . '/*', GLOB_ONLYDIR);

        foreach ($sites as $site) {
            $this->nodes = $this->nodes + $this->scanNodes($site, basename($site));
        }
    }

    /**
     * @param string $path
     * @param string $site
     * @return \Satellite\Nodes\AbstractNode[]
     */
    protected function scanNodes($path, $siteId)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)
        );

        $files = [];
        $nodes = [];
        $root = dirname($path);

        // Find all files and directories in $path
        /** @var $fileObject \SplFileInfo */
        foreach ($iterator as $fileObject) {
            $file = $this->parser->parse($siteId, $fileObject);
            $currentPath = $fileObject->getRealPath();

            $files[$currentPath] = $file;
            $parentPath = dirname($fileObject->getRealPath());
            while (isset($parentPath) && $parentPath !== $root) {
                if (!isset($files[$parentPath])) {
                    $files[$parentPath] = $this->parser->parse($siteId, new SplFileInfo($parentPath));
                }

                $files[$currentPath]->setParentId($files[$parentPath]->getId());

                $currentPath = $parentPath;
                $parentPath = dirname($parentPath);
            }
        }

        // Merge all templates with the same name and parent but with a different
        // locale into localized pages.
        // Example: index.de.html.twig and index.en.html.twig into index.html.twig
        foreach ($files as $path => $file) {
            if (!($file instanceof Types\Template)) {
                continue;
            }

            $templates = [$file];
            foreach ($files as $innerPath => $innerFile) {
                if ($innerFile instanceof Types\Template
                    && $innerFile->getId() !== $file->getId()
                    && $innerFile->getName() === $file->getName()
                    && $innerFile->getParentId() === $file->getParentId()) {
                    $templates[] = $innerFile;
                    unset($files[$innerPath]);
                    break;
                }
            }

            $files[$path] = Page::fromTemplates($templates);
        }

        // Merge data nodes into related nodes
        // Example: image.png.yml into image.png
        foreach ($files as $path => $file) {
            if (!($file instanceof Types\Data)) {
                continue;
            }
            if ($file->getName() === "index") {
                $file->mergeInto($files[dirname($path)]);
                unset($files[$path]);
                continue;
            }
            foreach ($files as $innerPath => $innerFile) {
                if ($innerFile->getId() !== $file->getId()
                    && $innerFile->getName() === $file->getName()
                    && $innerFile->getParentId() === $file->getParentId()) {
                    $file->mergeInto($innerFile);
                    unset($files[$path]);
                    break;
                }
            }
        }

        // Replace all folders which have an index page with the actual page.
        foreach ($files as $path => $file) {
            if (!($file instanceof Types\Directory)) {
                continue;
            }
            foreach ($files as $innerPath => $innerFile) {
                if ($innerFile instanceof Types\Page
                    && $innerFile->getName() === "index"
                    && $innerFile->getParentId() === $file->getId()) {
                    $innerFile->rebase($file);
                    $files[$path] = $innerFile;
                    unset($files[$innerPath]);
                    break;
                }
            }
        }

        foreach ($files as $node) {
            $nodes[$node->getId()] = $node;
        }

        // Set path for each node
        foreach ($files as $node) {
            $pointer = $node;
            $segments = [$pointer->getName()];
            while (!empty($pointer) && $pointer->hasParentId()) {
                $pointer = $nodes[$pointer->getParentId()];
                $segments[] = $pointer->getName();
            }

            array_pop($segments);
            $node->setPath("/" . implode("/", array_reverse($segments)));
        }

        return $nodes;
    }
}
