<?php

namespace Core\Views;

/**
 * Главный класс реализующий функционал отображения
 * представлений
 *
 */
class View
{
    /**
     */
    public static function render()
    {
        readfile($_SERVER['DOCUMENT_ROOT'] . '/../Vue/dist/index.html');
    }
}
