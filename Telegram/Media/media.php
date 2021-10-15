<?php
declare(strict_types=1);

namespace Telegram\Media;

use Telegram\Telegram;

class media extends Telegram
{
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * @param string $caption
     * @param string $newPath
     * @param int|null $chatID
     * @param int|null $messageID
     * @param array|null $inlineKeyboard
     * Edit media and caption
     * @param string $mediaType
     */
    public function editMedia(string $caption, string $newPath, int $chatID = null, int $messageID = null, array $inlineKeyboard = null, string $mediaType)
    {
        if (!empty($caption) and empty($newPath)) {
            /* Edit only caption (Not media) */
            $data = [
                'chat_id' => ($chatID ?? $this->chatID()),
                'message_id' => ($messageID ?? $this->botMessageID()),
                'caption' => $caption,
                'parse_mode' => "markdown"
            ];
            if ($inlineKeyboard) {
                $data['reply_markup'] = json_encode(['inline_keyboard' => $inlineKeyboard]);
            }
            $this->request("editMessageCaption", $data);
        } else {
            if (!empty($caption and !empty($newPath))) {
                /* Edit both (Media and caption) */
                $newMedia = [
                    'type' => $mediaType,
                    'media' => $this->pathToUrl($newPath),
                    'caption' => $caption
                ];
                if ($caption) {
                    $newMedia['caption'] = $caption;
                }
                $data = [
                    'chat_id' => ($chatID ?? $this->chatID()),
                    'message_id' => ($messageID ?? $this->botMessageID()),
                    'media' => json_encode($newMedia)
                ];
            }
            if (empty($caption and !empty($newPath))) {
                /* Edit only media */
                $newMedia = [
                    'type' => $mediaType,
                    'media' => $this->pathToUrl($newPath),
                ];
                $data = [
                    'chat_id' => ($chatID ?? $this->chatID()),
                    'message_id' => ($messageID ?? $this->botMessageID()),
                    'media' => json_encode($newMedia)
                ];
            }
            if ($inlineKeyboard) {
                $data['reply_markup'] = json_encode(['inline_keyboard' => $inlineKeyboard]);
            }
            $this->request("editMessageMedia", $data);
        }
    }
}