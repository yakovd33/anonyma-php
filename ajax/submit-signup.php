<?php
    require_once('../config.php');

    if (!$config->user->loggedin()) {
        $results = array('code' => 'success', 'errors' => array());
        $allowed_genders = array('male', 'female', 'other');

        if (isset($_GET['new-user'])) {
            if (isset($_POST['token'])) {
                if (isset($_POST['email']) && isset($_POST['gender']) && isset($_POST['age']) && isset($_POST['password']) && isset($_POST['re_pass'])) {
                    if (!empty($_POST['email']) && !empty($_POST['gender']) && !empty($_POST['age']) && !empty($_POST['password']) && !empty($_POST['re_pass'])) {
                        $email = $_POST['email'];
                        $gender = $_POST['gender'];
                        $age = $_POST['age'];
                        $password = $_POST['password'];
                        $re_pass = $_POST['re_pass'];
                        $password_hashed = md5($password);

                        if ($age >= $config->allowed_age) {
                            if ($password == $re_pass) {
                                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    if (in_array($gender, $allowed_genders)) {
                                        // Check whether email exists in db
                                        if ($config->link->query("SELECT * FROM `users` WHERE `email` = '{$email}'")->rowCount() == 0) {
                                            $insert_user_stmt = $config->link->prepare("INSERT INTO `users`(`id`, `user_hash`, `email`, `password_hashed`, `gender`, `join_date`, `role`) VALUES (NULL, ?, ?, ?, ?, NOW(), ?)");
                                            $insert_user_stmt->execute([$config->generate_hash(), $email, $password_hashed, $gender, 'user']);
                                            // Log in
                                            $new_user_id = $config->link->lastInsertId();
                                            $new_user_hash = $config->link->query("SELECT `user_hash` FROM `users` WHERE `id` = {$new_user_id}")->fetch()['user_hash']; 
                                            $_SESSION['user_hash'] = $new_user_hash;
                                        } else {
                                            $results['code'] = 'error';
                                            array_push($results['errors'], "אימייל כבר קיים");
                                        }
                                    } else {
                                        $results['code'] = 'error';
                                        array_push($results['errors'], "מין לא תקין");
                                    }
                                } else {
                                    array_push($results['errors'], "כתובת אימייל אינה תקינה");
                                }
                            } else {
                                $results['code'] = 'error';
                                array_push($results['errors'], "סיסמאות לא תואמות");
                            }
                        } else {
                            $results['code'] = 'error';
                            array_push($results['errors'], "גיל צריך להיות בין " . $config->allowed_age . " ל120");
                        }
                    } else {
                        array_push($results['errors'], 'שדה אחד או יותר ריקים');
                        $results['errors']['empty'] = array();
                        $fields_to_check_empty = ['email', 'age', 'password', 're_pass'];

                        foreach ($fields_to_check_empty as $field) {
                            if (empty($_POST[$field])) {
                                array_push($results['errors']['empty'], $field);
                            }
                        }
                    }
                }
            }
        }
    } else {
        $results['code'] = 'error';
        array_push($results['errors'], "You are already connected");
    }

    $results['errors'] = json_encode($results['errors']);
    echo json_encode($results);
?>