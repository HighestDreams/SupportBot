<?php
declare(strict_types=1);

namespace Telegram;

use Telegram\Database\Db;
use Telegram\Media\document;
use Telegram\Media\video;
use Telegram\Message\message;
use Telegram\Media\photo;

class Bot extends Telegram {
    /**
     * @var
     */
    public $token;
    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        parent::__construct($token);
    }

    /**
     * @return message
     */
    public function message(): message
    {
        return new message($this->token);
    }

    /**
     * @return photo
     */
    public function photo (): photo
    {
        return new photo($this->token);
    }

    /**
     * @return video
     */
    public function video (): video
    {
        return new video($this->token);
    }

    /**
     * @param string $path
     * @return Db
     */
    public function database (string $path): Db {
        return new Db($this->token, $path);
    }

    /**
     * @return Chat
     */
    public function chat (): Chat {
        return new Chat($this->token);
    }

    /**
     * @return document
     */
    public function document (): document {
        return $this->document($this->token);
    }
}