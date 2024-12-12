// Import required modules
const fs = require('fs');
const mysql = require('mysql2/promise');
require('dotenv').config();

// Load environment variables from .env file
const DB_HOST = process.env.DB_HOST;
const DB_USER = process.env.DB_USER;
const DB_PASSWORD = process.env.DB_PASSWORD;
const DB_NAME = process.env.DB_NAME;
const DB_PORT = process.env.DB_PORT || 3306;

(async () => {
  let connection;

  try {
    // Create a connection to MySQL
    connection = await mysql.createConnection({
      host: '127.0.0.1',
      user: DB_USER,
      password: DB_PASSWORD,
      port: DB_PORT,
    });

    console.log('Connected to MySQL');

    // Drop the database if it exists and create a new one
    await connection.query(`DROP DATABASE IF EXISTS \`${DB_NAME}\`;`);
    console.log('Database dropped successfully');

    await connection.query(`CREATE DATABASE \`${DB_NAME}\`;`);
    console.log('Database created successfully');

    // Use the created database
    await connection.changeUser({ database: DB_NAME });

    // Read and execute SQL files
    const createTablesSQL = fs.readFileSync('sql/create_tables_BarterDB.sql', 'utf8');
    const createIndexesSQL = fs.readFileSync('sql/add_index_BarterDB.sql', 'utf8');

    const createTableStatements = createTablesSQL.split(';').filter(statement => statement.trim() !== '');
    const createIndexStatements = createIndexesSQL.split(';').filter(statement => statement.trim() !== '' && !statement.trim().startsWith('CREATE TRIGGER'));

    // Helper function to execute SQL statements
    const executeSQL = async (statements, description) => {
      for (const statement of statements) {
        try {
          console.log(`Executing SQL statement: ${statement.trim()}`);
          await connection.query(statement);
        } catch (err) {
          console.error(`Error executing ${description} SQL:`, err);
          throw err; // Stop further execution if an error occurs
        }
      }
    };

    // Execute SQL statements to create tables
    await executeSQL(createTableStatements, 'table creation');
    console.log('Tables created successfully');

    // Execute SQL statements to create indexes
    await executeSQL(createIndexStatements, 'index creation');
    console.log('Indexes created successfully');

  } catch (err) {
    console.error('An error occurred:', err);
  } finally {
    if (connection) {
      await connection.end();
      console.log('Connection closed');
    }
  }
})();
