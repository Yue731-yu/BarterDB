import random
import csv
from faker import Faker
import csv
import bcrypt
import hashlib
from datetime import datetime, timedelta

# Initialize Faker
fake = Faker()

# CSV files to store original passwords separately for users and admins
user_passwords_csv = "user_passwords.csv"
admin_passwords_csv = "admin_passwords.csv"

# Initialize CSV files with headers
with open(user_passwords_csv, mode='w', newline='', encoding='utf-8') as user_file:
    user_writer = csv.writer(user_file)
    user_writer.writerow(["username", "email", "original_password"])

with open(admin_passwords_csv, mode='w', newline='', encoding='utf-8') as admin_file:
    admin_writer = csv.writer(admin_file)
    admin_writer.writerow(["username", "email", "original_password"])

# Function to save data as CSV without headers
def save_data(filename, data):
    with open(f"data/{filename}.csv", mode="w", newline="", encoding="utf-8") as csv_file:
        writer = csv.writer(csv_file)
        writer.writerows(data)

# Function to generate a 6-digit password
def generate_password():
    return ''.join([str(random.randint(0, 9)) for _ in range(6)])

# Function to hash the password using bcrypt
def hash_password(password):
    return bcrypt.hashpw(password.encode(), bcrypt.gensalt()).decode()

# Function to adjust datetime to avoid DST conflicts
def safe_date_time(start_date, end_date):
    while True:
        dt = fake.date_time_between(start_date=start_date, end_date=end_date)
        try:
            # Ensure the datetime value is valid
            datetime.strptime(dt.strftime('%Y-%m-%d %H:%M:%S'), '%Y-%m-%d %H:%M:%S')
            return dt  # Return the valid datetime
        except ValueError:
            continue  # Retry if the datetime is invalid


# Generate data for User table with hashed password
def generate_users(n=100):
    users = []
    for user_id in range(1, n + 1):
        username = fake.unique.user_name()
        email = fake.unique.email()
        
        # Generate and hash the 6-digit password
        password = generate_password()
        password_hash = hash_password(password)

        # Save username, email, and original password to User CSV
        with open(user_passwords_csv, mode='a', newline='', encoding='utf-8') as user_file:
            user_writer = csv.writer(user_file)
            user_writer.writerow([username, email, password])

        phone_number = fake.phone_number()[:15]
        users.append((
            user_id,
            username,
            password_hash,
            email,
            phone_number,
            fake.address(),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S'),
            fake.random_element(['active'])
        ))
    save_data("User", users)
    return [user[0] for user in users]  # Return list of user_ids

# Generate data for Admin table with hashed password, including YueYu
def generate_admins(n=10):
    admins = []
    
    # Add YueYu with fixed email and password
    yueyu_username = "YueYu"
    yueyu_email = "yueyu@example.com"
    yueyu_password = "123456"
    yueyu_password_hash = hash_password(yueyu_password)
    
    # Save YueYu's original password to Admin CSV
    with open(admin_passwords_csv, mode='a', newline='', encoding='utf-8') as admin_file:
        admin_writer = csv.writer(admin_file)
        admin_writer.writerow([yueyu_username, yueyu_email, yueyu_password])

    # Add YueYu to the admins list
    admins.append((
        1,
        yueyu_username,
        yueyu_password_hash,
        yueyu_email,
        safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S'),
        fake.random_element(['active'])
    ))

    # Generate other admins
    for admin_id in range(2, n + 1):
        username = fake.user_name()
        email = fake.unique.email()
        
        # Generate and hash the 6-digit password
        password = generate_password()
        password_hash = hash_password(password)

        # Save admin username, email, and original password to Admin CSV
        with open(admin_passwords_csv, mode='a', newline='', encoding='utf-8') as admin_file:
            admin_writer = csv.writer(admin_file)
            admin_writer.writerow([username, email, password])

        admins.append((
            admin_id,
            username,
            password_hash,
            email,
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S'),
            fake.random_element(['active'])
        ))
    save_data("Admin", admins)

# Generate data for Item table
# Extended list of item names
item_names = [
    "laptop", "phone", "headphones", "backpack", "watch", "camera", "keyboard",
    "monitor", "mouse", "tablet", "printer", "book", "bicycle", "desk lamp",
    "speaker", "router", "microphone", "chair", "desk", "notebook", "pen",
    "calculator", "charger", "power bank", "hard drive", "USB drive", "smartwatch",
    "gaming console", "vacuum cleaner", "blender", "coffee maker", "toaster",
    "microwave", "oven", "refrigerator", "washing machine", "air conditioner",
    "heater", "fan", "humidifier", "dehumidifier", "flashlight", "drone",
    "electric scooter", "e-reader", "projector", "TV", "water bottle", "treadmill",
    "exercise bike", "yoga mat", "dumbbells", "kettle", "skateboard", "rollerblades",
    "tent", "sleeping bag", "camping stove", "toolbox", "screwdriver", "hammer",
    "wrench", "paintbrush", "saw", "measuring tape", "tripod", "binoculars",
    "guitar", "piano", "violin", "trumpet", "saxophone", "helmet", "sunglasses",
    "backpack", "suitcase", "wallet", "handbag", "umbrella", "blanket", "pillow",
    "bed sheets", "cutlery", "plate", "bowl", "mug", "glass", "vase", "picture frame",
    "mirror", "clock", "scissors", "stapler", "calculator", "whiteboard", "marker",
    "eraser", "pencil", "crayons", "paint", "canvas", "sketchbook", "toothbrush",
    "toothpaste", "shampoo", "conditioner", "soap", "body lotion", "face cream",
    "hair dryer", "shaver", "nail clipper", "tweezers", "first aid kit", "bandages",
    "thermometer", "blood pressure monitor", "stethoscope", "gloves", "mask",
    "hand sanitizer", "water filter", "solar panel", "battery charger", "flash drive",
    "multimeter", "oscilloscope", "soldering iron", "wire cutters", "pliers",
    "extension cord", "power strip", "light bulb", "candle", "lantern", "incense",
    "perfume", "jewelry", "bracelet", "necklace", "ring", "earrings", "belt",
    "hat", "scarf", "gloves", "boots", "slippers", "flip-flops", "sneakers",
    "sandals", "jacket", "sweater", "t-shirt", "pants", "shorts", "dress", "skirt",
    "coat", "socks", "underwear"
]
unit_choices = ["kg", "pcs", "liters", "ml", "meters", "cm", "inches", "oz", "grams"]

# Generate data for Item table with related name and description
def generate_items(user_ids, n=200):
    items = []
    for item_id in range(1, n + 1):
        name = random.choice(item_names)
        
        description = f"{name.capitalize()} - {fake.sentence(nb_words=10)}"
        
        items.append((
            item_id,
            random.choice(user_ids),
            name,
            description,
            round(random.uniform(1, 100), 2),
            round(random.uniform(1, 1000), 2),
            random.choice(unit_choices),
            fake.random_element(['available', 'pending', 'exchanged']),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S'),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S')
        ))
    save_data("Item", items)
    return [item[0] for item in items]  # Return list of item_ids

# Generate data for Equivalence table
# def generate_equivalence(item_ids, n=100):
#     equivalences = []
#     for equivalence_id in range(1, n + 1):
#         item_p = random.choice(item_ids)
#         item_e = random.choice(item_ids)
#         equivalences.append((
#             equivalence_id,
#             item_p,
#             item_e,
#             round(random.uniform(0.5, 1.5), 2),
#             round(random.uniform(0.01, 0.1), 2),
#             round(random.uniform(0.01, 0.1), 2)
#         ))
#     save_data("Equivalence", equivalences)

def generate_equivalence(item_data, n=20):
    """
    Generate equivalence relationships for items based on price similarity.

    :param item_data: List of tuples (item_id, price).
    :param n: Number of closest items to generate equivalence for.
    """
    equivalences = []

    # Iterate through each item to calculate price differences
    for item_id, price in item_data:
        # Calculate price differences with all other items
        price_differences = [
            (other_id, abs(price - other_price))
            for other_id, other_price in item_data if other_id != item_id
        ]

        # Sort by price difference and pick the top `n` closest items
        closest_items = sorted(price_differences, key=lambda x: x[1])[:n]

        # Generate equivalence records for each closest item
        for other_id, diff in closest_items:
            equivalences.append((
                len(equivalences) + 1,  # Auto-increment equivalence ID
                item_id,                # Primary item ID
                other_id,               # Equivalent item ID
                round(random.uniform(0.5, 1.5), 2),  # Equivalence rate
                round(random.uniform(0.01, 0.1), 2), # Cost rate for primary item
                round(random.uniform(0.01, 0.1), 2)  # Cost rate for equivalent item
            ))

    # Save equivalences to a CSV file
    save_data("Equivalence", equivalences)
    print(f"Generated {len(equivalences)} equivalences for {len(item_data)} items.")


# Generate data for Bulletin table
def generate_bulletins(user_ids, item_ids, n=100):
    bulletins = []
    for bulletin_id in range(1, n + 1):
        bulletins.append((
            bulletin_id,
            random.choice(user_ids),
            random.choice(item_ids),
            random.choice(item_ids),
            round(random.uniform(1, 50), 2),
            int(fake.boolean()),  # Convert True/False to 1/0 for MySQL compatibility
            fake.date_time_between(start_date='-1y', end_date='now').strftime('%Y-%m-%d %H:%M:%S'),
            fake.random_element(['active', 'matched', 'withdrawn'])
        ))
    save_data("Bulletin", bulletins)




# Generate data for Transaction table
import random
import hashlib
from faker import Faker
fake = Faker()

# Generate data for Transaction table
def generate_transactions(user_ids, item_ids, n=100):
    transactions = []
    for transaction_id in range(1, n + 1):
        # Ensure unique selection for user_a, user_x, user_b, and user_y
        user_a, user_x, user_b, user_y = random.sample(user_ids, 4)
        
        item_p = random.choice(item_ids)
        item_e = random.choice(item_ids)

        # Generate 16-character unique hash key
        hash_key = hashlib.sha1(fake.uuid4().encode()).hexdigest()[:16]
        leading_8 = hash_key[:8]
        trailing_8 = hash_key[8:]

        # Randomly select transaction status
        status = fake.random_element(['initiated', 'in_progress', 'completed', 'failed'])
        created_at = fake.date_time_between(start_date='-1y', end_date='now')
        updated_at = fake.date_time_between(start_date=created_at, end_date='now')

        # Set completed_at time logic, ensuring chronological order
        if status == 'completed':
            completed_at = fake.date_time_between(start_date=updated_at, end_date='now').strftime('%Y-%m-%d %H:%M:%S')
        else:
            completed_at = updated_at.strftime('%Y-%m-%d %H:%M:%S')  # Maintain chronological order

        transactions.append((
            transaction_id,
            hash_key,
            item_p,
            item_e,
            user_a,
            user_x,
            user_b,
            user_y,
            status,
            round(random.uniform(100, 500), 2),  # cost_p
            round(random.uniform(100, 500), 2),  # cost_e
            created_at.strftime('%Y-%m-%d %H:%M:%S'),
            updated_at.strftime('%Y-%m-%d %H:%M:%S'),
            completed_at,
            leading_8,
            trailing_8
        ))
    
    save_data("Transaction", transactions)
    return [transaction[0] for transaction in transactions]  # Return list of transaction_ids





# Generate data for TransactionLog table
def generate_transaction_logs(transaction_ids, n=100):
    logs = []
    actions = ['initiated', 'sent_P', 'sent_E', 'verified', 'completed', 'failed']
    for log_id in range(1, n + 1):
        logs.append((
            log_id,
            random.choice(transaction_ids),
            random.choice(actions),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S')
        ))
    save_data("TransactionLog", logs)


# Generate data for UserTransactionLog table
def generate_user_transaction_logs(user_ids, transaction_ids, n=100):
    logs = []
    roles = ['buyer', 'seller', 'partner']
    for log_id in range(1, n + 1):
        logs.append((
            log_id,
            random.choice(user_ids),
            random.choice(transaction_ids),
            random.choice(roles),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S')  # Use safe datetime generator
        ))
    save_data("UserTransactionLog", logs)


# Generate data for TransactionCost table
def generate_transaction_costs(transaction_ids, item_ids, n=100):
    costs = []
    for cost_id in range(1, n + 1):
        costs.append((
            cost_id,
            random.choice(transaction_ids),
            random.choice(item_ids),
            round(random.uniform(10, 100), 2),
            safe_date_time('-1y', 'now').strftime('%Y-%m-%d %H:%M:%S')
        ))
    save_data("TransactionCost", costs)

# Generate and save data for each table
user_ids = generate_users(100)
generate_admins(10)
item_ids = generate_items(user_ids, 1000)

# generate_equivalence(item_ids, 100)

def load_item_data_from_csv(file_path):
    item_data = []
    with open(file_path, mode='r', newline='', encoding='utf-8') as csv_file:
        reader = csv.reader(csv_file)  
        for row in reader:
            item_id = int(row[0])  # item_id
            price = float(row[5])  # value
            item_data.append((item_id, price))  
    return item_data

csv_file_path = "data/Item.csv"  
item_data = load_item_data_from_csv(csv_file_path)
generate_equivalence(item_data, n=20)

generate_bulletins(user_ids, item_ids, 100)
transaction_ids = generate_transactions(user_ids, item_ids, 1000)
generate_transaction_logs(transaction_ids, 2000)
generate_user_transaction_logs(user_ids, transaction_ids, 1500)
generate_transaction_costs(transaction_ids, item_ids, 1000)

print("Data generation and saving completed successfully for all tables.")
print(f"Original passwords saved to {user_passwords_csv} and {admin_passwords_csv}")
