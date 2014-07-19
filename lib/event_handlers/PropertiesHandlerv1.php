<?php
namespace CustomProjectModule\EventHandlers;


class PropertiesHandler {
	const USER_TYPE_SETTINGS_CODE = 'USER_TYPE_SETTINGS';
	public static function getUserTypeDescription()
	{
		return array(
			"PROPERTY_TYPE" => "F",
			"USER_TYPE" => "ResizingManager",
			"DESCRIPTION" => "Ресайзинг",
			"GetPropertyFieldHtml" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "GetPropertyFieldHtml"),
			"GetPropertyFieldHtmlMulty" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "GetPropertyFieldHtmlMulty"),
			"ConvertToDB" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "ConvertToDB"),
			"ConvertFromDB" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "ConvertFromDB"),
			//"GetLength" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "GetLength"),
			"CheckFields" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "CheckFields"),
			//"GetAdminListViewHTML" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "GetAdminListViewHTML"),
			"GetSettingsHTML" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "getResizeSettingsHtml"),
			"PrepareSettings" => array("CustomProjectModule\\EventHandlers\\PropertiesHandler", "prepareResizeSettings"),
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

	function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
	{
		global $APPLICATION;
		echo '<pre>';
		print_r(array($arProperty, $value, $strHTMLControlName));
		echo '</pre>';
		if (strLen(trim($strHTMLControlName["FORM_NAME"])) <= 0)
			$strHTMLControlName["FORM_NAME"] = "form_element";
		$name = preg_replace("/[^a-zA-Z0-9_]/i", "x", htmlspecialcharsbx($strHTMLControlName["VALUE"]));

		if(is_array($value["VALUE"]))
		{
			$value["VALUE"] = $value["VALUE"]["VALUE"];
			$value["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}

		if($strHTMLControlName["MODE"]=="FORM_FILL" && \CModule::IncludeModule('fileman'))
		{
			return \CFileInput::Show($strHTMLControlName["VALUE"], $value["VALUE"],
				array(
					"IMAGE" => "Y",
					"PATH" => "Y",
					"FILE_SIZE" => "Y",
					"DIMENSIONS" => "Y",
					"IMAGE_POPUP" => "Y",
					"MAX_SIZE" => array(
						"W" => \COption::GetOptionString("iblock", "detail_image_size"),
						"H" => \COption::GetOptionString("iblock", "detail_image_size"),
					)
				), array(
					'upload' => true,
					'medialib' => true,
					'file_dialog' => true,
					'cloud' => true,
					'del' => true,
					'description' => $arProperty["WITH_DESCRIPTION"]=="Y"? array(
							"VALUE" => $value["DESCRIPTION"],
							"NAME" => $strHTMLControlName["DESCRIPTION"],
						): false,
				)
			);
		}
		else
		{
			$return = '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]).'" id="'.$name.'" size="'.$arProperty["COL_COUNT"].'" value="'.htmlspecialcharsEx($value["VALUE"]).'">';

			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
			{
				$return .= ' <span title="'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]).'" value="'.htmlspecialcharsEx($value["DESCRIPTION"]).'" size="18" type="text"></span>';
			}

			return $return;
		}
	}

	public function GetPropertyFieldHtmlMulty($arProperty, $arValues, $strHTMLControlName)
	{
		if($strHTMLControlName["MODE"]=="FORM_FILL" && \CModule::IncludeModule('fileman'))
		{
			$inputName = array();
			$description = array();
			foreach ($arValues as $intPropertyValueID => $arOneValue)
			{
				$key = $strHTMLControlName["VALUE"]."[".$intPropertyValueID."]";
				$inputName[$key."[VALUE]"] = $arOneValue["VALUE"];
				$description[$key."[DESCRIPTION]"] = $arOneValue["DESCRIPTION"];
			}

			return \CFileInput::ShowMultiple($inputName, $strHTMLControlName["VALUE"]."[n#IND#][VALUE]", array(
				"PATH" => "Y",
				"IMAGE" => "N",
				"MAX_SIZE" => array(
					"W" => \COption::GetOptionString("iblock", "detail_image_size"),
					"H" => \COption::GetOptionString("iblock", "detail_image_size"),
				),
			), false, array(
				'upload' => false,
				'medialib' => true,
				'file_dialog' => true,
				'cloud' => true,
				'del' => true,
				'description' => $arProperty["WITH_DESCRIPTION"]=="Y"? array(
						"VALUES" => $description,
						'NAME_TEMPLATE' => $strHTMLControlName["VALUE"]."[n#IND#][DESCRIPTION]",
					): false,
			));
		}
		else
		{
			$table_id = md5($strHTMLControlName["VALUE"]);
			$return = '<table id="tb'.$table_id.'" border=0 cellpadding=0 cellspacing=0>';
			foreach ($arValues as $intPropertyValueID => $arOneValue)
			{
				$return .= '<tr><td>';

				$return .= '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]."[$intPropertyValueID][VALUE]").'" size="'.$arProperty["COL_COUNT"].'" value="'.htmlspecialcharsEx($arOneValue["VALUE"]).'">';

				if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
					$return .= ' <span title="'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]."[$intPropertyValueID][DESCRIPTION]").'" value="'.htmlspecialcharsEx($arOneValue["DESCRIPTION"]).'" size="18" type="text"></span>';

				$return .= '</td></tr>';
			}

			$return .= '<tr><td>';
			$return .= '<input type="text" name="'.htmlspecialcharsbx($strHTMLControlName["VALUE"]."[n0][VALUE]").'" size="'.$arProperty["COL_COUNT"].'" value="">';
			if (($arProperty["WITH_DESCRIPTION"]=="Y") && ('' != trim($strHTMLControlName["DESCRIPTION"])))
				$return .= ' <span title="'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_TITLE").'">'.GetMessage("IBLOCK_PROP_FILEMAN_DESCRIPTION_LABEL").':<input name="'.htmlspecialcharsEx($strHTMLControlName["DESCRIPTION"]."[n0][DESCRIPTION]").'" value="" size="18" type="text"></span>';
			$return .= '</td></tr>';

			$return .= '<tr><td><input type="button" value="'.GetMessage("IBLOCK_PROP_FILEMAN_ADD").'" onClick="addNewRow(\'tb'.$table_id.'\')"></td></tr>';
			return $return.'</table>';
		}
	}

	function ConvertToDB($arProperty, $value)
	{
		echo '<pre>';
		print_r(array($arProperty, $value));
		echo '</pre>';
		//die();
		$result = array();
		$return = array();
		if(is_array($value["VALUE"]))
		{
			$result["VALUE"] = $value["VALUE"]["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"]["VALUE"];
		}
		else
		{
			$result["VALUE"] = $value["VALUE"];
			$result["DESCRIPTION"] = $value["DESCRIPTION"];
		}
		$return["VALUE"] = trim($result["VALUE"]);
		$return["DESCRIPTION"] = trim($result["DESCRIPTION"]);
		return $return;
	}

	function ConvertFromDB($arProperty, $value)
	{
		$return = array();
		if (strLen(trim($value["VALUE"])) > 0)
			$return["VALUE"] = $value["VALUE"];
		if (strLen(trim($value["DESCRIPTION"])) > 0)
			$return["DESCRIPTION"] = $value["DESCRIPTION"];
		return $return;
	}
	function CheckFields($arProperty, $value)
	{
		echo '1';
		echo '<pre>';
		print_r(array($arProperty, $value));
		echo '</pre>';
		//die();
		return true;
	}
} 