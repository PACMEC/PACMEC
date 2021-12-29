<?php

/**
 * This file is part of web3.php package.
 *
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 *
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3\Formatters;

use InvalidArgumentException;
use Ethereumico\EthereumWallet\Dependencies\Web3\Utils;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\IFormatter;
class IntegerFormatter implements IFormatter
{
    /**
     * format
     *
     * @param mixed $value
     * @return string
     */
    public static function format($value)
    {
        $value = (string) $value;
        $arguments = \func_get_args();
        $digit = 64;
        if (isset($arguments[1]) && \is_numeric($arguments[1])) {
            $digit = \intval($arguments[1]);
        }
        $bn = Utils::toBn($value);
        $bnHex = $bn->toHex(\true);
        $padded = \mb_substr($bnHex, 0, 1);
        if ($padded !== 'f') {
            $padded = '0';
        }
        if ($digit > \mb_strlen($bnHex)) {
            return \implode('', \array_fill(0, $digit - \mb_strlen($bnHex), $padded)) . $bnHex;
        }
        $bnHex = \ltrim($bnHex, '0');
        if ($digit > \mb_strlen($bnHex)) {
            return \implode('', \array_fill(0, $digit - \mb_strlen($bnHex), $padded)) . $bnHex;
        }
        return $bnHex;
    }
}