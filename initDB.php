<?php

$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASSWORD = '12345';
$DB_NAME = 'BarterDB';
$DB_PORT = 3306;

$connection = null;

try {
    // Connect to MySQL
    $connection = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, '', $DB_PORT);

    // Check for connection errors
    if ($connection->connect_error) {
        throw new Exception('Connection failed: ' . $connection->connect_error);
    }

    echo "Connected to MySQL\n";

    // Drop the database if it exists and create a new one
    $dropDatabaseQuery = "DROP DATABASE IF EXISTS `" . $DB_NAME . "`";
    if (!$connection->query($dropDatabaseQuery)) {
        throw new Exception('Error dropping database: ' . $connection->error);
    }
    echo "Database dropped successfully\n";

    // Create a new database
    $createDatabaseQuery = "CREATE DATABASE `" . $DB_NAME . "`";
    if (!$connection->query($createDatabaseQuery)) {
        throw new Exception('Error creating database: ' . $connection->error);
    }
    echo "Database created successfully\n";

    // Select the newly created database
    $connection->select_db($DB_NAME);

    // Read and execute SQL files
    $createTablesSQL = file_get_contents('sql/create_tables_BarterDB.sql');
    $createIndexesSQL = file_get_contents('sql/add_index_BarterDB.sql');

    // Split SQL statements and filter empty statements
    $createTableStatements = array_filter(array_map('trim', explode(';', $createTablesSQL)));
    $createIndexStatements = array_filter(array_map('trim', explode(';', $createIndexesSQL)), function ($statement) {
        return !empty($statement) && strpos($statement, 'CREATE TRIGGER') !== 0;
    });

    // Helper function to execute SQL statements
    function executeSQL($connection, $statements, $description) {
        foreach ($statements as $statement) {
            try {
                echo "Executing SQL statement: " . trim($statement) . "\n";
                if (!$connection->query($statement)) {
                    throw new Exception('Error executing ' . $description . ' SQL: ' . $connection->error);
                }
            } catch (Exception $e) {
                echo $e->getMessage() . "\n";
                throw $e; // Stop further execution if an error occurs
            }
        }
    }

    // Execute SQL statements to create tables
    executeSQL($connection, $createTableStatements, 'table creation');
    echo "Tables created successfully\n";

    // Execute SQL statements to create indexes
    executeSQL($connection, $createIndexStatements, 'index creation');
    echo "Indexes created successfully\n";

} catch (Exception $e) {
    // Handle exceptions and display error message
    echo 'An error occurred: ' . $e->getMessage() . "\n";
} finally {
    // Close the connection if it is open
    if ($connection) {
        $connection->close();
        echo "Connection closed\n";
    }
}
