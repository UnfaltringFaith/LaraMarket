<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Cookie;

class CartService
{
    /**
     * Add item to a cart Cookie.
     *
     * @param array $cartItems
     * @return void
     */
    static public function addItemToCart($product_id): int
    {
        $cartItems = self::getCartItemsFromCookie();

        $existing_item = null;

        foreach ($cartItems as $key => $item) {
            if ($item['product_id'] === $product_id) {
                $existing_item = $key;
                break;
            }
        }

        if ($existing_item !== null) {
            // If item already exists, increment the quantity
            $cartItems[$existing_item]['quantity']++;
            $cartItems[$existing_item]['total_amount'] = $cartItems[$existing_item]['quantity'] * $cartItems[$existing_item]['price'];
        } else {
            // If item does not exist, add it to the cart
            $cartItems[] = [
                'product_id' => Product::find($product_id)->id,
                'name' => Product::find($product_id)->name,
                'price' => Product::find($product_id)->price,
                'quantity' => 1,
                'total_amount' => Product::find($product_id)->price, // Assuming total_amount
            ];
        }

        self::addCartItemsToCookie($cartItems);

        return array_sum(array_column($cartItems, 'quantity'));
    }

    /**
     * Remove item from the cart.
     *
     * @param int $product_id
     * @return int
     */

    static public function removeCartItem($product_id): array
    {
        $cartItems = self::getCartItemsFromCookie();

        foreach ($cartItems as $key => $item) {
            if ($item['product_id'] === $product_id) {
                unset($cartItems[$key]);
                break;
            }
        }

        self::addCartItemsToCookie($cartItems);

        return $cartItems;
    }

    /**
     * Add cart items to a cookie.
     *
     * @param array $cartItems
     * @return void
     */
    static public function addCartItemsToCookie(array $cartItems): void
    {
        Cookie::queue('cart_items', json_encode($cartItems), 60 * 24 * 30); // 30 days
    }

    /**
     * Remove cart items from the cookie.
     *
     * @return void
     */
    static public function clearCartItems(): void
    {
        Cookie::queue(Cookie::forget('cart_items'));
    }

    /**
     * Get cart items from the cookie.
     *
     * @return array
     */
    static public function getCartItemsFromCookie(): array
    {
        $cartItems = Cookie::get('cart_items');
        return $cartItems ? json_decode($cartItems, true) : [];
    }

    /**
     * Increment item quantity in the cart.
     *
     * @return float
     */
    static public function incrementQuantityToCartItem($product_id)
    {
        $cartItems = self::getCartItemsFromCookie();

        foreach ($cartItems as &$item) {
            if ($item['product_id'] === $product_id) {
                $item['quantity']++;
                $item['total_amount'] = $item['quantity'] * $item['price'];
                break;
            }
        }

        self::addCartItemsToCookie($cartItems);

        return $cartItems;
    }

    /**  
     *  Get product count in the cart.
     *  @ return int
    */
    static public function getProductCountInCart($product_id): int
    {
        $cartItems = self::getCartItemsFromCookie();

        foreach ($cartItems as $item) {
            if ($item['product_id'] === $product_id) {
                return $item['quantity'];
            }
        }
        // If product not found in cart, return 0
        return 0;
    }

    /**
     * Decrement item quantity in the cart.
     *
     * @return float
     */
    static public function decrementQuantityToCartItem($product_id)
    {
        $cartItems = self::getCartItemsFromCookie();

        foreach ($cartItems as &$item) {
            if ($item['product_id'] === $product_id) {
                $item['quantity'] = max(0, $item['quantity'] - 1);
                $item['total_amount'] = $item['quantity'] * $item['price'];
                break;
            }
        }

        self::addCartItemsToCookie($cartItems);

        return $cartItems;
    }

    /**
     * Get total amount of the cart.
     *
     * @return float
     */
    static public function getTotalAmount(): float
    {
        $cartItems = self::getCartItemsFromCookie();

        return array_sum(array_column($cartItems, 'total_amount'));
    }
    /**
     * Get total count of the cart.
     *
     * @return float
     */
    static public function getTotalCount(): int
    {
        $cartItems = self::getCartItemsFromCookie();

        return array_sum(array_column($cartItems, 'quantity'));
    }

    /**
     * Get total count of the cart.
     *
     * @return float
     */
    static public function calculateGrandTotal($cartItems): int
    {
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['total_amount'];
        }

        return $total;

    }
}