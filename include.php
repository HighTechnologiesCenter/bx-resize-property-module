<?php
// Автозагрузка классов и регистрация обработчиков событий
\Bitrix\Main\Loader::registerAutoLoadClasses('custompropertiesmodule', array(
	'CustomPropertiesModule\\EventHandlers\\CustomPropertiesHandler' => 'lib/event_handlers/CustomPropertiesHandler.php',
	'CustomPropertiesModule\\Config' => 'lib/general/Config.php',
	'CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty' => 'lib/custom_property_types/PictureResizerIblockElementProperty.php'
));

// Регистрация обработчиков событий
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CustomPropertiesModule\\EventHandlers\\CustomPropertiesHandler', 'getPictureResizerTypeDescription'));
