<?php
/**
 * Created by PhpStorm.
 * User: heltru
 * Date: 21.02.2018
 * Time: 23:59
 */

#namespace Longman\TelegramBot\Commands\SystemCommands;
namespace common\components\telegram\UserCommands;

#use Longman\TelegramBot\Commands\AdminCommands\CategoryCommand;
use common\components\telegram\UserCommands\CategoryCommand;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

class CallbackqueryCommand extends SystemCommand
{
    protected $name = 'callbackquery';
    protected $description = 'Reply to callback query';
    protected $version = '1.0.0';

    public function execute()
    {
        $update         = $this->getUpdate();
        $callback_query = $update->getCallbackQuery();
        $callback_data  = $callback_query->getData();

        if (strpos($callback_data, 'category_') !== 0) {
            return Request::emptyResponse();
        }

        // Get the real category from the callback_data.
        $category = substr($callback_data, strlen('category_'));

        return Request::editMessageText([
            'chat_id'    => $callback_query->getMessage()->getChat()->getId(),
            'message_id' => $callback_query->getMessage()->getMessageId(),
            'text'       => CategoryCommand::$categories[$category],
        ]);
    }
}
