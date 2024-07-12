<?php
// setup includes
require_once('includes/master.inc.php');

// setup page
define("PAGE_NAME", t("faq_page_name", "FAQ"));
define("PAGE_DESCRIPTION", t("faq_meta_description", "Frequently Asked Questions"));
define("PAGE_KEYWORDS", t("faq_meta_keywords", "faq, frequently, asked, questions, file, hosting, site"));

// max allowed upload size
$maxUploadSizeFreeAcc = SITE_CONFIG_FREE_USER_MAX_UPLOAD_FILESIZE;
$maxUploadSizePaidAcc = SITE_CONFIG_PREMIUM_USER_MAX_UPLOAD_FILESIZE;

// if php restrictions are lower than permitted, override
$phpMaxSize = getPHPMaxUpload();
if($phpMaxSize < $maxUploadSizeFreeAcc)
{
    $maxUploadSizeFreeAcc = $phpMaxSize;
    $maxUploadSizePaidAcc = $phpMaxSize;
}

// get accepted file types
$acceptedFileTypes = getAcceptedFileTypes();

// when files will be removed
$fileRemovalFreeAcc = SITE_CONFIG_FREE_USER_UPLOAD_REMOVAL_DAYS;
$fileRemovalPaidAcc = SITE_CONFIG_PREMIUM_USER_UPLOAD_REMOVAL_DAYS;
if((int)$fileRemovalFreeAcc == 0)
{
    $fileRemovalFreeAcc = t('faq_unlimited', 'unlimited');
}
if((int)$fileRemovalPaidAcc == 0)
{
    $fileRemovalPaidAcc = t('faq_unlimited', 'unlimited');
}

// include header
require_once('_header.php');
?>

<div class="contentPageWrapper">

    <!-- main section -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("faq_page_name", "FAQ"); ?></h2>
            </div>
            <div class="faq">
                <ul>
                    <li>
                        <strong><?php echo t('faq_q1_question', 'Q: Is this free?'); ?></strong>
                        <br/><br/>
                        <?php echo t('faq_q1_answer', 'A: Yes, uploading and downloading is 100% Free for all users. We offer premium accounts which allows for greater flexibility with uploading/downloading.'); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q2_question', 'Q: Will my files be removed?'); ?></strong>
                        <br/><br/>
                        <?php echo t('faq_q2_answer', 'A: Free/non accounts files are kept for [[[KEPT_FOR_DAYS_FREE]]] days. Premium accounts files are kept for [[[KEPT_FOR_DAYS_PAID]]] days.', array('KEPT_FOR_DAYS_FREE'=>$fileRemovalFreeAcc, 'KEPT_FOR_DAYS_PAID'=>$fileRemovalPaidAcc)); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q3_question', 'Q: How many files can I upload?'); ?></strong>
                        <br/><br/>
                        <?php echo t('faq_q3_answer', 'A: You can upload as many files as you want, as long as each one adheres to the Terms of Service and the maximum file upload size.'); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q4_question', 'Q: Which files types am I allowed to upload?'); ?></strong>
                        <br/><br/>
                        <?php echo t('faq_q4_answer', 'A: You may upload the following types of files: [[[FILE_TYPES]]].', array('FILE_TYPES'=>((COUNT($acceptedFileTypes))?str_replace(".", "", implode(", ", $acceptedFileTypes)):t('faq_any', 'Any')))); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q5_question', 'Q: Are there any restrictions to the size of my uploaded files?'); ?></strong>
                        <br/><br/>
                        <?php echo t('faq_q5_answer', 'A: Each file you upload must be less than [[[MAX_UPLOAD_SIZE_FREE]]] in size for free/non accounts or less than [[[MAX_UPLOAD_SIZE_PAID]]] in size for premium accounts. If it is greater than that amount, your file will be rejected.', array('MAX_UPLOAD_SIZE_FREE'=>formatSize($maxUploadSizeFreeAcc), 'MAX_UPLOAD_SIZE_PAID'=>formatSize($maxUploadSizePaidAcc))); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q6_question', 'Q: Can I upload music or videos?'); ?></strong><br/><br/>
                        <?php echo t('faq_q6_answer', 'A: Yes. Music and video hosting is permitted as long as you own the copyright on the content and it adheres to the terms and conditions.'); ?>
                        <br/><br/>
                    </li>
                    <li>
                        <strong><?php echo t('faq_q7_question', 'Q: There are some files on our servers which may have been subject to copyright protection, how can I notify you of them?'); ?></strong><br/><br/>
                        <?php echo t('faq_q7_answer', 'A: Via our <a href="report_file.[[[SITE_CONFIG_PAGE_EXTENSION]]]">report abuse</a> pages.', array('SITE_CONFIG_PAGE_EXTENSION'=>SITE_CONFIG_PAGE_EXTENSION)); ?>
                    </li>
                </ul>
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