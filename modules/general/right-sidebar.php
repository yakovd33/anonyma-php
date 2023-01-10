<div class="grid__col grid__col--3-of-14">
    <?php if (!$config->user->loggedin()) : ?>
        <div class="card row">
            <?php include 'guest_random_chat_form.php'; ?>
        </div>
    <?php else : ?>
        <div class="card transparent">
            <?php include 'action-recommendations.php'; ?>
        </div>

        <div class="card grid__row">
            <?php include 'user_random_chat_form.php'; ?>
        </div>
    <?php endif; ?>

        <div id="user-ranking" class="card transparent">
            <h5>עונים מצטיינים</h5>
            <div id="user-ranking-tab-triggers">
                <div class="tab-trigger selected" data-tabtrigger="week">שבוע</div>
                <div class="tab-trigger" data-tabtrigger="month">חודש</div>
                <div class="tab-trigger" data-tabtrigger="alltime">כל הזמנים</div>
            </div>

            <?php
                $weekly_ranked_user_hashes = $config->link->query("
                    SELECT max(id) as id, `poster_hash`
                    FROM `public_consultation_answers`
                    WHERE `date` > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                    GROUP by `poster_hash`
                    ORDER by id DESC LIMIT 5");

                $monthly_ranked_user_hashes = $config->link->query("
                    SELECT max(id) as id, `poster_hash`
                    FROM `public_consultation_answers`
                    WHERE `date` > DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP by `poster_hash`
                    ORDER by id DESC LIMIT 5");
                
                $alltime_ranked_user_hashes = $config->link->query("
                    SELECT max(id) as id, `poster_hash`
                    FROM `public_consultation_answers`
                    GROUP by `poster_hash`
                    ORDER by id DESC LIMIT 5");

                $current_user_counter = 1;
            ?>
            
            <ul class="tab open" data-tabname="week" id="weekly-ranked-users">
                <?php while ($user = $weekly_ranked_user_hashes->fetch()) : ?>
                    <?php $current_user_details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$user['poster_hash']}'")->fetch(); ?>
                    <a href="profile.php?uh=<?php echo $current_user_details['user_hash']; ?>">
                        <li><div class="pp" style="background-image: url(<?php echo $config->user->get_user_pp_by_hash($current_user_details['user_hash']); ?>");></div>
                            <?php echo $current_user_details['nickname']; ?></li>
                    </a>
                <?php endwhile; ?>
            </ul>

            <ul class="tab" data-tabname="month" id="monthly-ranked-users">
                <?php while ($user = $monthly_ranked_user_hashes->fetch()) : ?>
                    <?php $current_user_details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$user['poster_hash']}'")->fetch(); ?>
                    <a href="profile.php?uh=<?php echo $current_user_details['user_hash']; ?>">
                        <li><div class="pp" style="background-image: url(<?php echo $config->user->get_user_pp_by_hash($current_user_details['user_hash']); ?>");></div>
                            <?php echo $current_user_details['nickname']; ?></li>
                    </a>
                <?php endwhile; ?>
            </ul>

            <ul class="tab" data-tabname="alltime" id="monthly-ranked-users">
                <?php while ($user = $alltime_ranked_user_hashes->fetch()) : ?>
                    <?php $current_user_details = $config->link->query("SELECT * FROM `users` WHERE `user_hash` = '{$user['poster_hash']}'")->fetch(); ?>
                    <a href="profile.php?uh=<?php echo $current_user_details['user_hash']; ?>">
                        <li><div class="pp" style="background-image: url(<?php echo $config->user->get_user_pp_by_hash($current_user_details['user_hash']); ?>");></div>
                            <?php echo $current_user_details['nickname']; ?></li>
                    </a>
                <?php endwhile; ?>
            </ul>
        </div>
</div>