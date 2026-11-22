USE student_finance_db;

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(60),
    budget_amount DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    category_id INT,
    expense_name VARCHAR(100),
    amount DECIMAL(12,2),
    expense_date DATE,
    note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

CREATE TABLE saving_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100),
    description VARCHAR(255),
    target_amount DECIMAL(12,2),
    current_amount DECIMAL(12,2) DEFAULT 0,
    target_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recurring_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    type ENUM('income','expense'),
    name VARCHAR(100),
    amount DECIMAL(12,2),
    category_id INT NULL,
    active TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    title VARCHAR(100),
    message VARCHAR(255),
    type VARCHAR(20) DEFAULT 'info',
    is_read TINYINT DEFAULT 0,
    ref_key VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY (user_id, ref_key),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
INSERT INTO expenses (user_id, category_id, expense_name, amount, expense_date, note)
SELECT @u, c.id, x.n, x.a, DATE_ADD(@m, INTERVAL x.d DAY), x.nt FROM (
  SELECT 'Food' c,'Groceries' n,6500 a,1 d,'Supermarket' nt UNION ALL SELECT 'Food','Lunch',850,3,'University cafeteria' UNION ALL SELECT 'Food','Dinner with friends',4150,6,NULL
  UNION ALL SELECT 'Transport','Bus pass',3000,1,'Monthly pass' UNION ALL SELECT 'Transport','Taxi',3300,5,'Late lab session' UNION ALL SELECT 'Education','Textbooks',7500,2,NULL
  UNION ALL SELECT 'Rent','Boarding house',15000,1,NULL UNION ALL SELECT 'Utilities','Internet',2000,2,NULL UNION ALL SELECT 'Entertainment','Movie night',1800,7,NULL
  UNION ALL SELECT 'Health','Pharmacy',900,8,NULL) x JOIN categories c ON c.user_id = @u AND c.name = x.c;