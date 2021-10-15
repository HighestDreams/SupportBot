<?php
declare(strict_types=1);

namespace Telegram\Message;

use Telegram\Telegram;

class message extends Telegram
{
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * @param string $text
     * @param int|null $chatID
     * @param bool $botReply
     * @param bool $userReply
     * @param array|null $keyboard
     * Sending message
     */
    public function send (string $text, int $chatID = null, bool $botReply = false, bool $userReply = false, array $keyboard = null)
    {
        if (empty($text)) {
            $debug = $this->debug();
            $this->logger("Error in {$debug['file']} file on line {$debug['line']}, Empty messages can't be send.");
            exit();
        }
        $data = [
            'chat_id' => ($chatID ?? $this->chatID()),
            'text' => $text,
            'parse_mode' => 'markdown'
        ];
        if ($botReply) {
            $data['reply_to_message_id'] = $this->userMessageID();
        }
        if ($userReply) {
            $data['reply_markup'] = json_encode(['force_reply' => true]);
        }
        if ($keyboard) {
            $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
        }
        $this->request("sendMessage", $data);
    }

    /**
     * @param string $text
     * @param int|null $chatID
     * @param int|null $messageID
     * @param array|null $keyboard
     * Edit messages (Only texts)
     */
    public function edit (string $text, int $chatID = null, int $messageID = null, array $keyboard = null)
    {
        if (empty($text)) {
            $debug = $this->debug();
            $this->logger("Error in {$debug['file']} file on line {$debug['line']}, Messages can't be edited to empty strings!");
            exit();
        }
        $data = [
            'chat_id' => ($chatID ?? $this->chatID()),
            'message_id' => ($messageID ?? $this->botMessageID()),
            'text' => $text,
            'parse_mode' => 'markdown'
        ];
        if ($keyboard) {
            $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
        }
        $this->request("editMessageText", $data);
    }
}