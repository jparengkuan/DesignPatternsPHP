<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Composite;

/**
 * Class Product
 *
 * Leaf node in the Composite buy-able hierarchy.
 * Represents a single SKU with a fixed unit price.
 */
class Product implements Buyable
{
    /**
     * Product constructor.
     *
     * @param string $sku        Stock-keeping unit (unique identifier).
     * @param string $name       Human-readable product name.
     * @param float  $unitPrice  Unit price in euros, **excluding** VAT.
     */
    public function __construct(
        private string $sku,
        private string $name,
        private float $unitPrice
    ) {
    }

    /**
     * Return the unit price of this product.
     *
     * @return float Price in euros (ex-VAT).
     */
    public function getPrice(): float
    {
        return $this->unitPrice;
    }

    /**
     * Dump a textual representation for quick demos / debugging.
     * In production you would render this via a template.
     *
     * @param string $indent Optional indentation for nested outputs.
     * @return void
     */
    public function print(string $indent = ''): void
    {
        echo "{$indent}- {$this->name} ({$this->sku}) €{$this->unitPrice}\n";
    }
}
