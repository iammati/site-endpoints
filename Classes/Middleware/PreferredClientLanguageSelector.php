<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Site\SiteEndpoints\Service\RequestedLanguageToSiteLanguageResolverService;

/**
 * Automatically select a language based on the Accept-Language HTTP header.
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
