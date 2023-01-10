<?php
    require_once('config.php');

    function timeDiff($firstTime, $lastTime) {
        $firstTime = strtotime($firstTime) - 10796;
        $seconds = $lastTime - $firstTime;

        $m = floor(($seconds % 3600) / 60);
        $s = $seconds % 60;
        return sprintf("<span id='minutes'>%02d</span>:<span id='seconds'>%02d</span>", $m, $s);
    }

    if (isset($_GET['chash'])) {
        $chat_details = $config->link->query("SELECT * FROM `guests_chats` WHERE `hash` = '{$_GET['chash']}'")->fetch();
        if ($chat_details['user_one_hash'] == $_SESSION['guest_session'] || $chat_details['user_two_hash'] == $_SESSION['guest_session']) {
        $_SESSION['guest-chat-token'] = md5($_GET['chash']);
?>
    <!DOCTYPE html>
    <html lang="en" dir="RTL">
        <head>
            <meta charset="UTF-8">
            <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
            <title>צ'אט עם זרים - <?php echo $config->website_name; ?></title>
            <link rel="stylesheet" href="stylesheets/general/main.css">
            <link rel="stylesheet" href="stylesheets/guest/guest_chat.css">
            <link rel="stylesheet" href="stylesheets/general/animate.css">
            <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://use.fontawesome.com/6b4470aa21.js"></script>
        </head>
        <body>
            <?php include 'includes/nav.php'; ?>
            <div class="site-wrap">
                <!-- Chat frame -->
                <div class="card grid__col grid__col--7-of-14" id="guest-chat-frame">
                    <div id="chat-head">
                        <div id="user-one-details" class="chat-head-user-info">
                            <div id="user-nickname"><?php echo $chat_details['user_one_nickname']; if ($chat_details['user_one_hash'] == $_SESSION['guest_session']) {echo '<span id="me-indicator">אני</span>';} ?></div>
                            <div id="user-chat-icon" style="background-image: url(media/user-chat-icons/<?php echo $chat_details['user_one_icon']; ?>.png);"></div>
                        </div>
                        
                        <div id="timer"><?php echo timeDiff($chat_details['created_time'], time()); ?></div>
                        <div id="user-two-details" class="chat-head-user-info">
                            <div id="user-nickname"><?php echo $chat_details['user_two_nickname']; if ($chat_details['user_two_hash'] == $_SESSION['guest_session']) {echo '<span id="me-indicator">אני</span>';} ?></div>
                            <div id="user-chat-icon" style="background-image: url(media/user-chat-icons/<?php echo $chat_details['user_two_icon']; ?>.png);"></div>
                        </div>
                    </div>
                    
                    <div id="chat-messages" class="hoverscroll">
                        <?php
                            $chat_messages = $config->link->query("SELECT * FROM `guests_chat_messages` WHERE `chat_hash` = '{$_GET['chash']}' ORDER BY `sent_date` ASC");
                            while ($message = $chat_messages->fetch()) {
                        ?>
                                <div class="guest-chat-message <?php if ($message['from_hash'] == $_SESSION['guest_session']) {echo 'self';} ?>">
                                    <?php
                                        if ($message['photo_hash'] != NULL) {
                                            $message_photo_path = $config->link->query("SELECT * FROM `photos` WHERE `hash` = '{$message['photo_hash']}'")->fetch()['path'];
                                        }
                                    ?>
                                    <div class="message-content"><?php if ($message['photo_hash'] != NULL) { echo '<img class="chat-photo-image nofullscreen" src="' . $message_photo_path . '">'; } echo $message['message']; ?></div>
                                    <div class="sent-date"><?php echo $config->time_format($message['sent_date']); ?></div>
                                </div>
                        <?php
                            }
                        ?>
                    </div>

                    <form id="chat-input-box">
                        <div id="chat-additionals">
                            <div id="chat-additionals-triggers">
                                <div id="emojis-trigger" class="trigger"><i class="fa fa-smile-o" aria-hidden="true"></i></div>
                                <div id="photos-trigger" class="trigger"><i class="fa fa-camera" aria-hidden="true"></i></div>
                            </div>

                            <input type="file" id="additional-image" accept="image/*">
                        </div>

                        <input type="hidden" id="guest-new-message-token" value="<?php echo $_SESSION['guest-chat-token']; ?>">
                        <div id="text-input" placeholder="תוכן ההודעה..." contenteditable></div>
                    </form>

                </div>

                <div class="grid__col grid__col--3-of-14">
                    <!-- <h4>צ'אטים אחרונים</h4> -->
                </div>

                <?php include 'modules/general/left-sidebar.php'; ?>    
            </div>
            <script src="scripts/main.js"></script>
            <script src="scripts/guest/guest-random-chat.js"></script>
        </body>
    </html>
<?php
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
?>