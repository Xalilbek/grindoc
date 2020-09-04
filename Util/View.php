<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 31.05.2018
 * Time: 15:57
 */

namespace Util;

use TypeError;

class View
{
    private static $templatesPath;

    public static function altPrintArray($array, $callback, $altString = '-', $wrapper = '%s')
    {
        if (!is_callable($callback)) {
            throw new TypeError(
                sprintf('Parameter 2 must be callable, %s given', gettype($callback))
            );
        }

        if (count($array)) {
            $items = [];
            foreach ($array as $key => $value) {
                $items[] = $callback($value, $key);
            }

            printf($wrapper, implode('', $items));
        } else {
            print $altString;
        }
    }

    public static function altPrint($string, $altString = '-', $return = false)
    {
        $string = (string)$string;

        $result = '' === $string ? $altString : $string;

        if ($return) {
            return $result;
        } else {
            print $result;
        }
    }

    public static function setTemplatesPath(string $templatesPath)
    {
        self::$templatesPath = $templatesPath;
    }

    public static function render($templateName, $vars = [])
    {
        ob_start();
        extract($vars);
        require self::$templatesPath . '/' . $templateName;

        return ob_get_clean();
    }
}