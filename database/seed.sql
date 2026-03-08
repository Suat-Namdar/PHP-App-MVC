INSERT INTO roles (name) VALUES ('Admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO permissions (name) VALUES ('customers.view')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO users (name, email, password)
VALUES ('Super Admin', 'admin@example.com', '$2y$10$JEgAcNusiSYvrU9JEbg0ieEXuY6sbf.rcchx3zOFkJKV6HsiQhrPO')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT IGNORE INTO role_user (role_id, user_id)
SELECT r.id, u.id
FROM roles r
CROSS JOIN users u
WHERE r.name = 'Admin' AND u.email = 'admin@example.com';

INSERT IGNORE INTO permission_role (permission_id, role_id)
SELECT p.id, r.id
FROM permissions p
CROSS JOIN roles r
WHERE p.name = 'customers.view' AND r.name = 'Admin';

INSERT INTO customers (name, email, phone) VALUES
('Ahmet Yilmaz', 'ahmet@example.com', '05550000001'),
('Ayse Demir', 'ayse@example.com', '05550000002'),
('Mehmet Kaya', 'mehmet@example.com', '05550000003'),
('Fatma Celik', 'fatma@example.com', '05550000004'),
('Ali Can', 'ali@example.com', '05550000005')
ON DUPLICATE KEY UPDATE name = VALUES(name);
