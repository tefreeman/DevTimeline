<?php
/* setup includes */
require_once('includes/master.inc.php');

// require login
$Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);

/* load user */
$user = UserPeer::loadUserById($Auth->id);
if (!$user)
{
    redirect(WEB_ROOT);
}

/* setup page */
define("PAGE_NAME", t("account_edit_page_name", "Account Details"));
define("PAGE_DESCRIPTION", t("account_edit_meta_description", "Account details"));
define("PAGE_KEYWORDS", t("account_edit_meta_keywords", "details, account, short, url, user"));

/* update user */
if (isset($_REQUEST['submitme']) && (int)$_REQUEST['submitme'])
{
    // validation
    $title = trim($_REQUEST['title']);
    $firstname = trim($_REQUEST['firstname']);
    $lastname = trim($_REQUEST['lastname']);
    $emailAddress = trim(strtolower($_REQUEST['emailAddress']));
    $password = trim($_REQUEST['password']);
    $languageId = null;
    if(isset($_REQUEST['languageId']))
    {
        $languageId = (int)$_REQUEST['languageId'];
    }
    $privateFileStatistics = (int)$_REQUEST['privateFileStatistics'];

    if (!strlen($title))
    {
        setError(t("please_enter_your_title", "Please enter your title"));
    }
    elseif (!strlen($firstname))
    {
        setError(t("please_enter_your_firstname", "Please enter your firstname"));
    }
    elseif (!strlen($lastname))
    {
        setError(t("please_enter_your_lastname", "Please enter your lastname"));
    }
    elseif (!strlen($emailAddress))
    {
        setError(t("please_enter_your_email_address", "Please enter your email address"));
    }
    elseif (!valid_email($emailAddress))
    {
        setError(t("your_email_address_is_invalid", "Your email address is invalid"));
    }
    elseif(_CONFIG_DEMO_MODE == true)
    {
        setError(t("no_changes_in_demo_mode"));
    }
    else
    {
        $checkEmail = UserPeer::loadUserByEmailAddress($emailAddress);
        if (($checkEmail) && ($checkEmail->id != $Auth->id))
        {
            // username exists
            setError(t("email_address_already_exists", "Email address already exists on another account"));
        }
        else
        {
            // check password if one set
            if(strlen($password))
            {
                if ((strlen($password) < 6) || (strlen($password) > 20))
                {
                    setError(t("password_length_incorrect", "Password should be between 6 - 20 characters in length"));
                }
                elseif (containsInvalidCharacters(strtolower($password), 'abcdefghijklmnopqrstuvwxyz1234567890@~#!-_£$&*()^%}{()'))
                {
                    setError(t("password_contains_illegal_characters", "Password contains invalid characters, please choose another."));
                }
            }
        }
    }

    // update the account
    if (!isErrors())
    {
        $db = Database::getDatabase(true);
        $rs = $db->query('UPDATE users SET title = :title, firstname = :firstname, lastname = :lastname, email = :email, languageId = :languageId, privateFileStatistics = :privateFileStatistics WHERE id = :id', array('title' => $title, 'firstname' => $firstname, 'lastname' => $lastname, 'email' => $emailAddress, 'id' => $Auth->id, 'languageId' => $languageId, 'privateFileStatistics' => $privateFileStatistics));
        if ($rs)
        {
            // do password
            if(strlen($password))
            {
                $rs = $db->query('UPDATE users SET password = :password WHERE id = :id', array('password' => MD5($password), 'id' => $Auth->id));
            }
            
            // reset site language if updated
            if($languageId != null)
            {
                $languageName = $db->getValue("SELECT languageName FROM language WHERE isActive = 1 AND id = " . (int)$languageId . " LIMIT 1");
                if($languageName)
                {
                    $_SESSION['_t'] = $languageName;
                }
            }
            
            setSuccess(t("account_updated_success_message", "Account details successfully updated"));
        }
        else
        {
            setError(t("problem_creating_your_account_try_again_later", "There was a problem creating your account, please try again later"));
        }
    }
}
else
{
    $title = $user->title;
    $firstname = $user->firstname;
    $lastname = $user->lastname;
    $emailAddress = $user->email;
    $languageId = $user->languageId;
    if($languageId == null)
    {
        $languageId = $db->getValue("SELECT id FROM language WHERE isActive = 1 AND languageName = '" . $db->escape(trim($_REQUEST['_t'])) . "' LIMIT 1");
    }
    $privateFileStatistics = $user->privateFileStatistics;
}

require_once('_header.php');
?>

<div class="contentPageWrapper">

    <?php
    if (isSuccess())
    {
        echo outputSuccess();
    }
    elseif (isErrors())
    {
        echo outputErrors();
    }
    ?>

    <!-- register form -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("account_details", "Account Details"); ?></h2>
            </div>
            <div>
                <p class="introText">
                    <?php t('keep_your_account_details_up_to_date_below', 'Keep your account details up to date below.'); ?>
                </p>
                <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/account_edit.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container">
                            <label for="title">
                                <span class="field-name"><?php echo t("title", "Title"); ?></span>
                                <select autofocus="autofocus" tabindex="1" id="title" name="title" onFocus="showHideTip(this);">
                                    <option value="Mr" <?php echo ($title == 'Mr')?'SELECTED':''; ?>><?php echo t('title_mr', 'Mr'); ?></option>
                                    <option value="Mrs" <?php echo ($title == 'Mrs')?'SELECTED':''; ?>><?php echo t('title_mrs', 'Mrs'); ?></option>
                                    <option value="Miss" <?php echo ($title == 'Miss')?'SELECTED':''; ?>><?php echo t('title_miss', 'Miss'); ?></option>
                                    <option value="Dr" <?php echo ($title == 'Dr')?'SELECTED':''; ?>><?php echo t('title_dr', 'Dr'); ?></option>
                                    <option value="Pro" <?php echo ($title == 'Pro')?'SELECTED':''; ?>><?php echo t('title_pro', 'Pro'); ?></option>
                                </select>
                            </label>
                            <div id="titleTip" class="hidden formTip">
                                <?php echo t('your_title', 'Your title'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="firstname">
                                <span class="field-name"><?php echo t("firstname", "Firstname"); ?></span>
                                <input type="text" tabindex="2" value="<?php echo isset($firstname) ? safeOutputToScreen($firstname) : ''; ?>" id="firstname" name="firstname" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="firstnameTip" class="hidden formTip">
                                <?php echo t('your_firstname', 'Your firstname'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="lastname">
                                <span class="field-name"><?php echo t("lastname", "Lastname"); ?></span>
                                <input type="text" tabindex="3" value="<?php echo isset($lastname) ? safeOutputToScreen($lastname) : ''; ?>" id="lastname" name="lastname" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="lastnameTip" class="hidden formTip">
                                <?php echo t('your_lastname', 'Your lastname'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="emailAddress">
                                <span class="field-name"><?php echo t("email_address", "Email Address"); ?></span>
                                <input type="text" tabindex="4" value="<?php echo isset($emailAddress) ? safeOutputToScreen($emailAddress) : ''; ?>" id="emailAddress" name="emailAddress" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="emailAddressTip" class="hidden formTip">
                                <?php echo t('your_new_email_address', 'Your new email address'); ?>
                            </div>
                        </li>

                        <li class="field-container" style="margin-bottom: 20px;"><label for="password">
                                <span class="field-name"><?php echo t("change_password", "Change Password"); ?></span>
                                <input type="password" tabindex="5" value="" id="password" name="password" class="uiStyle" onFocus="showHideTip(this);" autocomplete="off"></label>
                            <div id="passwordTip" class="hidden formTip">
                                <?php echo t('a_new_account_password_leave_blank_to_keep', 'Optional. A new account password, leave this blank to keep your existing.'); ?>
                            </div>
                        </li>

                        <?php
                        // language selector
                        if(SITE_CONFIG_LANGUAGE_USER_SELECT_LANGUAGE == 'yes')
                        {
                            $activeLanguages = $db->getRows("SELECT id, languageName FROM language WHERE isActive = 1 ORDER BY languageName ASC");
                            if (COUNT($activeLanguages))
                            {
                                ?>
                                <li class="field-container">
                                    <label for="languageId">
                                        <span class="field-name"><?php echo t("language", "Language"); ?></span>
                                        <select tabindex="6" id="languageId" name="languageId" onFocus="showHideTip(this);">
                                        <?php
                                        foreach ($activeLanguages AS $activeLanguage)
                                        {
                                            echo '<option value="'.$activeLanguage['id'].'"';
                                            if($languageId == $activeLanguage['id'])
                                            {
                                                echo ' SELECTED';
                                            }
                                            echo '>'.  safeOutputToScreen($activeLanguage['languageName']).'</option>';
                                        }
                                        ?>
                                        </select>
                                    </label>
                                    <div id="languageIdTip" class="hidden formTip">
                                        <?php echo t('settings_tip_site_language', 'The language to use on the site.'); ?>
                                    </div>
                                </li>
                                <?php
                            }
                        }
                        ?>
                                
                        <li class="field-container">
                            <label for="privateFileStatistics">
                                <span class="field-name"><?php echo t("file_statistics", "File Statistics"); ?></span>
                                <select tabindex="6" id="privateFileStatistics" name="privateFileStatistics" onFocus="showHideTip(this);">
                                <?php
                                $optionsArr = array('0'=>t('settings_statistics_public', 'Publicly Accessible (using ~s on url)'), '1'=>t('settings_statistics_private', 'Private (only via your account)'));
                                foreach ($optionsArr AS $k=>$option)
                                {
                                    echo '<option value="'.$k.'"';
                                    if($privateFileStatistics == $k)
                                    {
                                        echo ' SELECTED';
                                    }
                                    echo '>'.  safeOutputToScreen($option).'</option>';
                                }
                                ?>
                                </select>
                            </label>
                            <div id="privateFileStatisticsTip" class="hidden formTip">
                                <?php echo t('settings_tip_file_statistics', 'Whether to keep all file stats private or allow public access.'); ?>
                            </div>
                        </li>
                            
                        <li class="field-container">
                            <span class="field-name"></span>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("update_account", "update account"); ?>" class="submitInput" />
                        </li>
                    </ul>

                    <input type="hidden" value="1" name="submitme"/>
                </form>
            </div>
        </div>
    </div>
    
    <?php include_once("_bannerRightContent.inc.php"); ?>
    <div class="clear"><!-- --></div>
    
</div>

<?php
require_once('_footer.php');
?>