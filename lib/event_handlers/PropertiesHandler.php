<?php
namespace CustomProjectModule\EventHandlers;

class PropertiesHandler {
	const USER_TYPE_SETTINGS_CODE = 'USER_TYPE_SETTINGS';
	
	public static function getUserTypeDescription()
	{
		return array(
			'PROPERTY_TYPE' => 'S',
			'USER_TYPE' => 'ResizingManager',
			'DESCRIPTION' => 'Ресайзинг',
			'GetPropertyFieldHtml' => array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'getPropertyFieldHtml'),
			'GetPropertyFieldHtmlMulty' => array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'getMultiplePropertyFieldHtml'),
			'ConvertToDB' => array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'prepareValues'),
			'GetSettingsHTML' => array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'getResizeSettingsHtml'),
			'PrepareSettings' => array('CustomProjectModule\\EventHandlers\\PropertiesHandler', 'prepareResizeSettings'),
		);
	}


	public static function prepareResizeSettings($fields)
	{
		$values = array(
			'HEIGHT' => '',
			'WIDTH' => '',
			'USE_CROP' => 'N'
		);
		$userTypeSettingsKeyName = self::USER_TYPE_SETTINGS_CODE;
		if (is_array($fields[$userTypeSettingsKeyName])) {
			if (isset($fields[$userTypeSettingsKeyName]['HEIGHT'])) {
				$values['HEIGHT'] = intval($fields[$userTypeSettingsKeyName]['HEIGHT']);
			}
			if (isset($fields[$userTypeSettingsKeyName]['WIDTH'])) {
				$values['WIDTH'] = intval($fields[$userTypeSettingsKeyName]['WIDTH']);
			}
			if (
				isset($fields[$userTypeSettingsKeyName]['USE_CROP'])
				&& ($fields[$userTypeSettingsKeyName]['USE_CROP'] == 'Y')
			) {
				$values['USE_CROP'] = 'Y';
			}
		}

		return $values;
	}

	public static function getResizeSettingsHtml($fields, $htmlControlName, &$propertyOptions)
	{
		$propertyOptions = array(
			'HIDE' => array('FILTRABLE', 'ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE'),
			'SET' => array('FILTRABLE' => 'N', 'SEARCHABLE' => 'N'),
			'USER_TYPE_SETTINGS_TITLE' => 'Настройки ресайза'
		);

		if ($fields[self::USER_TYPE_SETTINGS_CODE]['USE_CROP'] == 'Y') {
			$useCrop = true;
		} else {
			$useCrop = false;
		}

		return '<tr>
        <td>Высота:</td>
        <td><input type="text" size="5" name="'
			. $htmlControlName["NAME"] . '[HEIGHT]" value="'
			. $fields[self::USER_TYPE_SETTINGS_CODE]['HEIGHT'] . '"></td>
        </tr>
        <tr>
        <td>Ширина:</td>
        <td><input type="text" size="5" name="'
		. $htmlControlName['NAME'] . '[WIDTH]" value="'
		. $fields[self::USER_TYPE_SETTINGS_CODE]['WIDTH'] . '"></td>
        </tr>
        <tr>
        <td>Использовать кроп:</td>
        <td><input type="checkbox" size="5" name="'
		. $htmlControlName['NAME'] . '[USE_CROP]" value="Y" ' . ($useCrop ? 'checked="checked"':'') .'></td>
        </tr>';
	}

	function getPropertyFieldHtml($propertyFields, $value, $htmlControlName)
	{
		if($htmlControlName['MODE'] == 'FORM_FILL' && \CModule::IncludeModule('fileman'))
		{
			return  self::createFileHtmlControl($value, $htmlControlName);
		}
		else
		{
			return false;
		}
	}

	public function getMultiplePropertyFieldHtml($propertyFields, $values, $htmlControlName)
	{
		if($htmlControlName["MODE"]=="FORM_FILL" && \CModule::IncludeModule('fileman'))
		{
			$inputHtml = '';
			$counter = 1;
			foreach ($values as $key => $arOneValue)
			{
				$inputHtml.= self::createFileHtmlControl($arOneValue, $htmlControlName, $counter);
				$counter++;
			}

			$inputHtml.= self::createFileHtmlControl(NULL, $htmlControlName, $counter);

			return $inputHtml;
		}
		else
		{
			return false;
		}
	}

	private static function createFileHtmlControl($value, $htmlControlName, $index=false)
	{
		if (intval($index > 0)) {
			$indexInputNamePart = "[{$index}]";
		} else {
			$indexInputNamePart = '';
		}
		$inputHtml = \CFileInput::Show(
			$htmlControlName['VALUE'] . $indexInputNamePart,
			$value['VALUE'],
			array(
				'IMAGE' => 'Y',
				'PATH' => 'Y',
				'FILE_SIZE' => 'Y',
				'DIMENSIONS' => 'Y',
				'IMAGE_POPUP' => 'Y',
				'MAX_SIZE' => array(
					'W' => \COption::GetOptionString('iblock', 'detail_image_size'),
					'H' => \COption::GetOptionString('iblock', 'detail_image_size'),
				)
			),
			array(
				'upload' => true,
				'medialib' => false,
				'file_dialog' => false,
				'cloud' => false,
				'del' => array(
					'NAME' => $htmlControlName['VALUE'] . $indexInputNamePart . '[DELETE]'
				),
				'description' => false
			)
		);
		$inputHtml .= '<input type="hidden" name=' .  $htmlControlName['VALUE'] . $indexInputNamePart . '[OLD_VALUE]" value="' . $value["VALUE"] . '">';

		return $inputHtml;
	}

	function prepareValues($propertyFields, $propertyValue)
	{
		return self::processSingleValue($propertyFields, $propertyValue);
	}

	private static function processSingleValue($propertyFields, $propertyValue)
	{
		if (! empty($propertyValue['VALUE']['DELETE'])) {
			return array('VALUE' => '');
		}
		if (! empty($propertyValue['VALUE']['tmp_name'])) {
			$tempFilePath = $_SERVER['DOCUMENT_ROOT'] . '/upload/'. $propertyValue['VALUE']['name'];
			move_uploaded_file($propertyValue['VALUE']['tmp_name'], $tempFilePath);
			$bitrixFileService = new \CFile();
			$file = $bitrixFileService->MakeFileArray($tempFilePath);

			if (
			(! empty($propertyFields['USER_TYPE_SETTINGS']['HEIGHT'])
				&& (! empty($propertyFields['USER_TYPE_SETTINGS']['WIDTH'])))
			) {
				$height = intval($propertyFields['USER_TYPE_SETTINGS']['HEIGHT']);
				$width = intval($propertyFields['USER_TYPE_SETTINGS']['WIDTH']);
				if ($propertyFields['USER_TYPE_SETTINGS']['USE_CROP'] == 'Y') {
					$resizeMode = BX_RESIZE_IMAGE_EXACT;
				} else {
					$resizeMode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT;
				}
				$bitrixFileService->ResizeImage(
					$file,
					array(
						'width' => $width,
						'height' => $height
					),
					$resizeMode
				);
			}

			$file = $bitrixFileService->MakeFileArray($file['tmp_name']);
			$file = array_merge($file, array('MODULE_ID' => 'iblock', 'del' => 'N'));
			$fileResisterResult = $bitrixFileService->SaveFile($file, 'iblock/resizer');

			if ($fileResisterResult !== false) {
				unlink($tempFilePath);
			}

			return array('VALUE' => $fileResisterResult);
		} elseif(! empty($propertyValue['VALUE']['OLD_VALUE'])) {
			return array('VALUE' => intval($propertyValue['VALUE']['OLD_VALUE']));
		} else {
			return false;
		}
	}
} 