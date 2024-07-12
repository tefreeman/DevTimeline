<?php
// initial constants
define('ADMIN_PAGE_TITLE', 'Plugins');
define('ADMIN_SELECTED_PAGE', 'plugins');

// includes and security
include_once('_local_auth.inc.php');

// error/success messages
if (isset($_REQUEST['sa']))
{
    // update plugin config in the session
    $_SESSION['pluginConfigs'] = pluginHelper::loadPluginConfigurationFiles();

    adminFunctions::setSuccess('Plugin successfully added. To enable the plugin, install it below and configure any plugin specific settings.');
}
elseif (isset($_REQUEST['se']))
{
    // update plugin config in the session
    $_SESSION['pluginConfigs'] = pluginHelper::loadPluginConfigurationFiles();

    adminFunctions::setSuccess('Plugin settings updated.');
}
elseif (isset($_REQUEST['sm']))
{
    // update plugin config in the session
    $_SESSION['pluginConfigs'] = pluginHelper::loadPluginConfigurationFiles();

    // redirect to plugin settings
    if(strlen(trim($_REQUEST['plugin'])))
    {
        redirect(PLUGIN_WEB_ROOT.'/'.urlencode(trim($_REQUEST['plugin'])).'/admin/settings.php?id='.(int)$_REQUEST['id'].'&sm='.urlencode($_REQUEST['sm']));
    }
    else
    {
        adminFunctions::setSuccess(urldecode($_REQUEST['sm']));
    }
}
elseif (isset($_REQUEST['error']))
{
    adminFunctions::setError(urldecode($_REQUEST['error']));
}

// page header
include_once('_header.inc.php');
?>

<script>
    oTable = null;
    gPluginId = null;
    $(document).ready(function(){
        // datatable
        oTable = $('#fileTable').dataTable({
            "sPaginationType": "full_numbers",
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": 'ajax/plugin_manage.ajax.php',
            "bJQueryUI": true,
            "iDisplayLength": 100,
            "bLengthChange": false,
            "bFilter": false,
            "aoColumns" : [   
                { bSortable: false, sWidth: '3%', sName: 'file_icon', sClass: "center" },
                { bSortable: false, sName: 'plugin_title', sWidth: '20%' },
                { bSortable: false, sName: 'description' },
                { bSortable: false, sName: 'directory_name', sWidth: '15%' },
                { bSortable: false, sName: 'installed', sWidth: '10%', sClass: "center" },
                { bSortable: false, sWidth: '15%', sClass: "center" }
            ],
            "oLanguage": {
                "sEmptyTable": "You have no plugins configured within your site. Go to <a href='http://www.yetishare.com' target='_blank'>YetiShare.com</a> to see a list of available plugins."
            }
        });
        
        // dialog box
        $( "#confirmInstall" ).dialog({
            modal: true,
            autoOpen: false,
            width: 450,
            buttons: {
                "Install": function() {
                    installPlugin();
                    $("#confirmInstall").dialog("close");
                },
                "Cancel": function() {
                    $("#confirmInstall").dialog("close");
                }
            },
            open: function() {
                resetOverlays();
            }
        });
        
        $( "#confirmUninstall" ).dialog({
            modal: true,
            autoOpen: false,
            width: 450,
            buttons: {
                "Uninstall": function() {
                    uninstallPlugin();
                    $("#confirmUninstall").dialog("close");
                },
                "Cancel": function() {
                    $("#confirmUninstall").dialog("close");
                }
            },
            open: function() {
                resetOverlays();
            }
        });
    });

    function reloadTable()
    {
        oTable.fnDraw(false);
    }
    
    function confirmInstallPlugin(plugin_id)
    {
        $('#confirmInstall').dialog('open');
        gPluginId = plugin_id;
    }
    
    function confirmUninstallPlugin(plugin_id)
    {
        $('#confirmUninstall').dialog('open');
        gPluginId = plugin_id;
    }
    
    function installPlugin()
    {
        $.ajax({
            type: "POST",
            url: "ajax/plugin_manage_install.ajax.php",
            data: { plugin_id: gPluginId },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    showError(json.msg, 'messageContainer');
                }
                else
                {
                    //showSuccess(json.msg, 'messageContainer');
                    //reloadTable();
                    window.location='plugin_manage.php?id='+encodeURIComponent(json.id)+'&plugin='+encodeURIComponent(json.plugin)+'&sm='+encodeURIComponent(json.msg);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'messageContainer');
            }
        });
    }
    
    function uninstallPlugin()
    {
        $.ajax({
            type: "POST",
            url: "ajax/plugin_manage_uninstall.ajax.php",
            data: { plugin_id: gPluginId },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    showError(json.msg, 'messageContainer');
                }
                else
                {
                    //showSuccess(json.msg, 'messageContainer');
                    //reloadTable();
                    window.location='plugin_manage.php?sm='+encodeURIComponent(json.msg);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'messageContainer');
            }
        });
    }
</script>

<div class="row clearfix">
    <div class="sectionLargeIcon largeServerIcon"></div>
    <div class="widget clearfix">
        <h2>Manage Plugins</h2>
        <div class="widget_inside">
            <?php echo adminFunctions::compileNotifications(); ?>
            <span id="messageContainer"></span>
            <div class="col_12">
                <table id='fileTable' class='dataTable'>
                    <thead>
                        <tr>
                            <th></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t("plugin_title", "plugin title")); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t("description", "description")); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t("directory_name", "directory name")); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t("installed", "installed?")); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t("action", "action")); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <input type="submit" value="Add Plugin" class="button blue" onClick="window.location='plugin_manage_add.php';"/>
			<input type="submit" value="Get Plugins" class="button blue" onClick="window.open('http://www.yetishare.com/plugins.html','_blank');"/>
        </div>
    </div>
</div>

<div id="confirmInstall" title="Confirm Action">
    <p>Are you sure you want to install this plugin?</p>
</div>

<div id="confirmUninstall" title="Confirm Action">
    <p>Are you sure you want to uninstall this plugin? All data associated with the plugin will be deleted and unrecoverable.</p>
</div>

<?php
include_once('_footer.inc.php');
?>