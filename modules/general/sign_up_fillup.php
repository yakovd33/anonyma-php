<link rel="stylesheet" href="stylesheets/general/sign_up_fillup.css">

<div id="sign-up-wrap">
    <div id="exit-signup-btn"><i class="fa fa-times" aria-hidden="true"></i></div>
    <form id="signup-steps" onsubmit="event.preventDefault()">
        <div class="step">
            <div class="step-content">
                <div id="sign-up-options">
                    <div class="sign-up-option">
                        <div class="text">הירשם עם פייסבוק</div>
                        <div class="icon"><i class="fa fa-facebook-official" aria-hidden="true"></i></div>
                    </div>
                    <div class="sign-up-option" id="sign-up-with-email-option">
                        <div class="text">הירשם עם כתובת אימייל</div>
                        <div class="icon"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="step">
            <div class="step-content">
                <input class="step-field-newline" placeholder="אימייל" id="step-one-email" type="email">
                <select id="step-one-gender">
                    <option>בחר מין...</option>
                    <option value="male">בן</option>
                    <option value="female">בת</option>
                    <option value="other">אחר</option>
                </select>
                <input id="step-one-age" type="number" min="5" max="120" placeholder="גיל">
                <button class="next-step-btn">שלב הבא</button>
            </div>
        </div>
        <div class="step">
            <div class="step-content" id="column-step-content">
                <input class="step-field-newline" placeholder="סיסמא" id="step-two-password" type="password">
                <input class="step-field-newline" placeholder="אימות סיסמא" id="step-two-re-password" type="password" autocomplete="off">
                <div class="step-control">
                    <button class="prev-step-btn">שלב קודם</button>
                    <button class="next-step-btn" id="submit-signup">הירשם</button>
                </div>
            </div>
        </div>

        <div id="sign-up-form-feedback"></div>
    </form>
</div>
<script src="scripts/modules/sign_up_fillup_module.js"></script>