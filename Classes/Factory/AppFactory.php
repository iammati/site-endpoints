<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Factory;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Site\SiteEndpoints\Service\EndpointsService;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;

class AppFactory
{
    private string $extensionName;
    private string $pluginName;
    protected array $route;
    protected ServerRequestInterface $request;
    protected RequestHandlerInterface $handler;
    protected RequestBuilder $extbaseRequestBuilder;
    protected Dispatcher $extbaseDispatcher;
    protected EndpointsService $endpointsService;

    public function create(ServerRequestInterface $request, RequestHandlerInterface $handler, array $config): self|ResponseInterface
    {
        $this->endpointsService = GeneralUtility::makeInstance(
            EndpointsService::class,
            $request
        );

        $path = $request->getUri()->getPath();

        $prefix = $config['prefix'];
        $routePath = $config['routePath'];
        $middlewares = $config['middlewares'];
        $routes = $config['routes'];
        $this->extensionName = $config['extensionName'];
        $this->pluginName = $config['pluginName'];

        // Last compare to make sure requested URI maps the configured route
        $slug = str_replace($prefix.$routePath, '', $path);
        $route = null;

        foreach ($routes as $item) {
            $routePath = $item['routePath'];

            if (!str_ends_with($routePath, '/') && str_ends_with($slug, '/')) {
                $routePath .= '/';
            }

            if ($routePath === $slug || $routePath === '/*') {
                $route = $item;
                break;
            }
        }

        if ($route === null) {
            return $handler->handle($request);
        }

        // Allowing only configured methods inside the $route to continue
        $methods = $route['methods'];

        if (!in_array($request->getMethod(), $methods)) {
            return $handler->handle($request);
        }

        $this->request = $request;
        $this->handler = $handler;
        $this->route = $route;

        $this->iterateMiddlewares($middlewares);

        return $this;
    }

    public function handle(): ResponseInterface
    {
        $callback = $this->route['callback'];
        $cbData = $this->endpointsService->resolveCallback($callback);

        $controllerName = $cbData['controllerName'];
        $actionName = $cbData['actionName'];
        $cnuControllerName = ClassNamingUtility::explodeObjectControllerName($controllerName)['controllerName'];

        $this->extbaseRequestBuilder = GeneralUtility::makeInstance(RequestBuilder::class);
        $this->extbaseDispatcher = GeneralUtility::makeInstance(Dispatcher::class);

        $extbaseRequest = $this->extbaseRequestBuilder->build($this->request);

        /** @var ExtbaseRequestParameters */
        $extbaseAttribute = $extbaseRequest->getAttribute('extbase');
        $extbaseAttribute->setControllerExtensionName($this->extensionName);
        $extbaseAttribute->setPluginName($this->pluginName);
        $extbaseAttribute->setControllerAliasToClassNameMapping([
            $cnuControllerName => $controllerName,
        ]);
        $extbaseAttribute->setControllerName($cnuControllerName);
        $extbaseAttribute->setControllerActionName($actionName);

        $response = $this->extbaseDispatcher->dispatch($extbaseRequest);

        return $response;
    }

    protected function iterateMiddlewares(?array $middlewares = []): void
    {
        foreach ($middlewares as $middleware) {
            /** @var MiddlewareInterface $instance */
            $instance = GeneralUtility::makeInstance($middleware);
            $instance->process($this->request, $this->handler);
        }
    }
}
