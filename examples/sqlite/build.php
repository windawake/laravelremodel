<?php
$sqlite = new SQLite3('test.db');

$sql =<<<EOF
    DROP TABLE IF EXISTS `common_status`;
    CREATE TABLE `common_status`(
        id INT PRIMARY KEY,
        store_id INT NOT NULL DEFAULT 1,
        status_id INT NOT NULL DEFAULT 0,
        name varchar(32) NOT NULL DEFAULT ''
    );

    INSERT INTO `common_status` (id, store_id, status_id, name) VALUES (1, 1, 0, '未审核');
    INSERT INTO `common_status` (id, store_id, status_id, name) VALUES (2, 1, 1, '初审');
    INSERT INTO `common_status` (id, store_id, status_id, name) VALUES (3, 1, 2, '复审');
    INSERT INTO `common_status` (id, store_id, status_id, name) VALUES (4, 1, 3, '已通过');
    INSERT INTO `common_status` (id, store_id, status_id, name) VALUES (5, 1, 4, '已驳回');

    DROP TABLE IF EXISTS `product`;
    CREATE TABLE product(
        pid INT PRIMARY KEY,
        store_id INT NOT NULL DEFAULT 1,
        product_name varchar(32) NOT NULL DEFAULT '',
        status INT NOT NULL DEFAULT 0,
        delete_time INT
    );

    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (1, 1, 'computer', 1, 111);
    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (2, 1, '火锅底料', 0, 222);
    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (3, 1, 'まんが', 1, null);
    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (4, 1, '携帯電話', 2, 111);
    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (5, 2, '风扇', 1, 333);
    INSERT INTO product (pid, store_id, product_name, status, delete_time) VALUES (6, 2, '玩具', 1, 111);

    DROP TABLE IF EXISTS `order`;
    CREATE TABLE `order`(
        o_id INT PRIMARY KEY,
        store_id INT NOT NULL DEFAULT 1,
        order_number varchar(32) NOT NULL DEFAULT '',
        status INT NOT NULL DEFAULT 0,
        created_date DATETIME
    );

    INSERT INTO `order` (o_id, store_id, order_number, status, created_date) VALUES (1, 1, 'no001', 1, '2021-09-01 08:40:50');
    INSERT INTO `order` (o_id, store_id, order_number, status, created_date) VALUES (2, 2, 'no002', 2, '2021-09-02 09:00:00');
    INSERT INTO `order` (o_id, store_id, order_number, status, created_date) VALUES (3, 3, 'no003', 3, '2021-09-05 10:00:00');

    DROP TABLE IF EXISTS `order_detail`;
    CREATE TABLE `order_detail`(
        od_id INT PRIMARY KEY,
        store_id INT NOT NULL DEFAULT 1,
        o_id INT NOT NULL DEFAULT 0,
        product_id INT NOT NULL DEFAULT 0,
        status INT NOT NULL DEFAULT 0
    );

    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (1, 1, 1, 1, 1);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (2, 1, 1, 2, 2);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (3, 2, 2, 1, 3);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (4, 2, 2, 2, 4);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (5, 1, 3, 3, 1);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (6, 1, 3, 4, 2);
    INSERT INTO `order_detail` (od_id, store_id, o_id, product_id, status) VALUES (7, 1, 4, 5, 3);
EOF;

$ret = $sqlite->exec($sql);
if(!$ret){
    echo $sqlite->lastErrorMsg();
} else {
    echo "Records created successfully\n";
}
$sqlite->close();