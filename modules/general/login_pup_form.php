<?php
    require_once('../../config.php');
    if (!$config->user->loggedin()) :
?>
        <link rel="stylesheet" href="stylesheets/loaded_modules/login-form.css">
        <form id="login-form">
            <div id="sign-in-form-wrap">
                <div id="login-form-website-logo"></div>
                <div id="login-form-details">
                    <input type="email" name="email" placeholder="אימייל">
                    <input type="password" name="password" placeholder="סיסמא">
                    <input type="hidden" name="login_token" id="login-token" value="<?php echo $_SESSION['login_token'] = md5(time()); ?>">
                    <a href="forgot_password.php" id="forgot-password-link">שכחתי את הסיסמא.</a>
                    <input type="submit" value="התחבר">
                </div>
            </div>
        </form>
        <script src="scripts/modules/sign_in_pup.js"></script>
<?php
    endif;
?>