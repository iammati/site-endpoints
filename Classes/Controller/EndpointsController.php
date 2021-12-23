<?php

declare(strict_types=1);

namespace Site\SiteEndpoints\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class EndpointsController extends ActionController
{
    public function testAction(): ResponseInterface
    {
        $this->view->assign('a', 'b');

        DebuggerUtility::var_dump('testAction inside EndpointsController has been called!');

        return new HtmlResponse(
            $this->view->render()
        );
    }
}
