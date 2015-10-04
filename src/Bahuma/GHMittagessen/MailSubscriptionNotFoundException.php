<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 04.10.15
 * Time: 22:39
 */

namespace Bahuma\GHMittagessen;


class MailSubscriptionNotFoundException extends \Exception {

    protected $code = 3;
    protected $message = "MAIL_SUBSCRIPTION_NOT_FOUND";
    protected $string = "Mail subscription not found";
}