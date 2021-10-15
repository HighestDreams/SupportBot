<?php
use Telegram\Bot;
spl_autoload_register(function ($class){require_once __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';});
# ========================= #
$admin = 0000000000;
$bot = new Bot("TOKEN");
# ========================= #
if ($bot->issetMessage()) {
    if ($bot->text() === "/start") {
        if ($bot->user()->id == $admin) {
            $bot->message()->send("[🎊] - Welcome to your support bot, reply to sent messages by users and send your message to answer them.");
        } else {
            if ($bot->chat()->isPv()) { // To check if sent messages from a pv not channel/bot/group/supergroup
                $bot->message()->send("[🎉] - Welcome to the support bot, send you message now, i will send it to my admin!");
            }
        }
        exit();
    }
    if ($bot->user()->id == $admin) {
        if (!is_null(($ID = $bot->replied()->forward_from->id))) {
            if ($ID != $admin) {
                $bot->forward($ID, NULL, NULL, true);
                $bot->message()->send("[✔] - Your message has been sent to the user.");
            } else {
                $bot->message()->send("[❌] - You can't answer your messages! wtf dude?");
            }
        } else {
            $bot->message()->send("[❌] - Can't answer those people who has *hidden forward account*! (Its a telegram *privacy issue*, not a *bug*!)");
        }
    } else {
        $bot->forward($admin);
        $bot->message()->send("[✔] - Your message has been sent to the admin.");
    }
}