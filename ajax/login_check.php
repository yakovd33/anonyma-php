<?php
    require_once('../config.php');

    $result = array('success', 'errors' => array());

    if (isset($_POST['email']) && isset($_POST['password']) && isset($_POST['login_token'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $token = $_POST['login_token'];
        $password_hashed = md5($password);

        $get_user_by_email_stmt = $config->link->query("SELECT * FROM `users` WHERE `email` = '{$email}'");

        if ($get_user_by_email_stmt->rowCount() == 1) {
            $user_details = $get_user_by_email_stmt->fetch();
            if ($password_hashed == $user_details['password_hashed']) {
                $_SESSION['user_hash'] = $user_details['user_hash'];
            }
        }
    }

    $result['errors'] = json_encode($result['errors']);
    echo json_encode($result);
?>