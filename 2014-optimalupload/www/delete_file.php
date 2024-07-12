<?php
/* setup includes */
require_once('includes/master.inc.php');

/* setup page */
define("PAGE_NAME", t("delete_file_page_name", "Delete File"));
define("PAGE_DESCRIPTION", t("delete_file_meta_description", "Delete File"));
define("PAGE_KEYWORDS", t("delete_file_meta_keywords", "delete, remove, file"));

// load file
$file = null;
if (isset($_REQUEST['u']))
{
    // figure out the delete hash
    $deleteHash = '';
    foreach($_REQUEST AS $k=>$item)
    {
        if(strlen($k) == 32)
        {
            $deleteHash = $k;
        }
    }

    // only keep the initial part if there's a forward slash
    $shortUrl = current(explode("/", str_replace("~d", "", $_REQUEST['u'])));
    $file = file::loadByShortUrl($shortUrl);

    // check it's active
    if($file->deleteHash != $deleteHash)
    {
        $file = null;
    }
}

// do we have a return page
$returnAccount = false;
if((isset($_REQUEST['returnAccount'])) && ((int)$_REQUEST['returnAccount'] == 1))
{
    $returnAccount = true;
}

/* load file details */
if(!$file)
{
    /* if no file found, redirect to home page */
    redirect(WEB_ROOT . "/index." . SITE_CONFIG_PAGE_EXTENSION);
}

// make sure we're on the correct server for deletes
if($file->getFullShortUrl(true) != $file->getFullShortUrl())
{
    redirect($file->getDeleteUrl($returnAccount, true));
}

/* delete file if submitted */
if ((int) $_REQUEST['delete'])
{
    // reomve file
    $file->removeByUser();

    // redirect to confirmation page
    $resultMsg = t('file_permanently_removed', 'File permanently removed.');
    if($file->errorMsg)
    {
        $resultMsg = $file->errorMsg;
    }
    
    if($returnAccount)
    {
        redirect(CORE_WEB_ROOT.'/account_home.'.SITE_CONFIG_PAGE_EXTENSION.'?s='.urlencode($resultMsg));
    }
    
    redirect(WEB_ROOT . "/error." . SITE_CONFIG_PAGE_EXTENSION.'?e='.urlencode($resultMsg));
}

// get file path
$filePath = file::getFileDomainAndPath($file->id);

require_once('_header.php');
?>

<div class="contentPageWrapper">

    <?php
    if (isErrors())
    {
        echo outputErrors();
    }
    ?>

    <!-- delete file form -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("delete_file", "Delete File"); ?></h2>
            </div>
            <div>
                <p class="introText">
                    <?php echo t("delete_file_intro", "Please confirm whether to delete the file below. Note: Once deleted, this file is removed from our servers and can not be recovered."); ?>
                    <br/><br/>
                </p>
                <form class="international" method="post" action="<?php echo $file->getDeleteUrl($returnAccount, true); ?>" id="form-join" AUTOCOMPLETE="off">
                    <ul>
                        <li class="field-container">
                            <?php echo t('file', 'File'); ?>: <a href="<?php echo _CONFIG_SITE_PROTOCOL; ?>://<?php echo $filePath; ?>/<?php echo $file->shortUrl; ?>" target="_blank"><?php echo $file->originalFilename; ?></a> (<?php echo formatSize($file->fileSize); ?>)
                        </li>
                        <li class="field-container">
                            <span class="field-name"></span>
                            <input name="delete" type="hidden" value="1"/>
                            <input name="submitme" type="hidden" value="1"/>
                            <input name="returnAccount" type="hidden" value="<?php echo (int)$returnAccount; ?>"/>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("delete_file", "Delete File"); ?>" class="submitInput" />
                            <input tabindex="100" type="reset" name="reset" value="<?php echo t("cancel", "Cancel"); ?>" class="cancelInput" onClick="window.location='<?php echo $returnAccount?(CORE_WEB_ROOT.'/account_home.'.SITE_CONFIG_PAGE_EXTENSION):WEB_ROOT; ?>';" />
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