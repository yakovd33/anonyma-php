<?php
    require_once('../config.php');

    $result = array();
    if (isset($_POST['new_message']) && isset($_POST['chash']) && isset($_POST['token'])) {
        $chash = $_POST['chash'];
        $token = $_POST['token'];

        if (isset($_POST['message_content']) && !empty($_POST['message_content'])) {
            $post_type = 'text';
            if ($token == $_SESSION['guest-chat-token']) {
                $message_content = strip_tags($_POST['message_content'], '<br>');
                // Allow some tags

                // Insert message
                $insert_stmt = $config->link->prepare("INSERT INTO `guests_chat_messages`(`id`, `chat_hash`, `from_hash`, `type`, `message`, `sent_date`, `is_read`) VALUES (NULL, ?, ?, ?, ?, NOW(), 0)");
                $insert_stmt->execute(array($chash, $_SESSION['guest_session'], $post_type, $message_content));
                $new_message_id = $config->link->lastInsertId();
                $new_message_details = $config->link->query("SELECT * FROM `guests_chat_messages` WHERE `id` = " . $new_message_id)->fetch();
                array_push($result, "success");
                array_push($result, $config->time_format($new_message_details['sent_date']));
                array_push($result, $new_message_details['message']);
            } else {
                array_push($result, "Invalid token");
            }
        } else {
            if ($token == $_SESSION['guest-chat-token']) {
                if (isset($_FILES['image'])) {
                    $file = $_FILES['image'];
                    // Insert photo
                    $new_photo_hash = $config->insert_photo($_FILES['image'], 'media/guest_chat_images');
                    $new_photo_details = $config->link->query("SELECT `hash`, `path` FROM `photos` WHERE `hash` = '{$new_photo_hash}'")->fetch();

                    // Insert message
                    $post_type = 'photo';
                    $insert_stmt = $config->link->prepare("INSERT INTO `guests_chat_messages`(`id`, `chat_hash`, `from_hash`, `type`, `photo_hash`, `message`, `sent_date`, `is_read`) VALUES (NULL, ?, ?, ?, ?, ?, NOW(), 0)");
                    $insert_stmt->execute(array($chash, $_SESSION['guest_session'], $post_type, $new_photo_details['hash'], NULL));
                    $new_message_id = $config->link->lastInsertId();
                    $new_message_details = $config->link->query("SELECT * FROM `guests_chat_messages` WHERE `id` = " . $new_message_id)->fetch();

                    array_push($result, "success");
                    array_push($result, $config->time_format($new_message_details['sent_date']));
                    array_push($result, $new_photo_details['path']);
                } else {
                    array_push($result, "Missing message content");
                }
            } else {
                array_push($result, "Invalid token");
            }
        }
    } else {
        if (!isset($_POST['token'])) {
            array_push($result, "Missing token");
        }

        if (!isset($_POST['chash'])) {
            array_push($result, "Missing Chat Hash");
        }
    }

    echo json_encode($result);
?>