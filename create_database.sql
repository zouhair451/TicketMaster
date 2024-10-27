CREATE DATABASE event_tickets;

USE event_tickets;

CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    schedule DATETIME,
    prices JSON
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT(11),
    event_date VARCHAR(10),
    ticket_adult_price INT(11),
    ticket_adult_quantity INT(11),
    ticket_kid_price INT(11),
    ticket_kid_quantity INT(11),
    barcode VARCHAR(120),
    user_id INT(11),
    equal_price INT(11),
    created DATETIME,
    FOREIGN KEY (event_id) REFERENCES events(id)
);

CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    type VARCHAR(50),
    price INT,
    barcode VARCHAR(120) UNIQUE,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);
