<?php
    require_once('../config.php');

    if (isset($_POST['check_hash'])) {
        $hash = $_POST['check_hash'];
        $get_chat = $config->link->query("SELECT * FROM `guests_chats` WHERE `hash` = '{$hash}'");
        if ($get_chat->fetch()['user_two_hash'] != '') {
            echo $result = json_encode(array("connected", $hash));
        }
    }
?>