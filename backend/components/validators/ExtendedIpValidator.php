<?php

namespace app\components\validators;

use yii\validators\IpValidator;

class ExtendedIpValidator extends IpValidator
{
    protected function validateValue($value)
    {
        // First, try parent (handles /bits, plain IPs)
        $result = parent::validateValue($value);
        if ($result === null) {
            return null; // valid
        }

        // If parent failed, check for /netmask
        if (strpos($value, '/') !== false) {
            [$ip, $mask] = explode('/', $value, 2);

            if (filter_var($ip, FILTER_VALIDATE_IP) &&
                filter_var($mask, FILTER_VALIDATE_IP) &&
                $this->isValidNetmask($mask)) {
                return null; // valid netmask CIDR
            }
        }

        return $result; // original error
    }

    private function isValidNetmask($mask)
    {
        $long = ip2long($mask);
        if ($long === false) {
            return false;
        }
        $neg = ~$long & 0xFFFFFFFF;
        return (($neg + 1) & $neg) === 0; // check contiguous bits
    }
}
