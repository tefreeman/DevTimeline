<?php
// initial constants
define('ADMIN_PAGE_TITLE', 'Manage Download Pages');
define('ADMIN_SELECTED_PAGE', 'configuration');

// includes and security
include_once('_local_auth.inc.php');

// page header
include_once('_header.inc.php');
?>

<script>
    oTable = null;
    gPageId = null;
    gDefaultLanguage = '';
    gEditPageId = null;
    $(document).ready(function(){
        // datatable
        oTable = $('#fileTable').dataTable({
            "sPaginationType": "full_numbers",
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": 'ajax/download_page_manage.ajax.php',
            "bJQueryUI": true,
            "iDisplayLength": 25,
            "aaSorting": [[ 1, "asc" ]],
            "aoColumns" : [   
                { bSortable: false, sWidth: '3%', sName: 'file_icon', sClass: "center" },
                { bSortable: false, sName: 'user_level', sWidth: '20%' },
                { bSortable: false },
                { bSortable: false, sWidth: '25%', sClass: "center" }
            ],
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                $.ajax({
                    "dataType": 'json',
                    "type": "GET",
                    "url": "ajax/download_page_manage.ajax.php",
                    "data": aoData,
                    "success": fnCallback
                });
            }
        });
        
        // update custom filter
        $('.dataTables_filter').html($('#customFilter').html());

        // dialog box
        $( "#addDownloadPageForm" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            height: 520,
            buttons: {
                "Add Page To User Type": function() {
                    processAddDownloadPage();
                },
                "Cancel": function() {
                    $("#addDownloadPageForm").dialog("close");
                }
            },
            open: function() {
                setLoader();
                loadAddDownloadPageForm();
                resetOverlays();
            }
        });
        
        // dialog box
        $( "#editDownloadPageForm" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            height: 520,
            buttons: {
                "Update Page For User Type": function() {
                    processEditDownloadPage();
                },
                "Cancel": function() {
                    $("#editDownloadPageForm").dialog("close");
                }
            },
            open: function() {
                setEditLoader();
                loadEditDownloadPageForm();
                resetOverlays();
            }
        });
        
        // dialog box
        $( "#confirmDelete" ).dialog({
            modal: true,
            autoOpen: false,
            width: 450,
            buttons: {
                "Delete Page On User Type": function() {
                    removePageOnUserType();
                    $("#confirmDelete").dialog("close");
                },
                "Cancel": function() {
                    $("#confirmDelete").dialog("close");
                }
            },
            open: function() {
                resetOverlays();
            }
        });
    });
    
    function setLoader()
    {
        $('#downloadPageForm').html('Loading, please wait...');
    }
    
    function setEditLoader()
    {
        $('#downloadPageEditForm').html('Loading, please wait...');
    }
    
    function loadAddDownloadPageForm()
    {
        $.ajax({
            type: "POST",
            url: "ajax/download_page_manage_add_form.ajax.php",
            data: { },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    $('#downloadPageForm').html(json.msg);
                }
                else
                {
                    $('#downloadPageForm').html(json.html);
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#downloadPageForm').html(XMLHttpRequest.responseText);
            }
        });
    }
    
    function loadEditDownloadPageForm()
    {
        $.ajax({
            type: "POST",
            url: "ajax/download_page_manage_add_form.ajax.php",
            data: { pageId: gEditPageId },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    $('#downloadPageEditForm').html(json.msg);
                }
                else
                {
                    $('#downloadPageEditForm').html(json.html);
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#downloadPageForm').html(XMLHttpRequest.responseText);
            }
        });
    }
    
    function processAddDownloadPage()
    {
        // get data
        download_page = $('#download_page').val();
        user_level_id = $('#user_level_id').val();
        page_order = $('#page_order').val();
        optional_timer = $('#optional_timer').val();
        additional_javascript_code = $('#additional_javascript_code').val();
        
        $.ajax({
            type: "POST",
            url: "ajax/download_page_manage_add_process.ajax.php",
            data: { download_page: download_page, user_level_id: user_level_id, page_order: page_order, optional_timer: optional_timer, additional_javascript_code: additional_javascript_code },
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
                    $("#addDownloadPageForm").dialog("close");
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'popupMessageContainer');
            }
        });

    }
    
    function processEditDownloadPage()
    {
        // get data
        download_page = $('#download_page').val();
        user_level_id = $('#user_level_id').val();
        page_order = $('#page_order').val();
        optional_timer = $('#optional_timer').val();
        additional_javascript_code = $('#additional_javascript_code').val();
        
        $.ajax({
            type: "POST",
            url: "ajax/download_page_manage_add_process.ajax.php",
            data: { download_page: download_page, user_level_id: user_level_id, page_order: page_order, optional_timer: optional_timer, additional_javascript_code: additional_javascript_code, pageId: gEditPageId },
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
                    $("#editDownloadPageForm").dialog("close");
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'popupMessageContainer');
            }
        });

    }
    
    function addDownloadPageForm()
    {
        $('#addDownloadPageForm').dialog('open');
    }
    
    function editDownloadPageForm(pageId)
    {
        gEditPageId = pageId;
        $('#editDownloadPageForm').dialog('open');
    }

    function reloadTable()
    {
        oTable.fnDraw(false);
    }
    
    function deletePageType(pageId)
    {
        $('#confirmDelete').dialog('open');
        gPageId = pageId;
    }
    
    function removePageOnUserType()
    {
        $.ajax({
            type: "POST",
            url: "ajax/download_page_manage_remove.ajax.php",
            data: { pageId: gPageId },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    showError(json.msg);
                }
                else
                {
                    showSuccess(json.msg);
                    reloadTable();
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText);
            }
        });
    }
</script>

<div class="row clearfix">
    <div class="sectionLargeIcon largeDownloadPageIcon"></div>
    <div class="widget clearfix">
        <h2>Download Pages</h2>
        <div class="widget_inside">
            <?php echo adminFunctions::compileNotifications(); ?>
            <div class="col_12">
                <p>Use this section to manage which pages your users see when they request a file download. Set 1 or more pages depending on user type. If a user type doesn't have any pages, the file will be downloaded directly. Some pages support countdown timers which you can also define here. If you manually add new pages, as long as the filename starts _download_page_ it will appear here.<br/><br/></p>
                <table id='fileTable' class='dataTable'>
                    <thead>
                        <tr>
                            <th></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t('user_level_page', 'User Level / Page')); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t('download_page', 'Download Page')); ?></th>
                            <th class="align-left"><?php echo UCWords(adminFunctions::t('actions', 'Actions')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            
            <div style="float: left;">
                <input type="submit" value="Set Download Page To User Type" class="button blue" onClick="addDownloadPageForm(); return false;"/>
            </div>
            
        </div>
    </div>
</div>

<div class="customFilter" id="customFilter" style="display: none;">

</div>

<div id="addDownloadPageForm" title="Add Page To User Type">
    <span id="downloadPageForm"></span>
</div>

<div id="editDownloadPageForm" title="Edit Page On User Type">
    <span id="downloadPageEditForm"></span>
</div>

<div id="confirmDelete" title="Confirm Action">
    <p>Are you sure you want to delete the download page on this user type?</p>
</div>

<?php
include_once('_footer.inc.php');
?>