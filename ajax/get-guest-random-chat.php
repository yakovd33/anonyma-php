<?php
    require_once('../config.php');
    if (isset($_POST['search'])) {
        if (isset($_POST['nickname'])) {
            $nickname = $_POST['nickname'];
        } else {
            $nickname = 'אנונימי';
        }

        if (isset($_POST['icon-name'])) {
            $icon = $_POST['icon-name'];
        } else {
            $chat_icons = array_diff(scandir("../media/user-chat-icons"), array('.', '..'));
            $icon = preg_replace('/\\.[^.\\s]{3,4}$/', '', $chat_icons[rand(2, count($chat_icons))]);
        }

        $NOW =  date('Y-m-d H:i:s');

        $user_hash = $_SESSION['guest_session'];
        $get_chats_stmt = $config->link->query("SELECT * FROM `guests_chats` WHERE `is_taken` = 0 AND `is_aborted` = 0");
        if ($get_chats_stmt->rowCount() > 0) {
            // Check if chat wasn't created by the current user
            while ($chat = $get_chats_stmt->fetch()) {
                if ($chat['user_one_hash'] != $user_hash) {
                    // Check if chat isn't expired
                    if (strtotime($chat['created_time']) > time() - 60 * 5) {
                        // Insert user to chat
                        $config->link->query("UPDATE `guests_chats` SET `user_two_hash` = '" . $user_hash . "', `user_two_nickname` = '" . $nickname . "', `user_two_icon` = '" . $icon . "', `is_taken` = 1, `user_two_last_message_check` = '{$NOW}' WHERE `hash` = '{$chat['hash']}'");
                        echo json_encode(array('found', $chat['hash']));
                    }
                }
            }
        } else {
            // Create new chat
            $hash = $config->generate_hash();
            $config->link->query("INSERT INTO `guests_chats`(`id`, `hash`, `created_time`, `user_one_hash`, `user_one_nickname`, `user_one_icon`, `user_one_last_message_check`) VALUES (NULL, '" . $hash . "', NOW(), '" . $user_hash . "', '" . $nickname . "', '" . $icon . "', '{$NOW}')");
            print_r($config->link->errorInfo());
            echo $result = json_encode(array('wait', $hash));
        }
    }
?>