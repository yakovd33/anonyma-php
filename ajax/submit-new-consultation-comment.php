<?php
    require_once('../config.php');

    $result = array('code' => 'success', 'errors' => array(), 'ans_content' => '', 'is_anonymous' => false, 'nickname' => '', 'profile_hash' => '', 'includes_image' => false, 'image_path' => '', 'user_pp' => '');

    if ($config->user->loggedin()) {
        if (isset($_POST['ans-content']) && isset($_POST['cons-hash'])) {
            if ((empty($_POST['ans-content']) && isset($_FILES['ans-add-img'])) || !empty($_POST['ans-content'])) {
                $hash = $config->generate_hash();
                $cons_hash = $_POST['cons-hash'];
                $content = strip_tags($_POST['ans-content'], '<br>');
                $is_anonymous = $_POST['is_anonymous'] == "true" ? '1' : '0';
                $anonymous_nickname = $_POST['anonymous-nickname'];

                $result['ans_content'] = $content;
                if ($is_anonymous == '1') {
                    $result['is_anonymous'] = true;
                    $result['nickname'] = $anonymous_nickname;
                    $result['user_pp'] = 'media/grey-anonymous.png';
                } else {
                    $result['profile_hash'] = $_SESSION['user_hash'];
                    $result['user_pp'] = $config->user->get_user_pp_by_hash($_SESSION['user_hash']);
                    $result['nickname'] = $config->user->get_user_details_by_hash($_SESSION['user_hash'], 'nickname');
                }

                // Insert answer
                $insert_ans_prep = $config->link->prepare("INSERT INTO `public_consultation_answers`(`id`, `hash`, `consultation_hash`, `poster_hash`, `answer`, `is_public`, `is_anonymous`, `anonymous_nickname`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $insert_ans_prep->execute([NULL, $hash, $cons_hash, $_SESSION['user_hash'], $content, 1, $is_anonymous, $anonymous_nickname]);
                $new_ans_id = $config->link->lastInsertId();

                // Insert image
                if (isset($_FILES['ans-add-img'])) {
                    $file = $_FILES['ans-add-img'];
                    if ($file['error'] == 0) {
                        $new_photo_hash = $config->insert_photo($file, 'media/consultations-ans-photos');
                        $new_photo_details = $config->link->query("SELECT `hash`, `path` FROM `photos` WHERE `hash` = '{$new_photo_hash}'")->fetch();
                        // Update consultation image
                        $config->link->query("UPDATE `public_consultation_answers` SET `image_hash` = '{$new_photo_hash}' WHERE `id` = {$new_ans_id}");
                        $result['includes_image'] = true;
                        $result['image_path'] = $new_photo_details['path'];
                    }
                }
            }
        }
    }

    echo json_encode($result);
?>