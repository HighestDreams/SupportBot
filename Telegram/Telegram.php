<?php
declare(strict_types=1);

namespace Telegram;

class Telegram
{
    /**
     * @var string
     */
    public $token;

    /**
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->antiUpdateFake();
    }

    /**
     * @return mixed
     */
    public function replied () {
        return $this->msg()->reply_to_message;
    }

    /**
     * So this is an anti update fake (Prevent hackers from hacking)
     */
    private function antiUpdateFake()
    {
        if (PHP_SAPI == 'cli') {
            die();
        }
        $telegram_ip_ranges = [
            ['lower' => '149.154.160.0', 'upper' => '149.154.175.255'],
            ['lower' => '91.108.4.0', 'upper' => '91.108.7.255'],
        ];

        $ip_dec = (float)sprintf("%u", ip2long($_SERVER['REMOTE_ADDR']));
        $ok = false;

        foreach ($telegram_ip_ranges as $telegram_ip_range) {
            if (!$ok) {
                $lower_dec = (float)sprintf("%u", ip2long($telegram_ip_range['lower']));
                $upper_dec = (float)sprintf("%u", ip2long($telegram_ip_range['upper']));
                if ($ip_dec >= $lower_dec and $ip_dec <= $upper_dec) {
                    $ok = true;
                }
            }
        }
        if (!$ok) {
            die();
        }
    }

    /**
     * @return mixed
     */
    public function user () {
        return $this->msg()->from;
    }

    /**
     * @param string $path
     * @return string
     * So this will convert path to URL (Usage is for sending/editing medias)
     */
    public function pathToUrl(string $path): string
    {
        if (preg_match('/http/i', $path)) {
            return $path;
        }
        $currentPath = $_SERVER['PHP_SELF'];
        $pathInfo = pathinfo($currentPath);
        $hostName = $_SERVER['HTTP_HOST'];
        $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https' ? 'https' : 'http';
        return $protocol . '://' . $hostName . $pathInfo['dirname'] . "/" . $path;
    }

    /**
     * @param int $to
     * @param string|null $from
     * @param int|null $messageID
     * @param bool $anonymousForward
     * Forward messages
     */
    public function forward(int $to, string $from = null, int $messageID = null, bool $anonymousForward = false)
    {
        $data = [
            'chat_id' => $to,
            'from_chat_id' => ($from ?? $this->user()->id),
            'message_id' => ($messageID ?? $this->userMessageID())
        ];
        $this->request($anonymousForward === false ? "forwardMessage" : "copyMessage", $data);
    }

    /**
     * @return mixed
     * Getting chat ID (telegram updates)
     */
    public function chatID()
    {
        return $this->chat()->id;
    }

    /**
     * @return mixed
     * Getting chat message (telegram updates)
     */
    public function chat()
    {
        return $this->msg()->chat;
    }

    /**
     * @return mixed
     * Getting message (telegram updates)
     */
    public function msg()
    {
        return ($this->updates()->message ?? $this->updates()->callback_query->message);
    }

    /**
     * @return mixed
     * Getting (telegram updates)
     */
    public function updates()
    {
        return json_decode(file_get_contents('php://input'));
    }

    /**
     * @return mixed
     * Getting the messages ID that user sent!
     */
    public function userMessageID()
    {
        return $this->msg()->message_id;
    }

    /**
     * @param string $method
     * @param array $data
     * @return bool|string|void
     * Sending requests to telegram with your Bot TOKEN!
     */
    public function request(string $method, array $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.telegram.org/bot' . $this->token . '/' . $method);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $result = curl_exec($ch);
        curl_close($ch);
        $myres = json_decode($result, true);
        if (isset($myres['error_code'])) {
            if ($myres['error_code'] == 404) {
                $this->logger('There is something wrong with entered method or token!');
            }
            if ($myres['error_code'] != 404) {
                $debug = $this->debug();
                $this->logger("Error in {$debug['file']} file on line {$debug['line']}: " . $myres['description']);
            }
            exit();
        } else {
            return $result;
        }
    }

    /**
     * @param string $message
     * Saves errors/notices into telegram_error.log
     */
    public function logger(string $message)
    {
        error_log($message . "\n", 3, "telegram_error.log");
    }

    /**
     * @return array
     * Getting telegram errors
     */
    public function debug(): array
    {
        $debug = debug_backtrace();
        return ['file' => $debug[0]['file'], 'line' => $debug[0]['line']];
    }

    /**
     * @return mixed
     * Getting text message (telegram updates)
     */
    public function text()
    {
        return $this->msg()->text;
    }

    /**
     * @return bool
     */
    public function issetMessage(): bool
    {
        return !is_null($this->updates()->message->text);
    }

    /**
     * @return bool
     */
    public function issetEvents (): bool {
        return $this->updates()->message;
    }

    /**
     * @return bool
     */
    public function issetPhoto(): bool
    {
        return !is_null($this->updates()->message->photo);
    }

    /**
     * @return bool
     */
    public function issetVideo(): bool
    {
        return !is_null($this->updates()->message->video);
    }

    /**
     * @return bool
     */
    public function issetDocument (): bool {
        return !is_null($this->updates()->message->document);
    }

    /**
     * @param int $amount
     * @param int|null $chatID
     * Delete collective messages from a chat
     */
    public function collectiveDeletion(int $amount, int $chatID = null)
    {
        for ($i = $this->botMessageID() - 1; $i >= ($this->botMessageID() - $amount); $i--) {
            $this->delete($chatID, $i);
        }
    }

    /**
     * @return int|mixed|null
     * Getting the messages ID that bot sent!
     */
    public function botMessageID()
    {
        $ID = $this->userMessageID();
        return !is_null($ID) ? $ID + 1 : $ID;
    }

    /**
     * @param int|null $chatID
     * @param int|null $messageID
     * Delete messages (Any type of message like media/text and...)
     */
    public function delete(int $chatID = null, int $messageID = null)
    {
        $data = [
            'chat_id' => ($chatID ?? $this->chatID()),
            'message_id' => ($messageID ?? $this->botMessageID()),
        ];
        $this->request("deleteMessage", $data);
    }
}