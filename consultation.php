<?php
    require_once('config.php');

    if (isset($_GET['cp'])) {
        $consultation_hash = $_GET['cp'];
        $consultation_details = $config->link->query("SELECT * FROM `consultations` WHERE `hash` = '{$consultation_hash}'")->fetch();
        $con_title = $consultation_details['title'];
        $upvotes = $consultation_details['upvotes'];
        $downvotes = $consultation_details['downvotes'];

        $allowed_sorts = ['date', 'rank'];
        if (isset($_GET['sort']) && in_array($_GET['sort'], $allowed_sorts)) {
            $sort = $_GET['sort'];
        } else {
            $sort = 'date';
        }

        $allowed_orders = ['asc', 'desc'];
        if (isset($_GET['order']) && in_array($_GET['order'], $allowed_orders)) {
            $order = $_GET['order'];
        } else {
            $order = 'ASC';
        }

        $consultation_answers = $config->link->query("SELECT * FROM `public_consultation_answers` WHERE `consultation_hash` = '{$consultation_details['hash']}' ORDER BY `{$sort}` " . $order);
        if ($config->user->loggedin()) {
            $has_user_upvoted = $config->link->query("SELECT * FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `voter_hash` = '{$_SESSION['user_hash']}' AND `vote` = 'up'")->rowCount() > 0;
            $has_user_downvoted = $config->link->query("SELECT * FROM `consultations_votes` WHERE `consultation_hash` = '{$consultation_hash}' AND `voter_hash` = '{$_SESSION['user_hash']}' AND `vote` = 'down'")->rowCount() > 0;
        }
    }
?>

<!DOCTYPE html>
<html lang="en" dir="RTL">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?php if (mb_strlen($con_title) > 40) {echo substr($con_title, 0, 40) . '...';} else {echo $con_title;} ?> - <?php echo $config->website_name; ?></title>
        <link rel="stylesheet" href="stylesheets/general/main.css">
        <link rel="stylesheet" href="stylesheets/consultations/display.css">
        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/6b4470aa21.js"></script>
    </head>
    <body>
        <?php include 'includes/nav.php'; ?>
        <div class="site-wrap">
            <?php include 'modules/general/right-sidebar.php'; ?>
            <div class="grid__col grid__col--7-of-14">
                <div class="card" id="consultation-details">
                    <div id="consultation-votes" class="<?php if ($config->user->loggedin()) { echo 'logged'; } ?>">
                        <div id="upvote-consultation" class="vote-item <?php if ($config->user->loggedin() && $has_user_upvoted) {echo 'clicked';} ?>" data-vote="up">
                            <div class="icon"><i class="fa fa-arrow-circle-up" aria-hidden="true"></i></div>
                            <div class="num"><?php echo $upvotes; ?></div>
                        </div>
                        <div id="downvote-consultation" class="vote-item <?php if ($config->user->loggedin() && $has_user_downvoted) {echo 'clicked';} ?>" data-vote="down">
                            <div class="icon">
                                <i class="fa fa-arrow-circle-down" aria-hidden="true"></i>
                            </div>
                        <div class="num"><?php echo $downvotes; ?></div>
                        </div>
                    </div>

                    <div>
                        <h3 id="cons-title"><?php echo $consultation_details['title']; ?></h3>
                        <div id="cons-desc"><?php echo nl2br($consultation_details['description']); ?></div>
                         <?php
                            if ($consultation_details['image_hash'] != NULL) {
                                $consultation_picture = $config->get_photo_path_by_hash($consultation_details['image_hash']);
                            } else {
                                $consultation_picture = 'media/grey-anonymous.png';
                            }
                        ?>
                        <?php if ($consultation_details['image_hash'] != NULL) : ?>
                            <div id="toggle-cons-img-show"><i class="fa fa-plus" aria-hidden="true"></i> הצג תמונה מצורפת</div>
                            <div id="cons-picture"><div class="toggle-fullscreen-photo"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div><img src="<?php echo $consultation_picture; ?>"></div>
                        <?php endif; ?>
                    </div>
                    <div id="cons-sub-details">
                        <div id="cons-user-details" class="user-title-details">
                            <?php if ($consultation_details['is_anonymous'] == 0) : ?>
                                <a href="profile.php?uh=<?php echo $consultation_details['asker_hash']; ?>"><?php echo $config->user->get_user_details_by_hash($consultation_details['asker_hash'], 'nickname'); ?></a>
                            <?php else : ?>
                                <?php echo $consultation_details['anonymous_nickname']; ?>
                            <?php endif; ?>
                        </div>
                        <div id="cons-posted-date" title="<?php echo $consultation_details['date']; ?>">לפני <?php echo $config->time_parse($consultation_details['date']); ?></div>
                        <div id="consultation-tags">
                            <?php $consultation_tags = $config->link->query("SELECT `id`, `consultation_hash`, `tag_hash` FROM `consultations_tags` WHERE `consultation_hash` = '{$consultation_hash}'"); ?>
                            <?php if ($consultation_tags->rowCount() > 0) : ?>
                                <strong>תגיות: </strong>
                                <?php while ($tag = $consultation_tags->fetch()) : ?>
                                    <a class="hover-underfline" href="tag.php?thash=<?php echo $tag['tag_hash']; ?>">#<?php echo $config->get_tag_details_by_hash($tag['tag_hash'])['title']; ?></a>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($consultation_details['is_public'] == 1) : ?>
                    <div id="cons-answer-options">
                        <div id="cons-answers-stats">
                            <div id="cons-num-ans"><?php echo $consultation_answers->rowCount(); ?> תשובות</div>
                        </div>
                        <div id="cons-toggle-add-ans">הוסף תשובה <i class="fa fa-plus-circle" aria-hidden="true"></i></div>
                        
                        <?php if ($consultation_answers->rowCount() > 0) : ?>
                            <div id="cons-filter-ans">מיין לפי 
                                <select id="cons-ans-sort">
                                    <option value="time">זמן</option>
                                    <option value="rank" <?php if (isset($_GET['sort']) && $_GET['sort'] == "rank") { echo 'selected="selected"'; } ?>>דירוג</option>
                                </select>
                                <?php
                                    if (isset($_GET['order'])) {
                                        $current_order = $_GET['order'];
                                    } else {
                                        $current_order = 'asc';
                                    }
                                ?>
                                <div id="cons-sort-asc-desc-toggle" data-order="<?php echo $current_order; ?>"><i class="fa fa-arrow-<?php echo $current_order == "asc" ? 'up' : 'down'; ?>" aria-hidden="true"></i></div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div id="cons-answer-list">
                        <?php while ($answer = $consultation_answers->fetch()) : ?>
                            <?php $ans_poster_details = $config->user->get_user_details_by_hash($answer['poster_hash'], 'all'); ?>
                            <div class="cons-answer <?php if ($answer['is_anonymous'] == 1) { echo 'anonymous'; } ?> <?php if ($config->user->loggedin() && $answer['poster_hash'] != $_SESSION['user_hash']) { echo 'self'; } ?>">
                                <?php if ($config->user->loggedin()) : ?>
                                    <div class="cons-ans-opts">
                                        <div class="toggle-opts">
                                            <div class="icon"><i class="fa fa-angle-down" aria-hidden="true"></i></div>
                                        </div>

                                        <ul class="options-list">
                                            <?php if ($answer['poster_hash'] == $_SESSION['user_hash']) : ?>
                                                <div class="options-section">
                                                    <li class="option delete-opt">
                                                        <div class="icon"><i class="fa fa-trash" aria-hidden="true"></i></div>
                                                        <div class="text">מחק</div>
                                                    </li>

                                                    <li class="option edit-opt">
                                                        <div class="icon"><i class="fa fa-pencil" aria-hidden="true"></i></div>
                                                        <div class="text">ערוך</div>
                                                    </li>
                                                </div>
                                            <?php else : ?>
                                                <div class="options-section">
                                                    <li class="option report-opt">
                                                        <div class="icon"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></div>
                                                        <div class="text">דווח לנו</div>
                                                    </li>
                                                </div>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <div class="content-wrap">
                                    <?php if ($answer['poster_hash'] == $consultation_details['asker_hash'] && $answer['is_anonymous'] == $consultation_details['is_anonymous']) : ?>
                                        <h6>שואל השאלה</h6>
                                    <?php endif; ?>

                                    <?php if (count($answer['answer']) > 0 && $answer['answer'] != NULL) : ?>
                                        <div class="answer-content"><?php echo nl2br($answer['answer']) . '<br>'; ?></div>
                                    <?php endif; ?>
                                    <?php if ($answer['image_hash'] != NULL) : ?>
                                        <div class="toggle-ans-img-show"><i class="fa fa-plus" aria-hidden="true"></i> הצג תמונה מצורפת</div>
                                        <div class="ans-add-img-wrap"><img src="<?php echo $config->get_photo_path_by_hash($answer['image_hash']); ?>" class="cons-ans-additional-image"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="cons-ans-poster-info">
                                    <?php if ($answer['is_anonymous'] == 0) : ?>
                                        <div class="pp">
                                            <div class="cons-ans-poster-pp" style="background-image: url(<?php echo $config->user->get_user_pp_by_hash($ans_poster_details['user_hash']); ?>)"></div>
                                        </div>
                                    <?php endif; ?>

                                    <div class="name-title">
                                        <?php if ($answer['is_anonymous'] == 0) : ?>
                                            <a href="profile.php?uh=<?php echo $ans_poster_details['user_hash']; ?>"><?php echo $ans_poster_details['nickname']; ?></a>
                                        <?php else : ?>
                                            <?php echo $answer['anonymous_nickname']; ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php $dt = (new DateTime($answer['date']))->format('Y-m-d'); ?>
                                    <div class="time-answered" title="<?php echo date("d/m/y H:m", strtotime($dt)); ?>">
                                        <?php echo $config->time_parse($answer['date']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <?php if ($config->user->loggedin() && $consultation_details['is_public']) : ?>
                        <form id="cons-add-ans" class="card">
                            <input type="hidden" name="cons-hash" id="cons-hash-val" value="<?php echo $consultation_hash; ?>">
                            <div id="cons-new-comment-wrap">
                                <div id="cons-new-comment-input" name="ans-content" contenteditable="plaintext-only" placeholder="הכנס תשובה"></div>
                                <div id="cons-new-comment-additionals">
                                    <input type="file" name="ans-add-img" id="cons-new-comment-add-image-input">
                                    <div id="cons-new-comment-add-image">
                                        <div class="icon"><i class="fa fa-camera" aria-hidden="true"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div id="cons-new-ans-toggle-anonymously">
                                <input type="checkbox" name="is_anonymous">
                                <div class="icon" title="פרסם באופן אנונימי"><i class="fa fa-user-secret" aria-hidden="true"></i></div>
                                <input type="text" name="anonymous-nickname" value="<?php if ($consultation_details['is_anonymous'] && $_SESSION['user_hash'] == $consultation_details['asker_hash']) {echo $consultation_details['anonymous_nickname'];} else {echo 'אנונימי';} ?>" title="כינוי אנונימי">
                            </div>
                            <input type="submit" value="שלח" class="cutebtn">
                            <div class="clearfix"></div>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <?php
                $show_consultations_like = $consultation_hash;
            ?>
            <?php include 'modules/general/left-sidebar.php'; ?>
        </div>

        <script src="scripts/main.js"></script>
        <script src="scripts/consultations/display.js"></script>
        <script src="scripts/jquery.query-object.js"></script>
    </body>
</html>