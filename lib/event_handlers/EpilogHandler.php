<?php
namespace CustomProjectModule\EventHandlers;

class EpilogHandler {
	public static function set404Status ()
	{
		if (
			! defined('ADMIN_SECTION') &&
			defined('ERROR_404') &&
			file_exists($_SERVER['DOCUMENT_ROOT'] . '/404.php')
		) {
			global $APPLICATION;
			$APPLICATION->RestartBuffer();
			include($_SERVER['DOCUMENT_ROOT'] . '/404.php');
		}
	}
} 