<?php
namespace CustomPropertiesModule\CustomProperties;

use Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);
// TODO проверить является ли файл картинкой
/**
 * Класс описывающий кастомное пользовательское свойство типа файл с возможностью использования ресайзинга
 *
 * Class PictureResizerUserTypeProperty
 * @package CustomPropertiesModule\CustomProperties
 */
class PictureResizerUserTypeProperty extends \CUserTypeFile {

	/**
	 * Проверка входящих значений
	 *
	 * @param $userFieldParameters
	 * @param $value
	 * @return array
	 */
	public function CheckFields($userFieldParameters, $value)
	{
		$errorMessages = parent::CheckFields($userFieldParameters, $value);

		$bitrixFileService = new \CFile();
		if (is_array($value)) {
			$isImage = $bitrixFileService->IsImage($value['tmp_name']);
		} else {
			$value = intval($value);
			$fileData = $bitrixFileService->GetFileArray($value);
			$isImage = $bitrixFileService->IsImage($fileData['SRC']);
		}
		if (! $isImage) {
			$errorMessages[] = array(
				'id' => $userFieldParameters['FIELD_NAME'],
				'text' => Loc::getMessage('FILE_IS_NOT_AN_IMAGE')
			);
		}

		return $errorMessages;
	}

	/**
	 * Сборка html-представления для настроек свойства
	 *
	 * @param bool $userFieldParameters
	 * @param $htmlControlName
	 * @param $valuesFromForm
	 * @return string
	 */
	public function GetSettingsHTML($userFieldParameters = false, $htmlControlName, $valuesFromForm)
	{
		$settingsHtml = '';

		if ($valuesFromForm) {
			$sizeValue = intval($GLOBALS[$htmlControlName['NAME']]['SIZE']);
		} elseif (is_array($userFieldParameters)) {
			$sizeValue = intval($userFieldParameters['SETTINGS']['SIZE']);
		} else {
			$sizeValue = 20;
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('USER_TYPE_FILE_SIZE') . ':</td>
			<td>
				<input type="text" name="'. $htmlControlName['NAME'] . '[SIZE]" size="20"  maxlength="20" value="'. $sizeValue .'">
			</td>
		</tr>
		';

		if ($valuesFromForm) {
			$widthValue = intval($GLOBALS[$htmlControlName['NAME']]['LIST_WIDTH']);
		} elseif (is_array($userFieldParameters)) {
			$widthValue = intval($userFieldParameters['SETTINGS']['LIST_WIDTH']);
		} else {
			$widthValue = 200;
		}

		if ($valuesFromForm) {
			$heightValue = intval($GLOBALS[$htmlControlName['NAME']]['LIST_HEIGHT']);
		} elseif (is_array($userFieldParameters)) {
			$heightValue = intval($userFieldParameters['SETTINGS']['LIST_HEIGHT']);
		} else {
			$heightValue = 200;
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('USER_TYPE_FILE_WIDTH_AND_HEIGHT') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[LIST_WIDTH]" size="7"  maxlength="20" value="' . $widthValue . '">
				&nbsp;x&nbsp;
				<input type="text" name="' . $htmlControlName['NAME'] . '[LIST_HEIGHT]" size="7"  maxlength="20" value="' . $heightValue . '">
			</td>
		</tr>
		';

		if ($valuesFromForm) {
			$maxShowSizeValue = intval($GLOBALS[$htmlControlName['NAME']]['MAX_SHOW_SIZE']);
		} elseif (is_array($userFieldParameters)) {
			$maxShowSizeValue = intval($userFieldParameters['SETTINGS']['MAX_SHOW_SIZE']);
		} else {
			$maxShowSizeValue = 0;
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('USER_TYPE_FILE_MAX_SHOW_SIZE') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[MAX_SHOW_SIZE]" size="20"  maxlength="20" value="' . $maxShowSizeValue . '">
			</td>
		</tr>
		';

		if($valuesFromForm) {
			$maxAllowedSizeValue = intval($GLOBALS[$htmlControlName['NAME']]['MAX_ALLOWED_SIZE']);
		} elseif (is_array($userFieldParameters)) {
			$maxAllowedSizeValue = intval($userFieldParameters['SETTINGS']['MAX_ALLOWED_SIZE']);
		} else {
			$maxAllowedSizeValue = 0;
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('USER_TYPE_FILE_MAX_ALLOWED_SIZE') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[MAX_ALLOWED_SIZE]" size="20"  maxlength="20" value="' . $maxAllowedSizeValue . '">
			</td>
		</tr>
		';

		if ($valuesFromForm) {
			$extensionsValue = htmlspecialcharsbx($GLOBALS[$htmlControlName['NAME']]['EXTENSIONS']);

			$settingsHtml .= '
			<tr>
				<td>' . Loc::getMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
				<td>
					<input type="text" size="20" name="'.$htmlControlName['NAME'].'[EXTENSIONS]" value="'.$extensionsValue.'">
				</td>
			</tr>
			';

		} else {
			if (is_array($userFieldParameters)) {
				$extensionsList = $userFieldParameters['SETTINGS']['EXTENSIONS'];
			} else {
				$extensionsList = '';
			}
			$extensionsValue = array();
			if (is_array($extensionsList)) {
				foreach($extensionsList as $extension => $flag) {
					$extensionsValue[] = htmlspecialcharsbx($extension);
				}
			}

			$settingsHtml .= '
			<tr>
				<td>' . Loc::getMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
				<td>
					<input type="text" size="20" name="' . $htmlControlName['NAME'] . '[EXTENSIONS]" value="' . implode(', ', $extensionsValue) . '">
				</td>
			</tr>
			';
		}

		if($valuesFromForm) {
			$resizeHeightValue = intval($GLOBALS[$htmlControlName['NAME']]['RESIZE_HEIGHT']);
		} elseif (is_array($userFieldParameters)) {
			$resizeHeightValue = intval($userFieldParameters['SETTINGS']['RESIZE_HEIGHT']);
			if ($resizeHeightValue <= 1) {
				$resizeHeightValue = '';
			}
		} else {
			$resizeHeightValue = '';
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('HEIGHT') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[RESIZE_HEIGHT]" size="20"  maxlength="20" value="' . $resizeHeightValue . '">
			</td>
		</tr>
		';

		if($valuesFromForm) {
		$resizeWidthValue = intval($GLOBALS[$htmlControlName['NAME']]['RESIZE_WIDTH']);
	} elseif (is_array($userFieldParameters)) {
		$resizeWidthValue = intval($userFieldParameters['SETTINGS']['RESIZE_WIDTH']);
		if ($resizeWidthValue <= 1) {
			$resizeWidthValue = '';
		}
	} else {
		$resizeWidthValue = '';
	}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('WIDTH') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[RESIZE_WIDTH]" size="20"  maxlength="20" value="' . $resizeWidthValue . '">
			</td>
		</tr>
		';
		if ($valuesFromForm) {
			$useCropValue = $GLOBALS[$htmlControlName['NAME']]['USE_CROP'];
		} elseif (is_array($userFieldParameters)) {
			$useCropValue = $userFieldParameters['SETTINGS']['USE_CROP'];
		} else {
			$useCropValue = 'N';
		}

		if ($useCropValue == 'Y') {
			$useCropCheckedAttribute = 'checked="checked"';
		} else {
			$useCropCheckedAttribute = '';
		}

		$settingsHtml .= '
		<tr>
			<td>' . Loc::getMessage('USE_CROP') . ':</td>
			<td>
				<input type="checkbox" name="' . $htmlControlName['NAME'] . '[USE_CROP]" value="Y" ' . $useCropCheckedAttribute . '>
			</td>
		</tr>
		';

		
		return $settingsHtml;
	}

	/**
	 * Обработка настроек свойства перед сохранением
	 *
	 * @param $userFieldParameters
	 * @return array
	 */
	public function PrepareSettings($userFieldParameters)
	{
		$size = intval($userFieldParameters['SETTINGS']['SIZE']);
		$supportedExtensions = array();

		if (is_array($userFieldParameters['SETTINGS']['EXTENSIONS'])) {
			$extension = $userFieldParameters['SETTINGS']['EXTENSIONS'];
		} else {
			$extension = explode(',', $userFieldParameters['SETTINGS']['EXTENSIONS']);
		}

		foreach($extension as $key => $value)
		{
			if ($value === true) {
				$value = trim($key);
			} else {
				$value = trim($value);
			}

			if (strlen($value) > 0) {
				$supportedExtensions[$value] = true;
			}
		}

		$resizeHeight = intval($userFieldParameters['SETTINGS']['RESIZE_HEIGHT']);
		$resizeWidth = intval($userFieldParameters['SETTINGS']['RESIZE_WIDTH']);

		return array(
			'SIZE' =>  ($size <= 1? 20: ($size > 255? 225: $size)),
			'LIST_WIDTH' => intval($userFieldParameters['SETTINGS']['LIST_WIDTH']),
			'LIST_HEIGHT' => intval($userFieldParameters['SETTINGS']['LIST_HEIGHT']),
			'MAX_SHOW_SIZE' => intval($userFieldParameters['SETTINGS']['MAX_SHOW_SIZE']),
			'MAX_ALLOWED_SIZE' => intval($userFieldParameters['SETTINGS']['MAX_ALLOWED_SIZE']),
			'EXTENSIONS' => $supportedExtensions,
			'RESIZE_HEIGHT' => ($resizeHeight <= 1 ? '' : $resizeHeight),
			'RESIZE_WIDTH' => ($resizeWidth <= 1 ? '' : $resizeWidth),
			'USE_CROP' => ($userFieldParameters['SETTINGS']['USE_CROP'] == 'Y' ? 'Y' : 'N')
		);
	}

	/**
	 * Обработка значения свойства перед сохранением
	 *
	 * @param $userFieldParameters
	 * @param $value
	 * @return array|bool|int|string
	 */
	function OnBeforeSave($userFieldParameters, $value)
	{
		if (is_array($value)) {
			if ((isset($value['old_id'])) && ($value['old_id'] > 0)) {
				if (is_array($userFieldParameters['VALUE'])) {
					if (! in_array($value['old_id'], $userFieldParameters['VALUE'])) {
						unset($value['old_id']);
					}
				} else {
					if ($userFieldParameters['VALUE'] != $value['old_id']) {
						unset($value['old_id']);
					}
				}
			}

			$bitrixFileService = new \CFile();

			if ($value['del'] && $value['old_id']) {
				$bitrixFileService->Delete($value['old_id']);
				$value['old_id'] = false;
			}

			if ($value['error']) {
				return $value['old_id'];
			} else {
				if ($value['old_id']) {
					$bitrixFileService->Delete($value['old_id']);
				}
				$value['MODULE_ID'] = 'main';
				if ((empty($userFieldParameters['SETTINGS']['RESIZE_HEIGHT'])) && (empty($userFieldParameters['SETTINGS']['RESIZE_WIDTH']))) {
					$fileId =  $bitrixFileService->SaveFile($value, 'uf');
				} else {
					$fileId = self::getResizedPicture($userFieldParameters, $value,$bitrixFileService);
				}

				return $fileId;
			}
		} else {
			if ((empty($userFieldParameters['SETTINGS']['RESIZE_HEIGHT'])) && (empty($userFieldParameters['SETTINGS']['RESIZE_WIDTH']))) {
				return $value;
			}
			$fileId = intval($value);
			$height = intval($userFieldParameters['SETTINGS']['RESIZE_HEIGHT']);
			$width = intval($userFieldParameters['SETTINGS']['RESIZE_WIDTH']);
			$bitrixFileService = new \CFile();
			$currentFileData = $bitrixFileService->GetFileArray($fileId);

			if (($currentFileData['WIDTH'] > $width) || ($currentFileData['HEIGHT'] > $height)) {
				return self::getResizedPicture($userFieldParameters, $value, $bitrixFileService);
			} else {
				return $value;
			}
		}
	}

	/**
	 * Создание обработанного ресайзом файла
	 *
	 * @param $userFieldParameters
	 * @param $value
	 * @param \CFile $bitrixFileService
	 * @return bool|int|string
	 */
	private static function getResizedPicture($userFieldParameters, $value, \CFile $bitrixFileService)
	{
		$oldValue = false;
		if (is_array($value)) {
			$tempFilePath = self::getUploadedPicturePath($value);
		} else {
			$oldValue = intval($value);
			$tempFilePath = $bitrixFileService->GetPath($oldValue);
		}

		$file = $bitrixFileService->MakeFileArray($tempFilePath);
		$tempFileSizes = getimagesize($tempFilePath);
		$sizes = \CustomPropertiesModule\ImageProcessingHelper::processResizeSizes(
			$userFieldParameters['SETTINGS']['RESIZE_WIDTH'],
			$userFieldParameters['SETTINGS']['RESIZE_HEIGHT'],
			$tempFileSizes[1],
			$tempFileSizes[0]
		);

		$height = $sizes['HEIGHT'];
		$width = $sizes['WIDTH'];

		if ($userFieldParameters['SETTINGS']['USE_CROP'] == 'Y') {
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
		$fileResizeResult = $bitrixFileService->SaveFile($file, 'uf/resizer');

		if ($fileResizeResult !== false) {
			if ($oldValue) {
				$bitrixFileService->Delete($oldValue);
			} else {
				unlink($tempFilePath);
			}

			return $fileResizeResult;
		} else {
			return '';
		}
	}

	/**
	 * Перемещение исходного изображения во временное хранилище и получение ссылки на него
	 *
	 * @param $value
	 * @return string
	 */
	private static function getUploadedPicturePath($value)
	{
		$documentRoot = \Bitrix\Main\Application::getDocumentRoot();
		$uploadDir = \Bitrix\Main\Config\Option::get('main', 'upload_dir', 'upload');
		$tempFilePath = $documentRoot . '/' . $uploadDir . '/'. $value['name'];
		move_uploaded_file($value['tmp_name'], $tempFilePath);

		return $tempFilePath;
	}

}