<?php if(!check_bitrix_sessid()) return;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

echo CAdminMessage::ShowNote(Loc::getMessage('MODULE_INSTALL_COMPLETED')); ?>

<form action='<?echo $APPLICATION->GetCurPage()?>'>
	<input type='hidden' name='lang' value='<?echo LANG?>'>
	<input type='submit' name='' value='<?echo GetMessage('MOD_BACK')?>'>
<form>