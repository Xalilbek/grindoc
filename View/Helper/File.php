<?php
/**
 * Created by PhpStorm.
 * User: b.shamsi
 * Date: 23.11.2018
 * Time: 12:48
 */

namespace View\Helper;

class File
{
    public static function getFAIconCSSClass($file) {
        $extIconMap = [
            'xls'  => 'fa-file-excel-o',
            'xlsx' => 'fa-file-excel-o',
            'docx' => 'fa-file-word-o',
            'doc'  => 'fa-file-word-o',
            'jpg'  => 'fa-file-image-o',
            'png'  => 'fa-file-image-o',
            'jpeg' => 'fa-file-image-o',
            'gif'  => 'fa-file-image-o',
            'pdf'  => 'fa-file-pdf-o',
            'zip'  => 'fa-file-archive-o',
            'rar'  => 'fa-file-archive-o',
        ];

        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (isset($extIconMap[$ext])) {
            return $extIconMap[$ext];
        } else {
            return 'fa-file-o';
        }
    }
}