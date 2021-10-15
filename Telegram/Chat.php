<?php
declare(strict_types=1);

namespace Telegram;

class Chat extends Telegram {
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        parent::__construct($token);
    }

    /**
     * @return mixed
     */
    public function user () {
        return $this->user();
    }
    /**
     * @return mixed
     * Group/SuperGroup/Privat/Channel/Bot
     */
    public function type () {
        return $this->chat()->type;
    }

    /**
     * @return bool
     */
    public function isGroup (): bool {
        return in_array($this->type(), ['group', 'supergroup']);
    }

    /**
     * @return bool
     */
    public function isChannel (): bool {
        return $this->type() === "channel";
    }

    /**
     * @return bool
     */
    public function isBot (): bool {
        return $this->type() === "bot";
    }

    /**
     * @return bool
     */
    public function isPv (): bool {
        return $this->type() === "private";
    }

    /**
     * @return bool
     */
    public function isDm (): bool {
        return $this->type() === "private";
    }

    /**
     * @return mixed
     */
    public function getId () {
        return $this->chatID();
    }

    /**
     * @return mixed
     */
    public function getTitle () {
        return $this->chat()->title;
    }
}