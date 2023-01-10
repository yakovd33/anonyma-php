<div id="user-random-talk-card" class="random-talk-wrap">
    <h2>שיחה עם אדם אקראי</h2>
    <form id="random-chat">
        <?php $chat_icons = array_diff(scandir("media/user-chat-icons"), array('.', '..')); ?>
        <?php $chat_icon = $chat_icons[rand(2, count($chat_icons))]; ?>
        <input type="hidden" id="chat-icon-name" value="<?php echo preg_replace('/\\.[^.\\s]{3,4}$/', '', $chat_icon); ?>">
        <div id="user-chat-icon" style="background-image: url('media/user-chat-icons/<?php echo $chat_icon; ?>'"></div>
        <div type="submit" id="random-chat-start">המשך <i class="fa fa-spinner" aria-hidden="true"></i></div>
        <div id="disclaimer">בהמשכת התהליך אני מאשר שקראתי את <a href="tos.php">תנאי השימוש</a>.</div>
    </form>
</div>