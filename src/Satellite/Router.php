<?php

namespace Satellite;

use Satellite\Nodes\Node;
use Satellite\Nodes\Repository;
use Slim\Http\Request;

class Router
{
    protected $routes = [];

    public function __construct() {}

    public function getRoutes()
    {
        return $this->routes;
    }

    public function refresh(Repository $nodes)
    {
        foreach ($nodes->getNodes() as $node) {
            if (!$node->getFlag("routable")) {
                continue;
            }
            $this->routes[$this->getPath($nodes, $node)] = $node->getId();
        }
    }

    public function match(Request $request)
    {
        $site = $this->findSite($request);
        $path = "/$site" . ltrim($request->getUri()->getPath(), "/");

        if (array_key_exists($path, $this->routes)) {
            return [$site, $this->routes[$path]];
        }

        return null;
    }

    protected function findSite(Request $request)
    {
        // Find the matching site
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

        return $site;
    }

    protected function getPath(Repository $nodes, Node $node)
    {
        $path = [];
        $pointer = $node;

        while ($pointer !== null) {
            $segment = $pointer->getName();
            // $data = $pointer->getData();

            // if (!empty($data) && isset($data['slug'])) {
            //     $segment = $data['slug'];
            // }

            $path[] = $segment;
            $pointer = $nodes->getParent($pointer);
        }

        return "/" . implode("/", array_reverse(array_filter($path)));
    }
}
