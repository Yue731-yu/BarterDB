import csv
import mysql.connector

# Database connection configuration
db_config = {
    'host': 'localhost',
    'user': 'root',
    'password': '12345',
    'database': 'BarterDB'
}

# Establish database connection
connection = mysql.connector.connect(**db_config)
cursor = connection.cursor()

# General function: Load data from a CSV file without headers
def load_csv_data(file_path):
    with open(file_path, mode='r', newline='', encoding='utf-8') as file:
        reader = csv.reader(file)
        data = [tuple(row) for row in reader]  # Read each row as a tuple
    return data  # Return all rows as a list of tuples

# General function: Truncate a table (delete all records)
def truncate_table(table_name):
    # Temporarily disable foreign key checks
    cursor.execute("SET FOREIGN_KEY_CHECKS = 0")
    # Delete all records in the table
    cursor.execute(f"DELETE FROM {table_name}")
    connection.commit()
    # Re-enable foreign key checks
    cursor.execute("SET FOREIGN_KEY_CHECKS = 1")
    print(f"Table {table_name} has been truncated.")
    
# General function: Insert data into a table
def insert_data(table_name, insert_query, data):
    # Check if the table has existing data
    cursor.execute(f"SELECT COUNT(*) FROM {table_name}")
    count = cursor.fetchone()[0]

    # If the table is not empty, truncate it
    if count > 0:
        truncate_table(table_name)

    # Insert new data
    cursor.executemany(insert_query, data)
    connection.commit()
    print(f"{len(data)} records have been inserted into {table_name}.")

# Insert User table data
users = load_csv_data("data/User.csv")
insert_data(
    "User",
    """
    INSERT INTO User (user_id, username, password_hash, email, phone_number, address, created_at, status)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """,
    users
)

# Insert Admin table data
admins = load_csv_data("data/Admin.csv")
insert_data(
    "Admin",
    """
    INSERT INTO Admin (admin_id, username, password_hash, email, created_at, status)
    VALUES (%s, %s, %s, %s, %s, %s)
    """,
    admins
)

# Insert Item table data
items = load_csv_data("data/Item.csv")
insert_data(
    "Item",
    """
    INSERT INTO Item (item_id, user_id, name, description, quantity, value, unit, status, created_at, updated_at)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """,
    items
)

# Insert Equivalence table data
equivalences = load_csv_data("data/Equivalence.csv")
insert_data(
    "Equivalence",
    """
    INSERT INTO Equivalence (equivalence_id, item_p_id, item_e_id, equivalence_rate, cost_p_rate, cost_e_rate)
    VALUES (%s, %s, %s, %s, %s, %s)
    """,
    equivalences
)

# Insert Bulletin table data
bulletins = load_csv_data("data/Bulletin.csv")
insert_data(
    "Bulletin",
    """
    INSERT INTO Bulletin (bulletin_id, user_id, item_p_id, item_e_id, quantity, is_partial_trade, created_at, status)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
    """,
    bulletins
)

# Insert Transaction table data
transactions = load_csv_data("data/Transaction.csv")
insert_data(
    "Transaction",
    """
    INSERT INTO Transaction (transaction_id, hash_key, item_p_id, item_e_id, user_a_id, user_x_id, user_b_id, user_y_id, status, cost_p, cost_e, created_at, updated_at, completed_at, leading_8, trailing_8)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    """,
    transactions
)

# Insert TransactionLog table data
logs = load_csv_data("data/TransactionLog.csv")
insert_data(
    "TransactionLog",
    """
    INSERT INTO TransactionLog (log_id, transaction_id, action, created_at)
    VALUES (%s, %s, %s, %s)
    """,
    logs
)

# Insert UserTransactionLog table data
user_logs = load_csv_data("data/UserTransactionLog.csv")
insert_data(
    "UserTransactionLog",
    """
    INSERT INTO UserTransactionLog (log_id, user_id, transaction_id, role, created_at)
    VALUES (%s, %s, %s, %s, %s)
    """,
    user_logs
)

# Insert TransactionCost table data
costs = load_csv_data("data/TransactionCost.csv")
insert_data(
    "TransactionCost",
    """
    INSERT INTO TransactionCost (cost_id, transaction_id, item_id, transfer_cost, created_at)
    VALUES (%s, %s, %s, %s, %s)
    """,
    costs
)

# Close the database connection
cursor.close()
connection.close()

print("Data successfully written to the database.")