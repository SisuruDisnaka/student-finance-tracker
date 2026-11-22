INSERT INTO categories (user_id, name) SELECT @u, name FROM default_categories;
UPDATE categories SET budget_amount = ELT(FIELD(name,'Food','Transport','Education','Rent','Utilities','Entertainment','Shopping','Health','Other'), 15000,6000,10000,15000,4000,5000,6000,3000,2000) WHERE user_id = @u;
