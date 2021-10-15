<?php
declare(strict_types=1);

namespace Telegram\Media;

class document extends media
{
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * @param string $path
     * @param string|null $caption
     * @param string|null $thumb
     * @param int|null $chatID
     * @param bool $botReply
     * @param bool $userReply
     * @param array|null $keyboard
     * Sending a photo
     */
    public function send(string $path, string $caption = null, string $thumb = null, int $chatID = null, bool $botReply = false, bool $userReply = false, array $keyboard = null)
    {
        $data = [
            'chat_id' => ($chatID ?? $this->chatID()),
            'document' => $this->pathToUrl($path),
            'caption' => $caption,
            'parse_mode' => 'markdown'
        ];
        if ($thumb) {
            $data['thumb'] = $this->pathToUrl($thumb);
        }
        if ($botReply) {
            $data['reply_to_message_id'] = $this->userMessageID();
        }
        if ($userReply) {
            $data['reply_markup'] = json_encode(['force_reply' => true]);
        }
        if ($keyboard) {
            $data['reply_markup'] = json_encode(['inline_keyboard' => $keyboard]);
        }
        $this->request("sendDocument", $data);
    }

    /**
     * @param string $caption
     * @param string $newPath
     * @param int|null $chatID
     * @param int|null $messageID
     * @param array|null $inlineKeyboard
     * Edit photo
     */
    public function edit(string $caption, string $newPath, int $chatID = null, int $messageID = null, array $inlineKeyboard = null)
    {
        $this->editMedia($caption, $newPath, $chatID, $messageID, $inlineKeyboard, 'document');
    }
}