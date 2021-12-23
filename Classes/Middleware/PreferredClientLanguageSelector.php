<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Middleware;

use Site\SiteEndpoints\Service\RequestedLanguageToSiteLanguageResolverService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Automatically select a language based on the Accept-Language HTTP header
 */
class PreferredClientLanguageSelector implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $language = (new RequestedLanguageToSiteLanguageResolverService())($request);
        $request = $request->withAttribute('language', $language);

        $GLOBALS['TYPO3_REQUEST'] = $request;

        return $handler->handle($request);
    }
}
