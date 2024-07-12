<?php
// setup includes
require_once('includes/master.inc.php');

// setup page
define("PAGE_NAME", t("report_abuse_page_name", "Report Abuse"));
define("PAGE_DESCRIPTION", t("report_abuse_meta_description", "Report Abuse or Copyright Infringement"));
define("PAGE_KEYWORDS", t("report_abuse_meta_keywords", "report, abuse, copyright, infringement, file, hosting"));

// success handling
if(isset($_REQUEST['s']))
{
    setSuccess(t('report_file_success', 'Thanks for submitting the information needed to report a file on our site. We\'ll review the file as soon as possible and remove if required.'));
}

// prepare variables
$file_url = '';
$other_information = '';
$reported_by_name = '';
$reported_by_email = '';
$reported_by_address = '';
$reported_by_telephone_number = '';
$digital_signature = '';
$confirm_1 = '';
$confirm_2 = '';

// send report if submitted
if ((int) $_REQUEST['submitme'])
{
    $file_url = trim($_REQUEST['file_url']);
    $other_information = trim($_REQUEST['other_information']);
    $reported_by_name = trim($_REQUEST['reported_by_name']);
    $reported_by_email = strtolower(trim($_REQUEST['reported_by_email']));
    $reported_by_address = trim($_REQUEST['reported_by_address']);
    $reported_by_telephone_number = trim($_REQUEST['reported_by_telephone_number']);
    $digital_signature = trim($_REQUEST['digital_signature']);
    $confirm_1 = trim($_REQUEST['confirm_1']);
    $confirm_2 = trim($_REQUEST['confirm_2']);

    if (strlen($file_url) == 0)
    {
        setError(t("report_abuse_error_no_url", "Please enter the url of the file you're reporting."));
    }
    elseif (strlen($other_information) == 0)
    {
        setError(t("report_abuse_error_description", "Please enter the description and support information of the reported file."));
    }
    elseif (strlen($reported_by_name) == 0)
    {
        setError(t("report_abuse_error_name", "Please enter your name."));
    }
    elseif (strlen($reported_by_email) == 0)
    {
        setError(t("report_abuse_error_email", "Please enter your email."));
    }
    elseif (strlen($digital_signature) == 0)
    {
        setError(t("report_abuse_error_signature", "Please provide the electronic signature of yourself or the copyright owner."));
    }
    elseif ($confirm_1 != 'yes')
    {
        setError(t("report_abuse_error_confirm_1", "Please confirm you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law."));
    }
    elseif ($confirm_2 != 'yes')
    {
        setError(t("report_abuse_error_confirm_2", "Please confirm the information in the notification is accurate, and, under the pains and penalties of perjury, that you are authorized to act on behalf of the copyright owner."));
    }
    
    // check file url is active and exists
    if(isErrors() == false)
    {
        // break apart url
        $file = file::loadByFullUrl($file_url);
        if(!$file)
        {
            setError(t("report_abuse_error_could_not_find_file", "Could not find a file with that url, please check and try again."));
        }
        else
        {
            // make sure the file is active
            if($file->statusId != 1)
            {
                setError(t("report_abuse_error_file_not_active", "The file url you've set is not active."));
            }
        }
    }
    
    // add to database and send email to admin
    if(isErrors() == false)
    {
        // add to database
        $dbInsert = new DBObject("file_report",
                        array("file_id", "report_date", "reported_by_name",
                            "reported_by_email", "reported_by_address", "reported_by_telephone_number", "digital_signature",
                            "report_status", "reported_by_ip", "other_information")
        );
        $dbInsert->file_id = $file->id;
        $dbInsert->report_date = sqlDateTime();
        $dbInsert->reported_by_name = $reported_by_name;
        $dbInsert->reported_by_email = $reported_by_email;
        $dbInsert->reported_by_address = $reported_by_address;
        $dbInsert->reported_by_telephone_number = $reported_by_telephone_number;
        $dbInsert->digital_signature = $digital_signature;
        $dbInsert->report_status = 'pending';
        $dbInsert->reported_by_ip = getUsersIPAddress();
        $dbInsert->other_information = $other_information;
        if ($dbInsert->insert())
        {
            // send email
            $subject = t('report_file_email_subject', 'New abuse report on [[[SITE_NAME]]]', array('SITE_NAME' => SITE_CONFIG_SITE_NAME));

            $replacements   = array(
                'FILE_DETAILS' => $file_url,
                'SITE_NAME'    => SITE_CONFIG_SITE_NAME,
                'WEB_ROOT'     => WEB_ROOT,
                'USERS_IP'     => getUsersIPAddress()
            );
            $defaultContent = "There is a new abuse report on [[[SITE_NAME]]] with the following details:<br/><br/>";
            $defaultContent .= "***************************************<br/>";
            $defaultContent .= "[[[FILE_DETAILS]]]<br/>";
            $defaultContent .= "***************************************<br/>";
            $defaultContent .= "Submitted IP: [[[USERS_IP]]]<br/>";
            $defaultContent .= "***************************************<br/><br/>";
            $defaultContent .= "Please login via [[[WEB_ROOT]]]/admin/ to investigate further.";
            $htmlMsg        = t('report_file_email_content', $defaultContent, $replacements);

            send_html_mail(SITE_CONFIG_REPORT_ABUSE_EMAIL, $subject, $htmlMsg, SITE_CONFIG_REPORT_ABUSE_EMAIL, strip_tags(str_replace("<br/>", "\n", $htmlMsg)));
            redirect(WEB_ROOT.'/report_file.'.SITE_CONFIG_PAGE_EXTENSION.'?s=1');
        }
        else
        {
            setError(t("report_abuse_error_failed_reporting", "Failed reporting file, please try again later"));
        }
        
    }
}
else
{
    // if url has been passed
    if(isset($_REQUEST['file_url']))
    {
        $file_url = trim($_REQUEST['file_url']);
    }
    
    // if user logged in
    if($Auth->loggedIn())
    {
        $reported_by_email = $Auth->user->email;
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
                <h2><?php echo t("report_file", "Report File"); ?></h2>
            </div>
            <div class="introText">
                <?php
                echo t('report_file_intro', 'Please use the following form to report any copyright infringements ensuring you supply all the following information:');
                ?>
                <br/><br/>
                <form id="form-join" class="international" method="post" action="<?php echo WEB_ROOT; ?>/report_file.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container">
                            <label for="file_url">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_full_file_url', 'Full file url');
                                    ?>:
                                </span>
                                <input name="file_url" type="text" value="<?php echo safeOutputToScreen($file_url); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="other_information">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_description_of_the_works', 'Description of the copyrighted works and supporting information');
                                    ?>:
                                </span>
                                <textarea rows="4" id="other_information" name="other_information"><?php echo safeOutputToScreen($other_information); ?></textarea>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="reported_by_name">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_your_name', 'Your name');
                                    ?>:
                                </span>
                                <input name="reported_by_name" type="text" class="medium" value="<?php echo safeOutputToScreen($reported_by_name); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="reported_by_email">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_email_address', 'Email address');
                                    ?>:
                                </span>
                                <input name="reported_by_email" type="text" value="<?php echo safeOutputToScreen($reported_by_email); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="reported_by_address">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_postal_address', 'Postal address');
                                    ?>:
                                </span>
                                <input name="reported_by_address" type="text" value="<?php echo safeOutputToScreen($reported_by_address); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="reported_by_telephone_number">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_phone_number', 'Phone number');
                                    ?>:
                                </span>
                                <input name="reported_by_telephone_number" type="text" value="<?php echo safeOutputToScreen($reported_by_telephone_number); ?>"/>
                            </label>
                        </li>

                        <li class="field-container">
                            <label for="digital_signature">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_signature', 'Signature');
                                    ?>:
                                </span>
                                <input name="digital_signature" type="text" class="medium" value="<?php echo safeOutputToScreen($digital_signature); ?>"/><br/>
                                <?php
                                echo '<span style="color: #999;">'.t('report_file_electronic_signature_of_the_copyright', 'Electronic signature of the copyright owner or the person authorized to act on its behalf').'</span>';
                                ?>
                            </label>
                        </li>

                        <li class="field-container" style="margin-top:16px;">
                            <label for="confirm_1">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_confirm_1', 'Confirm 1');
                                    ?>:
                                </span>
                                <?php
                                echo t('report_file_you_have_a_good_faith_belief', 'You have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.');
                                ?>&nbsp;
                                <select name="confirm_1">
                                    <?php
                                    $opts = array('no'=>t('report_file_no', 'no'), 'yes'=>t('report_file_yes', 'yes'));
                                    foreach ($opts AS $k=>$opt)
                                    {
                                        echo '<option value="' . $k . '"';
                                        if ($confirm_1 == $k)
                                        {
                                            echo ' SELECTED';
                                        }
                                        echo '>';
                                        echo UCWords($opt);
                                        echo '</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                        </li>

                        <li class="field-container" style="margin-top:16px;">
                            <label for="confirm_2">
                                <span class="field-name">
                                    <?php
                                    echo t('report_file_confirm_2', 'Confirm 2');
                                    ?>:
                                </span>
                                <?php
                                echo t('report_file_the_information_in_this_noticiation', 'The information in the notification is accurate, and, under the pains and penalties of perjury, that you are authorized to act on behalf of the copyright owner.');
                                ?>&nbsp;
                                <select name="confirm_2">
                                    <?php
                                    $opts = array('no'=>t('report_file_no', 'no'), 'yes'=>t('report_file_yes', 'yes'));
                                    foreach ($opts AS $k=>$opt)
                                    {
                                        echo '<option value="' . $k . '"';
                                        if ($confirm_2 == $k)
                                        {
                                            echo ' SELECTED';
                                        }
                                        echo '>';
                                        echo UCWords($opt);
                                        echo '</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                        </li>

                        <li class="field-container">
                            <span class="field-name"></span>
                            <input name="submitme" type="hidden" value="1"/>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("submit_report", "submit report"); ?>" class="submitInput" />
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