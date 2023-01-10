<?php
    require_once('../config.php');

    $result = array('code' => 'success', 'errors' => array(), 'new_hash' => '');

    if (isset($_POST['publicity']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['token']) && isset($_POST['max_answers']) && isset($_POST['is_infinite_answers']) && isset($_POST['is_anonymous']) && isset($_POST['anonymous-nickname'])) {
        if (!empty($_POST['publicity']) && !empty($_POST['title']) && !empty($_POST['token'])) {
            $token = $_POST['token'];
            $title =  htmlentities($_POST['title']);
            $description = strip_tags($_POST['description'], '<br>');
            $is_public = $_POST['publicity'] == 'public' ? 1 : 0;
            $is_max_ans_infinite = $_POST['is_infinite_answers'] == 'true' ? 1 : 0;
            $is_anonymous = $_POST['is_anonymous'] == 'true' ? 1 : 0;
            $hash = $config->generate_hash();
            $anonymous_nickname = $_POST['anonymous-nickname'];

            $max_answers = $_POST['max_answers'];

            if ($_SESSION['new_consultation_token'] == $token || 1) {
                // Insert
                $prepared_insert = $config->link->prepare("INSERT INTO `consultations`(`id`, `hash`, `date`, `title`, `description`, `asker_hash`, `is_public`, `is_infinite_answer`, `max_answers`, `is_locked`, `is_anonymous`, `anonymous_nickname`) VALUES (NULL, ?, NOW(), ?, ?, ?, ?, ?, ?, 0, ?, ?)");
                $prepared_insert->execute([$hash, $title, $description, $_SESSION['user_hash'], $is_public, $is_max_ans_infinite, $max_answers, $is_anonymous, $anonymous_nickname]);
                $new_consultation_id = $config->link->lastInsertId();
                $new_consultation_details = $config->link->query("SELECT * FROM `consultations` WHERE `id` = {$new_consultation_id}")->fetch();

                if (isset($_FILES['add_image'])) {
                    $file = $_FILES['add_image'];
                    // Insert photo
                    $new_photo_hash = $config->insert_photo($file, 'media/consultations-photos');
                    $new_photo_details = $config->link->query("SELECT `hash`, `path` FROM `photos` WHERE `hash` = '{$new_photo_hash}'")->fetch();
                    // Update consultation image
                    $config->link->query("UPDATE `consultations` SET `image_hash` = '{$new_photo_hash}' WHERE `id` = {$new_consultation_id}");
                    $result['new_hash'] = $hash;
                }

                if (isset($_POST['tags'])) {
                    foreach ($_POST['tags'] as $tag) {
                        // Insert tags
                        // Check if tag is already in db
                        $get_tag_stmt = $config->link->query("SELECT * FROM `tags` WHERE `title` = '{$tag}'");

                        if ($get_tag_stmt->rowCount() == 0) {
                            $insert_tag_prep_stmt = $config->link->prepare("INSERT INTO `tags`(`title`, `hash`) VALUES (?, ?)");
                            $insert_tag_prep_stmt->execute([$tag, $config->generate_hash()]);
                        }
                        
                        $tag_details = $config->link->query("SELECT * FROM `tags` WHERE `title` = '{$tag}'")->fetch();
                        $insert_cons_tag = $config->link->prepare("INSERT INTO `consultations_tags`(`consultation_hash`, `tag_hash`) VALUES (?, ?)");
                        $insert_cons_tag->execute([$hash, $tag_details['hash']]);
                    }
                }
            }
        }
    }

    echo json_encode($result);
?>