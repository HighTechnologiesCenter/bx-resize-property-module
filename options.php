<?php
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

CJSCore::Init(array('jquery'));

global $APPLICATION, $right, $Apply, $REQUEST_METHOD, $RestoreDefaults;
$moduleId = 'customprojectmodule';
$right = $APPLICATION->GetGroupRight($moduleId);

if ($right >= 'R') {
	Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/modules/main/options.php');

	$generalOptions = array(

	);

    $allOptions = array(
		$generalOptions,
    );

    $tabs = array(
		array(
			'DIV' => 'edit5',
			'TAB' => Loc::getMessage('MODULE_RIGHTS_TAB'),
			'ICON' => '',
			'TITLE' => Loc::getMessage('MODULE_RIGHTS_TAB_TITLE'),
		),

    );
	
    $tabControl = new CAdminTabControl('tabControl', $tabs);

    CModule::IncludeModule($moduleId);

    if ($REQUEST_METHOD == 'POST' && strlen($Update . $Apply . $RestoreDefaults) > 0 && $right == 'W' && check_bitrix_sessid()) {
        if (strlen($RestoreDefaults) > 0) {
            COption::RemoveOption($moduleId);
        } else {
            foreach ($allOptions as $optionsSection) {
				foreach ($optionsSection as $option) {
					$name = $option['ID'];
					$value = trim($_REQUEST[$name]);

					if ($option['TYPE'] == 'checkbox' && $value != 'Y') {
						$value = 'N';
					}
					COption::SetOptionString($moduleId, $name, $value, $option['MESSAGE']);
				}
            }
        }

        ob_start();
        $Update = $Update . $Apply;
        require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php');
        ob_end_clean();

        if (strlen($_REQUEST['back_url_settings']) > 0) {
            if ((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0)) {
                LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode($moduleId) . '&lang=' . urlencode(LANGUAGE_ID) . '&back_url_settings=' . urlencode($_REQUEST['back_url_settings']) . '&' . $tabControl->ActiveTabParam());
            } else {
                LocalRedirect($_REQUEST['back_url_settings']);
            }
        } else {
            LocalRedirect($APPLICATION->GetCurPage() . '?mid=' . urlencode($moduleId) . '&lang=' . urlencode(LANGUAGE_ID) . '&' . $tabControl->ActiveTabParam());
        }
    }

    ?>
    <form method='post' name='<?=$moduleId?>_opt_form'  action='<?=$APPLICATION->GetCurPage()?>?mid=<?=urlencode($moduleId)?>&amp;lang=<?=LANGUAGE_ID?>'>

		<?php
        $tabControl->Begin();
        $tabControl->BeginNextTab();
        require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/admin/group_rights.php');
        $tabControl->Buttons();
        ?>
        <input <?php if ($right<'W') echo 'disabled' ?> type='submit' name='Update' value='<?=Loc::getMessage('MAIN_SAVE')?>' title='<?=Loc::getMessage('MAIN_OPT_SAVE_TITLE')?>' class='adm-btn-save'>
        <input <?php if ($right<'W') echo 'disabled' ?> type='submit' name='Apply' value='<?=Loc::getMessage('MAIN_OPT_APPLY')?>' title='<?=Loc::getMessage('MAIN_OPT_APPLY_TITLE')?>'>
        <?php if (strlen($_REQUEST['back_url_settings']) > 0):?>
            <input <?php if ($right<'W') echo 'disabled' ?> type='button' name='Cancel' value='<?=Loc::getMessage('MAIN_OPT_CANCEL')?>' title='<?=Loc::getMessage('MAIN_OPT_CANCEL_TITLE')?>' onclick='window.location='<?=htmlspecialcharsbx(CUtil::addslashes($_REQUEST['back_url_settings']))?>''>
            <input type='hidden' name='back_url_settings' value='<?=htmlspecialcharsbx($_REQUEST['back_url_settings'])?>'>
        <?php endif?>
        <input type='submit' name='RestoreDefaults' title='<?=Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS')?>' OnClick='confirm('<?=AddSlashes(Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>')' value='<?=Loc::getMessage('MAIN_RESTORE_DEFAULTS')?>'>
        <?php
        echo bitrix_sessid_post();
        $tabControl->End();
        ?>
    </form>

    <?php
    if (!empty($notes)) {
        echo BeginNote();
        foreach($notes as $key => $str) {
            echo '<span class="required"><sup>' . ($key + 1) . '</sup></span>' . $str . '<br>';
        }
        echo EndNote();
    }
    ?>
<?php } ?>
