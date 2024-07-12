<?php
// initial constants
define('ADMIN_PAGE_TITLE', 'Site Settings');
define('ADMIN_SELECTED_PAGE', 'configuration');

// includes and security
include_once('_local_auth.inc.php');

// page header
include_once('_header.inc.php');

// load all file status
$sQL           = "SELECT config_group FROM site_config WHERE config_group != 'system' GROUP BY config_group ORDER BY config_group";
$groupDetails = $db->getRows($sQL);

// defaults
$filterByGroup = null;
if (isset($_REQUEST['filterByGroup']))
{
    $filterByGroup = trim($_REQUEST['filterByGroup']);
}
?>

<script>
    oTable = null;
    gConfigId = null;
    $(document).ready(function(){
        // datatable
        oTable = $('#fileTable').dataTable({
            "sPaginationType": "full_numbers",
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": 'ajax/setting_manage.ajax.php',
            "bJQueryUI": true,
            "iDisplayLength": 50,
            "aaSorting": [[ 1, "asc" ]],
            "aoColumns" : [   
                { bSortable: false, sWidth: '3%', sName: 'file_icon', sClass: "center" },
                { sName: 'config_key', sWidth: '13%' },
                { sName: 'config_description', sWidth: '35%' },
                { sName: 'config_value' },
                { bSortable: false, sWidth: '10%', sClass: "center" }
            ],
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push( { "name": "filterByGroup", "value": $('#filterByGroup').val() } );
                aoData.push( { "name": "filterText", "value": $('#filterText').val() } );
                $.ajax({
                    "dataType": 'json',
                    "type": "GET",
                    "url": "ajax/setting_manage.ajax.php",
                    "data": aoData,
                    "success": fnCallback
                });
            }
        });
        
        // update custom filter
        $('.dataTables_filter').html($('#customFilter').html());

        // dialog box
        $( "#editConfigurationForm" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            height: 330,
            buttons: {
                "Update Value": function() {
                    updateConfigurationValue();
                },
                "Cancel": function() {
                    $("#editConfigurationForm").dialog("close");
                }
            },
            open: function() {
                setLoader();
                loadEditConfigurationForm();
                resetOverlays();
            }
        });;
    });
    
    function setLoader()
    {
        $('#configurationForm').html('Loading, please wait...');
    }
    
    function loadEditConfigurationForm()
    {
        $.ajax({
            type: "POST",
            url: "ajax/setting_manage_edit_form.ajax.php",
            data: { gConfigId: gConfigId },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    $('#configurationForm').html(json.msg);
                }
                else
                {
                    $('#configurationForm').html(json.html);
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#configurationForm').html(XMLHttpRequest.responseText);
            }
        });
    }
    
    function updateConfigurationValue()
    {
        // get data
        configId = $('#configIdElement').val();
        configValue = $('#configValueElement').val();
        
        $.ajax({
            type: "POST",
            url: "ajax/setting_manage_edit_process.ajax.php",
            data: { configId: configId, configValue: configValue },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    showError(json.msg, 'popupMessageContainer');
                }
                else
                {
                    showSuccess(json.msg);
                    reloadTable();
                    $("#editConfigurationForm").dialog("close");
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'popupMessageContainer');
            }
        });

    }
    
    function editConfigurationForm(configId)
    {
        gConfigId = configId;
        $('#editConfigurationForm').dialog('open');
    }

    function reloadTable()
    {
        oTable.fnDraw(false);
    }
</script>

<div class="row clearfix configurationTable">
    <div class="sectionLargeIcon largeConfigIcon"></div>
    <div class="widget clearfix">
        <h2>Update Configuration</h2>
        <div class="widget_inside">
            <?php echo adminFunctions::compileNotifications(); ?>
			<div class="col_2 clearfix">
				<ul class="square">
				<?php
				if (COUNT($groupDetails))
				{
					foreach ($groupDetails AS $groupDetail)
					{
						echo '<li>';
						if (($filterByGroup) && ($filterByGroup == $groupDetail['config_group']))
						{
							echo '<strong>';
						}
						echo '<a href="setting_manage.php?filterByGroup='.urlencode($groupDetail['config_group']).'">'.$groupDetail['config_group'].'</a>';
						if (($filterByGroup) && ($filterByGroup == $groupDetail['config_group']))
						{
							echo '</strong>';
						}
						echo '</li>';
					}
				}
				?>
				</ul>
			</div>
            <div class="col_10 last">
                <table id='fileTable' class='dataTable'>
                    <thead>
                        <tr>
                            <th></th>
                            <th class="align-left"><?php echo adminFunctions::t('key', 'Key'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('description', 'Description'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('value', 'Value'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('actions', 'Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="customFilter" id="customFilter" style="display: none;">
    <label>
        Filter Results:
        <input name="filterText" id="filterText" type="text" onKeyUp="reloadTable(); return false;" style="width: 160px;"/>
    </label>
    <label style="padding-left: 6px;">
        By Group:
        <select name="filterByGroup" id="filterByGroup" onChange="reloadTable(); return false;" style="width: 220px;">
            <option value="">- all -</option>
            <?php
            if (COUNT($groupDetails))
            {
                foreach ($groupDetails AS $groupDetail)
                {
                    echo '<option value="' . $groupDetail['config_group'] . '"';
                    if (($filterByGroup) && ($filterByGroup == $groupDetail['config_group']))
                    {
                        echo ' SELECTED';
                    }
                    echo '>' . $groupDetail['config_group'] . '</option>';
                }
            }
            ?>
        </select>
    </label>
</div>



<?php
include_once('_footer.inc.php');
?>

<div id="editConfigurationForm" title="Edit Configuration">
    <span id="configurationForm"></span>
</div>