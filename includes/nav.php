<nav>
    <a href="index.php"><div id="spinning-anonymous"></div></a>
    <!-- <a href="index.php"><div id="nav-logo"></div></a> -->
    <div id="nav-links">
        <a href="index.php" class="nav-link">דף ראשי</a>
        <?php if ($config->user->loggedin()) : ?>
            <a href="profile.php?uh=<?php echo $_SESSION['user_hash']; ?>" id="nav-self-profile-link" class="nav-link">
                <div class="pp" style="background-image: url(<?php echo $config->user->get_user_pp_by_hash($_SESSION['user_hash']); ?>)"></div>
                <span class="fullname"><?php echo $config->user->get_user_details_by_hash($_SESSION['user_hash'], 'fullname'); ?></span>
            </a>
        <?php endif; ?>
        <form id="search-bar" action="search.php" method="GET">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input name="query" type="text" placeholder="חיפוש...">
        </form>
        <div id="member-options">
            <?php if (!$config->user->loggedin()) : ?>
                <a href="join.php" class="member-option" id="join-btn">הירשם</a>
                <a href="login.php" class="member-option" id="login-btn">התחבר</a>
            <?php else : ?>
                <a href="#" class="member-option" id="logout-btn">התנתק</a>
            <?php endif; ?>
        </div>
    </div>

    <div id="login-form-wrap"></div>
</nav>