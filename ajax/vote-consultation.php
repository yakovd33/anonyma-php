<?php
    require_once('../config.php');

    $result = array('code' => 'success', 'new_ups' => '', 'new_downs' => '');

    if ($config->user->loggedin()) {
        $allowed_votes = ['up', 'down'];

        if (isset($_POST['consultation_hash']) && isset($_POST['vote'])) {
            $consultation_hash = $_POST['consultation_hash'];
            $vote = $_POST['vote'];

            if (in_array($vote, $allowed_votes)) {
                $hash = $config->generate_hash();

                $vote_db_column = $vote . 'votes';

                // Check if user has voted this before
                $has_voted_same_vote = $config->link->query("SELECT * FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `voter_hash` = '{$_SESSION['user_hash']}' AND `vote` = '{$vote}'")->rowCount() > 0;
                if ($has_voted_same_vote) {
                    // Cancel current vote
                    $config->link->query("UPDATE `consultations` SET `" . $vote_db_column . "` = " . $vote_db_column . " - 1 WHERE `hash` = '{$consultation_hash}'"); // Update consultation table
                    $config->link->query("DELETE FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `voter_hash` = '{$_SESSION['user_hash']}' AND `vote` = '{$vote}'");
                } else {
                    // Delete other vote
                    if ($vote == "up") {
                        $vote_to_delete = "downvote";
                    } else {
                        $vote_to_delete = "upvotes";
                    }

                    $config->link->query("DELETE FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `voter_hash` = '{$_SESSION['user_hash']}' AND `vote` <> '{$vote}'");
                    // Insert vote
                    $new_vote_prepare = $config->link->prepare("INSERT INTO `consultations_votes`(`id`, `hash`, `consultation_hash`, `voter_hash`, `vote`) VALUES (NULL, ?, ?, ?, ?)");
                    $new_vote_prepare->execute([$hash, $consultation_hash, $_SESSION['user_hash'], $vote]);
                }
            }

            $num_upvotes = $config->link->query("SELECT * FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `vote` = 'up'")->rowCount();
            $num_downvotes = $config->link->query("SELECT * FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `vote` = 'down'")->rowCount();
            
            $config->link->query("UPDATE `consultations` SET `upvotes` = {$num_upvotes}, `downvotes` = {$num_downvotes} WHERE `hash` = '{$consultation_hash}'"); // Update consultation table
            $result['new_ups'] = $num_upvotes;
            $result['new_downs'] = $num_downvotes;
        }
    }

    echo json_encode($result);
?>