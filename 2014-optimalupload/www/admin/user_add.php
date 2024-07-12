<?php
// initial constants
define('ADMIN_PAGE_TITLE', 'Add New User');
define('ADMIN_SELECTED_PAGE', 'users');

// includes and security
include_once('_local_auth.inc.php');

// account types
$accountTypeDetails = $db->getRows('SELECT level_id, label FROM user_level ORDER BY level_id ASC');

// account status
$accountStatusDetails = array('active', 'pending', 'disabled', 'suspended');

// user titles
$titleItems = array('Mr', 'Mrs', 'Miss', 'Miss', 'Dr');

// prepare variables
$username         = '';
$password         = '';
$confirm_password = '';
$account_status   = 'active';
$account_type     = 1;
$expiry_date      = '';
$title            = 'Mr';
$first_name       = '';
$last_name        = '';
$email_address    = '';
$storage_limit    = '';

// handle page submissions
if (isset($_REQUEST['submitted']))
{
    // get variables
    $username         = trim(strtolower($_REQUEST['username']));
    $password         = trim($_REQUEST['password']);
    $confirm_password = trim($_REQUEST['confirm_password']);
    $account_status   = trim($_REQUEST['account_status']);
    $account_type     = trim($_REQUEST['account_type']);
    $expiry_date      = trim($_REQUEST['expiry_date']);
    $title            = trim($_REQUEST['title']);
    $first_name       = trim($_REQUEST['first_name']);
    $last_name        = trim($_REQUEST['last_name']);
    $email_address    = trim(strtolower($_REQUEST['email_address']));
    $storage_limit    = trim($_REQUEST['storage_limit']);
    $storage_limit = str_replace(array(',', ' ', '.', '(', ')', '-'), '', $storage_limit);
    $dbExpiryDate = '';

    // validate submission
    if (_CONFIG_DEMO_MODE == true)
    {
        adminFunctions::setError(adminFunctions::t("no_changes_in_demo_mode"));
    }
    elseif ((strlen($username) < 6) || (strlen($username) > 16))
    {
        adminFunctions::setError(adminFunctions::t("username_length_invalid"));
    }
    elseif ((strlen($password) < 6) || (strlen($password) > 16))
    {
        adminFunctions::setError(adminFunctions::t("password_length_invalid"));
    }
    elseif ($password != $confirm_password)
    {
        adminFunctions::setError(adminFunctions::t("confirmation_password_does_not_match", "Your confirmation password does not match"));
    }
    elseif (strlen($first_name) == 0)
    {
        adminFunctions::setError(adminFunctions::t("enter_first_name"));
    }
    elseif (strlen($last_name) == 0)
    {
        adminFunctions::setError(adminFunctions::t("enter_last_name"));
    }
    elseif (strlen($email_address) == 0)
    {
        adminFunctions::setError(adminFunctions::t("enter_email_address"));
    }
    elseif (valid_email($email_address) == false)
    {
        adminFunctions::setError(adminFunctions::t("entered_email_address_invalid"));
    }
    elseif (strlen($expiry_date))
    {
        // turn into db format
        $exp1 = explode(" ", $expiry_date);
        $exp  = explode("/", $exp1[0]);
        if (COUNT($exp) != 3)
        {
            adminFunctions::setError(adminFunctions::t("account_expiry_invalid_dd_mm_yy", "Account expiry date invalid, it should be in the format dd/mm/yyyy"));
        }
        else
        {
            $dbExpiryDate = $exp[2] . '-' . $exp[1] . '-' . $exp[0] . ' 00:00:00';

            // check format
            if (strtotime($dbExpiryDate) == false)
            {
                adminFunctions::setError(adminFunctions::t("account_expiry_invalid_dd_mm_yy", "Account expiry date invalid, it should be in the format dd/mm/yyyy"));
            }
        }
    }

    // check email/username doesn't already exist
    if (adminFunctions::isErrors() == false)
    {
        $checkEmail = UserPeer::loadUserByEmailAddress($email_address);
        if ($checkEmail)
        {
            // email exists
            adminFunctions::setError(adminFunctions::t("email_address_already_exists", "Email address already exists on another account"));
        }
        else
        {
            $checkUser = UserPeer::loadUserByUsername($username);
            if ($checkUser)
            {
                // username exists
                adminFunctions::setError(adminFunctions::t("username_already_exists", "Username already exists on another account"));
            }
        }
    }

    // add the account
    if (adminFunctions::isErrors() == false)
    {
        // create the intial record
        $dbInsert = new DBObject("users", array("username", "password", "level_id", "email", "status", "title", "firstname", "lastname", "paidExpiryDate", "storageLimitOverride"));
        $dbInsert->username = $username;
        $dbInsert->password = MD5($password);
        $dbInsert->level_id = $account_type;
        $dbInsert->email = $email_address;
        $dbInsert->status = $account_status;
        $dbInsert->title = $title;
        $dbInsert->firstname = $first_name;
        $dbInsert->lastname = $last_name;
        $dbInsert->paidExpiryDate = $dbExpiryDate;
        $dbUpdate->storageLimitOverride = strlen($storage_limit)?$storage_limit:0;
        if (!$dbInsert->insert())
        {
            adminFunctions::setError(adminFunctions::t("error_problem_record"));
        }
        else
        {
            adminFunctions::redirect('user_manage.php?sa=1');
        }
    }
}

// page header
include_once('_header.inc.php');
?>

<script>
    $(function() {
        // formvalidator
        $("#userForm").validationEngine();
        
        // date picker
        $( "#expiry_date" ).datepicker({
            "dateFormat": "dd/mm/yy"
        });
    });
</script>

<div class="row clearfix">
    <div class="col_12">
        <div class="sectionLargeIcon largeUserAddIcon"></div>
        <div class="widget clearfix">
            <h2>User Details</h2>
            <div class="widget_inside">
                <?php echo adminFunctions::compileNotifications(); ?>
                <form method="POST" action="user_add.php" name="userForm" id="userForm">
                    <div class="clearfix col_12">
                        <div class="col_4">
                            <h3>Login Details</h3>
                            <p>Enter the details that the user will use to access the site.</p>
                        </div>
                        <div class="col_8 last">
                            <div class="form">
                                <div class="clearfix alt-highlight">
                                    <label>Username:</label>
                                    <div class="input"><input id="username" name="username" type="text" class="large validate[required]" value="<?php echo adminFunctions::makeSafe($username); ?>"/></div>
                                </div>
                                <div class="clearfix">
                                    <label>Password:</label>
                                    <div class="input"><input id="password" name="password" type="password" class="large validate[required]" value="<?php echo adminFunctions::makeSafe($password); ?>"/></div>
                                </div>
                                <div class="clearfix alt-highlight">
                                    <label>Confirm Password:</label>
                                    <div class="input"><input id="confirm_password" name="confirm_password" type="password" class="large validate[required,equals[password]]]" value="<?php echo adminFunctions::makeSafe($confirm_password); ?>"/></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix col_12">
                        <div class="col_4">
                            <h3>Account Details</h3>
                            <p>Information about the account.</p>
                        </div>
                        <div class="col_8 last">
                            <div class="form">
                                <div class="clearfix alt-highlight">
                                    <label>Account Status:</label>
                                    <div class="input">
                                        <select name="account_status" id="account_status" class="medium validate[required]">
                                            <?php
                                            foreach ($accountStatusDetails AS $accountStatusDetail)
                                            {
                                                echo '<option value="' . $accountStatusDetail . '"';
                                                if (($account_status) && ($account_status == $accountStatusDetail))
                                                {
                                                    echo ' SELECTED';
                                                }
                                                echo '>' . UCWords($accountStatusDetail) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label>Account Type:</label>
                                    <div class="input">
                                        <select name="account_type" id="account_type" class="large validate[required]">
                                            <?php
                                            foreach ($accountTypeDetails AS $accountTypeDetail)
                                            {
                                                echo '<option value="' . $accountTypeDetail['level_id'] . '"';
                                                if (($account_type) && ($account_type == $accountTypeDetail['level_id']))
                                                {
                                                    echo ' SELECTED';
                                                }
                                                echo '>' . UCWords($accountTypeDetail['label']) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix alt-highlight">
                                    <label>Paid Expiry:</label>
                                    <div class="input">
                                        <input id="expiry_date" name="expiry_date" type="text" class="medium" value="<?php echo adminFunctions::makeSafe($expiry_date); ?>"/>&nbsp;&nbsp;(dd/mm/yyyy)
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label>Storage Limit:</label>
                                    <div class="input"><input id="storage_limit" name="storage_limit" placeholder="1073741824 = 1GB" type="text" class="medium" value="<?php echo adminFunctions::makeSafe($storage_limit); ?>"/>&nbsp;bytes. Overrides account type limits. Use zero for unlimited.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix col_12">
                        <div class="col_4">
                            <h3>User Details</h3>
                            <p>Details about the user.</p>
                        </div>
                        <div class="col_8 last">
                            <div class="form">
                                <div class="clearfix alt-highlight">
                                    <label>Title:</label>
                                    <div class="input">
                                        <select name="title" id="title">
                                            <?php
                                            foreach ($titleItems AS $titleItem)
                                            {
                                                echo '<option value="' . $titleItem . '"';
                                                if (($title) && ($title == $titleItem))
                                                {
                                                    echo ' SELECTED';
                                                }
                                                echo '>' . UCWords($titleItem) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label>First Name:</label>
                                    <div class="input"><input id="first_name" name="first_name" type="text" class="large validate[required]" value="<?php echo adminFunctions::makeSafe($first_name); ?>"/></div>
                                </div>
                                <div class="clearfix alt-highlight">
                                    <label>Last Name:</label>
                                    <div class="input"><input id="last_name" name="last_name" type="text" class="large validate[required]" value="<?php echo adminFunctions::makeSafe($last_name); ?>"/></div>
                                </div>
                                <div class="clearfix">
                                    <label>Email Address:</label>
                                    <div class="input"><input id="email_address" name="email_address" type="text" class="large validate[required,custom[email]]" value="<?php echo adminFunctions::makeSafe($email_address); ?>"/></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix col_12">
                        <div class="col_4">&nbsp;</div>
                        <div class="col_8 last">
                            <div class="clearfix">
                                <div class="input no-label">
                                    <input type="submit" value="Submit" class="button blue">
                                    <input type="reset" value="Reset" class="button grey">
                                </div>
                            </div>
                        </div>
                    </div>

                    <input name="submitted" type="hidden" value="1"/>
                </form>
            </div>
        </div>   
    </div>
</div>

<?php
include_once('_footer.inc.php');
?>