<?php

namespace Devsk\ContentElementRegistry\Listeners;

use Devsk\ContentElementRegistry\ContentElement\AbstractContentElementRegistryItem;
use Devsk\ContentElementRegistry\Core\ContentElementRegistry;
use Devsk\ContentElementRegistry\Core\ContentElementRegistryException;
use Devsk\ContentElementRegistry\Domain\Model\ContentElement;
use Devsk\ContentElementRegistry\Traits\Injection\InjectDataMapper;
use TYPO3\CMS\Backend\View\Event\PageContentPreviewRenderingEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Core\View\ViewInterface;

use function Symfony\Component\String\u;

/**
 * Class ContentElementPreviewRenderer
 * @package Devsk\ContentElementRegistry\Listeners
 */
class ContentElementPreviewRenderer
{
    use InjectDataMapper;

    /**
     * @param PageContentPreviewRenderingEvent $event
     * @return void
     * @throws ContentElementRegistryException
     * @throws \ReflectionException
     */
    public function __invoke(PageContentPreviewRenderingEvent $event): void
    {
        $row = $event->getRecord();
        $itemContent = $event->getPreviewContent();

        $contentElementRegistry = ContentElementRegistry::getInstance();
        if ($contentElementRegistry->existsContentElement($row['CType'])) {
            /** @var AbstractContentElementRegistryItem $contentElement */
            $contentElement = $contentElementRegistry->getContentElement($row['CType']);
            $view = $this->getView(
                extensionKey: $contentElement->getExtensionKey(),
                templateFile: "Templates/ContentElements/{$contentElement->getTemplateName()}.html",
                partialRootSubPath: "Partials",
                request: $event->getPageLayoutContext()->getCurrentRequest()
            );
            $contentElementObject = $this->dataMapper->map(
                ContentElement::class,
                [$row]
            )[0];

            /** Render preview */
            $preview = $view->renderSection(
                'Preview',
                [
                    'data' => $row,
                    'contentElement' => $contentElementObject,
                    'ce' => $contentElementObject,
                ],
                true
            );

            /** Check for using layout */
            $useLayout = file_exists(
                GeneralUtility::getFileAbsFileName(
                    "EXT:{$contentElement->getExtensionKey()}/Resources/Private/Layouts/ContentElements/Preview.html"
                )
            ) && !u($preview)->containsAny('<!--Without layout-->');

            if ($useLayout) {
                /** Create a Standalone view instance */
                $layout = $this->getView(
                    extensionKey: $contentElement->getExtensionKey(),
                    templateFile: 'Layouts/ContentElements/Preview.html',
                    request: $event->getPageLayoutContext()->getCurrentRequest()
                );
                /** Assign rendered preview to layout */
                $layout->assign('preview', $preview);
                /** Modify core preview */
                $itemContent .= $layout->render();
            } else {
                /** Modify core preview */
                $itemContent .= $preview;
            }
            $event->setPreviewContent($itemContent ?: '');
        }
    }

    /**
     * Helper to create a Fluid View using ViewFactory.
     */
    protected function getView(
        string $extensionKey,
        string $templateFile,
        string $templateRootSubPath = '',
        string $layoutRootSubPath = '',
        string $partialRootSubPath = '',
        $request = null
    ): ViewInterface {
        $basePath = "EXT:$extensionKey/Resources/Private/";

        $viewFactoryData = new ViewFactoryData(
            templateRootPaths: $templateRootSubPath ? [$basePath . rtrim($templateRootSubPath, '/')] : null,
            partialRootPaths: $partialRootSubPath ? [$basePath . rtrim($partialRootSubPath, '/')] : null,
            layoutRootPaths: $layoutRootSubPath ? [$basePath . rtrim($layoutRootSubPath, '/')] : null,
            templatePathAndFilename: GeneralUtility::getFileAbsFileName($basePath . $templateFile),
            request: $request ?? $GLOBALS['TYPO3_REQUEST']
        );

        /** @var ViewFactoryInterface $viewFactory */
        $viewFactory = GeneralUtility::makeInstance(ViewFactoryInterface::class);
        return $viewFactory->create($viewFactoryData);
    }
}
