<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid()) {
	return;
}

global $errors;

if ($errors === false) {
	echo CAdminMessage::ShowNote(GetMessage('UNINSTALL_COMPLETED'));
} else {
	for ($i = 0; $i < count($errors); $i++) {
		$alErrors .= $errors[$i] . '<br>';
	}
	echo CAdminMessage::ShowMessage(Array('TYPE' => 'ERROR', 'MESSAGE' => GetMessage('MOD_UNINST_ERR'), 'DETAILS' => $alErrors, 'HTML' => true));
}
?>

<form action='<? echo $APPLICATION->GetCurPage() ?>'>
	<input type='hidden' name='lang' value='<? echo LANG ?>'/>
	<input type='submit' name='' value='<? echo GetMessage('MOD_BACK') ?>'/>
</form>