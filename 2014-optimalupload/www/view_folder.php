<?php
/* setup includes */
require_once('includes/master.inc.php');

// initial checks
$folderId  = $_REQUEST['f'];
$folderExp = explode('~', $folderId);
$folderId  = (int) $folderExp[0];

// make sure it's a public folder or the owner is logged in
if ($folderId)
{
    $fileFolder = fileFolder::loadById($folderId);
    if (!$fileFolder)
    {
        // failed lookup of the fileFolder
        redirect(WEB_ROOT . '/index.' . SITE_CONFIG_PAGE_EXTENSION);
    }

    // check the folder is public
    if (($fileFolder->isPublic == 0) && ($fileFolder->userId != $Auth->id))
    {
        redirect(WEB_ROOT . '/index.' . SITE_CONFIG_PAGE_EXTENSION);
    }
}
else
{
    redirect(WEB_ROOT . '/account_folders.' . SITE_CONFIG_PAGE_EXTENSION);
}

// check for password if we need it
$showFolder = true;
if (strlen($fileFolder->accessPassword) > 0)
{
    /* check folder password */
    if ((int) $_REQUEST['passwordSubmit'])
    {
        // check password
        $folderPassword = trim($_REQUEST['folderPassword']);

        if (!strlen($folderPassword))
        {
            setError(t("please_enter_the_folder_password", "Please enter the folder password"));
        }
        else
        {
            if (md5($folderPassword) == $fileFolder->accessPassword)
            {
                // successful
                $_SESSION['folderPassword'] = md5($folderPassword);
            }
            else
            {
                // login failed
                setError(t("password_is_invalid", "The folder password is invalid"));
            }
        }
    }

    // figure out whether to show the folder
    $showFolder = false;
    if (isset($_SESSION['folderPassword']))
    {
        // check password
        if ($_SESSION['folderPassword'] == $fileFolder->accessPassword)
        {
            $showFolder = true;
        }
    }
}

// if the owner is logged in, ignore the password prompt
if ($fileFolder->userId == $Auth->id)
{
    $showFolder = true;
}

/* setup page */
define("PAGE_NAME", t("account_home_page_name", "View Folder"));
define("PAGE_DESCRIPTION", t("account_home_meta_description", "Your Account Home"));
define("PAGE_KEYWORDS", t("account_home_meta_keywords", "account, home, file, your, interface, upload, download, site"));

require_once('_header.php');

// show login box if password required
if ($showFolder == false)
{
    ?>
    <div class="contentPageWrapper">
        <?php
        if (isErrors())
        {
            echo outputErrors();
        }
        ?>
        <!-- password form -->
        <div class="pageSectionMain ui-corner-all">
            <div class="pageSectionMainInternal">
                <div id="pageHeader">
                    <h2><?php echo t("folder_restricted", "Folder Restricted"); ?></h2>
                </div>
                <div>
                    <p class="introText">
                        <?php echo t("folder_login_intro_text", "Please enter the password below to access this folder."); ?>
                    </p>
                    <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/<?php echo $fileFolder->id; ?>~f" id="form-join" AUTOCOMPLETE="off">
                        <ul>
                            <li class="field-container"><label for="folderPassword">
                                    <span class="field-name"><?php echo t("password", "password"); ?></span>
                                    <input type="password" tabindex="2" value="" id="folderPassword" name="folderPassword" class="uiStyle" onFocus="showHideTip(this);"></label>
                                <div id="loginPasswordMainTip" class="hidden formTip">
                                    <?php echo t("folder_password_requirements", "The folder password."); ?>
                                </div>
                            </li>

                            <li class="field-container">
                                <span class="field-name"></span>
                                <input tabindex="99" type="submit" name="submit" value="<?php echo t("continue", "continue"); ?>" class="submitInput" />
                            </li>
                        </ul>

                        <input type="hidden" value="1" name="passwordSubmit"/>
                    </form>

                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <?php include_once("_bannerRightContent.inc.php"); ?>
        <div class="clear"><!-- --></div>
    </div>
    <?php
}
// show folder listing
else
{
    // load any public child folders
    $childFolders = fileFolder::loadAllPublicChildren($folderId);
    
    // load all files by folder
    $files = file::loadAllActiveByFolderId($folderId);
    ?>

    <script>
        $(document).ready(function() {
            $('#fileData').dataTable( {
                "sPaginationType": "full_numbers",
                "aoColumns": [
                    { "asSorting": [ "asc", "desc" ] },
                    { "asSorting": [ "asc", "desc" ] },
                    null
                ],
                "oLanguage": {
                    "oPaginate": {
                        "sFirst": "<?php echo t('datatable_first', 'First'); ?>",
                        "sPrevious": "<?php echo t('datatable_previous', 'Previous'); ?>",
                        "sNext": "<?php echo t('datatable_next', 'Next'); ?>",
                        "sLast": "<?php echo t('datatable_last', 'Last'); ?>"
                    },
                    "sEmptyTable": "<?php echo t('datatable_no_data_available_in_table', 'No data available in table'); ?>",
                    "sInfo": "<?php echo t('datatable_showing_x_to_x_of_total_entries', 'Showing _START_ to _END_ of _TOTAL_ entries'); ?>",
                    "sInfoEmpty": "<?php echo t('datatable_no_data', 'No data'); ?>",
                    "sLengthMenu": "<?php echo t('datatable_show_menu_entries', 'Show _MENU_ entries'); ?>",
                    "sProcessing": "<?php echo t('datatable_loading_please_wait', 'Loading, please wait...'); ?>",
                    "sInfoFiltered": "<?php echo t('datatable_base_filtered', ' (filtered)'); ?>",
                    "sSearch": "<?php echo t('datatable_search_text', 'Search:'); ?>",
                    "sZeroRecords": "<?php echo t('datatable_no_matching_records_found', 'No matching records found'); ?>"
                }
            } );
        } );
    </script>

    <div class="contentPageWrapper">

        <!-- main section -->
        <div class="pageSectionMainFull ui-corner-all">
            <div class="pageSectionMainInternal">
                <div id="pageHeader">
                    <h2><?php echo t("files_within_folder", "Files Within Folder"); ?> '<?php echo htmlentities($fileFolder->folderName); ?>'</h2>
                </div>
                
                <?php if($fileFolder->userId == $Auth->id): ?>
                <div>
                    <p class="introText">
                        <?php
                        if($fileFolder->isPublic == 0)
                        {
                            echo t('folder_share_this_folder_can_not_be_shared_as_it_is_not_publicly_accessible', 'This folder can not be shared as it is not set to a publicly accessible folder. Only users with access to your account can see this listing.');
                        }
                        else
                        {
                            echo t('folder_share_you_can_share_this_page_with_other_external_users', 'You can share this page with other users who do not have access to your account. Just copy the website url in the url bar and provide this via email or other sharing method.');
                            if(strlen($fileFolder->accessPassword))
                            {
                                echo '<br/><br/>';
                                echo t('folder_share_as_youve_set_a_password_on_this_folder', 'Note: As you\'ve set a password on this folder, users will need to correctly enter this before they gain access to this page.');
                            }
                        }
                        ?>
                    </p>
                </div>
                <?php endif; ?>

                <div>
                    <p class="introText">
                        <?php
                        if ($files || $folders)
                        {
                            echo '<table id="fileData" width="100%" cellpadding="3" cellspacing="0">';
                            echo '<thead>';
                            echo '<th style="width: 19px;" class="ui-state-default"></th>';
                            echo '<th class="ui-state-default">' . t('download_url_filename', 'Download Url/Filename:') . '</th>';
                            echo '<th style="width: 85px; text-align: center;" class="ui-state-default">' . t('options', 'Options:') . '</th>';
                            echo '</thead>';
                            echo '<tbody>';
                            foreach ($childFolders AS $childFolder)
                            {
                                // get total files
                                $totalFiles = 0;
                                $allFiles = file::loadAllActiveByFolderId($childFolder['id']);
                                if($allFiles)
                                {
                                    $totalFiles = COUNT($allFiles);
                                }
                                
                                echo '<tr>';
                                echo '<td class="txtCenter">';
                                echo '  <img src="' . SITE_IMAGE_PATH . '/folder.png" width="32" height="32" title="' . t('folder', 'folder') . '"/>';
                                echo '</td>';
                                echo '<td title="' . $childFolder['folderName'] . '">';
                                echo '<a href="' . WEB_ROOT . '/' . $childFolder['id'] . '~f">' . safeOutputToScreen($childFolder['folderName']) . '</a>';
                                echo '<br/><span style="color: #999;">'.$totalFiles.' file'.($totalFiles != 1?'s':'').'</font>';
                                echo '</td>';

                                $links = array();
                                $links[] = '<a href="' . WEB_ROOT . '/' . $childFolder['id'] . '~f"><img src="' . SITE_IMAGE_PATH . '/group.png" width="16" height="16" title="share" style="margin:1px;"/></a>';
                                echo '<td class="txtCenter">' . implode("&nbsp;", $links) . '</td>';
                                echo '</tr>';
                            }
                            foreach ($files AS $file)
                            {
                                echo '<tr>';
                                echo '<td class="txtCenter">';
                                $fileTypePath = DOC_ROOT . '/themes/' . SITE_CONFIG_SITE_THEME . '/images/file_icons/32px/' . $file['extension'] . '.png';
                                if (file_exists($fileTypePath))
                                {
                                    echo '  <img src="' . SITE_IMAGE_PATH . '/file_icons/32px/' . $file['extension'] . '.png" width="32" height="32" title="' . $file['extension'] . ' file"/>';
                                }
                                else
                                {
                                    echo '  <img src="' . SITE_IMAGE_PATH . '/file_icons/32px/_page.png" width="32" height="32" title="' . $file['extension'] . ' file"/>';
                                }
                                echo '</td>';
                                echo '<td title="' . safeOutputToScreen($file['originalFilename']) . '">';
                                echo '<a href="' . file::getFileUrl($file['id']) . '" target="_blank">' . str_replace(array('http://', 'https://'), '', file::getFileUrl($file['id'])) . '</a>';
                                echo '<br/><span style="color: #999;">' . $file['originalFilename'];
                                echo '&nbsp;(' . formatSize($file['fileSize']) . ')</font>';
                                echo '</td>';

                                $links = array();
                                $links[] = '<a href="' . file::getFileShortInfoUrl($file['id']) . '"><img src="' . SITE_IMAGE_PATH . '/group.png" width="16" height="16" title="share" style="margin:1px;"/></a>';
                                echo '<td class="txtCenter">' . implode("&nbsp;", $links) . '</td>';
                                echo '</tr>';
                            }
                            echo '</tbody>';
                            echo '</table>';
                        }
                        else
                        {
                            echo '<strong>- '.t('there_are_no_files_within_this_folder', 'There are no files within this folder.').'</strong>';
                        }
                        ?>
                    </p>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

<?php
require_once('_footer.php');
?>