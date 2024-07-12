<?php
// setup includes
require_once('includes/master.inc.php');

// setup page
define("PAGE_NAME", t("contact_page_name", "Contact Us"));
define("PAGE_DESCRIPTION", t("contact_meta_description", "Contact us"));
define("PAGE_KEYWORDS", t("contact_meta_keywords", "contact, us, questions, queries, file, hosting"));

// success handling
if(isset($_REQUEST['s']))
{
    setSuccess(t('contact_success', 'Thanks for submitting the contact form on our site. We\'ll review the query as soon as possible and get back to your within the nexr 48 hours.'));
}

// prepare variables
$full_name = '';
$email_address = '';
$query = '';

// send report if submitted
if ((int) $_REQUEST['submitme'])
{
    $full_name = trim($_REQUEST['full_name']);
    $email_address = trim($_REQUEST['email_address']);
    $query = trim($_REQUEST['query']);

    if (strlen($full_name) == 0)
    {
        setError(t("contact_error_name", "Please enter your name."));
    }
    elseif (strlen($email_address) == 0)
    {
        setError(t("contact_error_email", "Please enter your email."));
    }
    elseif (valid_email($email_address) == false)
    {
        setError(t("contact_error_email_invalid", "Please enter a valid email address."));
    }
    elseif (strlen($query) == 0)
    {
        setError(t("contact_error_signature", "Please enter your query."));
    }
    
    // check captcha
    if((!isErrors()) && (SITE_CONFIG_CONTACT_FORM_SHOW_CAPTCHA == 'yes'))
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
    
    // send email to admin
    if(isErrors() == false)
    {
        // send email
        $subject = t('contact_email_subject', 'Contact form submission on [[[SITE_NAME]]]', array('SITE_NAME' => SITE_CONFIG_SITE_NAME));

        $replacements   = array(
            'FULL_NAME' => $full_name,
            'EMAIL_ADDRESS' => $email_address,
            'QUERY' => nl2br($query),
            'SITE_NAME'    => SITE_CONFIG_SITE_NAME,
            'WEB_ROOT'     => WEB_ROOT,
            'USERS_IP'     => getUsersIPAddress()
        );
        $defaultContent = "There has been a contact form submission from [[[SITE_NAME]]] with the following details:<br/><br/>";
        $defaultContent .= "***************************************<br/>";
        $defaultContent .= "Full Name: [[[FULL_NAME]]]<br/>";
        $defaultContent .= "Email Address: [[[EMAIL_ADDRESS]]]<br/><br/>";
        $defaultContent .= "[[[QUERY]]]<br/>";
        $defaultContent .= "***************************************<br/>";
        $defaultContent .= "Submitted IP: [[[USERS_IP]]]<br/>";
        $defaultContent .= "***************************************<br/><br/>";
        $htmlMsg        = t('contact_email_content', $defaultContent, $replacements);
            
        send_html_mail(SITE_CONFIG_SITE_CONTACT_FORM_EMAIL, $subject, $htmlMsg, SITE_CONFIG_DEFAULT_EMAIL_ADDRESS_FROM, strip_tags(str_replace("<br/>", "\n", $htmlMsg)), false, $full_name);
        redirect(WEB_ROOT.'/contact.'.SITE_CONFIG_PAGE_EXTENSION.'?s=1');        
    }
}
else
{
    if($Auth->loggedIn())
    {
        $full_name = $Auth->user->firstname.' '.$Auth->user->lastname;
        $email_address = $Auth->user->email;
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
    elseif (isSuccess())
    {
        echo outputSuccess();
    }
    ?>

    <!-- report abuse form -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("contact_us", "Contact Us"); ?></h2>
            </div>
            <div class="introText">
                <?php
                echo t('contact_intro', 'Please use the following form to contact us with any queries. Abuse reports should be sent via our <a href="[[[ABUSE_URL]]]">abuse pages</a>.', array('ABUSE_URL'=>WEB_ROOT.'/report_file.'.SITE_CONFIG_PAGE_EXTENSION));
                ?>
                <br/><br/>
                <form id="form-join" class="contactForm" method="post" action="<?php echo WEB_ROOT; ?>/contact.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container">
                            <label for="full_name">
                                <span class="field-name">
                                    <?php
                                    echo t('contact_full_name', 'Your full name');
                                    ?>:
                                </span>
                                <input name="full_name" type="text" value="<?php echo safeOutputToScreen($full_name); ?>"/>
                            </label>
                        </li>
                        
                        <li class="field-container">
                            <label for="email_address">
                                <span class="field-name">
                                    <?php
                                    echo t('contact_email_address', 'Email address');
                                    ?>:
                                </span>
                                <input name="email_address" type="text" value="<?php echo safeOutputToScreen($email_address); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="query">
                                <span class="field-name">
                                    <?php
                                    echo t('contact_your_query', 'Your query');
                                    ?>:
                                </span>
                                <textarea rows="10" id="query" name="query"><?php echo safeOutputToScreen($query); ?></textarea>
                            </label>
                        </li>
                        
                        <?php if(SITE_CONFIG_CONTACT_FORM_SHOW_CAPTCHA == 'yes'): ?>
                        <li class="field-container" style="height: auto; left: 118px;">
                            <label for="recaptcha_response_field">
                                <span class="field-name"><?php echo t("confirm_text", "Confirmation Text"); ?></span>
                            </label>
                            <div>
                                <?php echo outputCaptcha(); ?>
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php endif; ?>
                        
                        <li class="field-container" style="padding-top: 10px;">
                            <span class="field-name"></span>
                            <input name="submitme" type="hidden" value="1"/>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("contact_submit_form", "submit form"); ?>" class="submitInput" />
                        </li>
                    </ul>
                </form>

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