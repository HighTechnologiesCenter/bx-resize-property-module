<?php
// Автозагрузка классов и регистрация обработчиков событий
\Bitrix\Main\Loader::registerAutoLoadClasses('customprojectmodule', array(
	'CustomProjectModule\\EventHandlers\\PropertiesHandler' => 'lib/event_handlers/PropertiesHandler.php'
));

// Регистрация обработчиков событий
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'getUserTypeDescription'));
