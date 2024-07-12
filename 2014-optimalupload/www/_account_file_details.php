<?php
// setup includes
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

?>

<div class="accountFileDetailsPopup">
    <div id="pageHeader">
        <div class="pageHeaderPopupButtons">
            <div class="actions button-container">
                <div class="button-group minor-group">
                    <?php if($file->statusId == 1): ?>
                    <a class="button icon edit" href="<?php echo WEB_ROOT; ?>/account_edit_item.<?php echo SITE_CONFIG_PAGE_EXTENSION; ?>?u=<?php echo (int)$file->id; ?>"><?php echo UCWords(t('account_file_details_edit_file', 'Edit File')); ?></a>
                    <a class="button icon trash" href="<?php echo safeOutputToScreen($file->getDeleteUrl(WEB_ROOT.'/account_home.html')); ?>"><?php echo UCWords(t('account_file_details_delete', 'Delete')); ?></a>
                    <a class="button icon arrowdown" href="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>"><?php echo UCWords(t('account_file_details_download', 'Download')); ?></a>
                    <?php endif; ?>
                    <a class="button icon clock" href="<?php echo safeOutputToScreen($file->getStatisticsUrl()); ?>"><?php echo UCWords(t('account_file_details_stats', 'Stats')); ?></a>
                </div>
            </div>
        </div>
        <div class="pageHeaderPopupTitle">
            <h2 title="<?php echo safeOutputToScreen($file->originalFilename); ?>"><?php echo safeOutputToScreen($file->originalFilename, null, 50); ?></h2>
        </div>
    </div>
    
    <?php if(file::getIconPreviewImageUrlLarger((array)$file)): ?>
    <div style="float: right; padding-right: 12px;">
        <?php if($file->statusId == 1): ?><a href="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" target="_blank"><?php endif; ?>
            <img src="<?php echo file::getIconPreviewImageUrlLarger((array)$file); ?>" width="160" alt="" style="padding: 7px;"/>
        <?php if($file->statusId == 1): ?></a><?php endif; ?>
    </div>
    <?php endif; ?>
    <div>
        <table class="accountStateTable" style="width: 680px;">
            <tbody>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('filename', 'filename')); ?>:
                    </td>
                    <td>
                        <?php echo safeOutputToScreen($file->originalFilename); ?><?php if($file->statusId == 1): ?>&nbsp;&nbsp;<a href="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" target="_blank">(<?php echo t('download', 'download'); ?>)</a><?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('filesize', 'filesize')); ?>:
                    </td>
                    <td>
                        <?php echo formatSize($file->fileSize); ?>
                    </td>
                </tr>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('added', 'added')); ?>:
                    </td>
                    <td>
                        <?php echo dater($file->uploadedDate); ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="accountStateTable" style="width: 680px; margin-top: 16px;">
            <tbody>
                <tr>
                    <?php if($file->statusId == 1): ?>
                    <td class="first">
                        <?php echo UCWords(t('url', 'url')); ?>:
                    </td>
                    <td>
                        <a href="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" target="_blank"><?php echo safeOutputToScreen($file->getFullShortUrl()); ?></a>
                    </td>
                    <?php else: ?>
                    <td class="first">
                        <?php echo UCWords(t('status', 'status')); ?>:
                    </td>
                    <td>
                        <?php echo safeOutputToScreen(UCWords(file::getStatusLabel($file->statusId))); ?>
                    </td>
                    <?php endif; ?>
                </tr>
            </tbody>
        </table>
        
        <table class="accountStateTable" style="width: 680px; margin-top: 16px;">
            <tbody>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('downloads', 'downloads')); ?>:
                    </td>
                    <td>
                        <strong><?php echo safeOutputToScreen($file->visits); ?></strong>&nbsp;&nbsp;<?php echo ($file->lastAccessed != null)?('('.UCWords(t('last_accessed', 'last accessed')).': '.dater($file->lastAccessed).')'):''; ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"><!-- --></div>

    <?php if($file->statusId == 1): ?>
    <div id="pageHeader" style="padding-top: 12px;">
        <h2><?php echo UCWords(t("download_urls", "download urls")); ?></h2>
    </div>
    <div>
        <table class="accountStateTable">
            <tbody>
                <tr>
                    <td class="first">
                        <?php echo t('html_code', 'HTML Code'); ?>:
                    </td>
                    <td class="htmlCode">
                        <?php echo $file->getHtmlLinkCode(); ?>
                    </td>
                </tr>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('forum_code', 'forum code')); ?>
                    </td>
                    <td class="htmlCode">
                        <?php echo $file->getForumLinkCode(); ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="clear"><!-- --></div>
    <?php endif; ?>

    <div id="pageHeader" style="padding-top: 12px;">
        <h2><?php echo UCWords(t("options", "options")); ?></h2>
    </div>
    <div>
        <table class="accountStateTable">
            <tbody>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('statistics_url', 'statistics url')); ?>:
                    </td>
                    <td>
                        <a href="<?php echo safeOutputToScreen($file->getStatisticsUrl()); ?>" target="_blank"><?php echo safeOutputToScreen($file->getStatisticsUrl()); ?></a>
                    </td>
                </tr>
                
                <?php if($file->statusId == 1): ?>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('public_info_page', 'public info page')); ?>:
                    </td>
                    <td>
                        <a href="<?php echo safeOutputToScreen($file->getInfoUrl()); ?>" target="_blank"><?php echo current(explode("?", safeOutputToScreen($file->getInfoUrl()))); ?></a>
                    </td>
                </tr>
                
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('delete_file_url', 'delete file url')); ?>:
                    </td>
                    <td>
                        <a href="<?php echo safeOutputToScreen($file->getDeleteUrl()); ?>" target="_blank"><?php echo safeOutputToScreen($file->getDeleteUrl()); ?></a>
                    </td>
                </tr>
                <tr>
                    <td class="first">
                        <?php echo UCWords(t('share_file', 'share file')); ?>:
                    </td>
                    <td style="height: 33px;">
                        <!-- AddThis Button BEGIN -->
                        <div class="addthis_toolbox addthis_default_style addthis_32x32_style">
                        <a class="addthis_button_preferred_1" addthis:url="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" addthis:title="<?php echo safeOutputToScreen($file->originalFilename); ?>"></a>
                        <a class="addthis_button_preferred_2" addthis:url="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" addthis:title="<?php echo safeOutputToScreen($file->originalFilename); ?>"></a>
                        <a class="addthis_button_preferred_3" addthis:url="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" addthis:title="<?php echo safeOutputToScreen($file->originalFilename); ?>"></a>
                        <a class="addthis_button_preferred_4" addthis:url="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" addthis:title="<?php echo safeOutputToScreen($file->originalFilename); ?>"></a>
                        <a class="addthis_button_compact" addthis:url="<?php echo safeOutputToScreen($file->getFullShortUrl()); ?>" addthis:title="<?php echo safeOutputToScreen($file->originalFilename); ?>"></a>
                        </div>
                        <!-- AddThis Button END -->
                    </td>
                </tr>
                <?php endif; ?>
                
            </tbody>
        </table>
    </div>
    <div class="clear"><!-- --></div>
</div>