<?php
namespace Devsk\ContentElementRegistry\Events;

use Devsk\ContentElementRegistry\Core\ContentElementRegistry;

/**
 * Class RegisterContentElementRegistryClass
 * @package Devsk\ContentElementRegistry\Event
 */
class ContentElementRegistryClassEvent
{
    /**
     * @var ContentElementRegistry
     */
    protected $contentElementRegistry = null;

    /**
     * RegisterContentElementRegistryClass constructor.
     * @param ContentElementRegistry $contentElementRegistry
     */
    public function __construct(ContentElementRegistry $contentElementRegistry)
    {
        $this->contentElementRegistry = $contentElementRegistry;
    }

    /**
     * @return ContentElementRegistry
     */
    public function getContentElementRegistry(): ?ContentElementRegistry
    {
        return $this->contentElementRegistry;
    }

    /**
     * @param ContentElementRegistry|null $contentElementRegistry
     */
    public function setContentElementRegistry(?ContentElementRegistry $contentElementRegistry): void
    {
        $this->contentElementRegistry = $contentElementRegistry;
    }


}
