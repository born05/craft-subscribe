# Subscribe module for Craft CMS 3.x and 4.x

Subscribe users to a CRM

## Requirements

This module requires Craft CMS 3.0.0-RC1 or later.

## Installation

To install the module, follow these instructions.

1.  Then tell Composer to load the module:

        composer require born05/craft-subscribe

2.  Merge the following with your `/config/app.php`

        'modules' => [
                'craft-subscribe' => [
                        'class' => \born05\craftsubscribe\CraftSubscribe::class,
                ],
        ],
        'bootstrap' => ['craft-subscribe'],

3. Copy a configuration file into `/config` and rename it to  `craft-subscribe.php`


## License

Copyright © [Born05](https://www.born05.com/)

See [license](https://github.com/born05/craft-subscribe/blob/craft-4/LICENSE.md)
