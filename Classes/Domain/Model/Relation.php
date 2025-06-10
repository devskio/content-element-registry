<?php

namespace Devsk\ContentElementRegistry\Domain\Model;

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Relation
 * @package Devsk\ContentElementRegistry\Domain\Model
 */
abstract class Relation extends AbstractEntity
{
    /**
     * @var string
     */
    protected string $type = '';

    /**
     * @var string
     */
    protected string $title = '';

    /**
     * @var string
     */
    protected string $description = '';

    /**
     * @var ObjectStorage<FileReference>|null
     */
    protected ?ObjectStorage $media = null;

    /**
     * @var ContentElement|null
     */
    protected ?ContentElement $contentElement = null;

    /**
     * Relation constructor.
     */
    public function __construct()
    {
        $this->media = new ObjectStorage();
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return ObjectStorage
     */
    public function getMedia(): ObjectStorage
    {
        return $this->media;
    }

    /**
     * @return ContentElement
     */
    public function getContentElement(): ContentElement
    {
        return $this->contentElement;
    }
}
