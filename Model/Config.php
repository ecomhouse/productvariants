<?php
declare(strict_types=1);

namespace EcomHouse\ProductVariants\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Config
{
    const ONLY_SIMPLE = 'ecomhouse_productvariants/general/only_simple';
    const ENABLED = 'ecomhouse_productvariants/general/enable';

    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::ENABLED, ScopeInterface::SCOPE_STORE);
    }

    public function isOnlySimpleProducts(): bool
    {
        return $this->scopeConfig->isSetFlag(self::ONLY_SIMPLE, ScopeInterface::SCOPE_STORE);
    }
}
