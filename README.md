## <span style="color:skyblue;">📒 Deployment Steps</span>
### 1. Prerequisites
1.1 **Python**: Install necessary Python packages.
   ```bash
   pip install faker mysql-connector-python bcrypt
   ```

1.2 **JavaScript**: Install the required Node.js packages.
   ```bash
   npm install mysql2 dotenv
   ```
   (If `initDB.js` is not being used, this step is not necessary.)

1.3 Ensure that the MySQL database credentials in `includes/db_connect.php` and the `.env` file match your local setup.

### 2. Database Setup
Run `initDB.php` or `initDB.js` to execute the `create_tables.sql` and `insert_sample_data.sql` scripts, which will create the database `BarterBD` and insert indexes. Both files perform the same operations, one in JavaScript and the other in PHP.

Alternatively, you can manually import the two SQL files in XAMPP:
- Access [http://localhost/phpmyadmin](http://localhost/phpmyadmin), then manually import the SQL files.

### 3. Data Generation
Run `Data_ETL.py` to generate data streams with the `faker` package and insert data into the database.

Alternatively, run `Data_ETL_noinsert.py` to generate CSV files for the data. You can then manually import the CSV files in XAMPP, ensuring the following import order due to foreign key constraints:

1. User
2. Admin
3. Item
4. Equivalence
5. Bulletin
6. Transaction
7. TransactionLog
8. UserTransactionLog
9. TransactionCost

### 4. Running the Project
4.1 To run from the terminal or an IDE like VSCode, start the PHP server and run `index.php` to access the web application home page.

4.2 To run with XAMPP:
   - Place the `BarterDB` folder in the `htdocs` folder of your XAMPP installation (e.g., `C:\xampp\htdocs\BarterDB`).
   - Access the project in your browser at [http://localhost/BarterDB](http://localhost/BarterDB).

### 5. Additional Notes
5.1 Registration through the DBMS web interface is restricted to user accounts only. Admin accounts must be added directly in the database. Initially, the account with my name "YueYu" has been set as an admin with the password `123456`.


## <span style="color:orange;">🎨 Project Structure</span>
```
BarterDB/
│
├── assets/
│   ├── css/
│   │   ├── style.css               # General styles for the entire project
│   │   ├── dashboard.css           # Styles for user dashboard
│   │   ├── item.css                # Styles for item posting and management pages
│   │   └── admin.css               # Styles for admin dashboard
│   └── images/                     # Image assets for the project
│
├── public/
│   ├── index.php                   # Homepage
│   ├── register.php                # User registration page
│   ├── login.php                   # User login page
│   ├── dashboard.php               # User dashboard page
│   ├── post_item.php               # Item posting page
│   ├── match_trade.php             # Trade matching page
│   ├── transaction_detail.php      # Transaction details page
│   └── transaction_history.php     # Transaction history page
│
├── includes/
│   ├── db_connect.php              # Database connection
│   ├── functions.php               # Common helper functions
│   └── config.php                  # Global configuration file
│
├── controllers/
│   ├── userController.php          # Handles user registration, login, and account management
│   ├── itemController.php          # Handles item posting and management
│   └── transactionController.php   # Manages trade matching and transaction history
│
├── views/
│   ├── header.php                  # Header template, included in all pages
│   ├── footer.php                  # Footer template, included in all pages
│   ├── user_dashboard.php          # User dashboard view
│   ├── post_item_form.php          # Item posting form
│   ├── match_trade_form.php        # Trade matching form
│   ├── transaction_detail.php      # Transaction detail view
│   ├── admin_dashboard.php         # Admin dashboard view
│   └── transaction_history.php     # Transaction history view
│
├── initDB.php                      # Database initialization script
├── Data_ETL.py                     # Data extraction and loading (ETL) script
├── SQL/                             # Folder for SQL scripts
│   ├── create_tables.sql           # SQL script to create database tables
│   ├── insert_sample_data.sql      # SQL script to insert sample data
│   └── other_queries.sql           # Additional SQL queries
│
└── README.md                       # Project documentation
```