<?php
/* setup includes */
require_once('includes/master.inc.php');

// make sure user registration is enabled
if(SITE_CONFIG_ENABLE_USER_REGISTRATION == 'no')
{
    redirect(WEB_ROOT);
}

/* setup page */
define("PAGE_NAME", t("register_page_name", "Register"));
define("PAGE_DESCRIPTION", t("register_meta_description", "Register for an account"));
define("PAGE_KEYWORDS", t("register_meta_keywords", "register, account, short, url, user"));

/* register user */
if ((int) $_REQUEST['submitme'])
{
    // validation
    $title               = trim($_REQUEST['title']);
    $firstname           = trim($_REQUEST['firstname']);
    $lastname            = trim($_REQUEST['lastname']);
    $emailAddress        = trim(strtolower($_REQUEST['emailAddress']));
    $emailAddressConfirm = trim(strtolower($_REQUEST['emailAddressConfirm']));
    $username            = trim(strtolower($_REQUEST['username']));

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
    elseif ($emailAddress != $emailAddressConfirm)
    {
        setError(t("your_email_address_confirmation_does_not_match", "Your email address confirmation does not match"));
    }
    elseif (!valid_email($emailAddress))
    {
        setError(t("your_email_address_is_invalid", "Your email address is invalid"));
    }
    elseif (!strlen($username))
    {
        setError(t("please_enter_your_preferred_username", "Please enter your preferred username"));
    }
    elseif ((strlen($username) < 6) || (strlen($username) > 20))
    {
        setError(t("username_must_be_between_6_and_20_characters", "Your username must be between 6 and 20 characters"));
    }
    elseif (!valid_username($username))
    {
        setError(t("your_username_is_invalid", "Your username can only contact alpha numeric and underscores."));
    }
    else
    {
        $checkEmail = UserPeer::loadUserByEmailAddress($emailAddress);
        if ($checkEmail)
        {
            // username exists
            setError(t("email_address_already_exists", "Email address already exists on another account"));
        }
        else
        {
            $checkUser = UserPeer::loadUserByUsername($username);
            if ($checkUser)
            {
                // username exists
                setError(t("username_already_exists", "Username already exists on another account"));
            }
        }
    }

    // make sure the username is not reserved
    if (!isErrors())
    {
        if (strlen(SITE_CONFIG_RESERVED_USERNAMES))
        {
            $reservedUsernames = explode("|", SITE_CONFIG_RESERVED_USERNAMES);
            if (in_array($username, $reservedUsernames))
            {
                // username is reserved
                setError(t("username_is_reserved", "Username is reserved and can not be used, please choose another"));
            }
        }
    }

    // check captcha
    if((!isErrors()) && (SITE_CONFIG_REGISTER_FORM_SHOW_CAPTCHA == 'yes'))
    {
        if (!isset($_REQUEST['recaptcha_response_field']))
        {
            setError(t("invalid_captcha", "Captcha confirmation text is invalid."));
        }
        else
        {
            $rs = captchaCheck($_POST["recaptcha_challenge_field"], $_POST["recaptcha_response_field"]);
            if (!$rs)
            {
                setError(t("invalid_captcha", "Captcha confirmation text is invalid."));
            }
        }
    }

    // create the account
    if (!isErrors())
    {
        $newPassword = createPassword();
        $newUser     = UserPeer::create($username, $newPassword, $emailAddress, $title, $firstname, $lastname);
        if ($newUser)
        {
            $subject = t('register_user_email_subject', 'Account details for [[[SITE_NAME]]]', array('SITE_NAME' => SITE_CONFIG_SITE_NAME));

            $replacements = array(
                'FIRST_NAME'    => $firstname,
                'SITE_NAME'     => SITE_CONFIG_SITE_NAME,
                'WEB_ROOT'      => WEB_ROOT,
                'USERNAME'      => $username,
                'PASSWORD'      => $newPassword
            );
            $defaultContent = "Dear [[[FIRST_NAME]]],<br/><br/>";
            $defaultContent .= "Your account on [[[SITE_NAME]]] has been created. Use the details below to login to your new account:<br/><br/>";
            $defaultContent .= "<strong>Url:</strong> <a href='[[[WEB_ROOT]]]'>[[[WEB_ROOT]]]</a><br/>";
            $defaultContent .= "<strong>Username:</strong> [[[USERNAME]]]<br/>";
            $defaultContent .= "<strong>Password:</strong> [[[PASSWORD]]]<br/><br/>";
            $defaultContent .= "Feel free to contact us if you need any support with your account.<br/><br/>";
            $defaultContent .= "Regards,<br/>";
            $defaultContent .= "[[[SITE_NAME]]] Admin";
            $htmlMsg        = t('register_user_email_content', $defaultContent, $replacements);

            send_html_mail($emailAddress, $subject, $htmlMsg, SITE_CONFIG_DEFAULT_EMAIL_ADDRESS_FROM, strip_tags(str_replace("<br/>", "\n", $htmlMsg)));

            // if we came from a file
            if (isset($_REQUEST['f']))
            {
                // upgrades
                redirect(WEB_ROOT . "/upgrade." . SITE_CONFIG_PAGE_EXTENSION . "?f=" . urlencode($_REQUEST['f']) . "&i=" . urlencode($newUser->identifier));
            }
            else
            {
                // for non upgrades
                redirect(WEB_ROOT . "/register_complete." . SITE_CONFIG_PAGE_EXTENSION);
            }
        }
        else
        {
            setError(t("problem_creating_your_account_try_again_later", "There was a problem creating your account, please try again later"));
        }
    }
}

require_once('_header.php');
?>

<div class="contentPageWrapper">

    <?php
    if (isErrors())
    {
        echo outputErrors();
    }
    ?>

    <!-- register form -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("register_account", "Register Account"); ?></h2>
            </div>
            <div>
                <p class="introText">
                    <?php echo t('please_enter_you_information_below_to_register_for_an_account', 'Please enter your information below to register for an account. Your new account password will be sent to your email address.'); ?>
                </p>
                <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/register.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container">
                            <label for="title">
                                <span class="field-name"><?php echo t("title", "title"); ?></span>
                                <select autofocus="autofocus" tabindex="1" id="title" name="title" class="uiStyle" onFocus="showHideTip(this);">
                                    <option value="Mr" <?php echo ($title == 'Mr') ? 'SELECTED' : ''; ?>><?php echo t('title_mr', 'Mr'); ?></option>
                                    <option value="Mrs" <?php echo ($title == 'Mrs') ? 'SELECTED' : ''; ?>><?php echo t('title_mrs', 'Mrs'); ?></option>
                                    <option value="Miss" <?php echo ($title == 'Miss') ? 'SELECTED' : ''; ?>><?php echo t('title_miss', 'Miss'); ?></option>
                                    <option value="Dr" <?php echo ($title == 'Dr') ? 'SELECTED' : ''; ?>><?php echo t('title_dr', 'Dr'); ?></option>
                                    <option value="Pro" <?php echo ($title == 'Pro') ? 'SELECTED' : ''; ?>><?php echo t('title_pro', 'Pro'); ?></option>
                                </select>
                            </label>
                            <div id="titleTip" class="hidden formTip">
                                <?php echo t('your_title', 'Your title'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="firstname">
                                <span class="field-name"><?php echo t("firstname", "firstname"); ?></span>
                                <input type="text" tabindex="1" value="<?php echo isset($firstname) ? safeOutputToScreen($firstname) : ''; ?>" id="firstname" name="firstname" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="firstnameTip" class="hidden formTip">
                                <?php echo t('your_firstname', 'Your firstname'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="lastname">
                                <span class="field-name"><?php echo t("lastname", "lastname"); ?></span>
                                <input type="text" tabindex="1" value="<?php echo isset($lastname) ? safeOutputToScreen($lastname) : ''; ?>" id="lastname" name="lastname" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="lastnameTip" class="hidden formTip">
                                <?php echo t('your_lastname', 'Your lastname'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="emailAddress">
                                <span class="field-name"><?php echo t("email_address", "email address"); ?></span>
                                <input type="text" tabindex="1" value="<?php echo isset($emailAddress) ? safeOutputToScreen($emailAddress) : ''; ?>" id="emailAddress" name="emailAddress" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="emailAddressTip" class="hidden formTip">
                                <?php echo t('your_email_address', 'Your email address'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="emailAddressConfirm">
                                <span class="field-name"><?php echo t("email_address_confirm", "Email Confirm"); ?></span>
                                <input type="text" tabindex="2" value="<?php echo isset($emailAddressConfirm) ? safeOutputToScreen($emailAddressConfirm) : ''; ?>" id="emailAddressConfirm" name="emailAddressConfirm" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="emailAddressConfirmTip" class="hidden formTip">
                                <?php echo t('confirm_your_email_address', 'Confirm your email address'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="username">
                                <span class="field-name"><?php echo t("username", "username"); ?></span>
                                <input type="text" tabindex="3" value="<?php echo isset($username) ? safeOutputToScreen($username) : ''; ?>" id="username" name="username" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="usernameTip" class="hidden formTip">
                                <?php echo t('your_account_username', 'Your account username. 6 characters or more and alpha numeric.'); ?>
                            </div>
                        </li>
                        
                        <?php if(SITE_CONFIG_REGISTER_FORM_SHOW_CAPTCHA == 'yes'): ?>
                        <li class="field-container" style="height: auto; padding-bottom: 10px; left: 118px;">
                            <label for="recaptcha_response_field">
                                <span class="field-name"><?php echo t("confirm_text", "Confirmation Text"); ?></span>
                            </label>
                            <div>
                                <?php echo outputCaptcha(); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php endif; ?>

                        <li class="field-container">
                            <span class="field-name"></span>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo isset($_REQUEST['f']) ? t("proceed_to_payment", "proceed to payment") : t("register", "register"); ?>" class="submitInput" />
                        </li>
                    </ul>

                    <?php
                    if (isset($_REQUEST['f']))
                    {
                        echo '<input type="hidden" value="' . htmlentities(trim($_REQUEST['f'])) . '" name="f"/>';
                    }
                    ?>
                    <input type="hidden" value="1" name="submitme"/>
                </form>

                <div class="disclaimer">
                    <?php echo t('by_clicking_register_you_agree_to_our_terms', "By clicking 'register', you agree to our <a href='terms.[[[SITE_CONFIG_PAGE_EXTENSION]]]'>Terms of service</a>.", array('SITE_CONFIG_PAGE_EXTENSION'=>SITE_CONFIG_PAGE_EXTENSION)); ?>
                </div>
                <div class="clear"></div>
            </div>
        </div>
    </div>

    <?php include_once("_bannerRightContent.inc.php"); ?>
    <div class="clear"><!-- --></div>

</div>

<?php
require_once('_footer.php');
?>