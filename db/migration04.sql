USE student_finance_db;

CREATE TABLE incomes (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT, source VARCHAR(100), 
    amount DECIMAL(12,2), income_date DATE, 
    note VARCHAR(255), 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
  );