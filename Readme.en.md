# Kata TDD (Test-Driven Development) in PHP

## Context

The company `Noelas` wishes to improve the management of the user basket on its ecommerce site.
It also wishes to add business constraints following recent hacking incidents.

As an expert, they call on you to respond effectively to their needs.

The rules to follow to improve the user basket are as follows:

1. A user's shopping cart is unique and empty by default when their account is created.
2. The user basket can contain several instances of the same product, as well as different products.
3. It is forbidden to add a product to the basket if the chosen quantity is less than or equal to zero.
4. It must be possible to remove a product from the basket or reduce the quantity of a product in the basket.
5. `Example`:
   - If the user adds the same product 4 times, there must be a single product in the basket with a quantity of 4.
   - If the user removes 1 quantity of this product, there should be 3 quantities of the product left in the basket.
   - If the user adds the same product 2 more times, there must be a single product in the basket with a quantity of 5.
   - Users cannot delete more products than they have in their basket. For example, if he has 5 quantities of a product
    in his basket, he cannot request the deletion of 6.
   - It must be possible to calculate the total price of the basket.

## Objective

The objective is to implement these rules following the `TDD (Test-Driven Development)` methodology to improve user basket management on the
shopping cart on the company's e-commerce site `Noelas` using PHP.