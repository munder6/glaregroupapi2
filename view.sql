CREATE OR REPLACE VIEW itemsview AS
SELECT items.* , categories.*, (items_price - (items_price * items_descount / 100)) as itemspricedescount FROM items
INNER JOIN categories on items.items_cat = categories.categories_id;


CREATE OR REPLACE VIEW myfavorite AS
SELECT favorite.*, items.*, users.users_id FROM favorite
INNER JOIN users ON users.users_id = favorite.favorite_usersid
INNER JOIN items ON items.items_id = favorite.favorite_itemsid


CREATE OR REPLACE VIEW cartview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100) as itemsprice , COUNT(cart.cart_itemid) as countitems, cart.* , items.* FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemid
WHERE cart_orders = 0
GROUP BY cart.cart_itemid, cart.cart_usersid, cart.cart_orders;

CREATE OR REPLACE VIEW cartview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100) as itemsprice, COUNT(cart.cart_itemid) as countitems, cart.* , items.* FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemid
GROUP BY cart.cart_itemid, cart.cart_usersid


CREATE OR REPLACE VIEW orderdetailsview AS
SELECT SUM(items.items_price - items.items_price * items.items_descount / 100) as itemsprice , COUNT(cart.cart_itemid) as countitems, cart.* , items.* FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemid
WHERE cart_orders !=0
GROUP BY cart.cart_itemid, cart.cart_usersid, cart.cart_orders;

CREATE OR REPLACE VIEW ordersview AS
SELECT orders.* , address.* FROM orders 
LEFT JOIN address on address.address_id = orders.orders_address


CREATE OR REPLACE view itemstopselling AS
SELECT COUNT(cart_id) as countitems , cart.*, items.* , (items_price - (items_price * items_descount / 100)) as itemspricedescount FROM cart
INNER JOIN items ON items.items_id = cart.cart_itemid
WHERE cart_orders != 0
GROUP BY cart_itemid