<?php
/* setup includes */
require_once('includes/master.inc.php');

/* setup page */
define("PAGE_NAME", t("forgot_password_page_name", "Forgot Password"));
define("PAGE_DESCRIPTION", t("forgot_password_meta_description", "Forgot account password"));
define("PAGE_KEYWORDS", t("forgot_password_meta_keywords", "forgot, password, account, short, url, user"));

/* register user */
if ((int) $_REQUEST['submitme'])
{
    // validation
    $emailAddress = trim(strtolower($_REQUEST['emailAddress']));
    if (!strlen($emailAddress))
    {
        setError(t("please_enter_your_email_address", "Please enter the account email address"));
    }
    else
    {
        $checkEmail = UserPeer::loadUserByEmailAddress($emailAddress);
        if (!$checkEmail)
        {
            // username exists
            setError(t("account_not_found", "Account with that email address not found"));
        }
    }

    // create the account
    if (!isErrors())
    {
        $userAccount = UserPeer::loadUserByEmailAddress($emailAddress);
        if ($userAccount)
        {
            // create password reset hash
            $resetHash = UserPeer::createPasswordResetHash($userAccount->id);

            $subject = t('forgot_password_email_subject', 'Password reset instructions for account on [[[SITE_NAME]]]', array('SITE_NAME' => SITE_CONFIG_SITE_NAME));

            $replacements = array(
                'FIRST_NAME'     => $userAccount->firstname,
                'SITE_NAME'      => SITE_CONFIG_SITE_NAME,
                'WEB_ROOT'       => WEB_ROOT,
                'USERNAME'       => $username,
                'PAGE_EXTENSION' => SITE_CONFIG_PAGE_EXTENSION,
                'ACCOUNT_ID'     => $userAccount->id,
                'RESET_HASH'     => $resetHash
            );
            $defaultContent  = "Dear [[[FIRST_NAME]]],<br/><br/>";
            $defaultContent .= "We've received a request to reset your password on [[[SITE_NAME]]] for account [[[USERNAME]]]. Follow the url below to set a new account password:<br/><br/>";
            $defaultContent .= "<a href='[[[WEB_ROOT]]]/forgot_password_reset.[[[PAGE_EXTENSION]]]?u=[[[ACCOUNT_ID]]]&h=[[[RESET_HASH]]]'>[[[WEB_ROOT]]]/forgot_password_reset.[[[PAGE_EXTENSION]]]?u=[[[ACCOUNT_ID]]]&h=[[[RESET_HASH]]]</a><br/><br/>";
            $defaultContent .= "If you didn't request a password reset, just ignore this email and your existing password will continue to work.<br/><br/>";
            $defaultContent .= "Regards,<br/>";
            $defaultContent .= "[[[SITE_NAME]]] Admin";
            $htmlMsg         = t('forgot_password_email_content', $defaultContent, $replacements);

            send_html_mail($emailAddress, $subject, $htmlMsg, SITE_CONFIG_DEFAULT_EMAIL_ADDRESS_FROM, strip_tags(str_replace("<br/>", "\n", $htmlMsg)));
            redirect(WEB_ROOT . "/forgot_password." . SITE_CONFIG_PAGE_EXTENSION . "?s=1");
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
                <h2><?php echo t("forgot_password", "forgot password"); ?></h2>
            </div>
            <div>
                <?php if (isset($_REQUEST['s'])): ?>
                    <p class="introText">
                        <?php echo t("forgot_password_sent_intro_text", "An email has been sent with further instructions on how to reset your password. Please check your email inbox."); ?>
                    </p>
                <?php else: ?>
                    <p class="introText">
                        <?php echo t("forgot_password_intro_text", "Enter your email address below to receive further instructions on how to reset your account password."); ?>
                    </p>
                    <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/forgot_password.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                        <ul>
                            <li class="field-container"><label for="emailAddress">
                                    <span class="field-name"><?php echo t("email_address", "email address"); ?></span>
                                    <input type="text" tabindex="1" value="<?php echo isset($emailAddress) ? safeOutputToScreen($emailAddress) : ''; ?>" id="emailAddress" name="emailAddress" class="uiStyle" onFocus="showHideTip(this);"></label>
                                <div id="emailAddressTip" class="hidden formTip">
                                    <?php echo t('your_registered_account_email_address', 'Your registered account email address'); ?>
                                </div>
                            </li>

                            <li class="field-container">
                                <span class="field-name"></span>
                                <input tabindex="99" type="submit" name="submit" value="<?php echo t("request_reset", "request reset"); ?>" class="submitInput" />
                            </li>
							
							<li>
                            <div class="form-content">
								<a href="<?php echo getCoreSitePath(); ?>/login.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php echo t("login_form", "login form"); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="<?php echo getCoreSitePath(); ?>/register.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php echo t("register", "register"); ?></a>
                            </div>
                        </li>
                        </ul>

                        <input type="hidden" value="1" name="submitme"/>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include_once("_bannerRightContent.inc.php"); ?>
    <div class="clear"><!-- --></div>

</div>

<?php
require_once('_footer.php');
?>