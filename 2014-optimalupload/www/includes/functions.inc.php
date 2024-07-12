<?php

function printr($var)
{
    $output = print_r($var, true);
    $output = str_replace("\n", "<br>", $output);
    $output = str_replace(' ', '&nbsp;', $output);
    echo "<div style='font-family:courier;'>$output</div>";
}

// Formats a given number of seconds into proper mm:ss format
function format_time($seconds)
{
    return floor($seconds / 60) . ':' . str_pad($seconds % 60, 2, '0');
}

// Given a string such as "comment_123" or "id_57", it returns the final, numeric id.
function split_id($str)
{
    return match('/[_-]([0-9]+)$/', $str, 1);
}

// Creates a friendly URL slug from a string
function slugify($str)
{
    $str = preg_replace('/[^a-zA-Z0-9 -\.]/', '', $str);
    $str = str_replace(' ', '-', trim($str));
    $str = preg_replace('/-+/', '-', $str);
    return $str;
}

// Computes the *full* URL of the current page (protocol, server, path, query parameters, etc)
function full_url()
{
    $s        = empty($_SERVER['HTTPS']) ? '' : ($_SERVER['HTTPS'] == 'on') ? 's' : '';
    $protocol = substr(strtolower($_SERVER['SERVER_PROTOCOL']), 0, strpos(strtolower($_SERVER['SERVER_PROTOCOL']), '/')) . $s;
    //$port     = ($_SERVER['SERVER_PORT'] == '80') ? '' : (":" . $_SERVER['SERVER_PORT']);
    return $protocol . "://" . getSiteHost() . $_SERVER['REQUEST_URI'];
}

// Returns an English representation of a past date within the last month
function time2str($ts)
{
    if (!ctype_digit($ts))
        $ts = strtotime($ts);

    $diff = time() - $ts;
    if ($diff == 0)
        return 'now';
    elseif ($diff > 0)
    {
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0)
        {
            if ($diff < 60)
                return 'just now';
            if ($diff < 120)
                return '1 minute ago';
            if ($diff < 3600)
                return floor($diff / 60) . ' minutes ago';
            if ($diff < 7200)
                return '1 hour ago';
            if ($diff < 86400)
                return floor($diff / 3600) . ' hours ago';
        }
        if ($day_diff == 1)
            return 'Yesterday';
        if ($day_diff < 7)
            return $day_diff . ' days ago';
        if ($day_diff < 31)
            return ceil($day_diff / 7) . ' weeks ago';
        if ($day_diff < 60)
            return 'last month';
        return date('F Y', $ts);
    }
    else
    {
        $diff     = abs($diff);
        $day_diff = floor($diff / 86400);
        if ($day_diff == 0)
        {
            if ($diff < 120)
                return 'in a minute';
            if ($diff < 3600)
                return 'in ' . floor($diff / 60) . ' minutes';
            if ($diff < 7200)
                return 'in an hour';
            if ($diff < 86400)
                return 'in ' . floor($diff / 3600) . ' hours';
        }
        if ($day_diff == 1)
            return 'Tomorrow';
        if ($day_diff < 4)
            return date('l', $ts);
        if ($day_diff < 7 + (7 - date('w')))
            return 'next week';
        if (ceil($day_diff / 7) < 4)
            return 'in ' . ceil($day_diff / 7) . ' weeks';
        if (date('n', $ts) == date('n') + 1)
            return 'next month';
        return date('F Y', $ts);
    }
}

// Returns an array representation of the given calendar month.
// The array values are timestamps which allow you to easily format
// and manipulate the dates as needed.
function calendar($month = null, $year = null)
{
    if (is_null($month))
        $month = date('n');
    if (is_null($year))
        $year  = date('Y');

    $first = mktime(0, 0, 0, $month, 1, $year);
    $last  = mktime(23, 59, 59, $month, date('t', $first), $year);

    $start = $first - (86400 * date('w', $first));
    $stop  = $last + (86400 * (7 - date('w', $first)));

    $out = array();
    while ($start < $stop)
    {
        $week = array();
        if ($start > $last)
            break;
        for ($i = 0; $i < 7; $i++)
        {
            $week[$i] = $start;
            $start += 86400;
        }
        $out[] = $week;
    }

    return $out;
}

// Processes mod_rewrite URLs into key => value pairs
// See .htacess for more info.
function pick_off($grab_first = false, $sep = '/')
{
    $ret                    = array();
    $arr                    = explode($sep, trim($_SERVER['REQUEST_URI'], $sep));
    if ($grab_first)
        $ret[0]                 = array_shift($arr);
    while (count($arr) > 0)
        $ret[array_shift($arr)] = array_shift($arr);
    return (count($ret) > 0) ? $ret : false;
}

// Creates a list of <option>s from the given database table.
// table name, column to use as value, column(s) to use as text, default value(s) to select (can accept an array of values), extra sql to limit results
function get_options($table, $val, $text, $default = null, $sql = '')
{
    $db  = Database::getDatabase(true);
    $out = '';

    $table = $db->escape($table);
    $rows  = $db->getRows("SELECT * FROM `$table` $sql");
    foreach ($rows as $row)
    {
        $the_text = '';
        if (!is_array($text))
            $text     = array($text); // Allows you to concat multiple fields for display
        foreach ($text as $t)
            $the_text .= $row[$t] . ' ';
        $the_text = htmlspecialchars(trim($the_text));

        if (!is_null($default) && $row[$val] == $default)
            $out .= '<option value="' . htmlspecialchars($row[$val], ENT_QUOTES) . '" selected="selected">' . $the_text . '</option>';
        elseif (is_array($default) && in_array($row[$val], $default))
            $out .= '<option value="' . htmlspecialchars($row[$val], ENT_QUOTES) . '" selected="selected">' . $the_text . '</option>';
        else
            $out .= '<option value="' . htmlspecialchars($row[$val], ENT_QUOTES) . '">' . $the_text . '</option>';
    }
    return $out;
}

// More robust strict date checking for string representations
function chkdate($str)
{
    return strtotime($str);
}

// Converts a date/timestamp into the specified format
function dater($date = null, $format = null)
{
    if (is_null($format))
    {
        if (defined("SITE_CONFIG_DATE_TIME_FORMAT"))
        {
            $format = SITE_CONFIG_DATE_TIME_FORMAT;
        }
        else
        {
            $format = 'Y-m-d H:i:s';
        }
    }

    if (is_null($date))
    {
        return;
    }

    if ($date == '0000-00-00 00:00:00')
    {
        return;
    }

    // if $date contains only numbers, treat it as a timestamp
    if (ctype_digit($date) === true)
        return date($format, $date);
    else
        return date($format, strtotime($date));
}

// Formats a phone number as (xxx) xxx-xxxx or xxx-xxxx depending on the length.
function format_phone($phone)
{
    $phone = preg_replace("/[^0-9]/", '', $phone);

    if (strlen($phone) == 7)
        return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
    elseif (strlen($phone) == 10)
        return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
    else
        return $phone;
}

// Outputs hour, minute, am/pm dropdown boxes
function hourmin($hid = 'hour', $mid = 'minute', $pid = 'ampm', $hval = null, $mval = null, $pval = null)
{
    // Dumb hack to let you just pass in a timestamp instead
    if (func_num_args() == 1)
    {
        list($hval, $mval, $pval) = explode(' ', date('g i a', strtotime($hid)));
        $hid = 'hour';
        $mid = 'minute';
        $aid = 'ampm';
    }
    else
    {
        if (is_null($hval))
            $hval = date('h');
        if (is_null($mval))
            $mval = date('i');
        if (is_null($pval))
            $pval = date('a');
    }

    $hours = array(12, 1, 2, 3, 4, 5, 6, 7, 9, 10, 11);
    $out   = "<select name='$hid' id='$hid'>";
    foreach ($hours as $hour)
        if (intval($hval) == intval($hour))
            $out .= "<option value='$hour' selected>$hour</option>";
        else
            $out .= "<option value='$hour'>$hour</option>";
    $out .= "</select>";

    $minutes = array('00', 15, 30, 45);
    $out .= "<select name='$mid' id='$mid'>";
    foreach ($minutes as $minute)
        if (intval($mval) == intval($minute))
            $out .= "<option value='$minute' selected>$minute</option>";
        else
            $out .= "<option value='$minute'>$minute</option>";
    $out .= "</select>";

    $out .= "<select name='$pid' id='$pid'>";
    $out .= "<option value='am'>am</option>";
    if ($pval == 'pm')
        $out .= "<option value='pm' selected>pm</option>";
    else
        $out .= "<option value='pm'>pm</option>";
    $out .= "</select>";

    return $out;
}

// Returns the HTML for a month, day, and year dropdown boxes.
// You can set the default date by passing in a timestamp OR a parseable date string.
// $prefix_ will be appened to the name/id's of each dropdown, allowing for multiple calls in the same form.
// $output_format lets you specify which dropdowns appear and in what order.
function mdy($date = null, $prefix = null, $output_format = 'm d y')
{
    if (is_null($date))
        $date = time();
    if (!ctype_digit($date))
        $date = strtotime($date);
    if (!is_null($prefix))
        $prefix .= '_';
    list($yval, $mval, $dval) = explode(' ', date('Y n j', $date));

    $month_dd = "<select name='{$prefix}month' id='{$prefix}month'>";
    for ($i = 1; $i <= 12; $i++)
    {
        $selected = ($mval == $i) ? ' selected="selected"' : '';
        $month_dd .= "<option value='$i'$selected>" . date('F', mktime(0, 0, 0, $i, 1, 2000)) . "</option>";
    }
    $month_dd .= "</select>";

    $day_dd = "<select name='{$prefix}day' id='{$prefix}day'>";
    for ($i = 1; $i <= 31; $i++)
    {
        $selected = ($dval == $i) ? ' selected="selected"' : '';
        $day_dd .= "<option value='$i'$selected>$i</option>";
    }
    $day_dd .= "</select>";

    $year_dd = "<select name='{$prefix}year' id='{$prefix}year'>";
    for ($i = date('Y'); $i < date('Y') + 10; $i++)
    {
        $selected = ($yval == $i) ? ' selected="selected"' : '';
        $year_dd .= "<option value='$i'$selected>$i</option>";
    }
    $year_dd .= "</select>";

    $trans = array('m' => $month_dd, 'd' => $day_dd, 'y' => $year_dd);
    return strtr($output_format, $trans);
}

// Redirects user to $url
function redirect($url = null)
{
    if (is_null($url))
	{
        $url = $_SERVER['PHP_SELF'];
	}
	
	// headers already output
	if(headers_sent())
	{
		echo '<script>window.location=\''.$url.'\';</script>';
	}
	// no headers output yet
	else
	{
		header("Location: $url");
	}
	
    exit();
}

// Ensures $str ends with a single /
function slash($str)
{
    return rtrim($str, '/') . '/';
}

// Ensures $str DOES NOT end with a /
function unslash($str)
{
    return rtrim($str, '/');
}

// Returns an array of the values of the specified column from a multi-dimensional array
function gimme($arr, $key = null)
{
    if (is_null($key))
        $key = current(array_keys($arr));

    $out   = array();
    foreach ($arr as $a)
        $out[] = $a[$key];

    return $out;
}

// Fixes MAGIC_QUOTES
function fix_slashes($arr = '')
{
    if (is_null($arr) || $arr == '')
        return null;
    if (!get_magic_quotes_gpc())
        return $arr;
    return is_array($arr) ? array_map('fix_slashes', $arr) : stripslashes($arr);
}

// Returns the first $num words of $str
function max_words($str, $num, $suffix = '')
{
    $words = explode(' ', $str);
    if (count($words) < $num)
        return $str;
    else
        return implode(' ', array_slice($words, 0, $num)) . $suffix;
}

// Retrieves the filesize of a remote file.
function remote_filesize($url, $user = null, $pw = null)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    if (!is_null($user) && !is_null($pw))
    {
        $headers = array('Authorization: Basic ' . base64_encode("$user:$pw"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $head = curl_exec($ch);
    curl_close($ch);

    preg_match('/Content-Length:\s([0-9].+?)\s/', $head, $matches);

    return isset($matches[1]) ? $matches[1] : false;
}

// Outputs a filesize in human readable format.
function bytes2str($val, $round = 0)
{
    return formatSize($val);
}

// Tests for a valid email address and optionally tests for valid MX records, too.
function valid_email($email, $test_mx = false)
{
    if (preg_match("/^([_a-z0-9+-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i", $email))
    {
        if ($test_mx)
        {
            list(, $domain) = explode("@", $email);
            return getmxrr($domain, $mxrecords);
        }
        else
            return true;
    }
    else
        return false;
}

function valid_username($username)
{
    return preg_match('/^[a-zA-Z0-9_]+$/', $username);
}

// Grabs the contents of a remote URL. Can perform basic authentication if un/pw are provided.
function geturl($url, $username = null, $password = null)
{
    if (function_exists('curl_init'))
    {
        $ch   = curl_init();
        if (!is_null($username) && !is_null($password))
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode("$username:$password")));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        $html = curl_exec($ch);
        curl_close($ch);
        return $html;
    }
    elseif (ini_get('allow_url_fopen') == true)
    {
        if (!is_null($username) && !is_null($password))
            $url  = str_replace("://", "://$username:$password@", $url);
        $html = file_get_contents($url);
        return $html;
    }
    else
    {
        // Cannot open url. Either install curl-php or set allow_url_fopen = true in php.ini
        return false;
    }
}

// Returns the user's browser info.
// browscap.ini must be available for this to work.
// See the PHP manual for more details.
function browser_info()
{
    $info    = get_browser(null, true);
    $browser = $info['browser'] . ' ' . $info['version'];
    $os      = $info['platform'];
    $ip      = $_SERVER['REMOTE_ADDR'];
    return array('ip'      => $ip, 'browser' => $browser, 'os'      => $os);
}

// Quick wrapper for preg_match
function match($regex, $str, $i = 0)
{
    if (preg_match($regex, $str, $match) == 1)
        return $match[$i];
    else
        return false;
}

// Sends an HTML formatted email
function send_html_mail($to, $subject, $msg, $fromEmail = null, $plaintext = '', $debug = false, $fromName = null)
{
    // include email class
    require_once DOC_ROOT . '/includes/email_class/class.phpmailer.php';
    
    if (!is_array($to))
    {
        $to = array($to);
    }
    
    if($fromEmail == null)
    {
        $fromEmail = SITE_CONFIG_DEFAULT_EMAIL_ADDRESS_FROM;
    }
    
    if($fromName == null)
    {
        $fromName = SITE_CONFIG_SITE_NAME;
    }

    $css .= '<style type="text/css">';
    $css .= 'body { font: 11px Verdana,Geneva,Arial,Helvetica,sans-serif; }\n';
    $css .= '</style>';

    $msg = $css . $msg;

    // send using smtp
    if ((SITE_CONFIG_EMAIL_METHOD == 'smtp') && (strlen(SITE_CONFIG_EMAIL_SMTP_HOST)))
    {
        $error = '';
        $mail  = new PHPMailer();
        $body  = $msg;
        $body  = eregi_replace("[\]", '', $body);

        $mail->IsSMTP();
        try
        {
            $mail->Host      = SITE_CONFIG_EMAIL_SMTP_HOST;
            $mail->SMTPDebug = 1;
            $mail->SMTPAuth  = (SITE_CONFIG_EMAIL_SMTP_REQUIRES_AUTH == 'yes') ? true : false;
            $mail->Host      = SITE_CONFIG_EMAIL_SMTP_HOST;
            $mail->Port      = SITE_CONFIG_EMAIL_SMTP_PORT;
            if (SITE_CONFIG_EMAIL_SMTP_REQUIRES_AUTH == 'yes')
            {
                $mail->Username = SITE_CONFIG_EMAIL_SMTP_AUTH_USERNAME;
                $mail->Password = SITE_CONFIG_EMAIL_SMTP_AUTH_PASSWORD;
            }

            $mail->SetFrom($fromEmail, $fromName);
            $mail->AddReplyTo($fromEmail, $fromName);
            $mail->Subject = $subject;

            if (strlen($plaintext))
            {
                $mail->AltBody = $plaintext; // optional, comment out and test
            }

            $mail->MsgHTML($body);
            foreach ($to as $address)
            {
                $mail->AddAddress($address);
            }
            $mail->Send();
        }
        catch (phpmailerException $e)
        {
            $error = $e->errorMessage();
        }
        catch (Exception $e)
        {
            $error = $e->getMessage();
        }

        if (strlen($error))
        {
            if ($debug == true)
            {
                echo $error;
            }
            return false;
        }

        return true;
    }

    // send using php mail
    foreach ($to as $address)
    {
        $boundary = uniqid(rand(), true);

        $headers = "From: $fromEmail\n";
        $headers .= "MIME-Version: 1.0\n";
        $headers .= "Content-Type: multipart/alternative; boundary = $boundary\n";
        $headers .= "This is a MIME encoded message.\n\n";
        $headers .= "--$boundary\n" .
            "Content-Type: text/plain; charset=ISO-8859-1\n" .
            "Content-Transfer-Encoding: base64\n\n";
        $headers .= chunk_split(base64_encode($plaintext));
        $headers .= "--$boundary\n" .
            "Content-Type: text/html; charset=ISO-8859-1\n" .
            "Content-Transfer-Encoding: base64\n\n";
        $headers .= chunk_split(base64_encode($msg));
        $headers .= "--$boundary--\n" .
            mail($address, $subject, '', $headers);
    }
}

// Returns the lat, long of an address via Yahoo!'s geocoding service.
// You'll need an App ID, which is available from here:
// http://developer.yahoo.com/maps/rest/V1/geocode.html
function geocode($location, $appid)
{
    $location = urlencode($location);
    $appid    = urlencode($appid);
    $data     = file_get_contents("http://local.yahooapis.com/MapsService/V1/geocode?output=php&appid=$appid&location=$location");
    $data     = unserialize($data);

    if ($data === false)
        return false;

    $data = $data['ResultSet']['Result'];

    return array('lat' => $data['Latitude'], 'lng' => $data['Longitude']);
}

// Quick and dirty wrapper for curl scraping.
function curl($url, $referer = null, $post = null)
{
    static $tmpfile;

    if (!isset($tmpfile) || ($tmpfile == ''))
        $tmpfile = tempnam('/tmp', 'FOO');

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $tmpfile);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $tmpfile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; U; Intel Mac OS X; en-US; rv:1.8.1) Gecko/20061024 BonEcho/2.0");
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);

    if ($referer)
        curl_setopt($ch, CURLOPT_REFERER, $referer);
    if (!is_null($post))
    {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }

    $html = curl_exec($ch);

    // $last_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    return $html;
}

// Accepts any number of arguments and returns the first non-empty one
function pick()
{
    foreach (func_get_args() as $arg)
        if (!empty($arg))
            return $arg;
    return '';
}

// Secure a PHP script using basic HTTP authentication
function http_auth($un, $pw, $realm = "Secured Area")
{
    if (!(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && $_SERVER['PHP_AUTH_USER'] == $un && $_SERVER['PHP_AUTH_PW'] == $pw))
    {
        header('WWW-Authenticate: Basic realm="' . $realm . '"');
        header('Status: 401 Unauthorized');
        exit();
    }
}

// This is easier than typing 'echo WEB_ROOT'
function WEBROOT()
{
    echo WEB_ROOT;
}

// autoloader
function __autoload($class_name)
{
    if (file_exists(DOC_ROOT . '/includes/class.' . strtolower($class_name) . '.php'))
    {
        require_once(DOC_ROOT . '/includes/class.' . strtolower($class_name) . '.php');
    }
}

// Returns a file's mimetype based on its extension
function mime_type($filename, $default = 'application/octet-stream')
{
    $mime_types = array(
        '323'     => 'text/h323',
        'acx'     => 'application/internet-property-stream',
        'ai'      => 'application/postscript',
        'aif'     => 'audio/x-aiff',
        'aifc'    => 'audio/x-aiff',
        'aiff'    => 'audio/x-aiff',
        'asf'     => 'video/x-ms-asf',
        'asr'     => 'video/x-ms-asf',
        'asx'     => 'video/x-ms-asf',
        'au'      => 'audio/basic',
        'avi'     => 'video/x-msvideo',
        'axs'     => 'application/olescript',
        'bas'     => 'text/plain',
        'bcpio'   => 'application/x-bcpio',
        'bin'     => 'application/octet-stream',
        'bmp'     => 'image/bmp',
        'c'       => 'text/plain',
        'cat'     => 'application/vnd.ms-pkiseccat',
        'cdf'     => 'application/x-cdf',
        'cer'     => 'application/x-x509-ca-cert',
        'class'   => 'application/octet-stream',
        'clp'     => 'application/x-msclip',
        'cmx'     => 'image/x-cmx',
        'cod'     => 'image/cis-cod',
        'cpio'    => 'application/x-cpio',
        'crd'     => 'application/x-mscardfile',
        'crl'     => 'application/pkix-crl',
        'crt'     => 'application/x-x509-ca-cert',
        'csh'     => 'application/x-csh',
        'css'     => 'text/css',
        'dcr'     => 'application/x-director',
        'der'     => 'application/x-x509-ca-cert',
        'dir'     => 'application/x-director',
        'dll'     => 'application/x-msdownload',
        'dms'     => 'application/octet-stream',
        'doc'     => 'application/msword',
        'dot'     => 'application/msword',
        'dvi'     => 'application/x-dvi',
        'dxr'     => 'application/x-director',
        'eps'     => 'application/postscript',
        'etx'     => 'text/x-setext',
        'evy'     => 'application/envoy',
        'exe'     => 'application/octet-stream',
        'fif'     => 'application/fractals',
        'flac'    => 'audio/flac',
        'flr'     => 'x-world/x-vrml',
        'gif'     => 'image/gif',
        'gtar'    => 'application/x-gtar',
        'gz'      => 'application/x-gzip',
        'h'       => 'text/plain',
        'hdf'     => 'application/x-hdf',
        'hlp'     => 'application/winhlp',
        'hqx'     => 'application/mac-binhex40',
        'hta'     => 'application/hta',
        'htc'     => 'text/x-component',
        'htm'     => 'text/html',
        'html'    => 'text/html',
        'htt'     => 'text/webviewhtml',
        'ico'     => 'image/x-icon',
        'ief'     => 'image/ief',
        'iii'     => 'application/x-iphone',
        'ins'     => 'application/x-internet-signup',
        'isp'     => 'application/x-internet-signup',
        'jfif'    => 'image/pipeg',
        'jpe'     => 'image/jpeg',
        'jpeg'    => 'image/jpeg',
        'jpg'     => 'image/jpeg',
        'js'      => 'application/x-javascript',
        'latex'   => 'application/x-latex',
        'lha'     => 'application/octet-stream',
        'lsf'     => 'video/x-la-asf',
        'lsx'     => 'video/x-la-asf',
        'lzh'     => 'application/octet-stream',
        'm13'     => 'application/x-msmediaview',
        'm14'     => 'application/x-msmediaview',
        'm3u'     => 'audio/x-mpegurl',
        'm4v'     => 'video/mp4',
        'man'     => 'application/x-troff-man',
        'mdb'     => 'application/x-msaccess',
        'me'      => 'application/x-troff-me',
        'mht'     => 'message/rfc822',
        'mhtml'   => 'message/rfc822',
        'mid'     => 'audio/mid',
        'mny'     => 'application/x-msmoney',
        'mov'     => 'video/quicktime',
        'movie'   => 'video/x-sgi-movie',
        'mp2'     => 'video/mpeg',
        'mp3'     => 'audio/mpeg',
        'mp4'     => 'video/mp4',
        'mpa'     => 'video/mpeg',
        'mpe'     => 'video/mpeg',
        'mpeg'    => 'video/mpeg',
        'mpg'     => 'video/mpeg',
        'mpp'     => 'application/vnd.ms-project',
        'mpv2'    => 'video/mpeg',
        'ms'      => 'application/x-troff-ms',
        'mvb'     => 'application/x-msmediaview',
        'nws'     => 'message/rfc822',
        'oda'     => 'application/oda',
        'oga'     => 'audio/ogg',
        'ogg'     => 'audio/ogg',
        'ogv'     => 'video/ogg',
        'ogx'     => 'application/ogg',
        'p10'     => 'application/pkcs10',
        'p12'     => 'application/x-pkcs12',
        'p7b'     => 'application/x-pkcs7-certificates',
        'p7c'     => 'application/x-pkcs7-mime',
        'p7m'     => 'application/x-pkcs7-mime',
        'p7r'     => 'application/x-pkcs7-certreqresp',
        'p7s'     => 'application/x-pkcs7-signature',
        'pbm'     => 'image/x-portable-bitmap',
        'pdf'     => 'application/pdf',
        'pfx'     => 'application/x-pkcs12',
        'pgm'     => 'image/x-portable-graymap',
        'pko'     => 'application/ynd.ms-pkipko',
        'pma'     => 'application/x-perfmon',
        'pmc'     => 'application/x-perfmon',
        'pml'     => 'application/x-perfmon',
        'pmr'     => 'application/x-perfmon',
        'pmw'     => 'application/x-perfmon',
        'pnm'     => 'image/x-portable-anymap',
        'pot'     => 'application/vnd.ms-powerpoint',
        'ppm'     => 'image/x-portable-pixmap',
        'pps'     => 'application/vnd.ms-powerpoint',
        'ppt'     => 'application/vnd.ms-powerpoint',
        'prf'     => 'application/pics-rules',
        'ps'      => 'application/postscript',
        'pub'     => 'application/x-mspublisher',
        'qt'      => 'video/quicktime',
        'ra'      => 'audio/x-pn-realaudio',
        'ram'     => 'audio/x-pn-realaudio',
        'ras'     => 'image/x-cmu-raster',
        'rgb'     => 'image/x-rgb',
        'rmi'     => 'audio/mid',
        'roff'    => 'application/x-troff',
        'rtf'     => 'application/rtf',
        'rtx'     => 'text/richtext',
        'scd'     => 'application/x-msschedule',
        'sct'     => 'text/scriptlet',
        'setpay'  => 'application/set-payment-initiation',
        'setreg'  => 'application/set-registration-initiation',
        'sh'      => 'application/x-sh',
        'shar'    => 'application/x-shar',
        'sit'     => 'application/x-stuffit',
        'snd'     => 'audio/basic',
        'spc'     => 'application/x-pkcs7-certificates',
        'spl'     => 'application/futuresplash',
        'src'     => 'application/x-wais-source',
        'sst'     => 'application/vnd.ms-pkicertstore',
        'stl'     => 'application/vnd.ms-pkistl',
        'stm'     => 'text/html',
        'svg'     => "image/svg+xml",
        'sv4cpio' => 'application/x-sv4cpio',
        'sv4crc'  => 'application/x-sv4crc',
        't'       => 'application/x-troff',
        'tar'     => 'application/x-tar',
        'tcl'     => 'application/x-tcl',
        'tex'     => 'application/x-tex',
        'texi'    => 'application/x-texinfo',
        'texinfo' => 'application/x-texinfo',
        'tgz'     => 'application/x-compressed',
        'tif'     => 'image/tiff',
        'tiff'    => 'image/tiff',
        'tr'      => 'application/x-troff',
        'trm'     => 'application/x-msterminal',
        'tsv'     => 'text/tab-separated-values',
        'txt'     => 'text/plain',
        'uls'     => 'text/iuls',
        'ustar'   => 'application/x-ustar',
        'vcf'     => 'text/x-vcard',
        'vrml'    => 'x-world/x-vrml',
        'wav'     => 'audio/x-wav',
        'wcm'     => 'application/vnd.ms-works',
        'wdb'     => 'application/vnd.ms-works',
        'wks'     => 'application/vnd.ms-works',
        'wmf'     => 'application/x-msmetafile',
        'wps'     => 'application/vnd.ms-works',
        'wri'     => 'application/x-mswrite',
        'wrl'     => 'x-world/x-vrml',
        'wrz'     => 'x-world/x-vrml',
        'xaf'     => 'x-world/x-vrml',
        'xbm'     => 'image/x-xbitmap',
        'xla'     => 'application/vnd.ms-excel',
        'xlc'     => 'application/vnd.ms-excel',
        'xlm'     => 'application/vnd.ms-excel',
        'xls'     => 'application/vnd.ms-excel',
        'xlt'     => 'application/vnd.ms-excel',
        'xlw'     => 'application/vnd.ms-excel',
        'xof'     => 'x-world/x-vrml',
        'xpm'     => 'image/x-xpixmap',
        'xwd'     => 'image/x-xwindowdump',
        'z'       => 'application/x-compress',
        'zip'     => 'application/zip',
        'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xltx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
        'potx'    => 'application/vnd.openxmlformats-officedocument.presentationml.template',
        'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
        'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'sldx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
        'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dotx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
        'xlam'    => 'application/vnd.ms-excel.addin.macroEnabled.12',
        'xlsb'    => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12');
    $ext        = pathinfo($filename, PATHINFO_EXTENSION);
    return isset($mime_types[$ext]) ? $mime_types[$ext] : $default;
}

function sqlDateTime()
{
    return date("Y-m-d H:i:s");
}

function getUsersIPAddress()
{
    return $_SERVER['REMOTE_ADDR'];
}

function randomColor()
{
    mt_srand((double) microtime() * 1000000);
    $c = '';
    while (strlen($c) < 6)
    {
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function isValidUrl($url)
{
    /* validate base of url */
    $url = getBaseUrl($url);
    /* make sure there is at least 1 dot */
    if (!strpos($url, "."))
    {
        return FALSE;
    }
    $urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
    if (eregi($urlregex, $url))
    {
        return TRUE;
    }
    return FALSE;
}

function getBaseUrl($url)
{
    $urlExp = explode("/", $url);
    return $urlExp[0] . "//" . $urlExp[2];
}

function isValidIP($ipAddress)
{
    if (preg_match("/^(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5]).){3}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/", $ipAddress))
    {
        return true;
    }
    return false;
}

/* light error handling */
$pageErrorArr = array();

function isErrors()
{
    global $pageErrorArr;
    if (COUNT($pageErrorArr))
    {
        return TRUE;
    }
    return FALSE;
}

function setError($errorMsg)
{
    global $pageErrorArr;
    $pageErrorArr[] = $errorMsg;
}

function getErrors()
{
    global $pageErrorArr;
    return $pageErrorArr;
}

function outputErrors()
{
    $errors = getErrors();
    if (COUNT($errors))
    {
        $htmlArr = array();
        foreach ($errors AS $error)
        {
            $htmlArr[] = "<li>" . $error . "</li>";
        }
        return "<ul class='pageErrors'>" . implode("<br/>", $htmlArr) . "</ul>";
    }
}

/* light error handling */
$pageSuccessArr = array();

function isSuccess()
{
    global $pageSuccessArr;
    if (COUNT($pageSuccessArr))
    {
        return TRUE;
    }
    return FALSE;
}

function setSuccess($errorMsg)
{
    global $pageSuccessArr;
    $pageSuccessArr[] = $errorMsg;
}

function getSuccess()
{
    global $pageSuccessArr;
    return $pageSuccessArr;
}

function outputSuccess()
{
    $success = getSuccess();
    if (COUNT($success))
    {
        $htmlArr = array();
        foreach ($success AS $success)
        {
            $htmlArr[] = "<li>" . $success . "</li>";
        }
        return "<ul class='pageSuccess'>" . implode("<br/>", $htmlArr) . "</ul>";
    }
}

/* translation wrapper */

function t($key, $defaultContent = '', $replacements = array())
{
    return translate::getTranslation($key, $defaultContent, 0, $replacements);
}

function createPassword($length = 7)
{
    $chars = "abcdefghijkmnopqrstuvwxyz023456789";
    srand((double) microtime() * 1000000);
    $i     = 0;
    $pass  = '';

    while ($i <= $length)
    {
        $num  = rand() % 33;
        $tmp  = substr($chars, $num, 1);
        $pass = $pass . $tmp;
        $i++;
    }

    return $pass;
}

function outputFailureImage()
{
    $localFailureImage = DOC_ROOT . "/themes/" . SITE_CONFIG_SITE_THEME . "/images/trans_1x1.gif";
    header('Content-type: image/gif');
    echo file_get_contents($localFailureImage);
    die();
}

function formatSize($bytes)
{
    $size = $bytes / 1024;
    if ($size < 1024)
    {
        $size = number_format($size, 2);
        $size .= ' KB';
    }
    else
    {
        if ($size / 1024 < 1024)
        {
            $size = number_format($size / 1024, 2);
            $size .= ' MB';
        }
        else if ($size / 1024 / 1024 < 1024)
        {
            $size = number_format($size / 1024 / 1024, 2);
            $size .= ' GB';
        }
        else if ($size / 1024 / 1024 / 1024 < 1024)
        {
            $size = number_format($size / 1024 / 1024 / 1024, 2);
            $size .= ' TB';
        }
    }
    // remove unneccessary zeros
    $size = str_replace(".00 ", " ", $size);

    return $size;
}

function getPHPMaxUpload()
{
    $postMaxSize       = returnBytes(ini_get('post_max_size'));
    $uploadMaxFilesize = returnBytes(ini_get('upload_max_filesize'));
    if ($postMaxSize > $uploadMaxFilesize)
    {
        return $uploadMaxFilesize;
    }

    return $postMaxSize;
}

function returnBytes($val)
{
    $val  = trim($val);
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last)
    {
        // The 'G' modifier is available since PHP 5.1.0
        case 'g':
            $val *= 1024;
        case 'm':
            $val *= 1024;
        case 'k':
            $val *= 1024;
    }

    return $val;
}

function getAcceptedFileTypes()
{
    $rs = array();
    if (strlen(trim(SITE_CONFIG_ACCEPTED_UPLOAD_FILE_TYPES)) > 0)
    {
        $fileTypes = explode(";", trim(SITE_CONFIG_ACCEPTED_UPLOAD_FILE_TYPES));
        foreach ($fileTypes AS $fileType)
        {
            if (strlen(trim($fileType)))
            {
                $rs[] = strtolower(trim($fileType));
            }
        }
    }
    sort($rs);

    return $rs;
}

function deleteRedundantFiles()
{
    // check for any files to delete
    $nextCheck = trim(SITE_CONFIG_NEXT_CHECK_FOR_FILE_REMOVALS);
    if (strlen($nextCheck) == 0)
    {
        $nextCheck = time();
    }

    // dont run the check if we're not due to yet
    if ($nextCheck > time())
    {
        return false;
    }

    // connect db
    $db = Database::getDatabase(true);

    // file removal periods
    $fileRemovalNonAcc  = trim(SITE_CONFIG_NON_USER_UPLOAD_REMOVAL_DAYS);
    $fileRemovalFreeAcc = trim(SITE_CONFIG_FREE_USER_UPLOAD_REMOVAL_DAYS);
    $fileRemovalPaidAcc = trim(SITE_CONFIG_PREMIUM_USER_UPLOAD_REMOVAL_DAYS);

    // set a maximum of 5 years otherwise we hit unix timestamp calculation issues
    if ($fileRemovalNonAcc > 1825)
    {
        $fileRemovalNonAcc = 1825;
    }

    if ($fileRemovalFreeAcc > 1825)
    {
        $fileRemovalFreeAcc = 1825;
    }

    if ($fileRemovalPaidAcc > 1825)
    {
        $fileRemovalPaidAcc = 1825;
    }

    // non-accounts
    if ((int) $fileRemovalNonAcc != 0)
    {
        $sQL = 'SELECT file.id ';
        $sQL .= 'FROM file LEFT JOIN users ';
        $sQL .= 'ON file.userId = users.id ';
        $sQL .= 'WHERE file.statusId = 1 AND ';
        $sQL .= 'UNIX_TIMESTAMP(file.uploadedDate) < ' . strtotime('-' . $fileRemovalNonAcc . ' days') . ' AND ';
        $sQL .= '(UNIX_TIMESTAMP(file.lastAccessed) < ' . strtotime('-' . $fileRemovalNonAcc . ' days') . ' OR file.lastAccessed IS NULL) ';
        $sQL .= 'AND (file.userId IS NULL);';

        $rows = $db->getRows($sQL);
        if (is_array($rows))
        {
            foreach ($rows AS $row)
            {
                // load file object
                $file = file::loadById($row['id']);
                if ($file)
                {
                    // remove file
                    $file->removeBySystem();
                }
            }
        }
    }

    // free accounts
    if ((int) $fileRemovalFreeAcc != 0)
    {
        $sQL = 'SELECT file.id ';
        $sQL .= 'FROM file LEFT JOIN users ';
        $sQL .= 'ON file.userId = users.id ';
        $sQL .= 'WHERE file.statusId = 1 AND ';
        $sQL .= 'UNIX_TIMESTAMP(file.uploadedDate) < ' . strtotime('-' . $fileRemovalFreeAcc . ' days') . ' AND ';
        $sQL .= '(UNIX_TIMESTAMP(file.lastAccessed) < ' . strtotime('-' . $fileRemovalFreeAcc . ' days') . ' OR file.lastAccessed IS NULL) ';
        $sQL .= 'AND (users.level_id = 1);';

        $rows = $db->getRows($sQL);
        if (is_array($rows))
        {
            foreach ($rows AS $row)
            {
                // load file object
                $file = file::loadById($row['id']);
                if ($file)
                {
                    // remove file
                    $file->removeBySystem();
                }
            }
        }
    }

    // paid accounts
    if ((int) $fileRemovalPaidAcc != 0)
    {
        $sQL = 'SELECT file.id ';
        $sQL .= 'FROM file LEFT JOIN users ';
        $sQL .= 'ON file.userId = users.id ';
        $sQL .= 'WHERE file.statusId = 1 AND ';
        $sQL .= 'UNIX_TIMESTAMP(file.uploadedDate) < ' . strtotime('-' . $fileRemovalPaidAcc . ' days') . ' AND ';
        $sQL .= '(UNIX_TIMESTAMP(file.lastAccessed) < ' . strtotime('-' . $fileRemovalPaidAcc . ' days') . ' OR file.lastAccessed IS NULL) ';
        $sQL .= 'AND (users.level_id >= 2);';

        $rows = $db->getRows($sQL);
        if (is_array($rows))
        {
            foreach ($rows AS $row)
            {
                // load file object
                $file = file::loadById($row['id']);
                if ($file)
                {
                    // remove file
                    $file->removeBySystem();
                }
            }
        }
    }

    // update db for next check. Run file check again in 1 hour.
    $nextCheck = time() + (60 * 60);
    $db->query('UPDATE site_config SET config_value = :newValue WHERE config_key = \'next_check_for_file_removals\'', array('newValue' => $nextCheck));
}

function browserIsIE()
{
    if (isset($_SERVER['HTTP_USER_AGENT']) &&
        (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function containsInvalidCharacters($input, $allowedChars = 'abcdefghijklmnopqrstuvwxyz 1234567890')
{
    if (removeInvalidCharacters($input, $allowedChars) != $input)
    {
        return true;
    }

    return false;
}

function removeInvalidCharacters($input, $allowedChars = 'abcdefghijklmnopqrstuvwxyz 1234567890')
{
    $str = '';
    for ($i = 0; $i < strlen($input); $i++)
    {
        if (!stristr($allowedChars, $input[$i]))
        {
            continue;
        }

        $str .= $input[$i];
    }

    return $str;
}

function safeOutputToScreen($input, $allowedChars = null, $length = null)
{
    if ($allowedChars != null)
    {
        $input = removeInvalidCharacters($input);
    }

    if ($length != null)
    {
        if (strlen($input) > $length)
        {
            $input = substr($input, 0, $length - 3) . '...';
        }
    }

    $input = htmlspecialchars($input, ENT_QUOTES, "UTF-8");

    return $input;
}

function checkReferrer()
{
    // @TODO - migrate _config.inc.php into the database so external file servers
    // can have access to the core site domain and validate referrers.
    return true;

    $refererSite = getReffererDomain();
    if ($refererSite === false)
    {
        return true;
    }

    // prepare valid referrer urls
    $validUrls = file::getValidReferrers();

    // invalid site
    if (!in_array($refererSite, $validUrls))
    {
        return false;
    }

    return true;
}

function getReffererDomain()
{
    // the referer isn't always available
    if (!isset($_SERVER['HTTP_REFERER']))
    {
        return false;
    }

    $fullRefererUrl = strtolower(trim($_SERVER['HTTP_REFERER']));
    $actualRefExp   = explode("/", $fullRefererUrl);
    $refererSite    = $actualRefExp[2];

    return $refererSite;
}

function getReffererDomainOnly()
{
    // the referer isn't always available
    if (!isset($_SERVER['HTTP_REFERER']))
    {
        return false;
    }

    $fullRefererUrl = strtolower(trim($_SERVER['HTTP_REFERER']));
    $urlData        = parse_url($fullRefererUrl);
    $host           = $urlData['host'];
    if (isset($urlData['port']))
    {
        if (($urlData['port'] != 80) && ((int) $urlData['port'] != 0))
        {
            $host .= ':' . $urlData['port'];
        }
    }

    return $host;
}

function calculateDownloadSpeedFormatted($filesize, $speed = 0)
{
    if ($speed == 0)
    {
        // assume 2MB as an average
        $speed = 5242880;
    }

    $minutes = ceil($filesize / $speed);

    return secsToHumanReadable($minutes);
}

function secsToHumanReadable($secs)
{
    $units = array(
        "week"   => 7 * 24 * 3600,
        "day"    => 24 * 3600,
        "hour"   => 3600,
        "minute" => 60,
        "second" => 1,
    );

    // specifically handle zero
    if ($secs == 0)
        return "0 seconds";

    $s = "";

    foreach ($units as $name => $divisor)
    {
        if ($quot = intval($secs / $divisor))
        {
            $s .= "$quot $name";
            $s .= (abs($quot) > 1 ? "s" : "") . " ";
            $secs -= $quot * $divisor;
        }
    }

    return substr($s, 0, -1);
}

function getAvailableServerId()
{
    // connect db
    $db = Database::getDatabase(true);
	
	// check plugins for server to use
	$params = pluginHelper::includeAppends('functions_get_available_server_id.php', array('serverId' => null));
	if ((int)$params['serverId'])
	{
		return $params['serverId'];
	}

    // choose server
    switch (SITE_CONFIG_C_FILE_SERVER_SELECTION_METHOD)
    {
        case 'Least Used Space':
            $sQL = "SELECT file_server.id ";
            $sQL .= "FROM file_server ";
            $sQL .= "WHERE statusId = 2 ";
            $sQL .= "ORDER BY totalSpaceUsed ASC";

            $serverDetails = $db->getRow($sQL);
            if (is_array($serverDetails))
            {
                return $serverDetails['id'];
            }

            // none found so return the default local
            return 1;

            break;
        case 'Until Full':
            $sQL = "SELECT file_server.id ";
            $sQL .= "FROM file_server ";
            $sQL .= "WHERE IF(maximumStorageBytes > 0, totalSpaceUsed <= maximumStorageBytes, 1=1) AND statusId = 2 ";
            $sQL .= "ORDER BY priority ASC, id ASC";

            $serverDetails = $db->getRow($sQL);
            if (is_array($serverDetails))
            {
                return $serverDetails['id'];
            }

            // none found so return the default local
            return 1;

            break;
        default:
            $sQL           = "SELECT id FROM file_server WHERE serverLabel = " . $db->quote(SITE_CONFIG_DEFAULT_FILE_SERVER) . " AND statusId = 2 LIMIT 1";
            $serverDetails = $db->getRow($sQL);
            if (is_array($serverDetails))
            {
                return $serverDetails['id'];
            }

            // none found so return the default local
            return 1;

            break;
    }

    // fall back
    return 1;
}

function useCaptcha()
{
    if (showDownloadCaptcha() != true)
    {
        return false;
    }

    return true;
}

function outputCaptcha()
{
    // include the captcha functions
    require_once DOC_ROOT . '/includes/recaptcha/recaptchalib.php';
    
    // return the captcha html
    return recaptcha_get_html(SITE_CONFIG_CAPTCHA_PUBLIC_KEY);
}

function captchaCheck($challengeField, $responseField)
{
    // include the captcha functions
    require_once DOC_ROOT . '/includes/recaptcha/recaptchalib.php';
    
    // check captcha
    $resp = recaptcha_check_answer(SITE_CONFIG_CAPTCHA_PRIVATE_KEY, $_SERVER["REMOTE_ADDR"], $challengeField, $responseField);
    if (!$resp->is_valid)
    {
        return false;
    }
    
    return true;
}

function getTmpFolderSize($formatted = false)
{
    $bytes = disk_total_space(sys_get_temp_dir());
    if ($bytes == 0)
    {
        return 0;
    }

    if ($formatted == true)
    {
        return formatSize($bytes);
    }

    return $bytes;
}

function output404()
{
    header("HTTP/1.0 404 Not Found");
    exit;
}

function inPluginDemoMode()
{
    if (!isset($_SESSION['_plugins']))
    {
        return false;
    }

    if (($_SESSION['_plugins'] == false) || (_CONFIG_DEMO_MODE == false))
    {
        return false;
    }

    return true;
}

function getSiteHost()
{
    return $_SERVER['HTTP_HOST'];
}

function checkDomain()
{
    // get requesting host
    $siteHost = getSiteHost();

    // remove any ports
    $siteHostExp   = explode(':', $siteHost);
    $configHostExp = explode(':', _CONFIG_SITE_HOST_URL);

    // redirect to config file version if not
    if (strtolower($siteHostExp[0]) != strtolower($configHostExp[0]))
    {
        redirect(_CONFIG_SITE_PROTOCOL . '://' . _CONFIG_SITE_FULL_URL);
    }
}

function getRemainingFilesToday()
{
    if ((int) SITE_CONFIG_MAX_FILES_PER_DAY == 0)
    {
        return 10000;
    }

    $userIP         = getUsersIPAddress();
    $db             = Database::getDatabase(true);
    $totalUploads   = (int) $db->getValue('SELECT COUNT(id) AS total FROM file WHERE DATE(uploadedDate) = DATE(NOW()) AND uploadedIP = ' . $db->quote($userIP));
    $totalRemaining = (int) SITE_CONFIG_MAX_FILES_PER_DAY - $totalUploads;

    return $totalRemaining >= 0 ? $totalRemaining : 0;
}

function createUploadError($name, $msg)
{
    // setup object for errors
    $fileUpload                    = new stdClass();
    $fileUpload->size              = 0;
    $fileUpload->type              = '';
    $fileUpload->name              = $name;
    $fileUpload->error             = $msg;
    $fileUpload->error_result_html = uploader::generateErrorHtml($fileUpload);

    return json_encode(array($fileUpload));
}

function getCoreSitePath()
{
    if (!defined("_CONFIG_CORE_SITE_HOST_URL"))
    {
        return WEB_ROOT;
    }

    return _CONFIG_SITE_PROTOCOL . "://" . _CONFIG_CORE_SITE_FULL_URL;
}

function addSessionId($url)
{
    // database connection
    $db = Database::getDatabase(true);

    // create tracker key for session
    $sessionTrackerKey = MD5(microtime() . rand(0, 999999));

    // add session tracker to db, this will only be valid for 1 minute
    $sql  = "INSERT INTO session_transfer (transfer_key, session_id, date_added)
                    VALUES (:transfer_key, :session_id, NOW())";
    $vals = array(
        'transfer_key' => $sessionTrackerKey,
        'session_id'   => session_id(),
    );
    $db->query($sql, $vals);

    return $url . '?_sid=' . session_id() . '&_trk=' . $sessionTrackerKey;
}

function mainSiteAccessOnly()
{
    // make sure this is the main site, only uploads on the main site are permitted
    if (isMainSite() == false)
    {
        redirect(_CONFIG_SITE_PROTOCOL . '://' . _CONFIG_CORE_SITE_HOST_URL);
    }
}

function isMainSite()
{
    if (_CONFIG_SITE_HOST_URL != _CONFIG_CORE_SITE_HOST_URL)
    {
        return false;
    }

    return true;
}

// for setting up sessions cross site
function generateSessionUrl($directServerUrl)
{
    if (substr($directServerUrl, strlen($directServerUrl) - 1, 1) != '/')
    {
        $directServerUrl .= '/';
    }

    return addSessionId($directServerUrl . '_s.php');
}

function deviceIsAndroid()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];
    if (eregi('Android', $ua))
    {
        return true;
    }

    return false;
}

function showSiteAdverts($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return SITE_CONFIG_FREE_USER_SHOW_ADVERTS=='yes'?true:false;
        // non user
        case 0:
            return SITE_CONFIG_NON_USER_SHOW_ADVERTS=='yes'?true:false;
        // paid & admin users
        default:
            return SITE_CONFIG_PAID_USER_SHOW_ADVERTS=='yes'?true:false;
    }
}

function getDelayedRedirectWait($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    return getTotalWaitingTime($levelId);
}

function getAllowedToUpload($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return SITE_CONFIG_FREE_USER_ALLOW_UPLOADS=='yes'?true:false;
        // non user
        case 0:
            return SITE_CONFIG_NON_USER_ALLOW_UPLOADS=='yes'?true:false;
        // paid user
        case 2:
            return SITE_CONFIG_PAID_USER_ALLOW_UPLOADS=='yes'?true:false;
        // other users
        default:
            return true;
    }
}

function getMaxDailyDownloads($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_MAX_DOWNLOADS_PER_DAY;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_MAX_DOWNLOADS_PER_DAY;
        // paid & admin users
        default:
            return (int) SITE_CONFIG_PREMIUM_USER_MAX_DOWNLOADS_PER_DAY;
    }
}

function getMaxDownloadSize($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_FILESIZE;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_MAX_DOWNLOAD_FILESIZE;
        // paid & admin users
        default:
            return 0;
    }
}

function getMaxDownloadSpeed($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_SPEED;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_MAX_DOWNLOAD_SPEED;
        // paid & admin users
        default:
            return (int) SITE_CONFIG_PREMIUM_USER_MAX_DOWNLOAD_SPEED;
    }
}

function getMaxRemoteUrls($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_MAX_REMOTE_URLS;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_MAX_REMOTE_URLS;
        // paid & admin users
        default:
            return (int) SITE_CONFIG_PREMIUM_USER_MAX_REMOTE_URLS;
    }
}

function getMaxUploadFilesize($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_MAX_UPLOAD_FILESIZE;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_MAX_UPLOAD_FILESIZE;
        // paid & admin users
        default:
            return (int) SITE_CONFIG_PREMIUM_USER_MAX_UPLOAD_FILESIZE;
    }
}

function showDownloadCaptcha($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (SITE_CONFIG_FREE_USER_SHOW_CAPTCHA == 'yes') ? true : false;
        // non user
        case 0:
            return (SITE_CONFIG_NON_USER_SHOW_CAPTCHA == 'yes') ? true : false;
        // paid & admin users
        default:
            return false;
    }
}

function getWaitTimeBetweenDownloads($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int) SITE_CONFIG_FREE_USER_WAIT_BETWEEN_DOWNLOADS;
        // non user
        case 0:
            return (int) SITE_CONFIG_NON_USER_WAIT_BETWEEN_DOWNLOADS;
        // paid & admin users
        default:
            return 0;
    }
}

function enableUpgradePage($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return SITE_CONFIG_FREE_USER_SHOW_UPGRADE_PAGE;
        // non user
        case 0:
            return SITE_CONFIG_NON_USER_SHOW_UPGRADE_PAGE;
        // paid & admin users
        default:
            return SITE_CONFIG_PAID_USER_SHOW_UPGRADE_PAGE;
    }
}

function getMaxDownloadThreads($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    switch($levelId)
    {
        // free user
        case 1:
            return (int)SITE_CONFIG_FREE_USER_MAX_DOWNLOAD_THREADS;
        // non user
        case 0:
            return (int)SITE_CONFIG_NON_USER_MAX_DOWNLOAD_THREADS;
        // paid & admin users
        default:
            return (int)SITE_CONFIG_PAID_USER_MAX_DOWNLOAD_THREADS;
    }
}

function getMaxFileStorage()
{
    $fallback = 1024 * 1024 * 1024 * 1024 * 5; // 5TB fallback
    $limit    = $fallback;
    $Auth     = Auth::getAuth();
    if ($Auth->loggedIn() == true)
    {
        // limit based on account type
        if ($Auth->level == 'free user')
        {
            $limit = ((strlen(SITE_CONFIG_FREE_USER_MAXIMUM_STORAGE) == 0) || (SITE_CONFIG_FREE_USER_MAXIMUM_STORAGE == 0)) ? $fallback : SITE_CONFIG_FREE_USER_MAXIMUM_STORAGE;
        }
        else
        {
            $limit = ((strlen(SITE_CONFIG_PREMIUM_USER_MAXIMUM_STORAGE) == 0) || (SITE_CONFIG_PREMIUM_USER_MAXIMUM_STORAGE == 0)) ? $fallback : SITE_CONFIG_PREMIUM_USER_MAXIMUM_STORAGE;
        }

        // check for limit override
        if ((strlen($Auth->user->storageLimitOverride)) && ($Auth->user->storageLimitOverride > 0))
        {
            $limit = $Auth->user->storageLimitOverride;
        }
    }

    return $limit;
}

function getTotalWaitingTime($levelId = null)
{
    if($levelId == null)
    {
        $Auth = Auth::getAuth();
        $levelId = $Auth->level_id;
    }
    
    // lookup total waiting time
    $db = Database::getDatabase();
    $sQL = 'SELECT additional_settings FROM download_page WHERE user_level_id = '.(int)$levelId;
    $rows = $db->getRows($sQL);
    $totalTime = 0;
    if($rows)
    {
        foreach($rows AS $row)
        {
            $additionalSettings = $row['additional_settings'];
            if(strlen($additionalSettings))
            {
                $additionalSettingsArr = json_decode($additionalSettings, true);
                if(isset($additionalSettingsArr['download_wait']))
                {
                    $totalTime = $totalTime + (int)$additionalSettingsArr['download_wait'];
                }
            }
        }
    }
    
    return $totalTime;
}

function getAvailableFileStorage()
{
    $Auth           = Auth::getAuth();
    $maxFileStorage = getMaxFileStorage();
    if ($Auth->loggedIn() == true)
    {
        $totalUsed = file::getTotalActiveFileSizeByUser($Auth->id);
        if ($totalUsed > $maxFileStorage)
        {
            return 0;
        }

        return $maxFileStorage - $totalUsed;
    }

    return $maxFileStorage;
}

function dateformatPhpToJqueryUi($php_format)
{
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'dd',
        'D' => 'D',
        'j' => 'd',
        'l' => 'DD',
        'N' => '',
        'S' => '',
        'w' => '',
        'z' => 'o',
        // Week
        'W' => '',
        // Month
        'F' => 'MM',
        'm' => 'mm',
        'M' => 'M',
        'n' => 'm',
        't' => '',
        // Year
        'L' => '',
        'o' => '',
        'Y' => 'yy',
        'y' => 'y',
        // Time
        'a' => '',
        'A' => '',
        'B' => '',
        'g' => '',
        'G' => '',
        'h' => '',
        'H' => '',
        'i' => '',
        's' => '',
        'u' => ''
    );
    $jqueryui_format  = "";
    $escaping         = false;
    for ($i = 0; $i < strlen($php_format); $i++)
    {
        $char = $php_format[$i];
        if ($char === '\\') // PHP date format escaping character
        {
            $i++;
            if ($escaping)
                $jqueryui_format .= $php_format[$i];
            else
                $jqueryui_format .= '\'' . $php_format[$i];
            $escaping = true;
        }
        else
        {
            if ($escaping)
            {
                $jqueryui_format .= "'";
                $escaping = false;
            }
            if (isset($SYMBOLS_MATCHING[$char]))
                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
            else
                $jqueryui_format .= $char;
        }
    }
    return $jqueryui_format;
}

function showMaintenancePage()
{
    if (!defined('IGNORE_MAINTENANCE_MODE'))
    {
        include_once(DOC_ROOT . '/_maintenance_page.inc.php');
        exit;
    }
}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);

    return $d && $d->format($format) == $date;
}

function convertDateToTimestamp($date, $format = 'Y-m-d H:i:s')
{
    if (!validateDate($date, $format))
    {
        return false;
    }

    $d = DateTime::createFromFormat($format, $date);

    return $d->getTimestamp();
}

function executeBatchTasks()
{
    // check for file deletions & accounts to downgrade. This can be moved to a cron if required. It will only run checks every hour as-is.
    UserPeer::downgradeExpiredAccounts();
    deleteRedundantFiles();

    // do any batch tasks in the plugins
    pluginHelper::includeAppends('batch_tasks.php');
}

if (!function_exists('array_replace_recursive'))
{

    function array_replace_recursive($array, $array1)
    {
        $rs = array();
        foreach ($array AS $k => $arrayItem)
        {
            $rs[$k] = $arrayItem;
            if (isset($array1[$k]))
            {
                $rs[$k] = $array1[$k];
            }
        }

        return $rs;
    }

}