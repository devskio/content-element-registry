<?php

declare(strict_types=1);

namespace Devsk\ContentElementRegistry\EventListener;

use TYPO3\CMS\Core\Attribute\AsEventListener;
use TYPO3\CMS\Core\Context\LanguageAspect;
use TYPO3\CMS\Core\Domain\Event\BeforeRecordLanguageOverlayEvent;

/**
 * Relation records are normally stored once in the default language and shared by all translations of the
 * content element (free mode copies as well as connected translations). With a "strict" site language
 * (overlay type "on") untranslated relation rows would be dropped by the language overlay, so the
 * slides of a translated content element disappear. Use "mixed" overlays for this table: a translation
 * is used if it exists, the default language row otherwise.
 */
#[AsEventListener(identifier: 'content-element-registry/keep-untranslated-relations')]
final class KeepUntranslatedRelationsListener
{
    private const TABLE = 'tx_contentelementregistry_domain_model_relation';

    public function __invoke(BeforeRecordLanguageOverlayEvent $event): void
    {
        if ($event->getTable() !== self::TABLE) {
            return;
        }
        $aspect = $event->getLanguageAspect();
        if ($aspect->getOverlayType() === LanguageAspect::OVERLAYS_MIXED || $aspect->getContentId() === 0) {
            return;
        }
        $event->setLanguageAspect(new LanguageAspect(
            $aspect->getId(),
            $aspect->getContentId(),
            LanguageAspect::OVERLAYS_MIXED,
            $aspect->getFallbackChain()
        ));
    }
}
