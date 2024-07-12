<?php
/* setup includes */
require_once('includes/master.inc.php');

/* setup page */
define("PAGE_NAME", t("file_password_page_name", "File Password"));
define("PAGE_DESCRIPTION", t("file_password_meta_description", "Enter file password"));
define("PAGE_KEYWORDS", t("file_password_meta_keywords", "file, password, account, short, url, user"));

/* check password */
$file = file::loadByShortUrl($_REQUEST['file']);
if ((int) $_REQUEST['submitme'])
{
    // validation
    $filePassword = trim($_REQUEST['filePassword']);
    if(!strlen($filePassword))
    {
        setError(t("please_enter_the_file_password", "Please enter the file password."));
    }

    // create the account
    if (!isErrors())
    {
        if ($file)
        {
            // check password
            if(md5($filePassword) == $file->accessPassword)
            {
                $_SESSION['allowAccess'.$file->id] = true;
                redirect(addSessionId(file::getFileUrl($file->id)));
            }
            else
            {
                setError(t("file_password_is_invalid", "File password is invalid."));
            }
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
                <h2><?php echo t("file_password", "file password"); ?></h2>
            </div>
            <div>
                <p class="introText">
                    <?php echo t("file_password_intro_text", "A password is required to access this file, please enter it below."); ?>
                </p>
                <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/file_password.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container"><label for="filePassword">
                                <span class="field-name"><?php echo t("password", "password"); ?></span>
                                <input type="password" tabindex="1" value="" id="filePassword" name="filePassword" class="uiStyle" onFocus="showHideTip(this);"></label>
                            <div id="filePasswordTip" class="hidden formTip">
                                <?php echo t('the_file_password', 'The file password'); ?>
                            </div>
                        </li>

                        <li class="field-container">
                            <span class="field-name"></span>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("access_file", "access file"); ?>" class="submitInput" />
                        </li>
                    </ul>

                    <input type="hidden" value="1" name="submitme"/>
                    <input type="hidden" value="<?php echo safeOutputToScreen($_REQUEST['file']); ?>" name="file"/>
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