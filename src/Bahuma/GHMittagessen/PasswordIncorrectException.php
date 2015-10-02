<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 02.10.15
 * Time: 22:26
 */

namespace Bahuma\GHMittagessen;


class PasswordIncorrectException extends \Exception {
    protected $code = 2;
    protected $message = "PASSWORD_INCORRECT";
    protected $string = "Password incorrect";
}