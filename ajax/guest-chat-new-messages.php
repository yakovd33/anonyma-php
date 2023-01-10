<?php
    require_once('../config.php');
    $messages = array(
        'code' => '',
        'messages' => array(),
    );

    if (isset($_POST['chash'])) {
        $chash = $_POST['chash'];
        $chat_details = $config->link->query("SELECT * FROM `guests_chats` WHERE `hash` = '{$chash}'")->fetch();
        // Check whether user is user_one or user_two
        $user_one_or_two = $chat_details['user_one_hash'] == $_SESSION['guest_session'] ? 'one' : 'two';
        // Get last updated time
        $last_updated_time = $chat_details['user_' . $user_one_or_two . '_last_message_check'];
        // Get messages sent after last updated date
        $new_messages = $config->link->prepare("SELECT `message`, `sent_date`, `photo_hash` FROM `guests_chat_messages` WHERE `sent_date` > '{$last_updated_time}' AND `from_hash` <> ?");
        $new_messages->execute([$_SESSION['guest_session']]);
        // Update last updated time
        if ($new_messages->rowCount() > 0) {
            $current_time = date('Y-m-d H:i:s');
            $update_time_prepared_stmt = $config->link->prepare("UPDATE `guests_chats` SET `user_" . $user_one_or_two . "_last_message_check` = '{$current_time}' WHERE `hash` = ?");
            $update_time_prepared_stmt->execute([$chash]);
        }

        $messages['code'] = "success";

        while ($message = $new_messages->fetch()) {
            $message['sent_date'] = $config->time_format($message['sent_date']);
            if ($message['photo_hash'] != NULL) {
                $photo_path = $new_photo_details = $config->link->query("SELECT `hash`, `path` FROM `photos` WHERE `hash` = '{$message['photo_hash']}'")->fetch()['path'];
                $message['photo_path'] = $photo_path;
            }
            array_push($messages['messages'], json_encode($message, JSON_FORCE_OBJECT));
        }

        echo json_encode($messages);
    }
?>