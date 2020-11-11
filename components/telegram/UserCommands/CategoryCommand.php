<?php
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 21.02.2018
 * Time: 23:44
 */

namespace common\components\telegram\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;

use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboardMarkup;
use Longman\TelegramBot\Request;



class CategoryCommand  extends UserCommand
{
    protected $name = 'category';
    protected $description = 'Choose a category, inline.';
    protected $usage = '/category';
    protected $version = '1.0.0';

    public static $categories = [
        'apple'  => 'Yay, an apple!',
        'orange' => 'Sweetness...',
        'cherry' => 'A cherry on top.',
    ];

    public function execute()
    {
        $keyboard_buttons = [];
        foreach (self::$categories as $key => $value) {
            $keyboard_buttons[] = new InlineKeyboardButton([
                'text'          => ucfirst($key),
                'callback_data' => 'category_' . $key,
            ]);
        }

        $data = [
            'chat_id'      => $this->getMessage()->getChat()->getId(),
            'text'         => 'Choose a category:',
            'reply_markup' => new InlineKeyboardMarkup($keyboard_buttons),
        ];

        return Request::sendMessage($data);
    }
}