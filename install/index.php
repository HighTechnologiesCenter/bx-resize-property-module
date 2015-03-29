<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main;

/**
 * Class custompropertiesmodule
 */
class custompropertiesmodule extends \CModule
{
    /**
     * @var string
     */
    const MODULE_ID = 'custompropertiesmodule';
    /**
     * @var string
     */
    public $MODULE_ID = self::MODULE_ID;
    /**
     * @var string
     */
    public $MODULE_VERSION;
    /**
     * @var string
     */
    public $MODULE_VERSION_DATE;
    /**
     * @var string
     */
    public $MODULE_NAME;
    /**
     * @var string
     */
    public $MODULE_DESCRIPTION;

    /**
     * @var bool
     */
    public $errors = false;

    /**
     * Инициализация модуля
     */
    public function custompropertiesmodule()
    {
        Loc::loadMessages(__FILE__);
        $moduleVersion = array();
        include(realpath(__DIR__) . '/version.php');
        $this->MODULE_VERSION = $moduleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $moduleVersion['VERSION_DATE'];
        $this->MODULE_NAME = Loc::getMessage('CUSTOM_PROPERTIES_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('CUSTOM_PROPERTIES_MODULE_DESCRIPTION');
    }

    /**
     * Регистрация модуля в БД
     *
     * @return bool
     * @throws Exception
     */
    public function InstallDB()
    {
        global $errors;

        $errors = false;

        if (!empty($errors)) {
            throw new \Exception(implode('', $errors));
        }
        \Bitrix\Main\ModuleManager::registerModule(self::MODULE_ID);

        return true;
    }

    /**
     * Удалить модуль из БД
     *
     * @param array $arParams
     * @return bool
     */
    public function UnInstallDB($arParams = Array())
    {
        global $errors;

        \COption::RemoveOption(self::MODULE_ID);
        \Bitrix\Main\ModuleManager::unRegisterModule(self::MODULE_ID);

        return true;
    }

    /**
     * Инициализация установки модуля
     *
     * @throws Exception
     */
    public function DoInstall()
    {
        global $USER, $APPLICATION;
        if ($USER->IsAdmin()) {
            if (!IsModuleInstalled(self::MODULE_ID)) {
                $this->InstallDB();
                $this->InstallFiles();

                $GLOBALS['errors'] = $this->errors;

                $APPLICATION->IncludeAdminFile(Loc::getMessage('INSTALL_TITLE'), realpath(__DIR__) . '/step.php');
            }
        }
    }

    /**
     * Инициализация удаления модуля
     */
    public function DoUninstall()
    {
        global $USER, $APPLICATION, $step;

        if ($USER->IsAdmin()) {
            $this->UnInstallDB(array());
            $this->UnInstallFiles();
            $GLOBALS['errors'] = $this->errors;
            $APPLICATION->IncludeAdminFile(Loc::getMessage('UNINSTALL_TITLE'), realpath(__DIR__) . '/unstep.php');
        }
    }
}
