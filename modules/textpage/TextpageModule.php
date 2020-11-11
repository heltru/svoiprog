<?php

namespace app\modules\textpage;
use app\modules\textpage\events\TextpageCreatedEvent;

/**
 * textpage module definition class
 */
class TextpageModule extends \yii\base\Module
{
 //   public $layout = 'main';
    /**
     * @inheritdoc
     */
    //public $controllerNamespace = 'app\modules\textpage\controllers';

    const EVENT_TEXTPAGE_CREATED = 'textpageCreated';
    const EVENT_TEXTPAGE_UPDATED = 'textpageUpdated';

    const EVENT_USER_SIGNED_UP = 'userSignedUp';
    const EVENT_USER_DELETED = 'userDeleted';


    /**
     * @inheritdoc
     */

    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }

    public function notifyThatTextpageCreated($textpage) {
        $this->trigger(TextPageModule::EVENT_USER_SIGNED_UP, new TextpageCreatedEvent($textpage));
    }

}
