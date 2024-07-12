<?php

// includes and security
include_once('../_local_auth.inc.php');

if(isset($_REQUEST['languageId']))
{
    $languageId = (int)$_REQUEST['languageId'];
}

// defaults
$translation_name = '';
$translation_flag = '';

// is this an edit?
if($languageId)
{
    $language = $db->getRow("SELECT * FROM language WHERE id = ".(int)$languageId);
    if($language)
    {
        $translation_name = $language['languageName'];
        $translation_flag = $language['flag'];
    }
}

// load all flag icons
$flags = adminFunctions::getDirectoryList(ADMIN_ROOT.'/assets/images/icons/flags/', 'png');

// prepare result
$result = array();
$result['error'] = false;
$result['msg'] = '';
$result['html'] = 'Could not load the form, please try again later.';

$result['html']  = '<p style="padding-bottom: 4px;">Use the form below to add a new language. Once it\'s created, you can edit any of the text items into your preferred language.</p>';
$result['html'] .= '<span id="popupMessageContainer"></span>';
$result['html'] .= '<form id="addTranslationForm" class="form">';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>Language Name:</label>
                        <div class="input">
                            <input name="translation_name" id="translation_name" type="text" value="'.adminFunctions::makeSafe($translation_name).'" class="large"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>Language Flag:</label>
                        <div class="input">
                            <select name="translation_flag" id="translation_flag">
                                ';
                                foreach($flags AS $flag)
                                {
                                    $result['html'] .= '<option value="'.$flag.'" data-imagesrc="assets/images/icons/flags/'.$flag.'"';
                                    if($translation_flag.'.png' == $flag)
                                    {
                                        $result['html'] .= ' SELECTED';
                                    }
                                    $result['html'] .= '>'.$flag.'</option>';
                                }
                                $result['html'] .= '
                            </select>
                        </div>
                    </div>';
$result['html'] .= '<input name="translation_flag_hidden" id="translation_flag_hidden" type="hidden" value="'.adminFunctions::makeSafe($flag).'"/>';
$result['html'] .= '</form>';

echo json_encode($result);
exit;
