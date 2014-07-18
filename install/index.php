<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main;
Loc::loadMessages(__FILE__);

class customprojectmodule extends \CModule
{
	var $MODULE_ID = 'customprojectmodule';
	var $MODULE_VERSION;
	var $MODULE_VERSION_DATE;
	var $MODULE_NAME;
	var $MODULE_DESCRIPTION;

	var $errors = false;

	public function customprojectmodule()
	{
		$moduleVersion = array();
		include(realpath(__DIR__) . '/version.php');
		$this->MODULE_VERSION = $moduleVersion['VERSION'];
		$this->MODULE_VERSION_DATE = $moduleVersion['VERSION_DATE'];
		$this->MODULE_NAME = Loc::getMessage('MODULE_NAME');
		$this->MODULE_DESCRIPTION = Loc::getMessage('MODULE_DESCRIPTION');
	}

	public function InstallDB()
	{
		global $errors;

		$errors = false;

		if (! empty($errors))
		{
			throw new BitrixApiException(implode('', $errors));
		}
		\Bitrix\Main\ModuleManager::registerModule($this->MODULE_ID);

		return true;
	}

	public	function UnInstallDB($arParams = Array())
	{
		global $errors;

		COption::RemoveOption($this->getModuleId());
		\Bitrix\Main\ModuleManager::unRegisterModule($this->MODULE_ID);

		return true;
	}

	public function InstallFiles($arParams = array())
	{
		return true;
	}

	public function UnInstallFiles()
	{
		return true;
	}

	public function DoInstall()
	{
		global $USER, $APPLICATION;
		if ($USER->IsAdmin())
		{
			if (! IsModuleInstalled($this->MODULE_ID))
			{
				$this->InstallDB();
				$this->InstallFiles();

				$GLOBALS['errors'] = $this->errors;

				$APPLICATION->IncludeAdminFile(Loc::getMessage('INSTALL_TITLE'), realpath(__DIR__) . '/step.php');
			}
		}
	}

	public function DoUninstall()
	{
		global $USER, $APPLICATION, $step;

		if ($USER->IsAdmin())
		{
			$this->UnInstallDB(array());
			$this->UnInstallFiles();
			$GLOBALS['errors'] = $this->errors;
			$APPLICATION->IncludeAdminFile(Loc::getMessage('UNINSTALL_TITLE'), realpath(__DIR__) . '/unstep.php');
		}
	}



}

