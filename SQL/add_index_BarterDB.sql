-- Create indexes to improve query performance

-- User table indexes: Optimize searches and uniqueness checks on user information
CREATE INDEX idx_user_username ON User (username);   -- Search by username
CREATE INDEX idx_user_email ON User (email);         -- Search by email
CREATE INDEX idx_user_status ON User (status);       -- Filter by user status

-- Item table indexes: Improve joins and filters on item ownership and status
CREATE INDEX idx_item_user_id ON Item (user_id);     -- Join on user_id
CREATE INDEX idx_item_status ON Item (status);       -- Filter by item status
CREATE INDEX idx_item_name ON Item (name);           -- Search by item name

-- Equivalence table indexes: Facilitate lookup of equivalent items
CREATE INDEX idx_equivalence_item_p_id ON Equivalence (item_p_id);  -- Join on primary item
CREATE INDEX idx_equivalence_item_e_id ON Equivalence (item_e_id);  -- Join on equivalent item

-- Bulletin table indexes: Speed up searches for user posts and filter active trades
CREATE INDEX idx_bulletin_user_id ON Bulletin (user_id);         -- Join on user_id
CREATE INDEX idx_bulletin_item_p_id ON Bulletin (item_p_id);     -- Filter by primary item
CREATE INDEX idx_bulletin_item_e_id ON Bulletin (item_e_id);     -- Filter by equivalent item
CREATE INDEX idx_bulletin_status ON Bulletin (status);           -- Filter by trade status

-- Transaction table indexes: Enable faster joins, lookups, and status checks in transactions
CREATE INDEX idx_transaction_item_p_id ON Transaction (item_p_id);    -- Join on primary item
CREATE INDEX idx_transaction_item_e_id ON Transaction (item_e_id);    -- Join on equivalent item
CREATE INDEX idx_transaction_user_a_id ON Transaction (user_a_id);    -- Join on user A
CREATE INDEX idx_transaction_user_x_id ON Transaction (user_x_id);    -- Join on user X
CREATE INDEX idx_transaction_user_b_id ON Transaction (user_b_id);    -- Join on user B
CREATE INDEX idx_transaction_user_y_id ON Transaction (user_y_id);    -- Join on user Y
CREATE INDEX idx_transaction_status ON Transaction (status);          -- Filter by transaction status
CREATE INDEX idx_transaction_hash_key ON Transaction (hash_key);      -- Lookup by hash key

-- TransactionLog table indexes: Support transaction tracking and action status filtering
CREATE INDEX idx_transaction_log_transaction_id ON TransactionLog (transaction_id); -- Join on transaction_id
CREATE INDEX idx_transaction_log_action ON TransactionLog (action);                 -- Filter by action type

-- UserTransactionLog table indexes: Optimize search on user roles within transactions
CREATE INDEX idx_user_transaction_log_user_id ON UserTransactionLog (user_id);          -- Join on user_id
CREATE INDEX idx_user_transaction_log_transaction_id ON UserTransactionLog (transaction_id); -- Join on transaction_id
CREATE INDEX idx_user_transaction_log_role ON UserTransactionLog (role);                -- Filter by role

-- Admin table indexes: Improve search and filtering by unique admin attributes
CREATE INDEX idx_admin_username ON Admin (username);      -- Search by admin username
CREATE INDEX idx_admin_email ON Admin (email);            -- Search by admin email
CREATE INDEX idx_admin_status ON Admin (status);          -- Filter by admin status

-- TransactionCost table indexes: Optimize joins and searches on cost and transaction details
CREATE INDEX idx_transaction_cost_transaction_id ON TransactionCost (transaction_id);  -- Join on transaction_id
CREATE INDEX idx_transaction_cost_item_id ON TransactionCost (item_id);                -- Join on item_id
