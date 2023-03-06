<?php

namespace born05\craftsubscribe\events;

use craft\events\CancelableEvent;
use yii\base\DynamicModel;

class SubscribeEvent extends CancelableEvent
{
    /**
     * @var DynamicModel
     */
    public $subscriber;
    
    /**
     * @var string|null
     */
    public $success;
}
