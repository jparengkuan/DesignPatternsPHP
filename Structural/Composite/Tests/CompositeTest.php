<?php

declare(strict_types=1);

namespace DesignPatterns\Structural\Composite\Tests;

use DesignPatterns\Structural\Composite\Bundle;
use DesignPatterns\Structural\Composite\Product;
use PHPUnit\Framework\TestCase;

/**
 * CompositeTest
 *
 * Runs all unit-level checks for the Composite e-commerce example
 * (Product, Bundle, and their interaction) in a single PHPUnit class.
 *
 * To execute:
 *   vendor/bin/phpunit --filter CompositeTest
 */
final class CompositeTest extends TestCase
{
    /* -----------------------------------------------------------------
     *  Product (leaf) behaviour
     * -----------------------------------------------------------------*/

    public function testProductReturnsItsUnitPrice(): void
    {
        $product = new Product('SKU1', 'Widget', 12.50);
        $this->assertSame(12.50, $product->getPrice());
    }

    public function testProductPrintsExpectedString(): void
    {
        $product = new Product('SKU1', 'Widget', 12.50);
        ob_start();
        $product->print();
        $output = ob_get_clean();
        $this->assertSame("- Widget (SKU1) €12.5\n", $output);
    }

    /* -----------------------------------------------------------------
     *  Bundle (composite) behaviour
     * -----------------------------------------------------------------*/

    public function testEmptyBundlePriceIsZero(): void
    {
        $bundle = new Bundle('Empty');
        $this->assertSame(0.0, $bundle->getPrice());
    }

    public function testBundleWithoutDiscountEqualsSumOfChildren(): void
    {
        $bundle   = new Bundle('No-Discount');
        $mouse    = new Product('M1', 'Mouse', 25.00);
        $keyboard = new Product('K1', 'Keyboard', 75.00);
        $bundle->add($mouse);
        $bundle->add($keyboard);
        $this->assertSame(100.00, $bundle->getPrice());
    }

    public function testBundleAppliesPercentageDiscount(): void
    {
        $bundle = new Bundle('Spring-Promo', 10); // –10 %
        $bundle->add(new Product('A', 'A', 40.00));
        $bundle->add(new Product('B', 'B', 60.00));
        $this->assertSame(90.00, $bundle->getPrice()); // 100 × 0.9
    }

    public function testNestedBundlesCascadeAndRoundCorrectly(): void
    {
        // Child bundle (10 % off)
        $starter = new Bundle('Starter', 10);
        $starter->add(new Product('M', 'Mouse', 25.00));
        $starter->add(new Product('K', 'Keyboard', 70.00)); // 95 → 85.5

        // Parent bundle (15 % off)
        $pro = new Bundle('Pro Desk', 15);
        $pro->add($starter); // 85.5
        $pro->add(new Product('MON', 'Monitor', 150.00)); // +150
        // 235.5 × 0.85 = 200.175 → 200.18 (rounded)

        $this->assertSame(200.18, $pro->getPrice());
    }

    public function testBundlePrintShowsHierarchy(): void
    {
        $bundle = new Bundle('Kit');
        $bundle->add(new Product('A', 'Alpha', 10));
        ob_start();
        $bundle->print();
        $output = ob_get_clean();
        $this->assertStringStartsWith('Kit (bundle', $output);
        $this->assertStringContainsString("    - Alpha (A) €10\n", $output);
    }
}
