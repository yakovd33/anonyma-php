<div class="grid__col grid__col--3-of-14">
        <div id="profile-user-basic-details" class="card">
            <div class="textual-details">
                <div class="icon"><i class="fa fa-user" aria-hidden="true"></i></div>
                <div class="fullname"><?php echo $user_details['fullname']; ?></div>
            </div>
            <div class="pp"><img src="<?php echo $config->user->get_user_pp_by_hash($user_hash); ?>"></div>
            <div id="user-biography">
                <?php $get_bio_stmt = $config->link->query("SELECT * FROM `users-biography` WHERE `user_hash` = '{$user_hash}'"); ?>
                <?php if ($get_bio_stmt->rowCount() > 0) : ?>
                    <?php echo $get_bio_stmt->fetch()['content']; ?>
                <?php else : ?>
                    
                <?php endif; ?>
            </div>
            <strong><label>כינוי: </label></strong>
            <div class="nickname"><?php echo $user_details['nickname']; ?></div>
        </div>
</div>