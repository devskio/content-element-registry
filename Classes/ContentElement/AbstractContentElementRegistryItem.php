<?php
namespace Devsk\ContentElementRegistry\ContentElement;

use Devsk\ContentElementRegistry\Core\ContentElementRegistry;
use Devsk\ContentElementRegistry\DataProcessing\ContentElementObjectDataProcessor;
use Devsk\ContentElementRegistry\DataProcessing\HeadlessDataProcessor;
use Devsk\ContentElementRegistry\Utility\ContentElementRegistryUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class AbstractContentElementRegistryItem
 * @package Devsk\ContentElementRegistry\ContentElement
 */
abstract class AbstractContentElementRegistryItem
{
    /**
     * Palettes
     *
     * @var array
     */
    private $palettes = [];

    /**
     * Table columns mappings to Model properties
     *
     * @var array
     */
    protected $columnsMapping = [];

    /**
     * Get CE wizard tab name [common, menu, special, forms, plugins]
     * Specify in which wizard tab should be element placed
     *
     * @var string
     */
    protected $wizardTabName = 'common';

    /**
     * Should be element hidden in wizard
     *
     * @var bool
     */
    protected $hiddenInWizard = false;

    /**
     * @var bool
     */
    protected $isHeadless = false;

    /**
     * AbstractContentElementRegistryItem constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get CE name
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    /**
     * Get CE template name
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getTemplateName(): string
    {
        return $this->getName();
    }

    /**
     * Get CE identifier
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getIdentifier(): string
    {
        return \strtolower(\sprintf("%s_%s", $this->getExtensionName(), $this->getName()));
    }

    /**
     * get CE CType
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getCType(): string
    {
        return $this->getIdentifier();
    }

    /**
     * Get extension key
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getExtensionKey(): string
    {
        return GeneralUtility::camelCaseToLowerCaseUnderscored($this->getExtensionName());
    }

    /**
     * Get extension name
     *
     * @return string
     */
    public function getExtensionName(): string
    {
        return ContentElementRegistryUtility::getNamespaceConfiguration(static::class, 'extensionName');
    }

    /**
     * Get CE icon identifier
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getIconIdentifier(): string
    {
        return $this->getIdentifier();
    }

    /**
     * Get path to icons
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getIconsPath(): string
    {
        return "EXT:{$this->getExtensionKey()}/Resources/Public/Icons/ContentElement/";
    }

    /**
     * Get CE icon path
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getIconSrcPath(): string
    {
        $iconSource = "{$this->getIconsPath()}{$this->getIconIdentifier()}.svg";
        if (!file_exists(GeneralUtility::getFileAbsFileName($iconSource))) {
            $iconSource = "EXT:".ContentElementRegistry::EXTENSION_KEY."/Resources/Public/Icons/CEDefaultIcon.svg";
        }

        return $iconSource;
    }

    /**
     * Get CE LLL title
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getTitle(): string
    {
        return "LLL:EXT:{$this->getExtensionKey()}/Resources/Private/Language/locallang_db.xlf:tt_content.{$this->getIdentifier()}.title";
    }

    /**
     * Get CE LLL description
     *
     * @return string
     * @throws \ReflectionException
     */
    public function getDescription(): string
    {
        return "LLL:EXT:{$this->getExtensionKey()}/Resources/Private/Language/locallang_db.xlf:tt_content.{$this->getIdentifier()}.description";
    }

    /**
     * Get CE wizard tab name
     *
     * @return string
     */
    protected function getWizardTabHeader(): string
    {
        return "LLL:EXT:backend/Resources/Private/Language/locallang_db_new_content_el.xlf:{$this->wizardTabName}";
    }

    /**
     * Get CE group
     *
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->wizardTabName;
    }

    /**
     * Get CE group label
     *
     * @return string
     */
    public function getGroupLabel(): string
    {
        return $this->getWizardTabHeader();
    }

    /**
     * Get CE PageTSconfig
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getWizardPageTSconfig(): array
    {
        $config = [];

        if (false === $this->hiddenInWizard) {
            $config[$this->wizardTabName] = [
                'elements' => [
                    $this->getCType() => [
                        'iconIdentifier' => $this->getIconIdentifier(),
                        'title' => $this->getTitle(),
                        'description' => $this->getDescription(),
                        'tt_content_defValues' => [
                            'CType' => $this->getCType(),
                        ],
                    ],
                ],
                'show' => ":= addToList({$this->getCType()})",
                'header' => $this->getWizardTabHeader(),
            ];
        }

        return $config;
    }

    /**
     * Get CE TCA showitem config
     *
     * @return string
     */
    public function getTCAShowItemConfig(): string
    {
        return "--div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.general;general,
                    --palette--;;headers,
                    {$this->getPalettesShowItemString()}
                    {$this->getAdditionalTCAConfig()}
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.frames;frames,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.appearanceLinks;appearanceLinks,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    --palette--;;hidden,
                    --palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access;access,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                    categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                    rowDescription,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended";
    }


    /**
     * Get CE tt_content typoscript config
     *
     * @return array
     * @throws \ReflectionException
     */
    public function getTypoScriptConfiguration(): array
    {
        if ($this->isHeadless()) {
            return [
                'tt_content' => [
                    $this->getCType() => '< lib.contentElement',
                    $this->getCType().'.' => [
                        'fields' => [
                            'content' => [
                                'dataProcessing.' => [
                                    '0' => HeadlessDataProcessor::class,
                                    '0.as' => 'fields',
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return [
            'tt_content' => [
                $this->getCType() => '< lib.contentElement',
                $this->getCType().'.' => [
                    'templateName' => $this->getTemplateName(),
                    'dataProcessing.' => [
                        '0' => ContentElementObjectDataProcessor::class
                    ],
                ]
            ],
        ];
    }

    /**
     * Return related Domain Object class name
     *
     * @return bool|string
     * @throws \ReflectionException
     */
    public function getDomainModelClassName()
    {
        $modelNamespace = [
            ContentElementRegistryUtility::getNamespaceConfiguration(static::class, 'vendorName'),
            ContentElementRegistryUtility::getNamespaceConfiguration(static::class, 'extensionName'),
            'Domain',
            'Model',
            ContentElementRegistryUtility::getNamespaceConfiguration(static::class, 'modelName'),
            $this->getName(),
        ];
        $class = \implode('\\', $modelNamespace);

        return \class_exists($class) ? $class : false;
    }

    /**
     * Additional "showitem" configuration
     *
     * @return string
     */
    protected function getAdditionalTCAConfig(): string
    {
        return '';
    }

    /**
     * Additional columns overrides
     *
     * @return array
     */
    public function getColumnsOverrides(): array
    {
        return [];
    }

    /**
     * Add pallete to CE
     *
     * @param string $name Palette name
     * @param string $showItem Pallete showitem string
     * @throws \Exception
     */
    protected function addPalette(string $name, string $showItem)
    {
        $paletteIdentifier = \sprintf("%s_%s", $this->getIdentifier(), $name);
        if (\array_key_exists($paletteIdentifier, $this->palettes)) {
            throw new \Exception("Palette with name {$paletteIdentifier} already exists", 1540890148);
        }

        $this->palettes[$paletteIdentifier] = [
            'label' => "LLL:EXT:{$this->getExtensionKey()}/Resources/Private/Language/locallang_db.xlf:tt_content.{$this->getIdentifier()}.palette.{$name}",
            'showitem' => $showItem,
        ];
    }

    /**
     * Get palettes showitem string
     *
     * @return string
     */
    private function getPalettesShowItemString(): string
    {
        $palettesString = '';
        foreach ($this->palettes as $paletteName => $paletteConfig) {
            $palettesString .= "--palette--;{$paletteConfig['label']};{$paletteName},";
        }

        return $palettesString;
    }

    /**
     * Get Ce Palettes
     *
     * @return array
     */
    public function getPalettes(): array
    {
        return $this->palettes;
    }

    /**
     * @return bool
     * @throws \ReflectionException
     */
    public function flexFormDefinitionExists(): bool
    {
        return \file_exists(
            GeneralUtility::getFileAbsFileName(
                substr($this->getFlexFormFormDefinition(), \strlen('FILE:'))
            )
        );
    }

    /**
     * @return string
     * @throws \ReflectionException
     */
    public function getFlexFormFormDefinition(): string
    {
        return "FILE:EXT:{$this->getExtensionKey()}/Configuration/FlexForms/ContentElement/{$this->getIdentifier()}.xml";
    }

    /**
     * @return bool
     */
    public function isHeadless(): bool
    {
        return $this->isHeadless;
    }

    /**
     * @param bool $isHeadless
     */
    public function setIsHeadless(bool $isHeadless)
    {
        $this->isHeadless = $isHeadless;
    }

    public function jsonSerialize(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getColumnsMapping(): array
    {
        return $this->columnsMapping;
    }
}
