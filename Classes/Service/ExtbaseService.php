<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Service;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\Error\Http\PageNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Dispatcher;
use TYPO3\CMS\Extbase\Mvc\Exception;
use TYPO3\CMS\Extbase\Mvc\Exception\InfiniteLoopException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidActionNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidControllerNameException;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidExtensionNameException;
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

    /**
     * @param array<string>     $controllerAliasToClassNameMapping
     * @param null|array<mixed> $arguments
     *
     * @throws Exception
     * @throws RuntimeException
     * @throws InvalidArgumentException
     * @throws InvalidControllerNameException
     * @throws PageNotFoundException
     * @throws InvalidActionNameException
     * @throws InvalidExtensionNameException
     * @throws InvalidArgumentNameException
     * @throws InfiniteLoopException
     */
    public function performRequest(
        ServerRequestInterface $request,
        string $extensionName,
        string $pluginName,
        array $controllerAliasToClassNameMapping,
        string $controllerName,
        string $controllerActionName,
        ?array $arguments = []
    ): ResponseInterface {
        $extbaseRequest = $this->extbaseRequestBuilder->build($request);

        /** @var ExtbaseRequestParameters */
        $extbaseAttribute = &$extbaseRequest->getAttribute('extbase');
        $extbaseAttribute->setControllerExtensionName($extensionName);
        $extbaseAttribute->setPluginName($pluginName);
        $extbaseAttribute->setControllerAliasToClassNameMapping($controllerAliasToClassNameMapping);
        $extbaseAttribute->setControllerName($controllerName);
        $extbaseAttribute->setControllerActionName($controllerActionName);

        if (!empty($arguments)) {
            $extbaseAttribute->setArguments($arguments);
        }

        $response = $this->extbaseDispatcher->dispatch($extbaseRequest);

        return $response;
    }
}
