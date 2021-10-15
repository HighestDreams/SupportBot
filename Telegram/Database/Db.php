<?php
declare(strict_types=1);

namespace Telegram\Database;

use SQLite3;
use Telegram\Telegram;

class Db extends Telegram {
    /**
     * @var SQLite3
     */
    public static $db;

    /**
     * @param string $token
     * @param $path
     */
    public function __construct(string $token, $path)
    {
        self::$db = new SQLite3($path); /* Currently, only support sql3 */
        $this->createTables();
        parent::__construct($token);
    }

    public function createTables () {
        self::$db->query("CREATE TABLE IF NOT EXISTS users (
            number INTEGER PRIMARY KEY AUTOINCREMENT,
            ID INT NOT NULL,
            first_name TEXT NULL,
            last_name TEXT NULL,
            branch TEXT NULL,
            grade TEXT NULL,
            ban INT NULL,
            mute INT NULL,
            station_one TEXT NULL,
            station_two TEXT NULL,
            station_three TEXT NULL,
            status TEXT NULL
        );");
    }

    /**
     * @param string $data
     * @param int|null $chatID
     * @return mixed
     */
    public function get (string $data, int $chatID = null) {
        return self::$db->query("SELECT * FROM users WHERE ID = ' " . ($chatID ?? $this->chatID()) . "';")->fetchArray(SQLITE3_ASSOC)[$data];
    }

    /**
     * @param string $data
     * @param string $value
     * @param int|null $chatID
     */
    public function set (string $data, string $value, int $chatID = null) {
        self::$db->query("UPDATE users SET $data = $value WHERE ID = '" . ($chatID ?? $this->chatID()) . "';");
    }

    /**
     * @param int|null $chatID
     * @return bool
     */
    public function exists (int $chatID = null): bool {
        return !is_null($this->get('number', $chatID));
    }

    public function register () {
        self::$db->query("INSERT INTO users ('ID', 'first_name', 'last_name', 'branch', 'grade', 'ban', 'mute', 'station_one', 'station_two', 'station_three', 'status') VALUES (" . $this->chatID() . ", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);");
    }
}