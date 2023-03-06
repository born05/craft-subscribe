<?php

namespace born05\craftsubscribe;

use born05\craftsubscribe\services\Subscribe as SubscribeService;
use Craft;
use yii\base\Module;

class CraftSubscribe extends Module
{
    // Static Properties
    // =========================================================================

    const MAILCHIMP_CRM_HANDLE = 'mailchimp';

    /**
     * @var CraftSubscribe
     */
    public static $instance;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function __construct($id, $parent = null, array $config = [])
    {
        Craft::setAlias('@born05/craftsubscribe', $this->getBasePath());
        $this->controllerNamespace = 'born05\craftsubscribe\controllers';

        // Set this as the global instance of this module class
        static::setInstance($this);

        parent::__construct($id, $parent, $config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        self::$instance = $this;
        $this->setComponents([
            'subscribe' => SubscribeService::class,
        ]);
    }
}
