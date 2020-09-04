<?php

class Template
{

    public static function loopAndPrint($array, $printedKey, $altString = null, $elementWrapper = null)
    {
        $elementWrapper = is_null($elementWrapper) ? '<div>%s</div>' : $elementWrapper;
        $altString      = is_null($altString) ? '<i>Əlavə olunmayıb</i>' : $altString;

        if (count($array) === 0) {
            print $altString;
        } else {
            foreach ($array as $element) {
                printf($elementWrapper, htmlspecialchars($element[$printedKey]));
            }
        }
    }

    public static function showCheckbox($value)
    {
        if ((int)$value) {
            print "<i class='fa fa-check'></i>";
        } else {
            print "-";
        }
    }

    public static function renderAndReturn($templateName, $vars = [])
    {
        ob_start();
        extract($vars);
        require $templateName;

        return ob_get_clean();
    }

    public static function render($templateName, $vars = [])
    {
        extract($vars);
        require $templateName;
    }

}