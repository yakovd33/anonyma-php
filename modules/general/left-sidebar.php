<div class="grid__col grid__col--4-of-14">
    <?php include 'complete-registration.php'; ?>

    <?php if (isset($show_consultations_like)) : ?>
        <div class="card transparent">
            <h4>התייעצויות דומות</h4>
        </div>
    <?php endif; ?>

    <?php include 'new-consultations-module.php'; ?>

    <div class="card ads-wrap grid__row">
        <img src="media/ads-placeholder.png">
        <div class="ad-details">
            <a href="#" class="ad-link">לכו לD4DShop.com</a>
            <div class="link-details">וקבלו 20% הנחה.</div>
        </div>
    </div>

    <?php include 'footer-module.php'; ?>
</div>