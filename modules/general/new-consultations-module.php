<link rel="stylesheet" href="stylesheets/consultations/new_consultations_list.css">

<?php
    $get_last_consultations_stmt = $config->link->query("SELECT * FROM `consultations` ORDER BY `date` DESC LIMIT 25");
?>

<div class="card">
    <h4>התייעצויות אחרונות</h4>
    <div id="new-consultations-list" class="hoverscroll">
        <?php while ($consultation = $get_last_consultations_stmt->fetch()) : ?>
            <?php
                if ($consultation['image_hash'] != NULL) {
                    $consultation_picture = $config->get_photo_path_by_hash($consultation['image_hash']);
                } else {
                    $consultation_picture = '';
                }
            ?>
            <a class="new-consultation-link" href="consultation.php?cp=<?php echo $consultation['hash']; ?>" title="<?php echo $consultation['description']; ?>">
                <div class="picture" style="background-image: url(<?php echo $consultation_picture; ?>)"></div>
                <?php $con_title = $consultation['title']; ?>
                <div class="title" <?php if (mb_strlen($con_title) > 20) {echo 'style="vertical-align: top;"';} ?>><?php if (mb_strlen($con_title) > 40) {echo substr($con_title, 0, 40) . '...';} else {echo $con_title;} ?></div>
            </a>
        <?php endwhile; ?>
    </div>
</div>