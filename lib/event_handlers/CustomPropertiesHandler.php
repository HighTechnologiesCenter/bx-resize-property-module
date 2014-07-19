<?php
namespace CustomPropertiesModule\EventHandlers;

class CustomPropertiesHandler {

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
			'GetPropertyFieldHtml' => array('CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty', 'getPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array('CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty', 'getMultiplePropertyFieldHtml'),
			'ConvertToDB' => array('CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty', 'prepareValues'),
			'GetSettingsHTML' => array('CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty', 'getResizeSettingsHtml'),
			'PrepareSettings' => array('CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty', 'prepareResizeSettings'),
		);
	}
}