<?php

use Devsk\ContentElementRegistry\Core\ContentElementRegistry;
use Devsk\ContentElementRegistry\Utility\ContentElementRegistryUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

call_user_func(
    function ($extKey) {
        $contentElementsRegistry = ContentElementRegistry::getInstance();

        foreach ($contentElementsRegistry->getContentElements() as $contentElement) {
            //Register CE rendering definition
            ExtensionManagementUtility::addTypoScript(
                $extKey,
                'setup',
                ContentElementRegistryUtility::convertArrayToTypoScript($contentElement->getTypoScriptConfiguration())
            );
        }
    },
    'content_element_registry'
);
