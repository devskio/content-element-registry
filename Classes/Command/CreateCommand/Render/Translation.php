<?php
namespace Digitalwerk\ContentElementRegistry\Command\CreateCommand\Render;

use Digitalwerk\ContentElementRegistry\Command\CreateCommand\Render;
use DOMDocument;
use SimpleXMLElement;

/**
 * Class Translation
 * @package Digitalwerk\ContentElementRegistry\Command\CreateCommand\Render
 */
class Translation
{
    /**
     * @var Render
     */
    protected $render = null;

    public function __construct(Render $render)
    {
        $this->render = $render;
    }

    /**
     * @param $file
     * @param $translationId
     * @param $translationValue
     */
    public function addStringToTranslation($file, $translationId, $translationValue)
    {
        $xml = simplexml_load_file($file);
        $body = $xml->file->body;

        $transUnit = $body->addChild('trans-unit');
        $transUnit->addAttribute('id',$translationId);
        $transUnit->addChild('source', ''.str_replace('-',' ',$translationValue).'');

        $dom = new DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        $formatXml = new SimpleXMLElement($dom->saveXML());
        $formatXml->saveXML($file);
    }

    /**
     * @param $file
     */
    public function addFieldsTitleToTranslation($file)
    {
        $fields = $this->render->getFields();

        if ($fields) {
            $extensionName = str_replace('_', '', $this->render->getExtensionName());
            $name = $this->render->getStaticName();
            $secondDesignation = $this->render->getName();
            $table = $this->render->getTable();
            $xml = simplexml_load_file($file);
            $body = $xml->file->body;

            foreach ($fields->getFields() as $field) {
                $fieldName = $field->getName();
                $fieldTitle = $field->getTitle();

                if ($fieldTitle !== $field->getDefaultTitle())
                {
                    $transUnitField = $body->addChild('trans-unit');
                    $transUnitField->addAttribute('id',$table.'.' . strtolower($extensionName) . '_'. strtolower($name).'.'. strtolower($secondDesignation).'_'. strtolower($fieldName).'');
                    $transUnitField->addChild('source', ''.str_replace('-',' ',$fieldTitle).'');
                }
            }

            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($xml->asXML());
            $formatXml = new SimpleXMLElement($dom->saveXML());
            $formatXml->saveXML($file);
        }
    }
}