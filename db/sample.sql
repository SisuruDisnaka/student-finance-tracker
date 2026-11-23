USE student_finance_db;

SET @u = (SELECT id FROM users WHERE email = 'demo@student.lk');
SET @m = DATE_FORMAT(CURDATE(), '%Y-%m-01');   -- first day of current month

-- Categories with monthly budgets
INSERT INTO categories (user_id, name, budget_amount) VALUES
 (@u, 'Food',          15000),
 (@u, 'Transport',      6000),
 (@u, 'Education',     10000),
 (@u, 'Rent',          15000),
 (@u, 'Utilities',      4000),
 (@u, 'Entertainment',  5000),
 (@u, 'Shopping',       6000),
 (@u, 'Health',         3000),
 (@u, 'Other',          2000);

-- Incomes
INSERT INTO incomes (user_id, source, amount, income_date, note) VALUES
 (@u, 'Allowance',     50000, @m,                         'Monthly allowance'),
 (@u, 'Part-time Job', 22000, DATE_ADD(@m, INTERVAL 4 DAY), 'Weekend tutoring'),
 (@u, 'Freelancing',    8000, DATE_ADD(@m, INTERVAL 9 DAY), 'Logo design');

-- Expenses (category looked up by name)
INSERT INTO expenses (user_id, category_id, expense_name, amount, expense_date, note)
SELECT @u, c.id, x.n, x.a, DATE_ADD(@m, INTERVAL x.d DAY), x.nt
FROM (
    SELECT 'Food' c, 'Groceries' n, 6500 a, 1 d, 'Supermarket' nt
    UNION ALL SELECT 'Food',          'Lunch',               850,  3, 'University cafeteria'
    UNION ALL SELECT 'Food',          'Dinner with friends', 4150, 6, NULL
    UNION ALL SELECT 'Transport',     'Bus pass',            3000, 1, 'Monthly pass'
    UNION ALL SELECT 'Transport',     'Taxi',                3300, 5, 'Late lab session'
    UNION ALL SELECT 'Education',     'Textbooks',           7500, 2, NULL
    UNION ALL SELECT 'Rent',          'Boarding house',     15000, 1, NULL
    UNION ALL SELECT 'Utilities',     'Internet',            2000, 2, NULL
    UNION ALL SELECT 'Entertainment', 'Movie night',         1800, 7, NULL
    UNION ALL SELECT 'Health',        'Pharmacy',             900, 8, NULL
) x
JOIN categories c ON c.user_id = @u AND c.name = x.c;

-- Saving plans
INSERT INTO saving_plans (user_id, name, description, target_amount, current_amount, target_date) VALUES
 (@u, 'New Laptop',     'For final-year projects',  250000, 100000, DATE_ADD(CURDATE(), INTERVAL 10 MONTH)),
 (@u, 'Trip to Ella',   'Weekend trip with friends', 50000,  20000, DATE_ADD(CURDATE(), INTERVAL 4 MONTH)),
 (@u, 'Emergency Fund', 'Just in case',             100000,  65000, DATE_ADD(CURDATE(), INTERVAL 12 MONTH));

-- Saving transactions (opening balance for each plan)
INSERT INTO saving_transactions (saving_plan_id, amount, transaction_date, note)
SELECT id, current_amount, CURDATE(), 'Opening balance'
FROM saving_plans WHERE user_id = @u;

-- Recurring items
INSERT INTO recurring_items (user_id, type, name, amount, category_id)
SELECT @u, 'income',  'Allowance', 50000, NULL
UNION ALL SELECT @u, 'expense', 'Rent',      15000, (SELECT id FROM categories WHERE user_id=@u AND name='Rent')
UNION ALL SELECT @u, 'expense', 'Internet',   2000, (SELECT id FROM categories WHERE user_id=@u AND name='Utilities')
UNION ALL SELECT @u, 'expense', 'Transport',  5000, (SELECT id FROM categories WHERE user_id=@u AND name='Transport');

-- Notifications
INSERT INTO notifications (user_id, title, message, type, is_read, ref_key) VALUES
 (@u, 'Welcome!',        'Welcome to your Student Finance tracker.',              'info',    0, 'welcome'),
 (@u, 'Budget warning',  'You have used over 70% of your Food budget this month.', 'warning', 0, 'budget_food'),
 (@u, 'Savings goal',    'Your Emergency Fund is 65% complete. Keep going!',       'success', 0, 'goal_emergency');
