<?php
namespace CustomPropertiesModule\CustomProperties;
// TODO не работает ресайз в случае отсутствия одного из размеров
use Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);
/**
 * Класс, описывающий кастомное свойство элемента инфоблока, позволяющее загружать изображения и сразу обрабатывать его размер
 *
 * Class PictureResizerIblockElementProperty
 * @package CustomPropertiesModule\CustomProperties
 */
class PictureResizerIblockElementProperty
{
	const USER_TYPE_SETTINGS_CODE = 'USER_TYPE_SETTINGS';

	/**
	 * Подготовка и обработка опций, спецефических для данного свойства
	 *
	 * @param $fields
	 * @return array
	 */
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

	/**
	 * Сборка html-представления опций, спецефических для данного свойства
	 *
	 * @param $fields
	 * @param $htmlControlName
	 * @param $propertyOptions
	 * @return string
	 */
	public static function getResizeSettingsHtml($fields, $htmlControlName, &$propertyOptions)
	{
		$propertyOptions = array(
			'HIDE' => array('FILTRABLE', 'ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'MULTIPLE_CNT', 'SMART_FILTER', 'WITH_DESCRIPTION'),
			'SET' => array('FILTRABLE' => 'N', 'SEARCHABLE' => 'N'),
			'USER_TYPE_SETTINGS_TITLE' => Loc::getMessage('RESIZE_SETTINGS')
		);

		if ($fields[self::USER_TYPE_SETTINGS_CODE]['USE_CROP'] == 'Y') {
			$useCrop = true;
		} else {
			$useCrop = false;
		}

		return '<tr>
        <td>' . Loc::getMessage('HEIGHT') . ':</td>
        <td><input type="text" size="5" name="'
		. $htmlControlName["NAME"] . '[HEIGHT]" value="'
		. $fields[self::USER_TYPE_SETTINGS_CODE]['HEIGHT'] . '"></td>
        </tr>
        <tr>
        <td>' . Loc::getMessage('WIDTH') . ':</td>
        <td><input type="text" size="5" name="'
		. $htmlControlName['NAME'] . '[WIDTH]" value="'
		. $fields[self::USER_TYPE_SETTINGS_CODE]['WIDTH'] . '"></td>
        </tr>
        <tr>
        <td>' . Loc::getMessage('USE_CROP') . ':</td>
        <td><input type="checkbox" size="5" name="'
		. $htmlControlName['NAME'] . '[USE_CROP]" value="Y" ' . ($useCrop ? 'checked="checked"':'') .'></td>
        </tr>';
	}

	/**
	 * Сборка html-представления контрола для сохранения одного файла с картинкой
	 *
	 * @param $propertyFields
	 * @param $value
	 * @param $htmlControlName
	 * @return bool|string
	 */
	public static function getPropertyFieldHtml($propertyFields, $value, $htmlControlName)
	{
		if($htmlControlName['MODE'] == 'FORM_FILL' && \Bitrix\Main\Loader::includeModule('fileman'))
		{
			return  self::createFileHtmlControl($value, $htmlControlName);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Сборка html-представления контрола для сохранения множества файлов с картинкой
	 *
	 * @param $propertyFields
	 * @param $values
	 * @param $htmlControlName
	 * @return bool|string
	 */
	public static function getMultiplePropertyFieldHtml($propertyFields, $values, $htmlControlName)
	{
		if($htmlControlName['MODE']=='FORM_FILL' && \Bitrix\Main\Loader::includeModule('fileman'))
		{
			$inputHtml = '';
			$counter = 1;
			foreach ($values as $value)
			{
				$inputHtml .= self::createFileHtmlControl($value, $htmlControlName, $counter);
				$counter++;
			}

			$inputHtml .= self::createFileHtmlControl(NULL, $htmlControlName, $counter);

			return $inputHtml;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Сборка html-представления контрола для сохранения файла
	 *
	 * @param $value
	 * @param $htmlControlName
	 * @param bool $index
	 * @return string
	 */
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
		$inputHtml .= '<input type="hidden" name="' .  $htmlControlName['VALUE'] . $indexInputNamePart . '[OLD_VALUE]" value="' . $value["VALUE"] . '">';

		return $inputHtml;
	}

	/**
	 * Подготовка значений для сохранения в БД
	 *
	 * @param $propertyFields
	 * @param $propertyValue
	 * @return array|bool
	 */
	public static function prepareValues($propertyFields, $propertyValue)
	{
		if (is_array($propertyValue)) {
			return self::processFilledFormValue($propertyFields, $propertyValue);
		} elseif (intval($propertyValue > 0)) {
			$fileId = intval($propertyValue);
			if ((empty($propertyFields['USER_TYPE_SETTINGS']['HEIGHT'])) && (empty($propertyFields['USER_TYPE_SETTINGS']['WIDTH']))) {
				return $fileId;
			}
			$height = intval($propertyFields['USER_TYPE_SETTINGS']['HEIGHT']);
			$width = intval($propertyFields['USER_TYPE_SETTINGS']['WIDTH']);
			$bitrixFileService = new \CFile();
			$currentFileData = $bitrixFileService->GetFileArray($fileId);

			if (($currentFileData['WIDTH'] > $width) || ($currentFileData['HEIGHT'] > $height)) {
				$sizes = \CustomPropertiesModule\ImageProcessingHelper::processResizeSizes(
					$propertyFields['USER_TYPE_SETTINGS']['WIDTH'],
					$propertyFields['USER_TYPE_SETTINGS']['HEIGHT'],
					$currentFileData['HEIGHT'],
					$currentFileData['WIDTH']
				);
				$height = $sizes['HEIGHT'];
				$width = $sizes['WIDTH'];
				if ($propertyFields['USER_TYPE_SETTINGS']['USE_CROP'] == 'Y') {
					$resizeMode = BX_RESIZE_IMAGE_EXACT;
				} else {
					$resizeMode = BX_RESIZE_IMAGE_PROPORTIONAL_ALT;
				}
				$bitrixFileService->ResizeImage(
					$currentFileData,
					array(
						'width' => $width,
						'height' => $height
					),
					$resizeMode
				);
				$file = array_merge($currentFileData, array('MODULE_ID' => 'custompropertiesmodule', 'del' => 'N'));
				$fileResizeResult = $bitrixFileService->SaveFile($file, 'iblock/resizer');

				if ($fileResizeResult !== false) {
					$bitrixFileService->Delete($fileId);
				}

				return $fileResizeResult;
			} else {
				return $fileId;
			}
		} else {
			return $propertyValue;
		}
	}

	/**
	 * Обработка одного значения для сохранения в БД
	 *
	 * @param $propertyFields
	 * @param $propertyValue
	 * @return array|bool
	 */
	private static function processFilledFormValue($propertyFields, $propertyValue)
	{
		$documentRoot = \Bitrix\Main\Application::getDocumentRoot();
		$uploadDir = \Bitrix\Main\Config\Option::get('main', 'upload_dir', 'upload');
		if (! empty($propertyValue['VALUE']['DELETE'])) {
			return array('VALUE' => '');
		}
		if (! empty($propertyValue['VALUE']['tmp_name'])) {
			$bitrixFileService = new \CFile();

			$tempFilePath = $documentRoot . '/' . $uploadDir . '/'. $propertyValue['VALUE']['name'];
			move_uploaded_file($propertyValue['VALUE']['tmp_name'], $tempFilePath);

			if (! $bitrixFileService->IsImage($tempFilePath)) {
				unlink($tempFilePath);
				return array('VALUE' => '');
			}

			$file = $bitrixFileService->MakeFileArray($tempFilePath);

			if (
			(! empty($propertyFields['USER_TYPE_SETTINGS']['HEIGHT'])
				&& (! empty($propertyFields['USER_TYPE_SETTINGS']['WIDTH'])))
			) {
				$tempFileSizes = getimagesize($tempFilePath);
				$sizes = \CustomPropertiesModule\ImageProcessingHelper::processResizeSizes(
					$propertyFields['USER_TYPE_SETTINGS']['WIDTH'],
					$propertyFields['USER_TYPE_SETTINGS']['HEIGHT'],
					$tempFileSizes[1],
					$tempFileSizes[0]
				);

				$height = $sizes['HEIGHT'];
				$width = $sizes['WIDTH'];
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

				$file = $bitrixFileService->MakeFileArray($file['tmp_name']);
			}


			$file = array_merge($file, array('MODULE_ID' => 'custompropertiesmodule', 'del' => 'N'));
			$fileResizeResult = $bitrixFileService->SaveFile($file, 'iblock/resizer');

			if ($fileResizeResult !== false) {
				unlink($tempFilePath);
			}

			return array('VALUE' => $fileResizeResult);
		} elseif(! empty($propertyValue['VALUE']['OLD_VALUE'])) {
			return array('VALUE' => intval($propertyValue['VALUE']['OLD_VALUE']));
		} else {
			return false;
		}
	}
}