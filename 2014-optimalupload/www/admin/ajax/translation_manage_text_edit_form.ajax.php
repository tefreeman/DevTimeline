<?php

// includes and security
include_once('../_local_auth.inc.php');

$gTranslationId   = (int) $_REQUEST['gTranslationId'];

// prepare result
$result = array();
$result['error'] = false;
$result['msg'] = '';
$result['html'] = 'Could not load the form, please try again later.';

// load existing translation
$translation   = $db->getRow("SELECT language_content.id, language_content.content, language_key.languageKey, language_key.defaultContent FROM language_content LEFT JOIN language_key ON language_content.languageKeyId = language_key.id WHERE language_content.id = ".(int)$gTranslationId);
if(!$translation)
{
    $result['error'] = false;
    $result['msg'] = 'There was a problem loading the translation for editing, please try again later.';
}

$result['html']  = '<p style="padding-bottom: 4px;">Use the form below to update the translation.</p>';
$result['html'] .= '<span id="popupMessageContainer"></span>';
$result['html'] .= '<form id="updateConfigurationForm" class="form">';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>Key:</label>
                        <div class="input"><div class="info" style="margin-top: 6px; width:550px; font-weight: bold;">
                            '.adminFunctions::makeSafe($translation['languageKey']).'
                        </div></div>
                    </div>';
$result['html'] .= '<div class="clearfix" style="min-height:65px;">
                        <label>English Text:</label>
                        <div class="input"><div class="info" style="margin-top: 6px; width:550px; font-weight: bold;">
                            '.adminFunctions::makeSafe($translation['defaultContent']).'
                        </div></div>
                    </div>';

$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>Translation:</label>
                        <div class="input">
                            <textarea name="translated_content" id="translated_content" class="xxlarge">'.  adminFunctions::makeSafe($translation['content']).'</textarea>
                        </div>
                    </div>';
$result['html'] .= '<input id="translation_item_id" name="translation_item_id" value="'.$gTranslationId.'" type="hidden"/>';
$result['html'] .= '</form>';

echo json_encode($result);
exit;
