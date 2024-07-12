<?php

// whether to allow chunked uploaded. Recommend to keep as true unless you're experiencing issues.
define('USE_CHUNKED_UPLOADS', true);

// setup includes
require_once('includes/master.inc.php');

// make sure this is the main site, only uploads on the main site are permitted
mainSiteAccessOnly();

// setup page
define("PAGE_NAME", t("index_page_name", "Upload Files"));
define("PAGE_DESCRIPTION", t("index_meta_description", "Upload, share, track, manage your files in one simple to use file host."));
define("PAGE_KEYWORDS", t("index_meta_keywords", "upload, share, track, file, hosting, host"));

// run any batch tasks
executeBatchTasks();

// max allowed upload size & max permitted urls
$maxUploadSize    = (int)getMaxUploadFilesize();
$maxPermittedUrls = (int)getMaxRemoteUrls();

// get accepted file types
$acceptedFileTypes = getAcceptedFileTypes();

// whether to allow uploads or not
$showUploads = true;
if(getAllowedToUpload() == false)
{
    $showUploads = false;
}

// load folders
$folderArr = array();
if($Auth->loggedIn())
{
    $folderArr = fileFolder::loadAllForSelect($Auth->id);
}

// header section
require_once('_header.php');

// index JS
require_once('_indexJS.inc.php');
?>

<div class="preLoadImages hidden">
    <img src="<?php echo SITE_IMAGE_PATH; ?>/delete_small.png" height="1" width="1"/>
    <img src="<?php echo SITE_IMAGE_PATH; ?>/add_small.gif" height="1" width="1"/>
    <img src="<?php echo SITE_IMAGE_PATH; ?>/red_error_small.png" height="1" width="1"/>
    <img src="<?php echo SITE_IMAGE_PATH; ?>/green_tick_small.png" height="1" width="1"/>
    <img src="<?php echo SITE_IMAGE_PATH; ?>/blue_right_arrow.png" height="1" width="1"/>
    <img src="<?php echo SITE_IMAGE_PATH; ?>/processing_small.gif" height="1" width="1"/>
</div>

<div id="tabs" class="homeTabs" style="display: none;">
    <ul>
        <li><a href="#fileUpload"><?php echo UCWords(t('file_upload', 'file upload')); ?></a></li>
        <li><a href="#urlUpload"><?php echo UCWords(t('remote_url_upload', 'remote url upload')); ?></a></li>
        <?php
        // append any plugin includes
        pluginHelper::includeAppends('index_tab.inc.php');
        ?>
    </ul>

    <!-- FILE UPLOAD -->
    <div id="fileUpload">
        <div class="fileUploadMain ui-corner-all">
            <div id="fileUploadBadge" class="fileUploadBadge"></div>
            <div class="fileUploadMainInternal contentPageWrapper" <?php if($showUploads == false) { if ((getAllowedToUpload(0) == false) && (getAllowedToUpload(1) == true)) echo 'onClick="window.location=\'register.' . SITE_CONFIG_PAGE_EXTENSION . '\';"'; else echo 'onClick="alert(\''.t('index_uploading_disabled', 'Error: Uploading has been disabled.').'\'); return false;";'; } ?>>

                <!-- uploader -->
                <div id="uploaderContainer" class="uploaderContainer">

                    <div id="fileupload">
                        <form action="<?php echo file::getUploadUrl(); ?>/uploadHandler.php?r=<?php echo htmlspecialchars(_CONFIG_SITE_HOST_URL); ?>&p=<?php echo htmlspecialchars(_CONFIG_SITE_PROTOCOL); ?>" method="POST" enctype="multipart/form-data">
                            <div class="fileupload-buttonbar hiddenAlt">
                                <label class="fileinput-button">
                                    <span><?php echo t('add_files', 'Add files...'); ?></span>
                                    <?php
                                    if ($showUploads == true)
                                    {
                                        echo '<input id="add_files_btn" type="file" name="files[]" multiple>';
                                    }
                                    ?>
                                </label>
                                <button id="start_upload_btn" type="submit" class="start"><?php echo t('start_upload', 'Start upload'); ?></button>
                                <button id="cancel_upload_btn" type="reset" class="cancel"><?php echo t('cancel_upload', 'Cancel upload'); ?></button>
                            </div>
                            <div class="fileupload-content">
                                <label for="add_files_btn">
                                    <div id="initialUploadSection" class="initialUploadSection"<?php if (!browserIsIE()): ?> onClick="$('#add_files_btn').click();
                                                return false;"<?php endif; ?>>
                                        <div class="initialUploadText">
                                            <div class="uploadText">
                                                <h2><?php echo t('select_files', 'Select files'); ?>:</h2>
                                            </div>
                                            <div class="clearLeft"><!-- --></div>

                                            <div class="uploadElement">
                                                <div class="internal">
                                                    <?php if (browserIsIE()): ?>
                                                        <?php echo t('click_here_to_browse_your_files', 'Click here to browse your files...'); ?>
                                                    <?php else: ?>
                                                        <?php echo t('drag_and_drop_files_here_or_click_to_browse', 'Drag &amp; drop files here or click to browse...'); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="uploadFooter">
                                            <div class="baseText">
                                                <a class="showAdditionalOptionsLink"><?php echo UCFirst(t('options', 'options')); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo t('max_file_size', 'Max file size'); ?>: <?php echo formatSize($maxUploadSize); ?>. <?php echo COUNT($acceptedFileTypes) ? (t('allowed_file_types', 'Allowed file types') . ': ' . str_replace(".", "", implode(", ", $acceptedFileTypes)) . '.') : ''; ?>
                                            </div>
                                        </div>
                                        <div class="clear"><!-- --></div>
                                    </div>
                                </label>
                                <div id="fileListingWrapper" class="fileListingWrapper hidden">
                                    <div class="introText">
                                        <h2><?php echo t('files', 'Files'); ?>:</h2>
                                    </div>
                                    <div class="clearLeft"><!-- --></div>

                                    <div class="fileSection">
                                        <table id="files" class="files" width="100%"><tbody></tbody></table>
                                        <table id="addFileRow" class="addFileRow" width="100%">
                                            <tr class="template-upload">
                                                <td class="cancel">
                                                    <a href="#"<?php if (!browserIsIE()): ?> onClick="$('#add_files_btn').click();
                                                return false;"<?php endif; ?>>
                                                        <label for="add_files_btn">
                                                            <img src="<?php echo SITE_IMAGE_PATH; ?>/add_small.gif" height="9" width="9" alt="<?php echo t('add_file', 'add file'); ?>"/>
                                                        </label>
                                                    </a>
                                                </td>
                                                <td class="name">
                                                    <a href="#"<?php if (!browserIsIE()): ?> onClick="$('#add_files_btn').click();
                                                return false;"<?php endif; ?>>
                                                        <label for="add_files_btn">
                                                            <?php echo t('add_file', 'add file'); ?>
                                                        </label>
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <div id="processQueueSection" class="fileSectionFooterText">
                                        <div id="uploadButton" class="uploadButton" title="upload queue" onClick="$('#start_upload_btn').click();"><!-- --></div>
                                        <div class="baseText">
                                            <a class="showAdditionalOptionsLink"><?php echo UCFirst(t('options', 'options')); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo t('max_file_size', 'Max file size'); ?>: <?php echo formatSize($maxUploadSize); ?>. <?php echo COUNT($acceptedFileTypes) ? (t('allowed_file_types', 'Allowed file types') . ': ' . str_replace(".", "", implode(", ", $acceptedFileTypes)) . '.') : ''; ?>
                                        </div>
                                        <div class="clear"><!-- --></div>
                                    </div>

                                    <div id="processingQueueSection" class="fileSectionFooterText hidden">
                                        <div class="uploadProcessingButton" title="processing queue"><!-- --></div>
                                        <div class="globalProgressWrapper">
                                            <div id="progress" class="progress progress-success progress-striped">
                                                <div class="bar"></div>
                                            </div>
                                            <div id="fileupload-progresstext" class="fileupload-progresstext" style="width:720px;">
                                                <div id="fileupload-progresstextRight" style="width:50%; float: right; text-align: right;"><!-- --></div>
                                                <div id="fileupload-progresstextLeft" style="width:50%; float: left;"><!-- --></div>
                                            </div>
                                        </div>
                                        <div class="clear"><!-- --></div>
                                    </div>

                                    <div id="completedSection" class="fileSectionFooterText hidden">
                                        <div class="copyAllLinkWrapper">
                                            <a class="copyAllLink" data-clipboard-text="" href="#">[<?php echo t('copy_all_links', 'copy all links'); ?>]</a>
                                        </div>
                                        <div class="baseText">
                                            <?php echo t('file_upload_completed', 'File uploads completed.'); ?> <?php echo t('index_upload_more_files', '<a href="[[[WEB_ROOT]]]">Click here</a> to upload more files.', array('WEB_ROOT'=>WEB_ROOT)); ?>
                                        </div>
                                        <div class="clear"><!-- --></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <script id="template-upload" type="text/x-jquery-tmpl">
                        {% for (var i=0, file; file=o.files[i]; i++) { %}
                        <tr class="template-upload{% if (file.error) { %} errorText{% } %}" id="fileUploadRow{%=i%}">
                        <td class="cancel">
                        <a href="#" onClick="return false;">
                        <img src="<?php echo SITE_IMAGE_PATH; ?>/delete_small.png" height="10" width="10" alt="<?php echo t('delete', 'delete'); ?>"/>
                        </a>
                        </td>
                        <td class="name">{%=file.name%}&nbsp;&nbsp;{%=o.formatFileSize(file.size)%}
                        {% if (!file.error) { %}
                        <div class="start hidden"><button>start</button></div>
                        {% } %}
                        <div class="cancel hidden"><button>cancel</button></div>
                        </td>
                        {% if (file.error) { %}
                        <td colspan="2" class="error"><?php echo t('index_error', 'Error'); ?>:
                        {%=file.error%}
                        </td>
                        {% } else { %}
                        <td colspan="2" class="preview"><span class="fade"></span></td>
                        {% } %}
                        </tr>
                        {% } %}
                    </script>

                    <script id="template-download" type="text/x-jquery-tmpl">
                    </script>

                </div>
                <!-- end uploader -->

            </div>

            <div class="clear"><!-- --></div>
        </div>
    </div>

    <!-- URL UPLOAD -->
    <div id="urlUpload"  <?php if($showUploads == false) { if ((getAllowedToUpload(0) == false) && (getAllowedToUpload(1) == true)) echo 'onClick="window.location=\'register.' . SITE_CONFIG_PAGE_EXTENSION . '\';"'; else echo 'onClick="alert(\''.t('index_uploading_disabled', 'Error: Uploading has been disabled.').'\'); return false;";'; } ?>>
        <div class="urlUploadMain ui-corner-all">
            <div id="fileUploadBadge" class="fileUploadBadge"></div>
            <div class="urlUploadMainInternal contentPageWrapper">

                <!-- url uploader -->
                <div>
                    <div id="urlFileUploader">
                        <form action="<?php echo _CONFIG_SITE_PROTOCOL . '://' . _CONFIG_SITE_FULL_URL; ?>/urlUploadHandler.php" method="POST" enctype="multipart/form-data">
                            <div class="initialUploadText">
                                <div class="uploadText">
                                    <h2><?php echo t('enter_urls', 'Enter Urls'); ?>:</h2>
                                </div>
                                <div class="clearLeft"><!-- --></div>

                                <div class="inputElement">
                                    <textarea name="urlList" id="urlList" class="urlList" placeholder="http://example-site.com/file.zip"></textarea>
                                    <div class="clear"><!-- --></div>
                                </div>
                            </div>
                            <div class="urlUploadFooter">
                                <div id="transferFilesButton" class="transferFilesButton" title="transfer files" onClick="urlUploadFiles();
                                            return false;"><!-- --></div>
                                <div class="baseText">
                                    <a class="showAdditionalOptionsLink"><?php echo UCFirst(t('options', 'options')); ?></a>&nbsp;&nbsp;|&nbsp;&nbsp;<?php echo t('enter_up_to_x_file_urls', 'Enter up to [[[MAX_REMOTE_URL_FILES]]] file urls. Separate each url on it\'s own line.', array('MAX_REMOTE_URL_FILES' => $maxPermittedUrls)); ?>
                                </div>
                                <div class="clear"><!-- --></div>
                            </div>
                            <div class="clear"><!-- --></div>
                        </form>
                    </div>

                    <div id="urlFileListingWrapper" class="urlFileListingWrapper hidden">
                        <div class="introText">
                            <h2><?php echo t('files', 'Files'); ?>:</h2>
                        </div>
                        <div class="clearLeft"><!-- --></div>

                        <div class="fileSection">
                            <table id="urls" class="urls" width="100%">
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="clearLeft"><!-- --></div>

                        <div class="fileSectionFooterText hidden">
                            <div class="copyAllLinkWrapper">
                                <a class="copyAllLink" data-clipboard-text="" href="#">[<?php echo t('copy_all_links', 'copy all links'); ?>]</a>
                            </div>
                            <div class="baseText">
                                <?php echo t('file_transfers_completed', 'File transfers completed.'); ?> <?php echo t('index_upload_more_files', '<a href="[[[WEB_ROOT]]]">Click here</a> to upload more files.', array('WEB_ROOT'=>WEB_ROOT)); ?>
                            </div>
                            <div class="clear"><!-- --></div>
                        </div>
                    </div>
                    
                </div>
                <!-- end url uploader -->

            </div>

            <div class="clear"><!-- --></div>
        </div>
    </div>

    <?php
    // append any plugin includes
    pluginHelper::includeAppends('index_tab_content.inc.php');
    ?>

</div>

<div id="additionalOptionsWrapper" style="display: none;">
    <div class="clear homePageSpacer"><!-- --></div>
    <div class="contentPageWrapper" style="padding-bottom: 0px;">
        <div class="pageSectionMainFull ui-corner-all">

            <div class="pageSectionMainInternal itemRight">
                <div class="left">
                    <h2><?php echo t('set_password', 'set password:'); ?></h2>
                    <div>

                        <p class="introText">
                            <?php echo t('enter_a_password_below_to_secure_your_files', 'When downloading these files, users will be prompted for a password, if set. Download managers will not work if a password is set.'); ?>
                        </p>
                        <ul>
                            <li class="field-container">
                                <label for="set_password">
                                    <span class="field-name"><?php echo t("set_file_password", "Set Password"); ?>:</span>
                                    <input id="set_password" name="set_password" type="password" value="<?php echo isset($lastname) ? safeOutputToScreen($lastname) : ''; ?>" class="uiStyle"></label>
                                </label>
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="clear"><!-- --></div>
            </div>

            <div class="pageSectionMainInternal itemLeft">
                <div class="left">
                    <h2><?php echo t('send_via_email', 'send via email:'); ?></h2>
                    <div>

                        <p class="introText">
                            <?php echo t('enter_an_email_address_below_to_send_the_list_of_urls', 'Enter an email address below to send the list of urls via email once they\'re uploaded.'); ?>
                        </p>
                        <ul>
                            <li class="field-container">
                                <label for="send_via_email">
                                    <span class="field-name"><?php echo t("email_address", "Email Address"); ?>:</span>
                                    <input id="send_via_email" name="send_via_email" type="text" value="" class="uiStyle"></label>
                                </label>
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="clear"><!-- --></div>
            </div>
            
            <div class="pageSectionMainInternal itemLeft">
                <div class="left">
                    <h2><?php echo t('store_in_folder', 'store in folder:'); ?></h2>
                    <div>

                        <p class="introText">
                            <?php echo t('select_folder_below_to_store_intro_text', 'Select a folder below to store these files in. All current uploads files will be available within these folders.'); ?>
                        </p>
                        <ul>
                            <li class="field-container">
                                <label for="folder_id">
                                    <span class="field-name"><?php echo t("folder_name", "Folder Name"); ?>:</span>
                                    <select id="folder_id" name="folder_id" class="uiStyle" <?php echo !$Auth->loggedIn() ? 'DISABLED="DISABLED"' : ''; ?> style="width: 210px;">
                                        <option value=""><?php echo !$Auth->loggedIn() ? t("index_login_to_enable", "- login to enable -") : t("index_default", "- default -"); ?></option>
                                        <?php
                                        if(COUNT($folderArr))
                                        {
                                            foreach($folderArr AS $id => $folderLabel)
                                            {
                                                echo '<option value="'.(int)$id.'"';
                                                if($fid == (int)$id)
                                                {
                                                    echo ' SELECTED';
                                                }
                                                echo '>'.safeOutputToScreen($folderLabel).'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </label>
                            </li>
                        </ul>

                    </div>
                </div>
                <div class="clear"><!-- --></div>
            </div>
            <div class="clear"><!-- --></div>
            
            <div class="pageSectionMainInternal" style="text-align: center; padding-top: 0px;">
                <img src="<?php echo SITE_IMAGE_PATH; ?>/upload_save_and_close.png" onClick="saveAdditionalOptions(); return false;" class="saveButton"/>
            </div>
            <div class="clear"><!-- --></div>
            
        </div>
        <div class="clear"><!-- --></div>
    </div>
</div>

<div class="clear homePageSpacer"><!-- --></div>
<div class="contentPageWrapper" style="padding-top: 12px;">
    <div style="float: right;">
        <?php include_once("_bannerRightContent.inc.php"); ?>
    </div>
    <div class="fileUploadContent ui-corner-all">
        <div class="fileUploadContentInternal contentPageWrapper">
            <div class="left">
                <h2><?php echo t('upload_share_and_manage_your_files_for_free', 'Upload, share and manage your files for free.'); ?></h2>
                <div class="homepageInfoWrapper">
                    <div class="icon1"><!-- --></div>
                    <div class="homepageInfo">
                        <div class="homepageInfoTitle"><?php echo t('store_and_manage_all_your_files', 'Store and manage all your files!'); ?></div>
                        <div class="homepageInfoText"><?php echo t('upload_multiple_files_at_once_and_keep_them', 'Upload multiple files at once and keep them forever on this site. If you\'re using FireFox or Chrome, you can simply drag &amp; drop your files to begin uploading.'); ?></div>
                    </div>
                    <div class="clear"><!-- --></div>
                </div>

                <div class="homepageInfoWrapper">
                    <div class="icon2"><!-- --></div>
                    <div class="homepageInfo">
                        <div class="homepageInfoTitle"><?php echo t('share_your_files_with_everyone', 'Share your files with everyone!'); ?></div>
                        <div class="homepageInfoText"><?php echo t('we_supply_you_with_all_the_tools_necessary_to_easily_share', 'We supply you with all the tools necessary to easily share your files. Use our pre-generated html code to link from your website or post directly to Facebook or Twitter.'); ?></div>
                    </div>
                    <div class="clear"><!-- --></div>
                </div>

                <div class="homepageInfoWrapper">
                    <div class="icon3"><!-- --></div>
                    <div class="homepageInfo">
                        <div class="homepageInfoTitle"><?php echo t('fast_and_instant_downloading', 'Fast and instant downloading!'); ?></div>
                        <div class="homepageInfoText"><?php echo t('our_premium_members_benefit_from_no_waiting_time_and_direct', 'Our premium members benefit from no waiting time and direct downloads for all of their files. Unlike other file hosts we don\'t limit the transfer speed of our downloads.'); ?></div>
                    </div>
                    <div class="clear"><!-- --></div>
                </div>

                <div class="homepageInfoWrapper">
                    <div class="icon4"><!-- --></div>
                    <div class="homepageInfo">
                        <div class="homepageInfoTitle"><?php echo t('email_large_attachments', 'Email large attachments!'); ?></div>
                        <div class="homepageInfoText"><?php echo t('no_longer_do_you_have_to_risk_a_large_file_being_bounced_by_a_mail', 'No longer do you have to risk a large file being bounced by a mail server. Upload and send your recipient a link to download the file. You can even track when it\'s been downloaded.'); ?></div>
                    </div>
                    <div class="clear"><!-- --></div>
                </div>
                <div class="clear"><!-- --></div>
            </div>
            <div class="clear"><!-- --></div>
        </div>
        <div class="clear"><!-- --></div>
    </div>
    <div class="clear"><!-- --></div>
</div>

<?php if (inPluginDemoMode()): ?>
    <script type="text/javascript">
                                $(function() {
                                    $("#plugin_notice").dialog({
                                        resizable: false,
                                        width: 600,
                                        modal: true,
                                        buttons: {
                                            "Close": function() {
                                                $(this).dialog("close");
                                            }
                                        }
                                    });
                                });
    </script>
    <div id="plugin_notice" title="All Plugins Demo">
        <p><span class="ui-icon ui-icon-alert" style="float: left; margin: 0 7px 20px 0;"></span>This demo has all the available YetiShare plugins <strong>enabled</strong>.  This includes our FTP Uploader, Rewards Program and the Media Player Plugin.</p>
        <p> If you want to see what the core script looks like without any plugins, go to <a href="<?php echo WEB_ROOT; ?>/?_p=0"><?php echo WEB_ROOT; ?>/?_p=0</a> or disable them using the link at the top of the page.</p>
        <p>Click 'close' below to continue to the demo site.</p>
    </div>
<?php endif; ?>

<?php
require_once('_footer.php');
?>