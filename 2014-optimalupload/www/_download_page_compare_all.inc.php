<script>
    <!--
    var milisec = 0;
    var seconds = <?php echo (int)$additionalSettings['download_wait']; ?>;

    function display()
    {
        $('.btn-free').hide();
        $('.download-timer').show();
        if (seconds == 0)
        {
            $('.download-timer').html("<a href='<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>'><?php echo pluginHelper::pluginEnabled('mediaplayer') ? t("download_view_now", "download/view now") : t("download_now", "download now"); ?></a>");
        }
        else
        {
            $('.download-timer-seconds').html(seconds);
        }
        seconds--;
    }

    $(document).ready(function() {
        $('.download-timer-seconds').html(<?php echo (int)$additionalSettings['download_wait']; ?>);
        countdownTimer = setInterval('display()', 1000);
    });
    -->
</script>

<?php
if(isset($downloadPage['additional_javascript_code']))
{
    echo $downloadPage['additional_javascript_code'];
}
?>

<?php
// figure out upgrade url
$auth = Auth::getAuth();
$url  = getCoreSitePath() . "/register." . SITE_CONFIG_PAGE_EXTENSION . "?f=" . urlencode($file->shortUrl);
if ($auth->loggedIn == true)
{
    $url = getCoreSitePath() . "/upgrade." . SITE_CONFIG_PAGE_EXTENSION;
}
?>

<div class="contentPageWrapper">
    <div class="pageSectionMainFull ui-corner-all">
        <div class="pageSectionMainInternal">

            <?php if(showSiteAdverts()): ?>
            <!-- top ads -->
            <div class="metaRedirectWrapperTopAds">
                <?php echo SITE_CONFIG_ADVERT_DELAYED_REDIRECT_TOP; ?>
            </div>
            <?php endif; ?>

            <div class="downloadPageTableV2">
                <table>
                    <tbody>
                        <tr>
                            <th class="descr">
                                <strong>
                                    <?php echo wordwrap(safeOutputToScreen($file->originalFilename), 28, ' ', true); ?> (<?php echo formatSize($file->fileSize); ?>)<br/>
                                </strong>
                                <?php echo t('choose_free_or_premium_download', 'Choose free or premium download'); ?>
                            </th>
                            <th class="typeHeader" style="color: red;">
                                <?php echo strtoupper(t('free', 'free')); ?>
                            </th>
                            <th class="typeHeader" style="color: green;">
                                <?php echo strtoupper(t('registered', 'registered')); ?>
                            </th>
                            <th class="typeHeader">
                                <a class="link premiumBtn" href="<?php echo $url; ?>">
                                    <?php echo strtoupper(t('download_page_premium', 'PREMIUM')); ?>                          
                                </a>
                            </th>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('download_speed', 'download speed')); ?>:
                            </td>
                            <td>
                                <?php echo SITE_CONFIG_NON_USER_MAX_DOWNLOAD_SPEED > 0 ? formatSize(SITE_CONFIG_NON_USER_MAX_DOWNLOAD_SPEED) . 'ps' : UCFirst(t('limited', 'limited')); ?>
                            </td>
                            <td>
                                <?php echo SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_SPEED > 0 ? formatSize(SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_SPEED) . 'ps' : UCFirst(t('limited', 'limited')); ?>
                            </td>
                            <td>
                                <strong>
                                    <?php echo UCFirst(t('maximum', 'maximum')); ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('waiting_time', 'waiting time')); ?>:
                            </td>
                            <td>
                                <?php
                                $totalTime = (int)getTotalWaitingTime(0);
                                echo $totalTime > 0 ? $totalTime . ' ' . UCFirst(t('seconds', 'seconds')) : UCFirst(t('instant', 'instant'));
                                ?>
                            </td>
                            <td>
                                <?php
                                $totalTime = (int)getTotalWaitingTime(1);
                                echo $totalTime > 0 ? $totalTime . ' ' . UCFirst(t('seconds', 'seconds')) : UCFirst(t('instant', 'instant'));
                                ?>
                            </td>
                            <td>
                                <strong>
                                    <?php echo UCFirst(t('instant', 'instant')); ?>
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('site_advertising', 'site advertising')); ?>:
                            </td>
                            <td>
                                <?php
                                $showAdverts = showSiteAdverts(0);
                                if($showAdverts)
                                {
                                    echo UCFirst(t('download_page_yes', 'yes'));
                                }
                                else
                                {
                                    echo UCFirst(t('download_page_none', 'none'));
                                }
                                ?>                            
                            </td>
                            <td>
                                <?php
                                $showAdverts = showSiteAdverts(1);
                                if($showAdverts)
                                {
                                    echo UCFirst(t('download_page_yes', 'yes'));
                                }
                                else
                                {
                                    echo UCFirst(t('download_page_none', 'none'));
                                }
                                ?>                            
                            </td>
                            <td>
                                <strong>
                                    <?php
                                    $showAdverts = showSiteAdverts(2);
                                    if($showAdverts)
                                    {
                                        echo UCFirst(t('download_page_yes', 'yes'));
                                    }
                                    else
                                    {
                                        echo UCFirst(t('download_page_none', 'none'));
                                    }
                                    ?>                            
                                </strong>
                            </td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('how_long_to_keep_files', 'how long to keep files')); ?>:
                            </td>
                            <td>
                                <?php
                                if ((int)SITE_CONFIG_NON_USER_UPLOAD_REMOVAL_DAYS == 0)
                                {
                                    echo UCFirst(t('forever', 'forever'));
                                }
                                else
                                {
                                    echo SITE_CONFIG_NON_USER_UPLOAD_REMOVAL_DAYS .' '. UCFirst(t('days', 'days'));
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ((int)SITE_CONFIG_FREE_USER_UPLOAD_REMOVAL_DAYS == 0)
                                {
                                    echo UCFirst(t('forever', 'forever'));
                                }
                                else
                                {
                                    echo SITE_CONFIG_FREE_USER_UPLOAD_REMOVAL_DAYS .' '. UCFirst(t('days', 'days'));
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if ((int)SITE_CONFIG_PREMIUM_USER_UPLOAD_REMOVAL_DAYS == 0)
                                {
                                    echo UCFirst(t('forever', 'forever'));
                                }
                                else
                                {
                                    echo SITE_CONFIG_PREMIUM_USER_UPLOAD_REMOVAL_DAYS .' '. UCFirst(t('days', 'days'));
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('maximum_file_upload_size', 'Maximum file upload size')); ?>:
                            </td>
                            <td><?php echo getMaxUploadFilesize(0) > 0 ? formatSize(getMaxUploadFilesize(0)) : UCFirst(t('unlimited', 'unlimited')); ?></td>
                            <td><?php echo getMaxUploadFilesize(1) > 0 ? formatSize(getMaxUploadFilesize(1)) : UCFirst(t('unlimited', 'unlimited')); ?></td>
                            <td><?php echo getMaxUploadFilesize(2) > 0 ? formatSize(getMaxUploadFilesize(2)) : UCFirst(t('unlimited', 'unlimited')); ?></td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('interface_to_manage_uploaded_files', 'interface to manage uploaded files')); ?>:
                            </td>
                            <td><?php echo UCFirst(t('not_available', 'not available')); ?></td>
                            <td><?php echo UCFirst(t('available', 'available')); ?></td>
                            <td><?php echo UCFirst(t('available', 'available')); ?></td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('fast_download_even_when_servers_are_busy', 'fast download even when servers are busy')); ?>:
                            </td>
                            <td><?php echo UCFirst(t('not_available', 'not available')); ?></td>
                            <td><?php echo UCFirst(t('not_available', 'not available')); ?></td>
                            <td><?php echo UCFirst(t('available', 'available')); ?></td>
                        </tr>
                        <tr>
                            <td class="descr">
                                <?php echo UCFirst(t('estimated_download_time', 'estimated Download time')); ?>:
                            </td>
                            <td>
                                <?php
                                echo calculateDownloadSpeedFormatted($file->fileSize, getMaxDownloadSpeed(0));
                                ?>
                            </td>
                            <td>
                                <?php
                                echo calculateDownloadSpeedFormatted($file->fileSize, getMaxDownloadSpeed(1));
                                ?>
                            </td>
                            <td>
                                <?php echo calculateDownloadSpeedFormatted($file->fileSize, 0); ?>                              
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- download buttons -->
            <div class="largeDownloadButtons">
                <a href="<?php echo $file->getFullShortUrl(); ?>">
                    <img src="<?php echo SITE_IMAGE_PATH; ?>/slow_download_button.png" width="344" height="138" alt="<?php echo t('download_page_slow_download', 'slow download'); ?>"/>
                </a>
                <a href="<?php echo $url; ?>">
                    <img src="<?php echo SITE_IMAGE_PATH; ?>/high_speed_download.png" width="344" height="138" alt="<?php echo t('download_page_high_speed_download', 'high speed download'); ?>"/>
                </a>
                <div class="clear"><!-- --></div>
            </div>
            <!-- end download buttons -->


            <div id="pageHeader" style="padding-top: 18px;">
                <h2><?php echo t("account_benefits", "account benefits"); ?></h2>
            </div>
            <div class="clear"><!-- --></div>

            <?php include_once('_upgradeBenefits.inc.php'); ?>

        </div>
    </div>
</div>