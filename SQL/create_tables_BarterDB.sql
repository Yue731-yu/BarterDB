-- Create User table to store user information
CREATE TABLE User (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone_number VARCHAR(15),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'suspended', 'deleted') DEFAULT 'active'
);

-- Create Item table to store items available for trade
CREATE TABLE Item (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    quantity DECIMAL(10, 2) NOT NULL,
    value DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50),
    status ENUM('available', 'pending', 'exchanged') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id) ON DELETE CASCADE
);

-- Create Equivalence table to define item equivalence and trading costs
CREATE TABLE Equivalence (
    equivalence_id INT AUTO_INCREMENT PRIMARY KEY,
    item_p_id INT,                      -- ID of primary item in trade
    item_e_id INT,                      -- ID of equivalent item
    equivalence_rate DECIMAL(5, 2) NOT NULL, -- Equivalence rate between items
    cost_p_rate DECIMAL(5, 2) NOT NULL,      -- Cost rate for sender of primary item
    cost_e_rate DECIMAL(5, 2) NOT NULL,      -- Cost rate for sender of equivalent item
    FOREIGN KEY (item_p_id) REFERENCES Item(item_id),
    FOREIGN KEY (item_e_id) REFERENCES Item(item_id)
);

-- Create Bulletin table for users to post trade requests anonymously
CREATE TABLE Bulletin (
    bulletin_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    item_p_id INT,
    item_e_id INT,
    quantity DECIMAL(10, 2) NOT NULL,
    is_partial_trade BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'matched', 'withdrawn') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (item_p_id) REFERENCES Item(item_id),
    FOREIGN KEY (item_e_id) REFERENCES Item(item_id)
);

-- Create Transaction table to store trade details and ensure anonymity
CREATE TABLE Transaction (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    hash_key VARCHAR(16) NOT NULL UNIQUE,    -- Unique 16-character hash key for transaction
    item_p_id INT,                           -- Primary item being traded
    item_e_id INT,                           -- Equivalent item in exchange
    user_a_id INT,                           -- Buyer A
    user_x_id INT,                           -- Seller X
    user_b_id INT,                           -- Partner B in exchange
    user_y_id INT,                           -- Partner Y in exchange
    status ENUM('initiated', 'in_progress', 'completed', 'failed') DEFAULT 'initiated',
    cost_p DECIMAL(10, 2),                   -- Cost of primary item P
    cost_e DECIMAL(10, 2),                   -- Cost of equivalent item E
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at TIMESTAMP,
    leading_8 VARCHAR(8),                    -- Leading 8 characters of hash key (sent to A)
    trailing_8 VARCHAR(8),                   -- Trailing 8 characters of hash key (sent to Y)
    FOREIGN KEY (item_p_id) REFERENCES Item(item_id),
    FOREIGN KEY (item_e_id) REFERENCES Item(item_id),
    FOREIGN KEY (user_a_id) REFERENCES User(user_id),
    FOREIGN KEY (user_x_id) REFERENCES User(user_id),
    FOREIGN KEY (user_b_id) REFERENCES User(user_id),
    FOREIGN KEY (user_y_id) REFERENCES User(user_id)
);

-- Create TransactionLog table to record transaction actions and updates
CREATE TABLE TransactionLog (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    action ENUM('initiated', 'sent_P', 'sent_E', 'verified', 'completed', 'failed') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id)
);

-- Create Admin table to store administrator information
CREATE TABLE Admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'suspended') DEFAULT 'active'
);

-- Create UserTransactionLog table to track each user's role in transactions
CREATE TABLE UserTransactionLog (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    transaction_id INT,
    role ENUM('buyer', 'seller', 'partner') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id)
);

-- Create TransactionCost table to store costs associated with transactions
CREATE TABLE TransactionCost (
    cost_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    item_id INT,
    transfer_cost DECIMAL(10, 2) NOT NULL,  -- Cost of transferring item
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES Transaction(transaction_id),
    FOREIGN KEY (item_id) REFERENCES Item(item_id)
);
