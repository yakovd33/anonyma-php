<?php
session_start();
date_default_timezone_set('Israel');

class config {
    public $link;
    public $website_name;
    public $user;
    public $allowed_age = 13;

    public function __construct ($dbname, $dbuser, $dbpass, $website_name) {
        $this->link = new PDO("mysql:host=localhost;dbname=" . $dbname, $dbuser, $dbpass);
        $this->website_name = $website_name;

        $this->user = new user();
    }

    public function time_format ($date) {
         return date_format(date_create($date), "H:i");
    }

    function insert_photo ($file, $new_loc) {
        // Move to folder
        $tmp_name = $file["tmp_name"];
        $name = md5(date('Y-m-d H:i:s:u') . rand(0, 10000));
        $file_name = $file['name'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file['size'] != 0 && $file['error'] == 0 && $file['size'] < 2000000) {
            $allowed_extensions = array('image/png', 'image/jpg', 'image/jpeg', 'image/gif');
            if (in_array($file['type'], $allowed_extensions)) {
                $final_path = "/" . $name . "." . $ext;
                move_uploaded_file($tmp_name, "../" . $new_loc . '/' . $name . "." . $ext);

                // Insert to DB
                $photo_hash = md5(time() / rand(1, 100000));
                $insert_photo_to_db_stmt = $this->link->prepare("INSERT INTO `photos`(`id`, `path`, `type`, `date`, `hash`) VALUES (NULL, ?, ?, NOW(), ?)");
                $insert_photo_to_db_stmt->execute([$new_loc . $final_path, 'image', $photo_hash]);
                return $photo_hash;
            }
        }
    }

    function get_photo_path_by_hash ($hash) {
        return $this->link->query("SELECT `path` FROM `photos` WHERE `hash` = '{$hash}'")->fetch()['path'];
    }

    function generate_hash () {
        return md5(time() . rand(0, 10000000));
    }

    function getContrast50($hexcolor){
        return (hexdec($hexcolor) > 0xffffff/2) ? 'black':'white';
    }

    function time_parse ($time) {
	    $epoch = strtotime($time);
	    $now = time();

	    // Get the difference in time. This is how many seconds between now and the timestamp provided
	    $dif = $now - $epoch + 10800;

	    $times = array(
            array('term' => 'שניות', 'divide' => false),
            array('term' => 'דקות', 'divide' => 60),
            array('term' => 'שעות', 'divide' => 3600),
            array('term' => 'ימים', 'divide' => 86400),
            array('term' => 'שבועות', 'divide' => 604800),
            array('term' => 'חודשים', 'divide' => 2592000),
            array('term' => 'שנים', 'divide' => 31536000)
        );

        for ($i = 0; $i < count($times); $i++) {
            if (isset($times[$i+1])) {
                $next = $times[$i+1];
            } else{
                $next = false;
            }

            if($next == false || $dif < $next['divide']) {
                if ($times[$i]['divide'] != false) {
                    $time = floor($dif / $times[$i]['divide']);
                } else{
                    $time = $dif;
                }
                if ($time == 1) {
                    return $time.' '.$times[$i]['term'].'';
                } else{
                    return $time.' '.$times[$i]['term'].'';
                }
            }
        }

	    return '';
    }

    function convertHashtags($str){
        $regex = "/#+([a-zA-Z0-9_]+)/";
        $str = preg_replace($regex, '<a href="hashtag.php?tag=$1">$0</a>', $str);
        return($str);
    }

    function numberToMonthName ($number) {
        switch ($number) {
            case 1 :
                return "ינואר";
                break;
            case 2 :
                return "פברואר";
                break;
            case 3 :
                return "מרץ";
                break;
            case 4 :
                return "אפריל";
                break;
            case 5 :
                return "מאי";
                break;
            case 6 :
                return "יוני";
                break;
            case 7 :
                return "יולי";
                break;
            case 8 :
                return "אוגוסט";
                break;
            case 9 :
                return "ספטמבר";
                break;
            case 10 :
                return "אוקטובר";
                break;
            case 11 :
                return "נובמבר";
                break;
            case 12 :
                return "דצמבר";
                break;
        }
    }

    function get_tag_details_by_hash ($hash) {
        global $config;
        return $config->link->query("SELECT * FROM `tags` WHERE `hash` = '{$hash}'")->fetch();
    }
}

class user { 
    public function loggedin () {
        return (isset($_SESSION['user_hash']));
    }

    public function get_client_ip () {
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

    public function get_user_details_by_hash ($hash, $item) {
        global $config;
        $details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$hash}'");

        if ($item != 'all') {
            return $details->fetch()[$item];
        } else {
            return $details->fetch();
        }
    }

    public function get_user_pp_by_hash ($hash) {
        global $config;
        $pp_hash = $config->link->query("SELECT `profile_picture_hash` FROM `users` WHERE `user_hash` = '{$hash}'")->fetch()['profile_picture_hash'];
        if ($pp_hash != NULL) {
            return $this->get_photo_path_by_hash($pp_hash);
        } else {
            return 'media/default-profile-picture.jpg';
        }
    }

    public function is_basic_registration_complete () {
        global $config;
        $user_details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$_SESSION['user_hash']}'")->fetch();
        if ((is_null($user_details['fullname']) || empty($user_details['fullname'])) ||
        (is_null($user_details['nickname']) || empty($user_details['nickname'])) ||
        $user_details['email_validation'] == 0) {
            return false;
        } else {
            return true;
        }
    }
}

// $config = new config("yakovd33_anonyma", "yakovd33_root", "100201d", "anonyma");
$config = new config("Anonyma", "root", "", "אנונימה");
if (!isset($_SESSION['guest_session'])) {
    $_SESSION['guest_session'] = md5(time());
}