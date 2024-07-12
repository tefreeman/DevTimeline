<?php

// includes and security
include_once('../_local_auth.inc.php');

// prepare variables
$server_label = '';
$status_id = '';
$server_type = '';
$ftp_host = '';
$ftp_port = 21;
$ftp_username = '';
$ftp_password = '';
$sftp_host = '';
$sftp_port = 22;
$sftp_username = '';
$sftp_password = '';
$storage_path = 'files/';
$formType = 'set the new';
$file_server_domain_name = '';
$script_path = '/';
$max_storage_space = 0;
$server_priority = 0;
$route_via_main_site = 0;

// is this an edit?
$fileServerId = null;

if(isset($_REQUEST['gEditFileServerId']))
{
    $fileServerId = (int)$_REQUEST['gEditFileServerId'];
    if($fileServerId)
    {
        $sQL           = "SELECT * FROM file_server WHERE id=".$fileServerId;
        $serverDetails = $db->getRow($sQL);
        if($serverDetails)
        {
            $server_label = $serverDetails['serverLabel'];
            $status_id = $serverDetails['statusId'];
            $server_type = $serverDetails['serverType'];
            $ftp_host = $serverDetails['ipAddress'];
            $ftp_port = $serverDetails['ftpPort'];
            $ftp_username = $serverDetails['ftpUsername'];
            $ftp_password = $serverDetails['ftpPassword'];
            $sftp_host = $serverDetails['sftpHost'];
            $sftp_port = $serverDetails['sftpPort'];
            $sftp_username = $serverDetails['sftpUsername'];
            $sftp_password = $serverDetails['sftpPassword'];
            $storage_path = $serverDetails['storagePath'];
            $formType = 'update the';
            $file_server_domain_name = $serverDetails['fileServerDomainName'];
            $script_path = $serverDetails['scriptPath'];
            $max_storage_space = strlen($serverDetails['maximumStorageBytes'])?$serverDetails['maximumStorageBytes']:0;
            $server_priority = (int)$serverDetails['priority'];
            $route_via_main_site = (int)$serverDetails['routeViaMainSite'];
        }
    }
}

// load all server statuses
$sQL           = "SELECT id, label FROM file_server_status ORDER BY label";
$statusDetails = $db->getRows($sQL);

// prepare whether we should disable local server or not
$isDefaultServer = false;
if($server_label == 'Local Default')
{
    $isDefaultServer = true;
}

// prepare result
$result = array();
$result['error'] = false;
$result['msg'] = '';
$result['html'] = 'Could not load the form, please try again later.';

$result['html']  = '<p style="padding-bottom: 4px;">Use the form below to '.$formType.' file server details.</p>';
$result['html'] .= '<span id="popupMessageContainer"></span>';
$result['html'] .= '<form id="addFileServerForm">';

$result['html'] .= '<div class="form">';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("server_label", "server label")).':</label>
                        <div class="input">
                            <input name="server_label" id="server_label" type="text" value="'.adminFunctions::makeSafe($server_label).'" class="large" '.($isDefaultServer?'DISABLED':'').'/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("status", "status")).':</label>
                        <div class="input">
                            <select name="status_id" id="status_id">';
                                foreach($statusDetails AS $statusDetail)
                                {
                                    $result['html'] .= '        <option value="'.$statusDetail['id'].'"';
                                    if($status_id == $statusDetail['id'])
                                    {
                                        $result['html'] .= '        SELECTED';
                                    }
                                    $result['html'] .= '        >'.UCWords($statusDetail['label']).'</option>';
                                }
                                $result['html'] .= '        </select>
                        </div>
                    </div>';

$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("server_type", "server type")).':</label>
                        <div class="input">
                            <select name="server_type" id="server_type" class="xxlarge" onChange="showHideFTPElements(); return false;" '.($isDefaultServer?'DISABLED':'').'>
                                <option value="local"'.($server_type=='local'?' SELECTED':'').'>Local (storage located on the same server as your site)</option>
                                <option value="direct"'.($server_type=='direct'?' SELECTED':'').'>Remote Direct (users upload directly to remote file server)</option>
                                <option value="ftp"'.($server_type=='ftp'?' SELECTED':'').'>FTP (uses FTP via PHP to upload files into storage)</option>';

$params = pluginHelper::includeAppends('admin_server_manage_add_form_type_select.inc.php', array('html'=>'', 'server_type'=>$server_type));
if(isset($params['html']))
{
    $result['html'] .= $params['html'];
}

$result['html'] .= '            <!--<option value="sftp"'.($server_type=='sftp'?' SELECTED':'').'>SFTP</option>-->
                            </select>
                        </div>
                    </div>';

$result['html'] .= '<span class="localElements" style="display: none;">';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("storage_path", "storage path")).':</label>
                        <div class="input">
                            <input name="storage_path" id="local_storage_path" type="text" value="'.adminFunctions::makeSafe($storage_path).'" class="large" '.($isDefaultServer?'DISABLED':'').'/>
                        </div>
                    </div>';
$result['html'] .= '</span>';

$result['html'] .= '<span class="ftpElements" style="display: none;">';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("ftp_host", "ftp host")).':</label>
                        <div class="input">
                            <input name="ftp_host" id="ftp_host" type="text" value="'.adminFunctions::makeSafe($ftp_host).'"/>
                        </div>

                        <label>'.UCWords(adminFunctions::t("ftp_port", "ftp port")).':</label>
                        <div class="input">
                            <input name="ftp_port" id="ftp_port" type="text" value="'.adminFunctions::makeSafe($ftp_port).'" class="small"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix  alt-highlight">
                        <label>'.UCWords(adminFunctions::t("ftp_username", "ftp username")).':</label>
                        <div class="input">
                            <input name="ftp_username" id="ftp_username" type="text" value="'.adminFunctions::makeSafe($ftp_username).'"/>
                        </div>
                        <label>'.UCWords(adminFunctions::t("ftp_password", "ftp password")).':</label>
                        <div class="input">
                            <input name="ftp_password" id="ftp_password" type="password" value="'.adminFunctions::makeSafe($ftp_password).'"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("storage_path", "storage path")).':</label>
                        <div class="input">
                            <input name="storage_path" id="ftp_storage_path" type="text" value="'.adminFunctions::makeSafe($storage_path).'" class="large"/><br/><br/>- As the FTP user would see it. Login with this FTP user using an FTP client to confirm<br/>the path to use.
                        </div>
                    </div>';
$result['html'] .= '</span>';

$result['html'] .= '<span class="sftpElements" style="display: none;">';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("sftp_host", "sftp host")).':</label>
                        <div class="input">
                            <input name="sftp_host" id="sftp_host" type="text" value="'.adminFunctions::makeSafe($sftp_host).'" class="large"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("sftp_port", "sftp port")).':</label>
                        <div class="input">
                            <input name="sftp_port" id="sftp_port" type="text" value="'.adminFunctions::makeSafe($sftp_port).'" class="small"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("sftp_username", "sftp username")).':</label>
                        <div class="input">
                            <input name="sftp_username" id="sftp_username" type="text" value="'.adminFunctions::makeSafe($sftp_username).'"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("sftp_password", "sftp password")).':</label>
                        <div class="input">
                            <input name="sftp_password" id="sftp_password" type="password" value="'.adminFunctions::makeSafe($sftp_password).'"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("storage_path", "storage path")).':</label>
                        <div class="input">
                            <input name="storage_path" id="sftp_storage_path" type="text" value="'.adminFunctions::makeSafe($storage_path).'" class="large"/>
                        </div>
                    </div>';
$result['html'] .= '</span>';

$result['html'] .= '<span class="directElements" style="display: none;">';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("file_server_domain_name", "file server domain name")).':</label>
                        <div class="input">
                            <input name="file_server_domain_name" id="file_server_domain_name" placeholder="i.e. fs1.'._CONFIG_SITE_HOST_URL.'" type="text" value="'.adminFunctions::makeSafe($file_server_domain_name).'" onKeyUp="updateUrlParams();" class="xxlarge"/>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("site_path", "site path")).':</label>
                        <div class="input">
                            <input name="script_path" id="script_path" type="text" placeholder="/ - root, unless you installed into a sub-folder" value="'.adminFunctions::makeSafe($script_path).'" class="xxlarge" onKeyUp="updateUrlParams();"/><br/><br/>User /, unless you\'ve installed into a sub-folder.
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("file_storage_path", "file storage path")).':</label>
                        <div class="input">
                            <input name="storage_path" id="direct_storage_path" type="text" value="'.adminFunctions::makeSafe($storage_path).'" class="large"/><br/><br/>Which folder to store files in, related to the script root. Normally files/
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("use_main_site_url", "use main site url")).':</label>
                        <div class="input">
                            <select name="route_via_main_site" id="route_via_main_site">';
                                $options = array(0=>'no', 1=>'yes');
                                foreach($options AS $k=>$option)
                                {
                                    $result['html'] .= '        <option value="'.$k.'"';
                                    if($route_via_main_site == $k)
                                    {
                                        $result['html'] .= '        SELECTED';
                                    }
                                    $result['html'] .= '        >'.UCWords($option).'</option>';
                                }
                                $result['html'] .= '
                            </select>
                            <br/><br/><span style="width:520px; display: inline-block;">If \'yes\' '._CONFIG_SITE_HOST_URL.' will be used for all download urls generated on the site. Otherwise the above \'File Server Domain Name\' will be used. Changing this will not impact any existing download urls.</span>
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("code_setup", "code setup")).':</label>
                        <span style="width:550px; display: inline-block; padding-top: 6px; padding-left: 20px;">
                            Direct file server requirements: PHP5.3+, Apache Mod Rewrite, remote access to your MySQL database.<br/><br/>
                            So that your direct file server can receive the uploads and process downloads, it needs a copy of the full codebase installed. Upload all the files from your main site ('._CONFIG_SITE_HOST_URL.') to your new file server. This includes any plugin files within the plugin folder.<br/><br/>
                            Once uploaded, replace the _config.inc.php file on the new file server with the one listed below. Set your database password in the file (_CONFIG_DB_PASS). We\'ve removed it for security.<br/><br/>
                            <ul class="adminList"><li><a id="configLink" href="server_manage_direct_get_config_file.php?fileName=_config.inc.php" style="text-decoration: underline;">_config.inc.php</a></li></ul><br/>
                            In addition, replace the \'.htaccess\' on the file server with the one listed below.<br/><br/>
                            <ul class="adminList"><li><a id="htaccessLink" href="server_manage_direct_get_config_file.php?fileName=.htaccess&REWRITE_BASE=/" style="text-decoration: underline;">.htaccess</a></li></ul><br/>
                            Note: Only local file storage is available for direct server uploads so ensure the directory you\'ve set above as \'server storage path\' has been created with write permissions. (chmod 755 or 777)
                        </span>
                    </div>';
$result['html'] .= '</span>';

$result['html'] .= '</div>';

$result['html'] .= '<div class="form" style="margin-top: 12px;">';
$result['html'] .= '<div class="clearfix alt-highlight">
                        <label>'.UCWords(adminFunctions::t("max_storage_bytes", "max storage (bytes)")).':</label>
                        <div class="input">
                            <input name="max_storage_space" id="max_storage_space" type="text" value="'.adminFunctions::makeSafe($max_storage_space).'" class="medium" placeholder="2199023255552 = 2TB"/>&nbsp;bytes. Use zero for unlimited.
                        </div>
                    </div>';
$result['html'] .= '<div class="clearfix">
                        <label>'.UCWords(adminFunctions::t("server_priority", "server priority")).':</label>
                        <div class="input">
                            <input name="server_priority" id="server_priority" type="text" value="'.adminFunctions::makeSafe($server_priority).'" class="medium"/>&nbsp;A number. In order from lowest. 0 to ignore.<br/><br/>- Use for multiple servers when others are full. So when server with priority of 1 is full, server<br/>with priority of 2 will be used next for new uploads. 3 next and so on. "Server selection method"<br/>must be set to "Until Full" to enable this functionality.
                        </div>
                    </div>';
$result['html'] .= '</div>';

$result['html'] .= '</form>';

echo json_encode($result);
exit;
