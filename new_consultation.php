<?php
    require_once('config.php');

    if (!$config->user->loggedin()) {
        header("location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en" dir="RTL">
    <head>
        <meta charset="UTF-8">
        <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
        <title><?php echo $config->website_name; ?> - הוסף התייעצות</title>
        <link rel="stylesheet" href="stylesheets/general/main.css">
        <link rel="stylesheet" href="stylesheets/consultations/new_consultation.css">
        <link href="https://fonts.googleapis.com/css?family=Arimo" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://use.fontawesome.com/6b4470aa21.js"></script>
    </head>
    <body>
        <?php include 'includes/nav.php'; ?>
        <div class="site-wrap">
            <?php include 'modules/general/right-sidebar.php'; ?>
            <div class="grid__col grid__col--7-of-14">
                <div class="">
                    <form id="new-consultation-steps">
                        <div class="step open" data-stepnum="1">
                            <h4>בחר מצב התייעצות<div class="hint"><div class="question-mark"><i class="fa fa-question-circle" aria-hidden="true"></i></div><div class="text">בחר האם משתמשים יוכלו להגיב ישירות על ההתייעצות או שידרשו לענות בהודעה פרטית.</div></div></h4>
                            <div id="select-publicity-step">
                                <input type="radio" id="publicity-private" name="publicity" value="private">
                                <input type="radio" id="publicity-public" name="publicity" value="public">
                                <div id="publicity-triggers">
                                    <label for="publicity-private" class="publicity-trigger"><div class="icon"><i class="fa fa-user-secret" aria-hidden="true"></i></div><div class="text">פרטי</div></label>
                                    <label for="publicity-public" class="publicity-trigger"><div class="icon"><i class="fa fa-users" aria-hidden="true"></i></div><div class="text">ציבורי</div></label>
                                </div>
                            </div>
                        </div>

                        <div class="step" data-stepnum="2">
                            <h4>הוסף פרטים<div class="hint"><div class="question-mark"><i class="fa fa-question-circle" aria-hidden="true"></i></div><div class="text">אלו ם אשר יופיעו כאשר משתמש יכנס להתבונן בהתייעצות שלך. שאלה מפורטת תהיה קלה יותר למענה.</div></div></h4>
                            <div id="new-consultation-details">
                                <input type="text" name="title" id="new-consultation-title" placeholder="כותרת" data-text="כותרת השאלה">
                                <div id="new-consultation-desc-wrap">
                                    <div id="new-consultation-trigger-anonymously">
                                        <input type="checkbox" name="is_anonymous">
                                        <input type="text" name="anonymous-nickname" value="אנונימי" title="כינוי אנונימי">
                                        <div class="icon" title="פרסם באופן אנונימי"><i class="fa fa-user-secret" aria-hidden="true"></i></div>
                                    </div>

                                    <div id="new-consultation-desc" class="hoverscroll" name="description" placeholder="תיאור ההתייעצות..." contenteditable="plaintext-only"></div>
                                    <div id="new-consultation-additionals">
                                        <div class="additional-trigger">
                                            <div id="new-consultation-trigger-add-image" class="icon"><i class="fa fa-camera" aria-hidden="true"></i></div>
                                        </div>

                                        <div class="additional-trigger">
                                            <div class="icon"><i class="fa fa-link" aria-hidden="true"></i></div>
                                        </div>

                                        <div class="additional-trigger">
                                            <div class="icon"><i class="fa fa-smile-o" aria-hidden="true"></i></div>
                                        </div>

                                        <div class="additional-trigger">
                                            <div id="new-consultation-trigger-add-tag" class="icon"><i class="fa fa-hashtag" aria-hidden="true"></i></div>
                                        </div>
                                        <input type="file" name="add_image" accept="image/*" id="new-consultation-additional-photo">
                                    </div>

                                    <div id="new-consultation-letters-left-count">2500</div>
                                </div>

                                <div id="new-post-tags-input">
                                    <div id="added-tags"></div>
                                    <input type="text" id="current-tag-input">
                                </div>

                                <div id="new-consultation-disclaimer"></div>
                                <input type="hidden" name="token" value="<?php echo $_SESSION['new_consultation_token'] = $config->generate_hash();; ?>">
                                <div id="num-ans-wrap">
                                    <input type="checkbox" id="is_infinite_answers" name="is_infinite_answers" checked>
                                    <label for="is_infinite_answers">מספר תשובות לא מוגבל</label>
                                    <input type="number" id="select-num-answers" min="1" name="max_answers" value="1" placeholder="מספר תשובות">
                                </div>
                                <input type="submit" class="cutebtn" value="שלח">
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php include 'modules/general/left-sidebar.php'; ?>
        </div>
        <script src="scripts/main.js"></script>
        <script src="scripts/consultations/new_consultation.js"></script>
    </body>
</html>