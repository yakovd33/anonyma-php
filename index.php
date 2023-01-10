<?php
    require_once('config.php');
?>

<!DOCTYPE html>
<html lang="en" dir="RTL">
    <head>
        <meta charset="UTF-8">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <title><?php echo $config->website_name; ?> - דף בית</title>
        <link rel="stylesheet" href="stylesheets/general/main.css">
        <link rel="stylesheet" href="stylesheets/index/index.css">
        <link rel="stylesheet" href="stylesheets/slick.css">
        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/6b4470aa21.js"></script>
    </head>
    <body>
        <?php include 'includes/nav.php'; ?>
        <div class="site-wrap">
            <div id="index-cards">
                <div class="row">
                    <?php include 'modules/general/right-sidebar.php'; ?>
                    <div class="grid__col grid__col--7-of-14">
                        <div id="hot-new-consultations">
                            <h3>התייעצויות חמות</h3>
                            <div class="consultations regular slider" dir="LTR">
                                <?php $new_hot_consultations = $config->link->query("SELECT * FROM `consultations` ORDER BY `upvotes` ASC, `downvotes` DESC, `date` LIMIT 16"); ?>
                                <?php while ($hot_consultation = $new_hot_consultations->fetch()) : ?>
                                    <?php
                                        if ($hot_consultation['image_hash'] != NULL) {
                                            $consultation_picture = $config->get_photo_path_by_hash($hot_consultation['image_hash']);
                                        } else {
                                            $consultation_picture = '';
                                        }
                                    ?>
                                    <div class="hot-consultation" dir="RTL">
                                        <a href="consultation.php?cp=<?php echo $hot_consultation['hash']; ?>">
                                            <div class="hot-cons-picture" style="background-image: url(<?php echo $consultation_picture; ?>);"></div>
                                        </a>
                                        <div class="hot-cons-details">
                                            <div class="hot-cons-title"><?php echo $hot_consultation['title']; ?></div>
                                            <!--<div class="hot-cons-desc-show-more"><i class="fa fa-plus" aria-hidden="true"></i></div>-->
                                            <div class="hot-cons-desc"><?php mb_internal_encoding("UTF-8"); echo nl2br(mb_substr($hot_consultation['description'], 0, 14)); if (strlen($hot_consultation['description']) > 14) {echo '...';} else if (strlen($hot_consultation['description']) == 0) {echo 'אין תיאור...';} ?></div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <div id="hot-tags-section">
                            <h3>תגיות חמות</h3>
                            <?php $hot_tags = $config->link->query("SELECT `tag_hash` AS `tag_hash`, COUNT(*) AS `num` FROM `consultations_tags` GROUP BY `tag_hash` ORDER BY `num` DESC LIMIT 7"); ?>
                            <div id="hot-tags-links-wrap">
                                <?php while ($tag = $hot_tags->fetch()) : ?>
                                    <a href="tag.php?thash=<?php echo $tag['tag_hash']; ?>" class="hot-tag"><?php echo $config->get_tag_details_by_hash($tag['tag_hash'])['title']; ?></a>
                                <?php endwhile; ?>
                                <a class="hot-tag" href="tag.php?name=">נדל"ן</a>
                            </div>
                        </div>

                        <div id="website-stats-section">
                            <?php
                                $registered_users_num = $config->link->query("SELECT * FROM `users`")->rowCount();
                                $consultations_num = $config->link->query("SELECT * FROM `consultations`")->rowCount();
                                $consultations_answers_num = $config->link->query("SELECT * FROM `public_consultation_answers`")->rowCount();
                            ?>
                            <div class="stat">
                                <div class="text"><i class="fa fa-address-card" aria-hidden="true"></i> משתמשים רשומים</div>
                                <div class="number"><?php echo $registered_users_num; ?></div>
                            </div>

                            <div class="stat">
                                <div class="text"><i class="fa fa-question-circle" aria-hidden="true"></i> התייעצויות</div>
                                <div class="number"><?php echo $consultations_num; ?></div>
                            </div>

                            <div class="stat">
                                <div class="text"><i class="fa fa-commenting" aria-hidden="true"></i> תשובות להתייעצויות</div>
                                <div class="number"><?php echo $consultations_answers_num; ?></div>
                            </div>
                        </div>

                        
                        <div class="ad">
                            <img src="media/fff.png" style="margin-top: 10px; max-width: 100%">
                        </div>
                    </div>

                    <?php include 'modules/general/left-sidebar.php'; ?>
                </div>
            </div>
        </div>

        <script src="scripts/jquery.cookie.js"></script>
        <script src="scripts/slick.js"></script>
        <script src="scripts/main.js"></script>
        <script src="scripts/index.js"></script>
    </body>
</html>