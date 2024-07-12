<?php
// setup includes
require_once('includes/master.inc.php');

/* setup page */
define("PAGE_NAME", t("stats_page_name", "View file statistics"));
define("PAGE_DESCRIPTION", t("stats_meta_description", "Uploaded file statistics"));
define("PAGE_KEYWORDS", t("stats_meta_keywords", "stats, statistics, unique, visitors, hits, file, upload"));

$file = null;
if (isset($_REQUEST['u']))
{
    // only keep the initial part if there's a forward slash
    $shortUrl = current(explode("/", str_replace("~s", "", $_REQUEST['u'])));
    $file     = file::loadByShortUrl($shortUrl);
}

// load file details
if (!$file)
{
    /* if no file found, redirect to home page */
    redirect(WEB_ROOT . "/index." . SITE_CONFIG_PAGE_EXTENSION);
}

// make sure user is permitted to view stats
if($file->canViewStats() == false)
{
    $errorMsg = t("stats_error_file_statistics_are_private", "Statistics for this file are not publicly viewable.");
    redirect(getCoreSitePath() . "/error." . SITE_CONFIG_PAGE_EXTENSION . "?e=" . urlencode($errorMsg));
}

require_once('_header.php');
?>

<script src="<?php echo WEB_ROOT; ?>/js/charts/Chart.js"></script>
<script>
    $ = jQuery;
    $(document).ready(function($) {
<?php
// last 24 hours chart
$last24hours = charts::createBarChart($file, 'last24hours');
echo $last24hours['chartJS'];

// last 7 days chart
$last7days = charts::createBarChart($file, 'last7days');
echo $last7days['chartJS'];

// last 30 days chart
$last30days = charts::createBarChart($file, 'last30days');
echo $last30days['chartJS'];

// last 12 months chart
$last12months = charts::createBarChart($file, 'last12months');
echo $last12months['chartJS'];

// top countries pie
$countries = charts::createPieChart($file, 'countries');
echo $countries['chartJS'];

// top referrers pie
$referrers = charts::createPieChart($file, 'referrers');
echo $referrers['chartJS'];

// top browsers pie
$browsers = charts::createPieChart($file, 'browsers');
echo $browsers['chartJS'];

// top os pie
$os = charts::createPieChart($file, 'os');
echo $os['chartJS'];
?>

    });

    function showChart(chartId)
    {
        $('.chart').hide();
        $('.link_selected').removeClass('link_selected');
        $('#' + chartId).fadeIn();
        $('#link_' + chartId).addClass('link_selected');

        return false;
    }

    $(document).ready(function() {
        $("#tabs").tabs();
        $("#tabs").css("display", "block");
    });
</script>

<div class="statsHeaderWrapper">
    <div class="statsHeader" style="background: url(<?php echo SITE_IMAGE_PATH; ?>/stats/stats_head.png) no-repeat;">
        <div class="rightTotalVisits">
            <div class="visits">
                <?php echo $file->visits; ?>
            </div>
            <div class="label">
                <?php echo t("downloads", "downloads"); ?>:
            </div>
        </div>
        <div class="leftShortUrlDetails">
            <?php if ($file->statusId == 1): ?><a href="<?php echo $file->getFullShortUrl(); ?>" target="_blank"><?php else: ?><strong><?php endif; ?><?php echo $file->originalFilename; ?><?php if ($file->statusId != 1): ?></strong><?php else: ?></a>&nbsp;&nbsp;<a href="<?php echo $file->getShortInfoUrl(); ?>">(<?php echo t("stats_file_details", "file details"); ?>)</a><?php endif; ?><br/>
            <?php echo t("uploaded", "Uploaded"); ?> <?php echo dater($file->uploadedDate); ?>
        </div>
    </div>
</div>

<div class="statsBoxWrapper">
    <div id="tabs" class="statsTabs">
        <ul>
            <li><a href="#tab1"><?php echo t("visitors", "visitors"); ?></a></li>
            <li><a href="#tab2"><?php echo t("countries", "countries"); ?></a></li>
            <li><a href="#tab3"><?php echo t("top_referrers", "top referrers"); ?></a></li>
            <li><a href="#tab4"><?php echo t("browsers", "browsers"); ?></a></li>
            <li><a href="#tab5"><?php echo t("operating_systems", "operating systems"); ?></a></li>
        </ul>            
        <div id="tab1" class="tabContent">
            <!-- TAB 1 -->
            <a href="#" onClick="$('#tab1_chart1').show(); $('#tab1_chart2').hide(); $('#tab1_chart3').hide(); $('#tab1_chart4').hide(); return false;"><?php echo t("last_24_hours", "last 24 hours"); ?></a> | <a href="#" onClick="$('#tab1_chart2').show(); $('#tab1_chart1').hide(); $('#tab1_chart3').hide(); $('#tab1_chart4').hide(); return false;"><?php echo t("last_7_days", "last 7 days"); ?></a> | <a href="#" onClick="$('#tab1_chart3').show(); $('#tab1_chart2').hide(); $('#tab1_chart1').hide(); $('#tab1_chart4').hide(); return false;"><?php echo t("last_30_days", "last 30 days"); ?></a> | <a href="#" onClick="$('#tab1_chart4').show(); $('#tab1_chart2').hide(); $('#tab1_chart3').hide(); $('#tab1_chart1').hide(); return false;"><?php echo t("last_12_months", "last 12 months"); ?></a><br/><br/>

            <div id="tab1_chart1">
                <?php echo $last24hours['canvasHTML']; ?>
                <div> 
                    <?php echo $last24hours['dataTableHTML']; ?>
                </div>
            </div>

            <div id="tab1_chart2" style="display:none;">
                <?php echo $last7days['canvasHTML']; ?>
                <div> 
                    <?php echo $last7days['dataTableHTML']; ?>
                </div>
            </div>

            <div id="tab1_chart3" style="display:none;">
                <?php echo $last30days['canvasHTML']; ?>
                <div> 
                    <?php echo $last30days['dataTableHTML']; ?>
                </div>
            </div>

            <div id="tab1_chart4" style="display:none;">
                <?php echo $last12months['canvasHTML']; ?>
                <div> 
                    <?php echo $last12months['dataTableHTML']; ?>
                </div>
            </div>
        </div>

        <div id="tab2" class="tabContent">
            <?php echo $countries['canvasHTML']; ?>
            <div> 
                <?php echo $countries['dataTableHTML']; ?>
            </div>
        </div>

        <div id="tab3" class="tabContent">
            <?php echo $referrers['canvasHTML']; ?>
            <div> 
                <?php echo $referrers['dataTableHTML']; ?>
            </div>
        </div>

        <div id="tab4" class="tabContent">
            <?php echo $browsers['canvasHTML']; ?>
            <div> 
                <?php echo $browsers['dataTableHTML']; ?>
            </div>
        </div>

        <div id="tab5" class="tabContent">
            <?php echo $os['canvasHTML']; ?>
            <div> 
                <?php echo $os['dataTableHTML']; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once('_footer.php');
?>