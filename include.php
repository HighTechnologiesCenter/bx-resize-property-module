<?php
// Автозагрузка классов и регистрация обработчиков событий
\Bitrix\Main\Loader::registerAutoLoadClasses('custompropertiesmodule', array(
    'HtcCs\CustomPropertiesModule\\EventHandlers\\CustomPropertiesHandler'
        => 'lib/EventHandlers/CustomPropertiesHandler.php',
    'HtcCs\CustomPropertiesModule\\Config\\Config' => 'lib/Config/Config.php',
    'HtcCs\CustomPropertiesModule\\Helpers\\ImageProcessingHelper' => 'lib/Helpers/ImageProcessingHelper.php',
    'HtcCs\CustomPropertiesModule\\CustomProperties\\PictureResizerIblockElementProperty'
        => 'lib/CustomProperties/PictureResizerIblockElementProperty.php',
    'HtcCs\CustomPropertiesModule\\CustomProperties\\PictureResizerUserTypeProperty'
        => 'lib/CustomProperties/PictureResizerUserTypeProperty.php',
));

// Регистрация обработчиков событий
$eventManager = \Bitrix\Main\EventManager::getInstance();
$eventManager->addEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    array('HtcCs\CustomPropertiesModule\\EventHandlers\\CustomPropertiesHandler', 'getPictureResizerTypeDescription')
);
$eventManager->addEventHandler(
    'main',
    'OnUserTypeBuildList',
    array(
        'HtcCs\CustomPropertiesModule\\EventHandlers\\CustomPropertiesHandler',
        'getPictureResizerUserTypeDescription'
    )
);
