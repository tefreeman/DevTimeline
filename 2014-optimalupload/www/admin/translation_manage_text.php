<?php

// includes and security
include_once('_local_auth.inc.php');

// redirect if we don't know the languageId
if(!isset($_REQUEST['languageId']))
{
    adminFunctions::redirect('translation_manage.php');
}

// try to load the language
$sQL           = "SELECT * FROM language WHERE id = ".(int)$_REQUEST['languageId']." LIMIT 1";
$languageDetail = $db->getRow($sQL);
if(!$languageDetail)
{
    adminFunctions::redirect('translation_manage.php');
}

// error/success messages
if (isset($_REQUEST['sa']))
{
    adminFunctions::setSuccess('Translations successully imported.');
}

// initial constants
define('ADMIN_PAGE_TITLE', 'Manage Translations For \''.$languageDetail['languageName'].'\'');
define('ADMIN_SELECTED_PAGE', 'configuration');

// page header
include_once('_header.inc.php');


// defaults
$filterByGroup = null;
if (isset($_REQUEST['filterByGroup']))
{
    $filterByGroup = trim($_REQUEST['filterByGroup']);
}
?>

<script>
    oTable = null;
    gTranslationId = null;
    $(document).ready(function(){
        // datatable
        oTable = $('#fileTable').dataTable({
            "sPaginationType": "full_numbers",
            "bServerSide": true,
            "bProcessing": true,
            "sAjaxSource": 'ajax/translation_manage_text.ajax.php?languageId=<?php echo $languageDetail['id']; ?>',
            "bJQueryUI": true,
            "iDisplayLength": 50,
            "aaSorting": [[ 1, "asc" ]],
            "aoColumns" : [   
                { bSortable: false, sWidth: '3%', sName: 'file_icon', sClass: "center" },
                { sName: 'language_key', sWidth: '17%' },
                { sName: 'english_content', sWidth: '35%' },
                { sName: 'translated_content' },
                { bSortable: false, sWidth: '10%', sClass: "center" }
            ],
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                aoData.push( { "name": "filterText", "value": $('#filterText').val() } );
                $.ajax({
                    "dataType": 'json',
                    "type": "GET",
                    "url": "ajax/translation_manage_text.ajax.php?languageId=<?php echo $languageDetail['id']; ?>",
                    "data": aoData,
                    "success": fnCallback
                });
            }
        });
        
        // update custom filter
        $('.dataTables_filter').html($('#customFilter').html());

        // dialog box
        $( "#editTranslationForm" ).dialog({
            modal: true,
            autoOpen: false,
            width: 800,
            height: 420,
            buttons: {
                "Update": function() {
                    updateTranslationValue();
                },
                "Cancel": function() {
                    $("#editTranslationForm").dialog("close");
                }
            },
            open: function() {
                setLoader();
                loadEditTranslationForm();
                resetOverlays();
            }
        });;
    });
    
    function setLoader()
    {
        $('#translationForm').html('Loading, please wait...');
    }
    
    function loadEditTranslationForm()
    {
        $.ajax({
            type: "POST",
            url: "ajax/translation_manage_text_edit_form.ajax.php",
            data: { gTranslationId: gTranslationId, languageId: <?php echo $languageDetail['id']; ?> },
            dataType: 'json',
            success: function(json) {
                if(json.error == true)
                {
                    $('#translationForm').html(json.msg);
                }
                else
                {
                    $('#translationForm').html(json.html);
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                $('#translationForm').html(XMLHttpRequest.responseText);
            }
        });
    }
    
    function updateTranslationValue()
    {
        // get data
        translation_item_id = $('#translation_item_id').val();
        translated_content = $('#translated_content').val();
        
        $.ajax({
            type: "POST",
            url: "ajax/translation_manage_text_edit_process.ajax.php",
            data: { translation_item_id: translation_item_id, translated_content: translated_content, languageId: <?php echo $languageDetail['id']; ?> },
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
                    $("#editTranslationForm").dialog("close");
                }
                
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                showError(XMLHttpRequest.responseText, 'popupMessageContainer');
            }
        });

    }
    
    function editTranslationForm(translationId)
    {
        gTranslationId = translationId;
        $('#editTranslationForm').dialog('open');
    }

    function reloadTable()
    {
        oTable.fnDraw(false);
    }
</script>

<div class="row clearfix">
    <div class="sectionLargeIcon largeLanguageIcon"></div>
    <div class="widget clearfix">
        <h2>Translations</h2>
        <div class="widget_inside">
            <?php echo adminFunctions::compileNotifications(); ?>
            <div class="col_12">
                <table id='fileTable' class='dataTable'>
                    <thead>
                        <tr>
                            <th></th>
                            <th class="align-left"><?php echo adminFunctions::t('language_key', 'Language Key'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('english_content', 'English Content'); ?></th>
                            <th class="align-left"><?php echo adminFunctions::t('translated_content', 'Translated Content'); ?></th>
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
</div>

<div id="editTranslationForm" title="Edit Configuration">
    <span id="translationForm"></span>
</div>

<?php
include_once('_footer.inc.php');
?>