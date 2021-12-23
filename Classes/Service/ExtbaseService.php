<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Service;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Web\RequestBuilder;

class ExtbaseService // implements SingletonInterface
{
    protected RequestBuilder $extbaseRequestBuilder;
    protected Dispatcher $extbaseDispatcher;

    public function __construct()
    {
        $this->extbaseRequestBuilder = GeneralUtility::makeInstance(RequestBuilder::class);
        $this->extbaseDispatcher = GeneralUtility::makeInstance(Dispatcher::class);
    }

    public function performRequest(
        ServerRequestInterface $request,
        string $extensionName,
        string $pluginName,
        array $controllerAliasToClassNameMapping,
        string $controllerName,
        string $controllerActionName,
        array $arguments = []
    ): ResponseInterface
    {
        $extbaseRequest = $this->extbaseRequestBuilder->build($request);

        /** @var ExtbaseRequestParameters */
        $extbaseAttribute = &$extbaseRequest->getAttribute('extbase');
        $extbaseAttribute->setControllerExtensionName($extensionName);
        $extbaseAttribute->setPluginName($pluginName);
        $extbaseAttribute->setControllerAliasToClassNameMapping($controllerAliasToClassNameMapping);
        $extbaseAttribute->setControllerName($controllerName);
        $extbaseAttribute->setControllerActionName($controllerActionName);

        !empty($arguments) && $extbaseAttribute->setArguments($arguments);

        $response = $this->extbaseDispatcher->dispatch($extbaseRequest);

        return $response;
    }
}
