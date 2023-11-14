<?php

const API = "TOKEN"; // bot token

function bot($method, $data = [])
{
    $url = "https://api.telegram.org/bot" . API . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}

$level_1 = [
    ['text' => '☺️', 'callback_data' => 'part1'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong']
];
shuffle($level_1);
$key1 = array_chunk($level_1, 3);
$key1 = json_encode(['inline_keyboard' => $key1]);

$level_2 = [
    ['text' => '☺️', 'callback_data' => "part2"],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong'],
    ['text' => '☹️', 'callback_data' => 'wrong']
];

shuffle($level_2);
$key2 = array_chunk($level_2, 4);
$key2 = json_encode(['inline_keyboard' => $key2]);


function editMessage($cid, $mid, $text, $button)
{
    bot(
        'editMessageText',
        [
            'chat_id' => $cid,
            'message_id' => $mid,
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => $button
        ]
    );
}

function SendMessage($cid, $text, $button = null)
{
    if ($button == null) {
        $button = json_encode(['remove_keyboard' => true]);
    }
    bot(
        'sendmessage',
        [
            'chat_id' => $cid,
            'text' => $text,
            'parse_mode' => 'html',
            'reply_markup' => $button
        ]
    );
    exit();
}

function top()
{
    $dir = 'points';
    $files = scandir($dir);
    unset($files[0], $files[1]);
    $result = [];
    foreach ($files as $file) {
        $count = file_get_contents($dir . '/' . $file);
        $userid = str_replace(".txt", "", $file); //
        $result[$userid] = $count;
    }
    arsort($result);
    $top = '';
    $num = 1;
    foreach ($result as $userid => $count) {
        if ($num < 11) {
            $top .= "$num) <a href='tg://user?id=$userid'>$userid</a> -- $count ta \n";
            $num++;
        } else {
            break;
        }
    }
    return $top;
}

$dev = "@abdukhalilovazim";
$update = json_decode(file_get_contents('php://input'));
$message = $update->message ?? null;
$callbackquery = $update->callback_query ?? null;
$text = $message->text ?? null;
$data = $update->callback_query->data ?? null;
$call_id = $update->callback_query->id ?? null;
$name = $message->from->first_name ?? null;

if (isset($callbackquery)) {
    $cid = $update->callback_query->message->chat->id ?? null;
    $type = $update->callback_query->message->chat->type ?? null;
    $mid = $update->callback_query->message->message_id ?? null;
    $from_id = $update->callback_query->from->id ?? null;
} elseif (isset($message)) {
    $from_id = $message->from->id ?? null;
    $cid = $message->chat->id ?? null;
    $type = $message->chat->type ?? null;
    $mid = $message->message_id ?? null;
}

$main = json_encode([
    'inline_keyboard' => [
        [
            ['text' => 'Start game ✈️', 'callback_data' => "start"],
            ['text' => '🔰 About bot', 'callback_data' => 'about']
        ],
        [
            ['text' => "Top users 🔝", 'callback_data' => "top"],
            ['text' => '⭐ Bot owner', 'url' => 't.me/abdukhalilovAzim']
        ],
    ]
]);
$words = array("Good 👍", "Nice 👏", "Wonderfull 🤗", "Grate! 🤩", "Strike 🤓", "Bravo 👍", "Cool 😎", "Awesome 🤘");
$motiv = $words[array_rand($words)];
if ($type == "group" || $type == "supergroup") {
    bot('leaveChat', ['chat_id' => $cid]);
    exit();
}
$help = "🔶 I'm a game of finding a smiley face. I'll send you a sad☹️ and a smiling☺️ emoji and you'll find a smiling☺️ emoji.\n\nThere is also a top 10 section of participants with the most points.\n\n⁉️<b>Rules of the game:</b>\n\nAfter the game starts, you are given 5 seconds. You have to find that smiling☺️ emoji within 5 seconds. Find the smiling☺️ emoji,if you find a smiling☺️ emoji, you get 1 point, if you don't find it, it's game over and your score starts at 0. Click the Start Game button to start the game.\n\n👨‍💻 Bot developer: $dev";

function has($folder, $cid)
{
    if (!is_dir($folder)) {
        mkdir($folder);
    }
    if (!file_exists($folder . '/' . $cid . '.txt')) {
        file_put_contents($folder . '/' . $cid . '.txt', 0);
    }
}

has('flood', $cid);
has('points', $cid);

$user_flood = file_get_contents("flood/$cid.txt") ?? null;
$points = file_get_contents("points/$cid.txt") ?? null;


// var_dump((file_exists()))
if ($message) {
    switch ($text) {
        case '/start':
            SendMessage($cid, "👋Hello $name I'm a game of find the smiling face.\n❗️Before playing, please get to know about the bot structure in the bot section", $main);
            break;
        case '/top':
            SendMessage($cid, "➖➖➖🏆TOP 10🏆➖➖➖\n\n" . top(), $main);
            break;
        case '/help':
            SendMessage($cid, $help, $main);
            break;
        case '/my':
            SendMessage($cid, "You have earned a total of <b>$points</b> point from the bot\nList of top users\n\n👉/top👈", $main);
            break;
        default:
            SendMessage($cid, "this $text Undefined", $main);
    }
}

if ($callbackquery) {

    if ($data == 'start') {
        $text = "You have 5 seconds to find an emoji. The bot will calculate how much time is left and the game will continue if you haven't run out of time, if your time has run out the game will stop immediately.\nAre you ready to start the game?";
        $menu = json_encode(['inline_keyboard' => [[['text' => " 🔍 Are you ready ?", 'callback_data' => 'startgo']]]]);
        editMessage($cid, $mid, $text, $menu);
        exit();
    }

    if ($data == 'startgo') {
        unlink("points/$cid.txt");
        editMessage($cid, $mid, "3️⃣...️", null);
        editMessage($cid, $mid, "2️⃣...", null);
        editMessage($cid, $mid, "1️⃣...️", null);
        editMessage($cid, $mid, "Go . . . 🚀", null);
        editMessage($cid, $mid, "Find the smiling emoji from emojis", $key1);
        file_put_contents("flood/$cid.txt", time() + 5);
        exit();
    }
}
if ($data == "goo") {
    bot(
        'answerCallbackQuery',
        [
            'callback_query_id' => $call_id,
            'text' => ''
        ]
    );
    if ($user_flood == null || 0 <= (time() - $user_flood)) {
        editMessage($cid, $mid, "Time is up ⌛️", $main);
        exit();
    } else {
        file_put_contents("points/$cid.txt", $points + 1);
        editMessage($cid, $mid, "$motiv\n➖➖➖➖➖➖➖➖➖➖➖➖➖➖ \n<b>You are given +1 points</b>", $key1);
        file_put_contents("flood/$cid.txt", time() + 5);
        exit();
    }
}

if ($data == "part1") {
    bot(
        'answerCallbackQuery',
        [
            'callback_query_id' => $call_id,
            'text' => ''
        ]
    );
    if ($user_flood == null || 0 <= (time() - $user_flood)) {
        editMessage($cid, $mid, "Time is up ⌛️", $main);
        exit();
    } else {
        if ($points < 25 || $points == 25) {
            file_put_contents("points/$cid.txt", $points + 1);
            editMessage($cid, $mid, "<b>You are given +1 points</b>\n➖➖➖➖➖➖➖➖➖➖➖➖➖➖\n$motiv", $key1);
            file_put_contents("flood/$cid.txt", time() + 5);
            exit();
        } else {
            editMessage($cid, $mid, "$motiv \n➖➖➖➖➖➖➖➖➖➖➖➖➖➖ \n<b>You are given +1 points</b>", $key2);
            file_put_contents("flood/$cid.txt", time() + 5);
            exit();
        }
    }
}

if ($data == "part2") {
    bot(
        'answerCallbackQuery',
        [
            'callback_query_id' => $call_id,
            'text' => ''
        ]
    );
    if ($user_flood == null || 0 <= (time() - $user_flood)) {
        editMessage($cid, $mid, "Time is up ⌛️", $main);
        exit();
    } else {
        file_put_contents("points/$cid.txt", $points + 1);
        editMessage($cid, $mid, "<b>You are given +1 points</b>\n➖➖➖➖➖➖➖➖➖➖➖➖➖➖ \n$motiv", $key2);
        file_put_contents("flood/$cid.txt", time() + 5);
        exit();
    }
}
if ($data == "part2") {
    bot(
        'answerCallbackQuery',
        [
            'callback_query_id' => $call_id,
            'text' => ''
        ]
    );
    if ($user_flood == null || 0 <= (time() - $user_flood)) {
        editMessage($cid, $mid, "Time is up ⌛️", $main);
        exit();
    } else {
        file_put_contents("points/$cid.txt", $points + 1);
        editMessage($cid, $mid, "$motiv\n➖➖➖➖➖➖➖➖➖➖➖➖➖➖ \n<b>You are given +1 points</b>", $key2);
        file_put_contents("flood/$cid.txt", time() + 5);
        exit();
    }
}

if ($data == "about") {
    editMessage($cid, $mid, $help, $main);
    exit();
}
if ($data == "top") {
    editMessage($cid, $mid, top(), $main);
}
if ($data == 'wrong') {
    editMessage($cid, $mid, "You didn't find the smiling emoji 😕", $main);
    unlink("flood/$cid.txt");
    exit();
}