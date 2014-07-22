<?php
namespace CustomPropertiesModule\CustomProperties;
// TODO запилить языковой файл
class PictureResizerUserTypeProperty extends \CUserTypeFile {

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
			<td>' . GetMessage('USER_TYPE_FILE_SIZE') . ':</td>
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
			<td>' . GetMessage("USER_TYPE_FILE_WIDTH_AND_HEIGHT") . ':</td>
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
			<td>' . GetMessage('USER_TYPE_FILE_MAX_SHOW_SIZE') . ':</td>
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
			<td>' . GetMessage('USER_TYPE_FILE_MAX_ALLOWED_SIZE') . ':</td>
			<td>
				<input type="text" name="' . $htmlControlName['NAME'] . '[MAX_ALLOWED_SIZE]" size="20"  maxlength="20" value="' . $maxAllowedSizeValue . '">
			</td>
		</tr>
		';

		if ($valuesFromForm) {
			$extensionsValue = htmlspecialcharsbx($GLOBALS[$htmlControlName['NAME']]['EXTENSIONS']);

			$settingsHtml .= '
			<tr>
				<td>' . GetMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
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
				<td>' . GetMessage('USER_TYPE_FILE_EXTENSIONS') . ':</td>
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
			<td>' . 'Ресайз. Высота' . ':</td>
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
			<td>' . 'Ресайз. Ширина' . ':</td>
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
			<td>' . 'Ресайз. Использовать кроп' . ':</td>
			<td>
				<input type="checkbox" name="' . $htmlControlName['NAME'] . '[USE_CROP]" value="Y" ' . $useCropCheckedAttribute . '>
			</td>
		</tr>
		';

		
		return $settingsHtml;
	}

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
} 