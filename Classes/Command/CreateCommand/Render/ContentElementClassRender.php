<?php
namespace Digitalwerk\ContentElementRegistry\Command\CreateCommand\Render;

use Digitalwerk\ContentElementRegistry\Command\CreateCommand\Object\Fields\FieldObject;
use Digitalwerk\ContentElementRegistry\Command\CreateCommand\RenderCreateCommand;
use InvalidArgumentException;

/**
 * Class ContentElementClass
 * @package Digitalwerk\ContentElementRegistry\Command\CreateCommand\Render
 */
class ContentElementClassRender
{
    /**
     * @var RenderCreateCommand
     */
    protected $render = null;

    /**
     * ContentElementClass constructor.
     * @param RenderCreateCommand $render
     */
    public function __construct(RenderCreateCommand $render)
    {
        $this->render = $render;
    }

    /**
     * @return string
     */
    public function fieldsToMapping()
    {
        $name = $this->render->getName();
        $extraSpaces = '        ';
        $createdFields = [];

        foreach ($this->render->getFields()->getFields() as $field) {
            if ($field->getName() === $field->getType() && $field->isDefault()) {
                //Default fields (no action)
            } elseif ($field->getName() !== $field->getType() && $field->isDefault()) {
                $createdFields[] = '"' . $field->getType() . '" => "' . str_replace(' ','',lcfirst(ucwords(str_replace('_',' ', $field->getName())))) . '"';
            } elseif ($field->exist()) {
                $createdFields[] = '"' .  strtolower($name) . '_' . $field->getName() . '" => "' . str_replace(' ','',lcfirst(ucwords(str_replace('_',' ', $field->getName())))) . '"';
            } else {
                throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.6');
            }
        }

        return implode(",\n" . $extraSpaces, $createdFields);
    }

    /**
     * @return string|null
     */
    public function getFieldsToPalette()
    {
        if ($this->render->getFields()) {
            $name = $this->render->getName();
            $extraSpace = '            ';
            $createdFields = [];

            foreach ($this->render->getFields()->getFields() as $field) {
                if ($field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . $field->getType();
                } elseif (!$field->isDefault()) {
                    $createdFields[] = '--linebreak--, ' . strtolower($name) . '_' . $field->getName();
                } else {
//                    Fieldtype does not exist
                    throw new InvalidArgumentException('Field "' . $field->getType() . '" does not exist.1');
                }
            }
            return preg_replace('/--linebreak--, /', '', implode(",\n" . $extraSpace, $createdFields),1);
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function fieldsToColumnsOverrides()
    {
        $table = $this->render->getTable();
        $contentElementName = $this->render->getName();
        $secondDesignation = $contentElementName;
        $defaultFieldsWithAnotherTitle = [];
        $extensionName = $this->render->getExtensionName();

        /** @var FieldObject $field */
        foreach ($this->render->getFields()->getFields() as $field) {
            $fieldName = $field->getName();
            $fieldType = $field->getType();
            $fieldTitle = $field->getTitle();
            $pathToModel = '\\' . $this->render->getModelNamespace() . '\\' . $this->render->getName();
            if ($fieldTitle !== $field->getDefaultTitle() && $field->isDefault())
            {
                if ($field->isInlineItemsAllowed()) {
                    $fieldItemName = $field->getFirstItem()->getName();

                    $defaultFieldsWithAnotherTitle[] =
            '\''.$fieldType.'\' => [
                \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($contentElementName).'.'. strtolower($secondDesignation).'_'. strtolower($fieldName).'\',
                \'config\' => [
                    \'foreign_match_fields\' => [
                        \'type\' => ' . $pathToModel . '::CONTENT_RELATION_' . strtoupper($fieldItemName) . ',
                    ],
                    \'overrideChildTca\' => [
                        \'columns\' => [
                            \'type\' => [
                                \'config\' => [
                                    \'items\' => [
                                        [\'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($contentElementName).'_'.strtolower($fieldItemName).'\', ' . $pathToModel . '::CONTENT_RELATION_'.strtoupper($fieldItemName).'],
                                    ],
                                    \'default\' => ' . $pathToModel . '::CONTENT_RELATION_' . strtoupper($fieldItemName) . '
                                ],
                            ],
                        ],
                    ],
                ],
            ],';
                    $this->render->translation()->addStringToTranslation(
                        'public/typo3conf/ext/' . $extensionName . '/Resources/Private/Language/locallang_db.xlf',
                        $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($contentElementName).'_'.strtolower($fieldItemName),
                        str_replace('-', ' ', $field->getFirstItem()->getTitle())
                    );
                } else {
                    $defaultFieldsWithAnotherTitle[] =
            '\''.$fieldType.'\' => [
                \'label\' => \'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang_db.xlf:' . $table . '.' . str_replace('_', '', $extensionName) . '_'.strtolower($contentElementName).'.'. strtolower($secondDesignation).'_'. strtolower($fieldName).'\',
            ],';
                }

            }
        }

        return implode("\n" . '            ', $defaultFieldsWithAnotherTitle);
    }

    /**
     * @return string|null
     */
    public function getColumnMapping()
    {
        if ($this->render->getFields()) {
            return
'    /**
     * @var array
     */
    protected $columnsMapping = [
        ' . $this->fieldsToMapping() . '
    ];';
        } else {
            return null;
        }
    }

    /**
     * @return string|null
     */
    public function getColumnOverride()
    {
        if ($this->render->getFields()) {
            return
'    /**
     * @return array
     */
    public function getColumnsOverrides()
    {
        return [
            ' . $this->fieldsToColumnsOverrides() . '
        ];
    }';
        } else {
            return null;
        }
    }

    public function template()
    {
        $extensionName = str_replace(' ','',ucwords(str_replace('_',' ', $this->render->getExtensionName())));
        $template[] = '<?php';
        $template[] = 'declare(strict_types=1);';
        $template[] = 'namespace Digitalwerk\\' . $extensionName . '\ContentElement;';
        $template[] = '';
        $template[] = 'use Digitalwerk\ContentElementRegistry\ContentElement\AbstractContentElementRegistryItem;';
        $template[] = '';
        $template[] = '/**';
        $template[] = ' * Class ' . $this->render->getName();
        $template[] = ' * @package Digitalwerk\\' . $extensionName . '\ContentElement';
        $template[] = ' */';
        $template[] = 'class ' . $this->render->getName() . ' extends AbstractContentElementRegistryItem';
        $template[] = '{';

        $columnMapping = $this->getColumnMapping();
        if ($columnMapping) {
            $template[] = $columnMapping;
            $template[] = '';
        }

        $template[] = '    /**';
        $template[] = '     * ' . $this->render->getName() . ' constructor.';
        $template[] = '     * @throws \Exception';
        $template[] = '     */';
        $template[] = '    public function __construct()';
        $template[] = '    {';
        $template[] = '        parent::__construct();';

        $fieldsToPalette = $this->getFieldsToPalette();
        if ($fieldsToPalette) {
            $template[] = '        $this->addPalette(';
            $template[] = '            \'default\',';
            $template[] = "            '" . $fieldsToPalette . "'";
            $template[] = '        );';
        }
        $template[] = '    }';

        $columnOverride = $this->getColumnOverride();
        if ($columnOverride) {
            $template[] = '';
            $template[] = $columnOverride;
        }
        $template[] = '}';


        file_put_contents(
            'public/typo3conf/ext/' . $this->render->getExtensionName() . '/Classes/ContentElement/' . $this->render->getName() . '.php',
            implode("\n", $template)
        );
    }
}