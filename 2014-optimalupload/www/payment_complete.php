<?php
// setup includes
require_once('includes/master.inc.php');

// setup page
define("PAGE_NAME", t("payment_complete_page_name", "Payment Complete"));
define("PAGE_DESCRIPTION", t("payment_complete_meta_description", "Payment Complete"));
define("PAGE_KEYWORDS", t("payment_complete_meta_keywords", "payment, complete, file, hosting, site"));

// include header
require_once('_header.php');
?>

<div class="contentPageWrapper">

    <!-- main section -->
    <div class="pageSectionMain ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo PAGE_NAME; ?></h2>
            </div>
            <p>
                <?php echo t('thanks_for_your_payment', 'Thanks for your payment!'); ?>
            </p>
            <p>
                <?php echo t('once_we_receive_notication_from_gateway_your_account_will_be_upgraded', 'Once we receive notification from the payment gateway, your account will be upgraded/extended. Please allow up to an hour for this to complete.'); ?>
            </p>
            <p>
                <?php echo t('you_can_check_your_accout_status_by_going', 'You can check your account status by going '); ?><a href="upgrade.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>"><?php t('here', 'here'); ?></a>.
            </p>
        </div>
    </div>
    <?php include_once("_bannerRightContent.inc.php"); ?>
    <div class="clear"><!-- --></div>
</div>

<?php
require_once('_footer.php');
?>