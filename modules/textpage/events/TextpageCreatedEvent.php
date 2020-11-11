<?php
/**
 * Created by PhpStorm.
 * User: o.trushkov
 * Date: 15.12.17
 * Time: 16:03
 */

namespace app\modules\textpage\events;


use common\models\Textpage;
use yii\base\Event;

class TextpageCreatedEvent extends Event
{
    public $textpage;

    public function __construct(Textpage $textpage)
    {
        $this->textpage = $textpage;
    }

}