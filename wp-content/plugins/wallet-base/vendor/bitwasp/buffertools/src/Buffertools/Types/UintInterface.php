<?php

declare (strict_types=1);
namespace Ethereumico\EthereumWallet\Dependencies\BitWasp\Buffertools\Types;

interface UintInterface extends TypeInterface
{
    /**
     * @return int
     */
    public function getBitSize() : int;
}
