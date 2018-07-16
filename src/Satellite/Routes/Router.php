<?php

namespace Satellite\Routes;

use Cocur\Slugify\Slugify;
use Satellite\Config;
use Satellite\Nodes\Interfaces\Databag;
use Satellite\Nodes\Interfaces\Localizable;
use Satellite\Nodes\Repository;
use Satellite\Nodes\Types\AbstractNode;
use Slim\Http\Request;

class Router
{
    /**
     * @var string[]
     */
    static $routable = ['page', 'file'];

    /**
     * @var \Cocur\Slugify\Slugify
     */
    protected $slugify;

    /**
     * @var Route[]
     */
    protected $routes = [];

    /**
     */
    public function __construct()
    {
        // $this->slugify = new Slugify();
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param Request $request
     * @return Route|null
     */
    public function match(Request $request)
    {
        $sites = Config::get('sites', []);
        $site = null;

        if (count($sites) <= 1) {
            $site = 'default';
        } else {
            $host = $request->getUri()->getHost();
            foreach ($sites as $identifier => $siteConfig) {
                foreach ($siteConfig['domains'] as $domain) {
                    if (preg_match($domain, $host) !== 0) {
                        $site = $identifier;
                        break;
                    }
                }
            }
        }

        $path = "/" . trim($request->getUri()->getPath(), "/");
        foreach ($this->routes as $route) {
            if ($route->getPath() === $path && $route->getSite() === $site) {
                return $route;
            }
        }

        return null;
    }

    public function compile(Repository $nodes, $locale)
    {
        $routes = [];
        $slugs = [];

        foreach ($nodes->getNodes() as $node) {
            if (!in_array($node->getType(), static::$routable)) {
                continue;
            }
            $slugs[$node->getId()] = $this->getSlug($node, $locale);
        }

        foreach ($slugs as $id => $node) {
            $path = [];
            $node = $nodes[$id];
            $pointer = $nodes[$id];

            if ($slugs[$id] === false) {
                continue;
            }

            while ($pointer !== null) {
                if (array_key_exists($pointer->getId(), $slugs)) {
                    $path[] = $slugs[$pointer->getId()];
                }
                $pointer = $nodes->getParent($pointer);
            }

            $route = new Route();
            $route->setSite($node->getSiteId());
            $route->setNodeId($id);
            $route->setLocale($locale);
            $route->setPath("/" . implode("/", array_reverse(array_filter($path))));

            $routes[] = $route;
        }

        $this->routes = $this->routes + $routes;
    }

    protected function getSlug(AbstractNode $node, $locale)
    {
        if (!$node->hasParentId()) {
            return "";
        }

        if ($node instanceof Localizable && !empty($locale)) {
            $localeNode = $node->getLocale($locale);
            if (!empty($localeNode)) {
                return $this->getSlug($localeNode, null);
            }
        }

        if ($node instanceof Databag) {
            if ($node->hasData('slug')) {
                return $node->getData('slug');
            }
            if ($node->hasData('title')) {
                return strtolower($node->getData('title'));
            }
        }

        return $node->getName();
    }
}
