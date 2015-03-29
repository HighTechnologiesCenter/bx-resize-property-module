<?php

namespace HtcCs\CustomPropertiesModule\EventHandlers;

/**
 * Class CustomPropertiesHandler
 * @package HtcCs\CustomPropertiesModule\EventHandlers
 */
class CustomPropertiesHandler
{
    /**
     * Обработчик события OnIBlockPropertyBuildList для подключения кастомного свойства
     *
     * @return array
     */
    public static function getPictureResizerTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S',
            'USER_TYPE' => 'ImageResizingProperty',
            'DESCRIPTION' => 'Картинки с ресайзом',
            'GetPropertyFieldHtml' => array(
                'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty',
                'getPropertyFieldHtml'
            ),
            'GetPropertyFieldHtmlMulty' => array(
                'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty',
                'getMultiplePropertyFieldHtml'
            ),
            'ConvertToDB' => array(
                'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty',
                'prepareValues'
            ),
            'GetSettingsHTML' => array(
                'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty',
                'getResizeSettingsHtml'
            ),
            'PrepareSettings' => array(
                'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty',
                'prepareResizeSettings'
            ),
        );
    }

    /**
     * Обработчик события OnUserTypeBuildList для подключения кастомного свойства
     *
     * @return array
     */
    public static function getPictureResizerUserTypeDescription()
    {
        return array(
            'USER_TYPE_ID' => 'resize_file',
            'CLASS_NAME' => 'HtcCs\\CustomPropertiesModule\\CustomProperties\\PictureResizerUserTypeProperty',
            'DESCRIPTION' => 'Картинки с ресайзом',
            'BASE_TYPE' => 'file',
        );

    }
}
