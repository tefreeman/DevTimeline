<?php
/* setup includes */
require_once('includes/master.inc.php');

// require login
$Auth->requireUser(WEB_ROOT.'/login.'.SITE_CONFIG_PAGE_EXTENSION);

/* load file */
if (isset($_REQUEST['u']))
{
    $file = file::loadById($_REQUEST['u']);
    if (!$file)
    {
        // failed lookup of file
        redirect(WEB_ROOT . '/account_home.' . SITE_CONFIG_PAGE_EXTENSION);
    }

    // check current user has permission to edit file
    if ($file->userId != $Auth->id)
    {
        redirect(WEB_ROOT . '/account_home.' . SITE_CONFIG_PAGE_EXTENSION);
    }
}
else
{
    redirect(WEB_ROOT . '/account_home.' . SITE_CONFIG_PAGE_EXTENSION);
}

/* setup page */
define("PAGE_NAME", t("edit_page_name", "Edit"));
define("PAGE_DESCRIPTION", t("edit_meta_description", "Edit existing item"));
define("PAGE_KEYWORDS", t("edit_meta_keywords", "edit, existing, item"));

/* handle submission */
if ((int) $_REQUEST['submitme'])
{
    // validation
    $filename = trim($_REQUEST['filename']);
    $filename = strip_tags($filename);
    $filename = str_replace(array("'", "\""), "", $filename);
    $reset_stats = (int) trim($_REQUEST['reset_stats']);
    $folder      = (int) trim($_REQUEST['folder']);
    if (!strlen($filename))
    {
        setError(t("please_enter_the_filename", "Please enter the filename"));
    }
    elseif (_CONFIG_DEMO_MODE == true)
    {
        setError(t("no_changes_in_demo_mode"));
    }

    // no errors
    if (!isErrors())
    {
        // update file
        $db = Database::getDatabase(true);
        $rs = $db->query('UPDATE file SET originalFilename = :originalFilename, folderId = :folderId WHERE id = :id', array('originalFilename' => $filename . '.' . $file->extension, 'folderId'         => $folder, 'id'               => $file->id));
        if ($rs)
        {
            // clean stats if needed
            if ($reset_stats == 1)
            {
                $db->query('UPDATE file SET visits = 0 WHERE id = :id', array('id' => $file->id));
                $db->query("DELETE FROM stats WHERE file_id = :id", array('id' => $file->id));
            }

            // redirect
            redirect(WEB_ROOT . "/account_home." . SITE_CONFIG_PAGE_EXTENSION.'?s='.urlencode(t('file_item_updated', 'File updated.')));
        }
        else
        {
            setError(t("problem_updating_item", "There was a problem updating the item, please try again later."));
        }
    }
}

// load folders
$folders = fileFolder::loadAllForSelect($Auth->id);

require_once('_header.php');
?>

<div class="contentPageWrapper">

    <?php
    if (isErrors())
    {
        echo outputErrors();
    }
    ?>

    <!-- main section -->
    <div class="pageSectionMainFull ui-corner-all">
        <div class="pageSectionMainInternal">
            <div id="pageHeader">
                <h2><?php echo t("edit_existing_item", "Edit Existing Item"); ?> (<?php echo safeOutputToScreen($file->originalFilename, null, 55); ?>)</h2>
            </div>
            <div>
                <p class="introText">
                    <?php echo t("edit_existing_item_intro_text", "Use the form below to amend the selected item."); ?>
                    <br/><br/>
                </p>

                <form class="international" method="post" action="<?php echo WEB_ROOT; ?>/account_edit_item.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>" id="form-join">
                    <ul>
                        <li class="field-container"><label for="filename"><span class="field-name"><?php echo t("filename", "filename"); ?></span><input type="text" value="<?php echo safeOutputToScreen($file->getFilenameExcExtension()); ?>" id="filename" name="filename" class="uiStyle" onFocus="showHideTip(this);" style="width:300px;"></label>
                            <div id="filenameTip" class="hidden formTip" style="left: 522px;">
                                <?php echo t('the_filename_on_download', 'The filename on download'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="folder"><span class="field-name"><?php echo t("folder", "folder"); ?></span><select id="folder" name="folder" class="uiStyle" onFocus="showHideTip(this);" style="width:300px;">
                                    <option value=""><?php echo t('_default_', '- Default -'); ?></option>
                                    <?php
                                    if (COUNT($folders))
                                    {
                                        foreach ($folders AS $id => $folderLabel)
                                        {
                                            echo '<option value="' . (int)$id . '"';
                                            if ((int)$id == $file->folderId)
                                            {
                                                echo ' SELECTED';
                                            }
                                            echo '>' . safeOutputToScreen($folderLabel) . '</option>';
                                        }
                                    }
                                    ?>
                                </select></label>
                            <div id="folderTip" class="hidden formTip" style="left: 522px;">
                                <?php echo t('the_items_folder', 'The items folder'); ?>
                            </div>
                        </li>

                        <li class="field-container"><label for="reset_stats"><span class="field-name"><?php echo t("reset_stats", "reset stats"); ?></span><select id="reset_stats" name="reset_stats" class="uiStyle" onFocus="showHideTip(this);">
                                    <option value="0"><?php echo t('no_keep_stats', 'No, keep stats'); ?></option>
                                    <option value="1"><?php echo t('yes_remove_stats', 'Yes, remove stats'); ?></option>
                                </select></label>
                            <div id="reset_statsTip" class="hidden formTip" style="left: 522px;">
                                <?php echo t('whether_to_reset_the_statistics_or_not', 'Whether to reset the statistics or not'); ?>
                            </div>
                        </li>

                        <li class="field-container">
                            <span class="field-name"></span>
                            <input tabindex="99" type="submit" name="submit" value="<?php echo t("update_item", "update item"); ?>" class="submitInput" />
                        </li>
                    </ul>

                    <input type="hidden" value="1" name="submitme"/>
                    <input type="hidden" value="<?php echo (int) $_REQUEST['u']; ?>" name="u"/>
                </form>
            </div>
            <div class="clear"><!-- --></div>
        </div>
    </div>
</div>

<?php
require_once('_footer.php');
?>