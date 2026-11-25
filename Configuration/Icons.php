<?php

declare(strict_types=1);

use Devsk\ContentElementRegistry\Core\ContentElementRegistry;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

$icons = [];
foreach (ContentElementRegistry::getInstance()->getContentElements() as $contentElement) {
    $icons[$contentElement->getIconIdentifier()] = [
        'provider' => SvgIconProvider::class,
        'source' => $contentElement->getIconSrcPath(),
    ];
}

return $icons;
