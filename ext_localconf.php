<?php

(function () {
    !isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints']) && $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_endpoints'] = [
        'routes' => [],
    ];
})();
