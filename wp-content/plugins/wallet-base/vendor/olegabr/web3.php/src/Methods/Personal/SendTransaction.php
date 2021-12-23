<?php

/**
 * This file is part of web3.php package.
 * 
 * (c) Kuan-Cheng,Lai <alk03073135@gmail.com>
 * 
 * @author Peter Lai <alk03073135@gmail.com>
 * @license MIT
 */
namespace Ethereumico\EthereumWallet\Dependencies\Web3\Methods\Personal;

use InvalidArgumentException;
use Ethereumico\EthereumWallet\Dependencies\Web3\Methods\EthMethod;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\TransactionValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Validators\StringValidator;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\TransactionFormatter;
use Ethereumico\EthereumWallet\Dependencies\Web3\Formatters\StringFormatter;
class SendTransaction extends EthMethod
{
    /**
     * validators
     * 
     * @var array
     */
    protected $validators = [TransactionValidator::class, StringValidator::class];
    /**
     * inputFormatters
     * 
     * @var array
     */
    protected $inputFormatters = [TransactionFormatter::class, StringFormatter::class];
    /**
     * outputFormatters
     * 
     * @var array
     */
    protected $outputFormatters = [];
    /**
     * defaultValues
     * 
     * @var array
     */
    protected $defaultValues = [];
    /**
     * construct
     * 
     * @param string $method
     * @param array $arguments
     * @return void
     */
    // public function __construct($method='', $arguments=[])
    // {
    //     parent::__construct($method, $arguments);
    // }
}
