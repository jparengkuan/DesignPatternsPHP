<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Composite;

/**
 * Class Response
 *
 * A composite “Buyable” that can hold products or other bundles
 * and apply a single percentage discount to the whole set.
 *
 * @package DesignPatterns\Structural\Composite
 */
class Bundle implements Buyable
{
    /**
     * All buyable items (products or nested bundles) that belong to this bundle.
     *
     * @var Buyable[]
     */
    private array $items = [];

    /**
     * Response constructor.
     *
     * @param string $title            Human-readable name shown in listings.
     * @param float  $discountPercent  Discount applied to the sum of child prices
     *                                 (e.g. 10 means “take 10 % off”). Defaults to 0.
     */
    public function __construct(
        private string $title,
        private float $discountPercent = 0.0
    ) {
    }

    /**
     * Attach another buyable (product or sub-bundle) to this bundle.
     *
     * @param Buyable $item
     * @return void
     */
    public function add(Buyable $item): void
    {
        $this->items[] = $item;
    }

    /**
     * Return the current price of the bundle after discount.
     *
     * @return float Price in euros (rounded to 2 decimals).
     */
    public function getPrice(): float
    {
        $total = array_sum(array_map(
            fn (Buyable $item) => $item->getPrice(),
            $this->items
        ));

        return round($total * (1 - $this->discountPercent / 100), 2);
    }

    /**
     * Dump a simple textual representation of the bundle and its children.
     * Primarily for demo/debug purposes—would be replaced by proper view code
     * in a real application.
     *
     * @param string $indent Indentation prefix for pretty printing nested levels.
     * @return void
     */
    public function print(string $indent = ''): void
    {
        echo "{$indent}{$this->title} "
            . "(bundle, –{$this->discountPercent}% -> €{$this->getPrice()})\n";

        foreach ($this->items as $item) {
            $item->print($indent . '    ');
        }
    }
}
