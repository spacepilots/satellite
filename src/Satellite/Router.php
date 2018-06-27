<?php

namespace Satellite;

use \Slim\Http\Request;

class Router
{
    protected $routes = [];

    public function __construct() {}

    public function getRoutes()
    {
        return $this->routes;
    }

    public function refresh(Nodes $nodes)
    {
        foreach ($nodes->getNodes() as $node) {
            if (!$node->isRoutable()) {
                continue;
            }
            $this->routes[$this->getPath($nodes, $node)] = $node->getId();
        }
    }

    public function match(Request $request)
    {
        $site = $this->findSite($request);
        $path = "/$site" . $request->getUri()->getPath();

        if (array_key_exists($path, $this->routes)) {
            return [$site, $this->routes[$path]];
        }
        if (array_key_exists($path . 'index', $this->routes)) {
            return [$site, $this->routes[$path . 'index']];
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

    protected function getPath(Nodes $nodes, Node $node)
    {
        $path = [];
        $pointer = $node;

        do {
            $segments = explode(".", basename($pointer->getPath()));
            $path[] = reset($segments);
            $pointer = $nodes->getParent($pointer);
        } while ($pointer !== null);

        return "/" . implode("/", array_reverse($path));
    }
}
