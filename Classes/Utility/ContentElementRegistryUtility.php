<?php
namespace Devsk\ContentElementRegistry\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ContentElementRegistryUtility
 * @package Devsk\ContentElementRegistry\Utility
 */
class ContentElementRegistryUtility
{

    /**
     * Converts given array to TypoScript
     *
     * @param array $typoScriptArray The array to convert to string
     * @param string $addKey Prefix given values with given key (eg. lib.whatever = {...})
     * @param integer $tab Internal
     * @param boolean $init Internal
     * @return string TypoScript
     */
    public static function convertArrayToTypoScript(
        array $typoScriptArray,
        $addKey = '',
        $tab = 0,
        $init = true
    ): string {
        $typoScript = '';
        if ($addKey !== '') {
            $typoScript .= str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . $addKey . " {\n";
            if ($init === true) {
                $tab++;
            }
        }
        $tab++;
        foreach ($typoScriptArray as $key => $value) {
            if (!is_array($value)) {
                //                TODO: replace with str_starts_with() in PHP 8
                if (str_starts_with($value, ":=") === true) {
                    $typoScript .= str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . "$key $value\n";
                } elseif (strpos($value, "\n") === false) {
                    $typoScript .= str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . "$key = $value\n";
                } else {
                    $typoScript .= str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . "$key (\n$value\n" .
                        str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . ")\n";
                }
            } else {
                $typoScript .= self::convertArrayToTypoScript($value, $key, $tab, false);
            }
        }
        if ($addKey !== '') {
            $tab--;
            $typoScript .= str_repeat("\t", ($tab === 0) ? $tab : $tab - 1) . '}';
            if ($init !== true) {
                $typoScript .= "\n";
            }
        }
        return $typoScript;
    }

    /**
     * Gets namespace information
     *
     * @param string $class
     * @param null $key
     * @return array|string
     */
    public static function getNamespaceConfiguration(string $class, $key = null)
    {
        list($vendorName, $extensionName, $modelName) = GeneralUtility::trimExplode('\\', $class);
        $data = [
            'vendorName'    => $vendorName,
            'extensionName' => $extensionName,
            'modelName'     => $modelName,
        ];

        if (null !== $key and \array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $data;
    }

    /**
     * Convert camelCaseString to camel-case-dashed-string
     *
     * @param string $string
     * @return string
     */
    public static function camelCase2Dashed(string $string): string
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $string));
    }

    /**
     * SVG icon registration helper
     *
     * @param array $icons
     * @param string $extKey
     */
    public static function registerIcons(array $icons, string $extKey): void
    {
        $iconRegistry = GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        foreach ($icons as $icon) {
            $iconName = stripos($icon, '/') === false ? $icon : end(explode('/', $icon));
            $iconRegistry->registerIcon(
                $iconName,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => "EXT:{$extKey}/Resources/Public/Icons/{$icon}.svg"]
            );
        }
    }
}
