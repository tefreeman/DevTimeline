<?php
define('ADMIN_PAGE_TITLE', 'Dashboard');
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('_local_auth.inc.php');
include_once('_header.inc.php');

// load stats
$totalActiveFiles     = (int) $db->getValue("SELECT COUNT(id) AS total FROM file WHERE statusId = 1");
$totalDownloads       = (int) $db->getValue("SELECT SUM(visits) AS total FROM file");
$totalHDSpacePre      = $db->getValue("SELECT SUM(file.fileSize) FROM file WHERE file.statusId = 1 AND fileHash IS NULL");
$totalHDSpacePost     = $db->getValue("SELECT SUM(fileSelect.fileSize) FROM (SELECT * FROM file WHERE file.fileHash IS NOT NULL GROUP BY file.fileHash) AS fileSelect WHERE fileSelect.statusId = 1 AND fileSelect.fileHash IS NOT NULL");
$totalHDSpace         = $totalHDSpacePre+$totalHDSpacePost;
$totalRegisteredUsers = (int) $db->getValue("SELECT COUNT(id) AS total FROM users WHERE status='active'");
$totalPaidUsers       = (int) $db->getValue("SELECT COUNT(id) AS total FROM users WHERE status='active' AND level_id=2");
$totalReports         = (int) $db->getValue("SELECT COUNT(id) AS total FROM file_report WHERE report_status='pending'");
$payments30Days       = (float) $db->getValue("SELECT SUM(amount) AS total FROM payment_log WHERE date_created BETWEEN NOW() - INTERVAL 30 DAY AND NOW()");
?>

<script>
// check for script upgrades
$(document).ready(function(){
    $.ajax({
        url: "ajax/check_for_upgrade.ajax.php",
        dataType: "html"
    }).done(function(response) {
        if(response.length > 0)
        {
            showInfo(response);
        }
    });
});
</script>

<div class="row clearfix">
    <div class="col_12">
        <div class="sectionLargeIcon largeDashboardIcon"></div>
        <div class="widget clearfix">
            <h2><?php echo adminFunctions::t('quick_overview', 'Quick Overview'); ?></h2>
            <div class="widget_inside">
                <h3>Current Statistics</h3>
                <div class="report">
                    <a href="file_manage.php">
                        <div class="button up">
                            <span class="value"><?php echo $totalActiveFiles; ?></span>
                            <span class="attr">Active Files</span>
                        </div>
                    </a>

                    <a href="<?php if($Auth->hasAccessLevel(20)): ?>server_manage.php<?php else: ?>#<?php endif; ?>">
                        <div class="button up">
                            <span class="value"><?php echo adminFunctions::formatSize($totalHDSpace, 2); ?></span>
                            <span class="attr">Space Used</span>
                        </div>
                    </a>

                    <a href="file_manage.php">
                        <div class="button up">
                            <span class="value"><?php echo $totalDownloads; ?></span>
                            <span class="attr">File Downloads</span>
                        </div>
                    </a>

                    <?php if($Auth->hasAccessLevel(20)): ?>
                    <a href="user_manage.php?filterByAccountStatus=active">
                        <div class="button up">
                            <span class="value"><?php echo $totalRegisteredUsers; ?>/<?php echo $totalPaidUsers; ?></span>
                            <span class="attr">Active/Paid Users</span>
                        </div>
                    </a>
                    <?php endif; ?>

                    <a href="file_report_manage.php?filterByReportStatus=pending">
                        <div class="button up">
                            <span class="value"><?php echo $totalReports; ?></span>
                            <span class="attr">Copyright Reports</span>
                        </div>
                    </a>

                    <?php if($Auth->hasAccessLevel(20)): ?>
                    <a href="payment_manage.php">
                        <div class="button up">
                            <span class="value"><?php echo SITE_CONFIG_COST_CURRENCY_SYMBOL . ' ' . number_format($payments30Days, 2, '.', ''); ?></span>
                            <span class="attr">30 Day Payments</span>
                        </div>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// last 14 days chart
$tracker              = 14;
$last14Days           = array();
while ($tracker >= 0)
{
    $date              = date("Y-m-d", strtotime("-" . $tracker . " day"));
    $last14Days[$date] = 0;
    $tracker--;
}

$tracker = 1;
$data    = array();
$label   = array();
foreach ($last14Days AS $k => $total)
{
    $totalFiles = $db->getValue("SELECT COUNT(id) AS total FROM file WHERE MID(uploadedDate, 1, 10) = '" . $k . "'");
    $data[]     = '[' . $tracker . ',' . (int) $totalFiles . ']';
    $label[]    = '[' . $tracker . ',\'' . date('jS', strtotime($k)) . '\']';
    $tracker++;
}
?>

<div class="row clearfix">
    <div class="col_8">
        <div class="widget clearfix">
            <h2><?php echo adminFunctions::t("dashboard_graph_last_14_days_title", "New Files (last 14 days)"); ?></h2>
            <div class="widget_inside">
                <div id="14_day_chart" style="width:100%; height:300px;" class="centered"></div>
                <script type="text/javascript">
                    $(function() {
                        var css_id = "#14_day_chart";
                        var data = [
                            {label: '<?php echo UCWords(adminFunctions::t("files", "files")); ?>', data: [<?php echo implode(", ", $data); ?>]}
                        ];
                        var options = {
                            series: {stack: 0,
                                lines: {show: false, steps: false},
                                grid: {backgroundColor: {colors: ["#fff", "#eee"]}},
                                bars: {show: true, barWidth: 0.9, align: 'center'}},
                            xaxis: {ticks: [<?php echo implode(", ", $label); ?>]},
                            colors: ["#55A9D3"]
                        };

                        $.plot($(css_id), data, options);
                    });
                </script>
            </div>
        </div>
    </div>

    <?php
    // pie chart of the status of items
    $data       = array();
    $dataForPie = $db->getRows("SELECT COUNT(file.id) AS total, file_status.label AS status FROM file LEFT JOIN file_status ON file.statusId = file_status.id GROUP BY file.statusId");
    foreach ($dataForPie AS $dataRow)
    {
        $data[] = '{ label: "' . UCWords(adminFunctions::t($dataRow['status'], $dataRow['status'])) . '",  data: ' . (int) $dataRow['total'] . '}';
    }
    ?>
    <div class="col_4 last">
        <div class="widget">
            <h2><?php echo adminFunctions::t('file_status', 'File Status'); ?></h2>
            <div class="widget_inside">
                <div id="file_status_chart" style="width:100%; height: 300px" class="centered"></div>
                <div id="file_status_chart_hover" class="pieHoverText"></div>
                <script type="text/javascript">
                    $(function() {
                        // data
                        var data = [
<?php echo implode(', ', $data); ?>
                        ];

                        // INTERACTIVE
                        $.plot($("#file_status_chart"), data,
                                {
                                    series: {
                                        pie: {
                                            show: true
                                        }
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true
                                    },
                                    legend: {
                                        show: true
                                    }
                                });
                        $("#file_status_chart").bind("plothover", fileStatusChartHover);

                    });

                    function fileStatusChartHover(event, pos, obj)
                    {
                        if (!obj)
                            return;
                        percent = parseFloat(obj.series.percent).toFixed(2);
                        $("#file_status_chart_hover").html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + obj.series.label + ' (' + percent + '%)</span>');
                    }
                </script>
            </div>
        </div>
    </div>
</div>


<?php
// last 12 months files
$tracker      = 12;
$last12Months = array();
while ($tracker >= 0)
{
    $date                = date("Y-m", strtotime("-" . $tracker . " month"));
    $last12Months[$date] = 0;
    $tracker--;
}

$tracker = 1;
$data    = array();
$label   = array();
foreach ($last12Months AS $k => $total)
{
    $totalFiles = $db->getValue("SELECT COUNT(id) AS total FROM file WHERE MID(uploadedDate, 1, 7) = '" . $k . "'");
    $data[]     = '[' . $tracker . ',' . (int) $totalFiles . ']';
    $label[]    = '[' . $tracker . ',\'' . date('M y', strtotime($k)) . '\']';
    $tracker++;
}
?>

<div class="row clearfix">
    <div class="col_8">
        <div class="widget clearfix">
            <h2><?php echo adminFunctions::t("dashboard_graph_last_12_months_title", "New Files (last 12 months)"); ?></h2>
            <div class="widget_inside">
                <div id="12_months_chart" style="width:100%; height:300px;" class="centered"></div>
                <script type="text/javascript">
                    $(function() {
                        var css_id = "#12_months_chart";
                        var data = [
                            {label: '<?php echo UCWords(adminFunctions::t("files", "files")); ?>', data: [<?php echo implode(", ", $data); ?>]}
                        ];
                        var options = {
                            series: {stack: 0,
                                lines: {show: false, steps: false},
                                grid: {backgroundColor: {colors: ["#fff", "#eee"]}},
                                bars: {show: true, barWidth: 0.9, align: 'center'}},
                            xaxis: {ticks: [<?php echo implode(", ", $label); ?>]},
                            colors: ["#55A9D3"]
                        };

                        $.plot($(css_id), data, options);
                    });
                </script>
            </div>
        </div>
    </div>

    <?php
// pie chart of file types
    $data       = array();
    $dataForPie = $db->getRows("SELECT COUNT(file.id) AS total, file.extension AS status FROM file WHERE statusId=1 GROUP BY file.extension ORDER BY COUNT(file.id) DESC");
    $counter    = 1;
    $otherTotal = 0;
    foreach ($dataForPie AS $dataRow)
    {
        if ($counter > 10)
        {
            $otherTotal = $otherTotal + $dataRow['total'];
        }
        else
        {
            $data[] = '{ label: "' . strtolower(adminFunctions::t($dataRow['status'], $dataRow['status'])) . '",  data: ' . (int) $dataRow['total'] . '}';
        }
        $counter++;
    }
    if ($otherTotal > 0)
    {
        $data[] = '{ label: "' . strtolower(adminFunctions::t('other', 'other')) . '",  data: ' . (int) $otherTotal . '}';
    }
    ?>
    <div class="col_4 last">
        <div class="widget">
            <h2><?php echo adminFunctions::t('file_type', 'File Type'); ?></h2>
            <div class="widget_inside">
                <div id="file_type_chart" style="width:100%; height: 300px" class="centered"></div>
                <div id="file_type_chart_hover" class="pieHoverText"></div>
                <script type="text/javascript">
                    $(function() {
                        // data
                        var data = [
<?php echo implode(', ', $data); ?>
                        ];

                        // INTERACTIVE
                        $.plot($("#file_type_chart"), data,
                                {
                                    series: {
                                        pie: {
                                            show: true
                                        }
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true
                                    },
                                    legend: {
                                        show: false
                                    }
                                });
                        $("#file_type_chart").bind("plothover", fileTypeChartHover);

                    });

                    function fileTypeChartHover(event, pos, obj)
                    {
                        if (!obj)
                            return;
                        percent = parseFloat(obj.series.percent).toFixed(2);
                        $("#file_type_chart_hover").html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + obj.series.label + ' (' + percent + '%)</span>');
                    }
                </script>
            </div>
        </div>
    </div>
</div>


<?php if($Auth->hasAccessLevel(20)): ?>

<?php
// last 14 days user registrations
$tracker    = 14;
$last14Days = array();
while ($tracker >= 0)
{
    $date              = date("Y-m-d", strtotime("-" . $tracker . " day"));
    $last14Days[$date] = 0;
    $tracker--;
}

$tracker  = 1;
$dataFree = array();
$dataPaid = array();
$label    = array();
foreach ($last14Days AS $k => $total)
{
    $totalUsers = $db->getValue("SELECT COUNT(id) AS total FROM users WHERE MID(datecreated, 1, 10) = '" . $k . "' AND level_id=1");
    $dataFree[] = '[' . $tracker . ',' . (int) $totalUsers . ']';
    $totalUsers = $db->getValue("SELECT COUNT(id) AS total FROM users WHERE MID(datecreated, 1, 10) = '" . $k . "' AND level_id=2");
    $dataPaid[] = '[' . $tracker . ',' . (int) $totalUsers . ']';
    $label[]    = '[' . $tracker . ',\'' . date('jS', strtotime($k)) . '\']';
    $tracker++;
}
?>

<div class="row clearfix">
    <div class="col_8">
        <div class="widget clearfix">
            <h2><?php echo adminFunctions::t("dashboard_graph_user_registrations_title", "New Users (last 14 days)"); ?></h2>
            <div class="widget_inside">
                <div id="14_day_users" style="width:100%; height:300px;" class="centered"></div>
                <script type="text/javascript">
                    $(function() {
                        var css_id = "#14_day_users";
                        var data = [
                            {label: '<?php echo UCWords(adminFunctions::t("free_user", "free user")); ?>', data: [<?php echo implode(", ", $dataFree); ?>]},
                            {label: '<?php echo UCWords(adminFunctions::t("paid_user", "paid user")); ?>', data: [<?php echo implode(", ", $dataPaid); ?>]}
                        ];
                        var options = {
                            series: {stack: 0,
                                lines: {show: false, steps: false},
                                grid: {backgroundColor: {colors: ["#fff", "#eee"]}},
                                bars: {show: true, barWidth: 0.9, align: 'center'}},
                            xaxis: {ticks: [<?php echo implode(", ", $label); ?>]},
                            colors: ["#55A9D3", "#4DA74D"]
                        };

                        $.plot($(css_id), data, options);
                    });
                </script>
            </div>
        </div>
    </div>

    <?php
    // pie chart of user status
    $data       = array();
    $dataForPie = $db->getRows("SELECT COUNT(users.id) AS total, user_level.label FROM users LEFT JOIN user_level ON users.level_id = user_level.level_id GROUP BY users.level_id ORDER BY COUNT(users.id) DESC");
    foreach ($dataForPie AS $dataRow)
    {
        $data[] = '{ label: "' . UCWords(adminFunctions::t($dataRow['label'], $dataRow['label'])) . '",  data: ' . (int) $dataRow['total'] . '}';
    }
    ?>
    <div class="col_4 last">
        <div class="widget">
            <h2><?php echo adminFunctions::t('user_status', 'User Status'); ?></h2>
            <div class="widget_inside">
                <div id="user_status_chart" style="width:100%; height: 300px" class="centered"></div>
                <div id="user_status_chart_hover" class="pieHoverText"></div>
                <script type="text/javascript">
                    $(function() {
                        // data
                        var data = [
<?php echo implode(', ', $data); ?>
                        ];

                        // INTERACTIVE
                        $.plot($("#user_status_chart"), data,
                                {
                                    series: {
                                        pie: {
                                            show: true
                                        }
                                    },
                                    grid: {
                                        hoverable: true,
                                        clickable: true
                                    },
                                    legend: {
                                        show: false
                                    }
                                });
                        $("#user_status_chart").bind("plothover", userStatusChartHover);

                    });

                    function userStatusChartHover(event, pos, obj)
                    {
                        if (!obj)
                            return;
                        percent = parseFloat(obj.series.percent).toFixed(2);
                        $("#user_status_chart_hover").html('<span style="font-weight: bold; color: ' + obj.series.color + '">' + obj.series.label + ' (' + percent + '%)</span>');
                    }
                </script>
            </div>
        </div>
    </div>
</div>

<?php endif; ?>

<?php
include_once('_footer.inc.php');
?>