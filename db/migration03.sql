USE student_finance_db;

CREATE TABLE saving_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    saving_plan_id INT,
    amount DECIMAL(12,2),
    transaction_date DATE,
    note VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (saving_plan_id) REFERENCES saving_plans(id) ON DELETE CASCADE
);
