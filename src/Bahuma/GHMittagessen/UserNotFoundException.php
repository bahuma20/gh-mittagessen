<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 02.10.15
 * Time: 22:02
 */

namespace Bahuma\GHMittagessen;


class UserNotFoundException extends \Exception {
    protected $message = "USER_NOT_FOUND";
    protected $string = "User not found";
    protected $code = 1;
}