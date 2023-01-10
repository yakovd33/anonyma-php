<link rel="stylesheet" href="stylesheets/modules/finish-registration.css">

<?php if ($config->user->loggedin()) : ?>
    <?php if (!$config->user->is_basic_registration_complete()) : ?>
        <?php
            $user_details = $config->user->get_user_details_by_hash($_SESSION['user_hash'], 'all');
            $registration_percentage = 0;
            $missing_info_arr = [(is_null($user_details['fullname']) || empty($user_details['fullname'])),
            (is_null($user_details['nickname']) || empty($user_details['nickname'])),
            $user_details['email_validation'] == 0,];
            for ($i = 0; $i < count($missing_info_arr); $i++) {
                if ($missing_info_arr[$i] == false) {
                    $registration_percentage += 100 / count($missing_info_arr);
                }
            }

            $registration_percentage = floor($registration_percentage);
        ?>

        <link rel="stylesheet" href="stylesheets/circle.css">
        <div class="card" id="finish-registration-card">
            <h5>השלם הרשמה</h5>
            <div class="c100 p<?php echo $registration_percentage; ?>">
                <span><?php echo $registration_percentage; ?>%</span>
                <div class="slice">
                    <div class="bar"></div>
                    <div class="fill"></div>
                </div>
            </div>

            <div id="registration-finish-trigger-info-changes">
                <?php if ($user_details['email'] == 0) : ?>
                    <div class="trigger-info-update" id="trigger-fullname-change">
                        <div class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                        <div class="text">אמת כתובת אימייל</div>
                    </div>
                <?php endif; ?>

                <?php if (is_null($user_details['fullname']) || empty($user_details['fullname'])) : ?>
                    <div class="trigger-info-update" id="trigger-fullname-change">
                        <div class="icon"><i class="fa fa-id-card-o" aria-hidden="true"></i></div>
                        <div class="text">עדכן את שמך המלא</div>
                    </div>
                <?php endif; ?>

                <?php if (is_null($user_details['nickname']) || empty($user_details['nickname'])) : ?>
                    <div class="trigger-info-update" id="trigger-nickname-change">
                        <div class="icon"><i class="fa fa-user-circle-o" aria-hidden="true"></i></div>
                        <div class="text">עדכן את כינויך</div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>
<?php endif; ?>