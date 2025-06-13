<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Composite;

/**
 * Interface Buyable
 *
 * **Component** role in the Composite pattern used for the e-commerce example.
 * Anything that can be placed in a basket (a single Product *or* a Response that
 * aggregates other buyables) must implement this contract so the checkout code
 * can treat them uniformly.
 */
interface Buyable
{
    /**
     * Returns the current price of the item **in euros**.
     * For bundles this is already net of any bundle-level discounts.
     *
     * @return float Price in euros (rounded to two decimals, ex-VAT).
     */
    public function getPrice(): float;

    /**
     * Outputs a quick textual representation—handy for demos and debugging.
     * In a real application this would be replaced by templating logic.
     *
     * @param string $indent Optional indentation prefix to make nested
     *                       structures readable when printing composites.
     *
     * @return void
     */
    public function print(string $indent = ''): void;
}
