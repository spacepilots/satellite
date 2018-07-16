<?php

namespace Satellite;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Satellite\Controllers\Satellite as SatelliteController;
use Satellite\Nodes\Repository as NodeRepository;
use Satellite\Routes\Router;

class App
{
    /**
     * @var string
     */
    protected $rootPath;

    /**
     * @var string
     */
    protected $systemPath;

    /**
     * @var \Satellite\App
     */
    protected $app;

    /**
     * @param string $rootPath
     */
    public static function initialize(string $rootPath)
    {
        Config::load(realpath($rootPath . "/config.php"), true);
        return new self($rootPath);
    }

    /**
     * @param string $rootPath
     */
    public function __construct(string $rootPath)
    {
        $this->rootPath = $rootPath;
        $this->systemPath = realpath(__DIR__ . '/../../');

        $satellite = $this;

        $this->app = new \Slim\App(Config::get('system'));

        $container = $this->app->getContainer();
        $container['satellite.nodes'] = new NodeRepository();
        $container['satellite.nodes']->scan($this->rootPath . '/pages');

        $container['satellite.router'] = new Router();
        $container['satellite.router']->compile($container['satellite.nodes'], 'de');

        $container['view'] = function ($c) use ($satellite) {
            $view = new \Satellite\Twig\View([
                '__main__' => $satellite->rootPath,
                'base' => $satellite->systemPath . '/base',
            ], [
                'debug' => Config::env() === 'development',
                'cache' => Config::get(['cache', 'path']),
            ]);

            $view->addExtension(new \Satellite\Twig\RouterExtensions($c['satellite.router']));

            return $view;
        };

        // $container['notFoundHandler'] = function ($c) {
        //     return function ($request, $response) use ($c) {
        //         return $c['view']->render($response->withStatus(404), 'pages/404.html.twig');
        //     };
        // };

        if (Config::env() === "development") {
            $this->app->any('/satellite', SatelliteController::class . ':displayDashboard');
            $this->app->any('/satellite/routes', SatelliteController::class . ':displayRoutes');
            $this->app->any('/satellite/nodes', SatelliteController::class . ':displayNodes');
            $this->app->any('/satellite/nodes/{id}', SatelliteController::class . ':displayNode');
        }

        $this->app->get('/[{path:.*}]', function (Request $request, Response $response, $args) use ($satellite) {
            $nodes = $this->get("satellite.nodes");
            $router = $this->get("satellite.router");

            $route = $router->match($request);

            var_dump($route);
            exit;

            if (!$route) {
                throw new \Slim\Exception\NotFoundException($request, $response);
            }

            // $node = $nodes[$route->getNodeId()];
            // $node = $node->getVariant("de");

            // $path = str_replace($satellite->rootPath, '', $node->getRealPath());

            // return $this->view->render($response, $path, []);
        });
    }

    public function run()
    {
        $this->app->run();
    }
}
