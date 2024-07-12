<?php
// initial constants
define('ADMIN_PAGE_TITLE', 'Manage Files');
define('ADMIN_SELECTED_PAGE', 'files');

// includes and security
define('MIN_ACCESS_LEVEL', 10); // allow moderators
include_once('_local_auth.inc.php');

// page header
include_once('_header.inc.php');

// load all users
$sQL         = "SELECT id, username AS selectValue FROM users ORDER BY username";
$userDetails = $db->getRows($sQL);

// load all file servers
$sQL           = "SELECT id, serverLabel FROM file_server ORDER BY serverLabel";
$serverDetails = $db->getRows($sQL);

// load all file status
$sQL           = "SELECT id, label FROM file_status ORDER BY label";
$statusDetails = $db->getRows($sQL);

// defaults
$filterText = '';
if (isset($_REQUEST['filterText']))
{
    $filterText = trim($_REQUEST['filterText']);
}

$filterByStatus = 1;
if (isset($_REQUEST['filterByStatus']))
{
    $filterByStatus = (int) $_REQUEST['filterByStatus'];
}

$filterByServer = null;
if (isset($_REQUEST['filterByServer']))
{
    $filterByServer = (int) $_REQUEST['filterByServer'];
}

$filterByUser = null;
if (isset($_REQUEST['filterByUser']))
{
    $filterByUser = (int) $_REQUEST['filterByUser'];
}
?>

<script>
    oTable = null;
    gFileId = null;
    checkboxIds = {};
    $(document).ready(function(){
        // datatable
        oTable = $('#fileTable').dataTable({
            "sPaginationType": "full_numbers",
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": 'ajax/file_manage.ajax.php',
            "bJQueryUI": true,
            "iDisplayLength": 25,
            "aaSorting": [[ 2, "desc" ]],
            "aoColumns" : [   
                { bSortable: false, sWidth: '3%', sName: 'file_icon', sClass: "center" },
                { sName: 'filename' },
                { sName: 'date_uploaded', sWidth: '12%', sClass: "center" },
                { sName: 'filesize', sWidth: '12%', sClass: "center" },
                { sName: 'downloads', sWidth: '10%', sClass: "center" },
                { sName: 'owner', sWidth: '12%', sClass: "center" },
                { sName: 'status', sWidth: '12%', sClass: "center" },
                { bSortable: false, sWidth: '165px', sClass: "center" }
            ],
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push( { "name": "filterText", "value": $('#filterText').val() } );
                aoData.push( { "name": "filterByUser", "value": $('#filterByUser').val() } );
                aoData.push( { "name": "filterByServer", "value": $('#filterByServer').val() } );
                aoData.push( { "name": "filterByStatus", "value": $('#filterByStatus').val() } );
                $.ajax({
                    "dataType": 'json',
                    "type": "GET",
                    "url": "ajax/file_manage.ajax.php",
                    "data": aoData,
                    "success": fnCallback
                });
            },
            "fnDrawCallback": function (oSettings) {
                reloadCheckedItems();
            }
        });
        
        // update custom filter
        $('.dataTables_filter').html($('#customFilter').html());
        
        // dialog box
        $( "#confirmDelete" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            buttons: {
                "Delete File": function() {
                    removeFile(function() {
                        $("#confirmDelete").dialog("close");
                    });
                },
                "Cancel": function() {
                    $("#confirmDelete").dialog("close");
                }
            },
            open: function() {
                resetOverlays();
            }
        });
        
        $( "#showNotes" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            buttons: {
                "OK": function() {
                    $("#showNotes").dialog("close");
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
    
    function confirmRemoveFile(fileId)
    {
        $('#confirmDelete').dialog('open');
        gFileId = fileId;
    }
    
    function showNotes(notes)
    {
        $('#showNotes').html(notes);
        $('#showNotes').dialog('open');
    }
    
    function removeFile(callback)
    {
        // find out file server first
        $.ajax({
            type: "POST",
            url: "ajax/get_file_server_path.ajax.php",
            data: { fileId: gFileId },
            dataType: 'json',
            success: function(jsonOuter) {
                if(jsonOuter.error == true)
                {
                    showError(jsonOuter.msg);
                }
                else
                {
                    //  delete file
                    $.ajax({
                        type: "POST",
                        url: "<?php echo _CONFIG_SITE_PROTOCOL; ?>://"+jsonOuter.filePath+"/<?php echo ADMIN_FOLDER_NAME; ?>/ajax/update_file_state.ajax.php",
                        data: { fileId: gFileId, statusId: $('#removal_type').val(), adminNotes: $('#admin_notes').val() },
                        dataType: 'json',
                        xhrFields: {
                            withCredentials: true
                        },
                        success: function(json) {
                            if(json.error == true)
                            {
                                showError(json.msg);
                            }
                            else
                            {
                                showSuccess(json.msg);
                                $('#removal_type').val(3);
                                $('#admin_notes').val('');
                                reloadTable();
                                callback();
                            }

                        },
                        error: function (XMLHttpRequest, textStatus, errorThrown) {
                            showError(XMLHttpRequest.responseText);
                        }
                    });
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText);
            }
        });
    }
    
    function toggleFileIds(ele)
    {
        if($(ele).is(':checked'))
        {
            checkboxIds['cb'+$(ele).val()] = $(ele).val();
        }
        else
        {
            if(typeof(checkboxIds['cb'+$(ele).val()]) != 'undefined')
            {
                delete checkboxIds['cb'+$(ele).val()];
            }
        }
        
        updateButtonText();
    }
    
    function reloadCheckedItems()
    {
        for(i in checkboxIds)
        {
            $elementId = 'cbElement'+checkboxIds[i];
            if(typeof($('#'+$elementId)) != 'undefined')
            {
                $('#'+$elementId).prop('checked', true);
            }
        }
    }
    
    function updateButtonText()
    {
        totalFiles = countCheckboxFiles();
        if(totalFiles == 0)
        {
            totalFiles = '';
            $('#removeMultiFilesButton').removeClass('blue');
            $('#deleteMultiFilesButton').removeClass('blue');
            //$('#moveMultiFilesButton').removeClass('blue');
        }
        else
        {
            totalFiles = ' ('+totalFiles+')';
            $('#removeMultiFilesButton').addClass('blue');
            $('#deleteMultiFilesButton').addClass('blue');
            //$('#moveMultiFilesButton').addClass('blue');
        }
        
        baseRemoveText = "<?php echo adminFunctions::t('remove_files_total', 'Remove Files[[[FILE_COUNT]]]'); ?>";
        baseRemoveText = baseRemoveText.replace('[[[FILE_COUNT]]]', totalFiles);
        $('#removeMultiFilesButton').html(baseRemoveText);
        
        baseDeleteText = "<?php echo adminFunctions::t('delete_files_and_data_total', 'Delete Files And Stats Data[[[FILE_COUNT]]]'); ?>";
        baseDeleteText = baseDeleteText.replace('[[[FILE_COUNT]]]', totalFiles);
        $('#deleteMultiFilesButton').html(baseDeleteText);
        
        //baseMoveText = "<?php echo adminFunctions::t('move_files_total', 'Move Files[[[FILE_COUNT]]]'); ?>";
        //baseMoveText = baseMoveText.replace('[[[FILE_COUNT]]]', totalFiles);
        //$('#moveMultiFilesButton').html(baseMoveText);
    }
    
    function countCheckboxFiles()
    {
        count = 0;
        for(i in checkboxIds)
        {
            count++;
        }
        
        return count;
    }
    
    function getCheckboxFiles()
    {
        count = 0;
        for(i in checkboxIds)
        {
            count++;
        }
        
        return checkboxIds;
    }
    
    function bulkDeleteFiles(deleteData)
    {
        if(typeof(deleteData) == 'undefined')
        {
            deleteData = false;
        }

        if(countCheckboxFiles() == 0)
        {
            alert('Please select some files to remove.');
            return false;
        }
        
        msg = 'Are you sure you want to remove '+countCheckboxFiles()+' files? This can not be undone once confirmed.';
        if(deleteData == true)
        {
            msg += '\n\nAll file data and associated data such as the stats, will also be deleted from the database. This will entirely clear any record of the upload. (exc logs)';
        }
        else
        {
            msg += '\n\nThe original file record will be retained along with the file stats.';
        }
        
        if(confirm(msg))
        {
            bulkDeleteConfirm(deleteData);
        }
    }
    
    var bulkError = '';
    var bulkSuccess = '';
    var totalDone = 0;
    function addBulkError(x)
    {
        bulkError += x;
    }
    function getBulkError(x)
    {
        return bulkError;
    }
    function addBulkSuccess(x)
    {
        bulkSuccess += x;
    }
    function getBulkSuccess(x)
    {
        return bulkSuccess;
    }
    function clearBulkResponses()
    {
        bulkError = '';
        bulkSuccess = '';
    }
    function bulkDeleteConfirm(deleteData)
    {
        // get server list first
        $.ajax({
            type: "POST",
            url: "ajax/get_all_file_server_paths.ajax.php",
            data: { fileIds: checkboxIds },
            dataType: 'json',
            success: function(jsonOuter) {
                if(jsonOuter.error == true)
                {
                    showError(jsonOuter.msg);
                }
                else
                {
                    // loop file servers and attempt to remove files
                    totalDone = 0;
                    filePathsObj = jsonOuter.filePaths;
                    affectedServers = 0;
                    for(filePath in filePathsObj)
                    {
                        affectedServers++;
                    }
                    for(filePath in filePathsObj)
                    {
                        //  call server with file ids to delete
                        $.ajax({
                            type: "POST",
                            url: "<?php echo _CONFIG_SITE_PROTOCOL; ?>://"+filePath+"/<?php echo ADMIN_FOLDER_NAME; ?>/ajax/file_manage_bulk_delete.ajax.php",
                            data: { fileIds: filePathsObj[filePath], deleteData: deleteData },
                            dataType: 'json',
                            xhrFields: {
                                withCredentials: true
                            },
                            success: function(json) {
                                if(json.error == true)
                                {
                                    addBulkError(filePath+': '+json.msg+'<br/>');
                                }
                                else
                                {
                                    addBulkSuccess(filePath+': '+json.msg+'<br/>');
                                }
                                
                                totalDone++;
                                if(totalDone == affectedServers)
                                {
                                    finishBulkProcess();
                                }
                            },
                            error: function (XMLHttpRequest, textStatus, errorThrown) {
                                addBulkError(filePath+": Failed connecting to server to remove files.<br/>");
                                totalDone++;
                                if(totalDone == affectedServers)
                                {
                                    finishBulkProcess();
                                }
                            }
                        });
                    }
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError('Failed connecting to server to get the list of servers, please try again later.');
            }
        });
    }
    
    function finishBulkProcess()
    {
        // get final response
        bulkError = getBulkError();
        bulkSuccess = getBulkSuccess();

        // compile result
        if(bulkError.length > 0)
        {
            showError(bulkError+bulkSuccess);
        }
        else
        {
            showSuccess(bulkSuccess);
        }
        reloadTable();
        clearBulkResponses();
        checkboxIds = {};
        updateButtonText();
        
        // scroll to the top of the page
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#selectAllCB').prop('checked', false);
    }
    
    function toggleSelectAll()
    {
        if($('#selectAllCB').is(':checked'))
        {
            selectAllFiles();
        }
        else
        {
            deselectAllFiles();
        }
    }
    
    function selectAllFiles()
    {
        $("#fileTable .checkbox").each(function(index, ele) {
            checkboxIds['cb'+$(ele).val()] = $(ele).val();
        });
        reloadCheckedItems();
        updateButtonText();
    }
    
    function deselectAllFiles()
    {
        $("#fileTable .checkbox").each(function(index, ele) {
            if(typeof(checkboxIds['cb'+$(ele).val()]) != 'undefined')
            {
                delete checkboxIds['cb'+$(ele).val()];
                $('#cbElement'+$(ele).val()).prop('checked', false);
            }
        });
        reloadCheckedItems();
        updateButtonText();
        $('#selectAllCB').prop('checked', false);
    }
</script>

<div class="row clearfix">
    <div class="sectionLargeIcon largeFileIcon"></div>
    <div class="widget clearfix">
        <h2>File List</h2>
        <div class="widget_inside">
            <?php echo adminFunctions::compileNotifications(); ?>
            <div class="col_12">
                <table id='fileTable' class='dataTable'>
                    <thead>
                        <tr>
                            <th><div style="padding-top: 5px;"><input type="checkbox" id="selectAllCB" onClick="toggleSelectAll();"/></div></th>
                            <th class="align-left"><?php echo adminFunctions::t('filename', 'Filename'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('date_uploaded', 'Date Uploaded'); ?></th>
                            <th><?php echo adminFunctions::t('filesize', 'Filesize'); ?></th>
                            <th style="width: 10%;"><?php echo adminFunctions::t('downloads', 'Downloads'); ?></th>
                            <th style="width: 10%;"><?php echo adminFunctions::t('owner', 'Owner'); ?></th>
                            <th style="width: 10%;"><?php echo adminFunctions::t('status', 'Status'); ?></th>
                            <th class="align-left" style="width: 165px;"><?php echo adminFunctions::t('actions', 'Actions'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <?php if($Auth->hasAccessLevel(20)): ?>
            <div style="float: right;">
                <a href="export_csv.php?type=files" class="button blue">Export File Data (csv)</a>
            </div>
            <?php endif; ?>
            
            <div style="float: left;">
                <a href="#" onClick="bulkDeleteFiles(false); return false;" id="removeMultiFilesButton" class="button"><?php echo adminFunctions::t('remove_files_total', 'Remove Files[[[FILE_COUNT]]]', array('FILE_COUNT'=>'')); ?></a>&nbsp;
                <a href="#" onClick="bulkDeleteFiles(true); return false;" id="deleteMultiFilesButton" class="button"><?php echo adminFunctions::t('delete_files_and_data_total', 'Delete Files And Stats Data[[[FILE_COUNT]]]', array('FILE_COUNT'=>'')); ?></a>&nbsp;
                <!--<a id="moveMultiFilesButton" class="button"><?php echo adminFunctions::t('move_files_total', 'Move Files[[[FILE_COUNT]]]', array('FILE_COUNT'=>'')); ?></a>-->
            </div>
            <div class="clear"></div>
            
        </div>
    </div>
</div>

<div class="customFilter" id="customFilter" style="display: none;">
    <label>
        Filter Results:
        <input name="filterText" id="filterText" type="text" value="<?php echo adminFunctions::makeSafe($filterText); ?>" onKeyUp="reloadTable(); return false;" style="width: 160px;"/>
    </label>
    <label style="padding-left: 6px;">
        By User:
        <select name="filterByUser" id="filterByUser" onChange="reloadTable(); return false;" style="width: 160px;">
            <option value="">- all -</option>
            <?php
            if (COUNT($userDetails))
            {
                foreach ($userDetails AS $userDetail)
                {
                    echo '<option value="' . $userDetail['id'] . '"';
                    if (($filterByUser) && ($filterByUser == $userDetail['id']))
                    {
                        echo ' SELECTED';
                    }
                    echo '>' . $userDetail['selectValue'] . '</option>';
                }
            }
            ?>
        </select>
    </label>
    <label style="padding-left: 6px;">
        By Server:
        <select name="filterByServer" id="filterByServer" onChange="reloadTable(); return false;" style="width: 120px;">
            <option value="">- all -</option>
            <?php
            if (COUNT($serverDetails))
            {
                foreach ($serverDetails AS $serverDetail)
                {
                    echo '<option value="' . $serverDetail['id'] . '"';
                    if (($filterByServer) && ($filterByServer == $serverDetail['id']))
                    {
                        echo ' SELECTED';
                    }
                    echo '>' . $serverDetail['serverLabel'] . '</option>';
                }
            }
            ?>
        </select>
    </label>
    <label style="padding-left: 6px;">
        By Status:
        <select name="filterByStatus" id="filterByStatus" onChange="reloadTable(); return false;" style="width: 120px;">
            <option value="">- all -</option>
            <?php
            if (COUNT($statusDetails))
            {
                foreach ($statusDetails AS $statusDetail)
                {
                    echo '<option value="' . $statusDetail['id'] . '"';
                    if (($filterByStatus) && ($filterByStatus == $statusDetail['id']))
                    {
                        echo ' SELECTED';
                    }
                    echo '>' . $statusDetail['label'] . '</option>';
                }
            }
            ?>
        </select>
    </label>
</div>

<div id="confirmDelete" title="Confirm Action">
    <p>Select the type of removal below. You can also add removal notes such as a copy of the original removal request. The notes are only visible by an admin user.</p>
    <form id="removeFileForm" class="form">
        <div class="clearfix">
            <label>Removal Type:</label>
            <div class="input">
                <select name="removal_type" id="removal_type" class="large">
                    <option value="3">General</option>
                    <option value="4">Copyright Breach (DMCA)</option>
                </select>
            </div>
        </div>
        <div class="clearfix alt-highlight">
            <label>Notes:</label>
            <div class="input">
                <textarea name="admin_notes" id="admin_notes" class="xxlarge"></textarea>
            </div>
        </div>
    </form>
</div>

<div id="showNotes" title="File Notes"></div>

<?php
include_once('_footer.inc.php');
?>