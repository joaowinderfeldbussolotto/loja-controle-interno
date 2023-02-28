CREATE TABLE IF NOT EXISTS Customer (
   CustomerID SERIAL,
   Name varchar(200) NOT NULL,
   Birthday date NOT NULL,
   RG varchar NOT NULL,
   CPF varchar NOT NULL,
   Address varchar NOT NULL,
   Civil_state varchar NOT NULL,
   spouse varchar NULL,
   filiation varchar NULL,
   note varchar NULL,
   status varchar NULL,
   CONSTRAINT pk_Customer PRIMARY KEY (CustomerID),
   CONSTRAINT uc_Customer_RG UNIQUE (RG),
   CONSTRAINT uc_Customer_CPF UNIQUE (CPF)
); 

CREATE TABLE IF NOT EXISTS Customer_Phone (
   PhoneID serial,
   CustomerID int NOT NULL,
   Phone varchar NOT NULL,
   CONSTRAINT pk_Customer_Phone PRIMARY KEY (PhoneID)
); 

CREATE TABLE IF NOT EXISTS Brand (
   BrandID serial,
   Name varchar NOT NULL,
   CONSTRAINT pk_Brand PRIMARY KEY (BrandID)
); 

CREATE TABLE IF NOT EXISTS "Type" (
   TypeID serial,
   Description varchar NOT NULL,
   CONSTRAINT pk_Type PRIMARY KEY (TypeID)
); 

CREATE TABLE IF NOT EXISTS Product (
   id serial,
   Description varchar NOT NULL,
   "Type" int NOT NULL,
   BrandID int NOT NULL,
   Size varchar NOT NULL,
   Color varchar NOT NULL,
   Price varchar NOT NULL,
   Quantity int NOT NULL,
   note varchar NOT NULL,
   CONSTRAINT pk_Product PRIMARY KEY (id)
); 

CREATE TABLE IF NOT EXISTS Payment_method (
   id serial,
   Description varchar NOT NULL,
   CONSTRAINT pk_Payment_method PRIMARY KEY (id)
); 

CREATE TABLE IF NOT EXISTS Sale (
   SaleID serial,
   CostumerID int NOT NULL,
   status varchar NOT NULL,
   date date NOT NULL,
   conditional bool NOT NULL,
   note varchar NOT NULL,
   CONSTRAINT pk_Sale PRIMARY KEY (SaleID)
); 

CREATE TABLE IF NOT EXISTS Sale_Item (
   id serial,
   SaleID int NOT NULL,
   ProductID int NOT NULL,
   quantity int NOT NULL,
   discount money NOT NULL,
   price money NOT NULL,
   CONSTRAINT pk_Sale_Item PRIMARY KEY (id)
); 

ALTER TABLE
   Sale_Item
ADD
   UNIQUE (SaleID, ProductID); 

CREATE TABLE IF NOT EXISTS Payment (
   id serial,
   SaleID int NOT NULL,
   payer varchar NOT NULL,
   value money NOT NULL,
   payment_method int NOT NULL,
   date date NOT NULL,
   CONSTRAINT pk_Payment PRIMARY KEY (id)
); 

CREATE TABLE IF NOT EXISTS "User" (
   id serial,
   name varchar NOT NULL,
   cpf varchar NOT NULL,
   login varchar NOT NULL,
   email varchar NOT NULL,
   password varchar NOT NULL,
   permission_level int NOT NULL,
   CONSTRAINT pk_User PRIMARY KEY (id),
   CONSTRAINT uc_User_cpf UNIQUE (cpf),
   CONSTRAINT uc_User_login UNIQUE (login)
); 

ALTER TABLE
   Customer_Phone
ADD
   CONSTRAINT fk_Customer_Phone_CustomerID FOREIGN KEY(CustomerID) REFERENCES Customer (CustomerID) ON DELETE CASCADE;

ALTER TABLE
   Product
ADD
   CONSTRAINT fk_Product_Type FOREIGN KEY("Type") REFERENCES "Type" (TypeID); 

ALTER TABLE
   Product
ADD
   CONSTRAINT fk_Product_BrandID FOREIGN KEY(BrandID) REFERENCES Brand (BrandID); 

ALTER TABLE
   Sale
ADD
   CONSTRAINT fk_Sale_CostumerID FOREIGN KEY(CostumerID) REFERENCES Customer (CustomerID) ON DELETE CASCADE;

ALTER TABLE
   Sale_Item
ADD
   CONSTRAINT fk_Sale_Item_SaleID FOREIGN KEY(SaleID) REFERENCES Sale (SaleID); 

ALTER TABLE
   Sale_Item
ADD
   CONSTRAINT fk_Sale_Item_ProductID FOREIGN KEY(ProductID) REFERENCES Product (id); 

ALTER TABLE
   Payment
ADD
   CONSTRAINT fk_Payment_SaleID FOREIGN KEY(SaleID) REFERENCES Sale (SaleID) ON delete cascade;

ALTER TABLE
   Payment
ADD
   CONSTRAINT fk_Payment_payment_method FOREIGN KEY(payment_method) REFERENCES Payment_method (id); 

CREATE INDEX idx_Customer_Name ON Customer (Name); 

-- Populate the Customer table
-- Inserting data into the Customer table
INSERT INTO
   Customer (
      Name,
      Birthday,
      RG,
      CPF,
      Address,
      Civil_state,
      spouse,
      filiation,
      note,
      status
   )
VALUES
   (
      'John Doe',
      '1980-01-01',
      '12345678',
      '10242757952',
      '123 Main St',
      'Married',
      'Jane Doe',
      'Mary Doe; Joe Doe',
      NULL,
      'Active'
   ),
   (
      'Jane Doe',
      '1985-05-10',
      '87654321',
      '09876543210',
      '456 Elm St',
      'Single',
      NULL,
      'Susan Doe; Bob Doe',
      NULL,
      'Inactive'
   ),
   (
      'Bob Smith',
      '1990-12-15',
      '45678901',
      '23456787012',
      '789 Oak St',
      'Divorced',
      'Emily Smith',
      NULL,
      NULL,
      'Active'
   ),
   (
      'Emily Smith',
      '1992-08-20',
      '90123456',
      '34567890123',
      '567 Maple St',
      'Married',
      'Bob Smith',
      NULL,
      'Has a dog',
      'Inactive'
   ),
   (
      'Mark Johnson',
      '1977-03-25',
      '34567890',
      '45678901234',
      '321 Pine St',
      'Widowed',
      NULL,
      NULL,
      'Frequent customer',
      'Active'
   ),
   (
      'Sara Wilson',
      '1995-07-08',
      '78901234',
      '89012345678',
      '111 Cedar St',
      'Single',
      NULL,
      'Susan Wilson; Tom Wilson',
      NULL,
      'Inactive'
   ),
   (
      'Tom Wilson',
      '1991-01-31',
      '23456789',
      '56789012345',
      '222 Walnut St',
      'Married',
      'Sara Wilson',
      NULL,
      NULL,
      'Active'
   ),
   (
      'Susan Wilson',
      '1969-12-05',
      '56789012',
      '90123456789',
      '444 Cherry St',
      'Married',
      'Bob Wilson',
      'Emily Wilson; Alice Wilson',
      'Loyal customer',
      'Inactive'
   ),
   (
      'Bob Wilson',
      '1971-06-13',
      '89012345',
      '12345678901',
      '555 Birch St',
      'Divorced',
      'Susan Wilson',
      NULL,
      NULL,
      'Active'
   ),
   (
      'Alice Wilson',
      '2000-04-17',
      '12345178',
      '23456789012',
      '777 Oak St',
      'Single',
      NULL,
      NULL,
      'New customer',
      'Inactive'
   ); 

;

-- Inserting data into the Customer_Phone table
INSERT INTO
   Customer_Phone (CustomerID, Phone)
VALUES
   (1, '(123) 456-7890'),
   (1, '(234) 567-8901'),
   (2, '(345) 678-9012'),
   (3, '(456) 789-0123'),
   (3, '(567) 890-1234'),
   (4, '(678) 901-2345'),
   (5, '(789) 012-3456'),
   (6, '(890) 123-4567'),
   (7, '(901) 234-5678'),
   (8, '(012) 345-6789'),
   (8, '(123) 456-7890'),
   (9, '(234) 567-8901'),
   (10, '(345) 678-9012'); 

-- Inserting data into the Brand table
INSERT INTO
   Brand (Name)
VALUES
   ('Nike'),
   ('Adidas'),
   ('Puma'),
   ('Reebok'),
   ('Levis'),
   ('Calvin Klein'),
   ('Tommy Hilfiger'),
   ('Ralph Lauren'),
   ('Diesel'),
   ('Gucci'); 

-- "Type" table
INSERT INTO
   "Type" (Description)
VALUES
   ('Shirt'),
   ('T-Shirt'),
   ('Pants'),
   ('Shorts'),
   ('Dress'),
   ('Skirt'),
   ('Jacket'),
   ('Sweater'),
   ('Coat'),
   ('Socks'); 

-- "Product" table
INSERT INTO
   Product (
      Description,
      "Type",
      BrandID,
      Size,
      Color,
      Price,
      Quantity,
      note
   )
VALUES
   (
      'Cotton T-Shirt',
      2,
      1,
      'M',
      'White',
      '29.99',
      20,
      'Classic T-Shirt made of soft cotton'
   ),
   (
      'Denim Shorts',
      4,
      1,
      '32',
      'Blue',
      '59.99',
      10,
      'Stylish denim shorts with distressed finish'
   ),
   (
      'Leather Jacket',
      7,
      2,
      'L',
      'Black',
      '399.99',
      5,
      'High-quality genuine leather jacket'
   ),
   (
      'Floral Dress',
      5,
      3,
      'S',
      'Multicolor',
      '89.99',
      15,
      'Elegant floral dress with a fitted waist'
   ),
   (
      'Chino Pants',
      3,
      4,
      '30',
      'Beige',
      '69.99',
      10,
      'Comfortable and stylish chino pants'
   ),
   (
      'Cashmere Sweater',
      8,
      5,
      'M',
      'Gray',
      '149.99',
      8,
      'Luxurious and soft cashmere sweater'
   ),
   (
      'Wool Coat',
      9,
      6,
      'XL',
      'Navy',
      '249.99',
      6,
      'Warm and stylish wool coat for the winter'
   ),
   (
      'Striped Skirt',
      6,
      7,
      'S',
      'Red/White',
      '39.99',
      12,
      'Fun and playful striped skirt'
   ),
   (
      'Sports Socks',
      10,
      8,
      'L',
      'Black',
      '9.99',
      30,
      'Comfortable and breathable sports socks'
   ),
   (
      'Silk Blouse',
      1,
      9,
      'XS',
      'Pink',
      '79.99',
      20,
      'Elegant and lightweight silk blouse'
   ); 

INSERT INTO
   Payment_method (Description)
VALUES
   ('Credit card'),
   ('Debit card'),
   ('Cash'),
   ('Bank transfer'); 

-- Sale data
INSERT INTO
   Sale (CostumerID, status, date, conditional, note)
VALUES
   (1, 'Closed', '2022-12-20', false, 'Paid in full'),
   (
      2,
      'Open',
      '2023-02-28',
      true,
      'To be paid in two installments'
   ),
   (3, 'Closed', '2022-11-10', false, 'Paid in full'),
   (
      4,
      'Open',
      '2023-03-15',
      false,
      'Pending payment'
   ),
   (5, 'Closed', '2022-12-05', false, 'Paid in full'),
   (6, 'Closed', '2022-10-01', false, 'Paid in full'),
   (
      7,
      'Open',
      '2023-04-10',
      true,
      'To be paid in three installments'
   ),
   (8, 'Closed', '2022-09-18', false, 'Paid in full'),
   (9, 'Closed', '2022-11-30', false, 'Paid in full'),
   (
      10,
      'Open',
      '2023-05-01',
      false,
      'Pending payment'
   ); 

-- Sale_Item data
INSERT INTO
   Sale_Item (SaleID, ProductID, quantity, discount, price)
VALUES
   (1, 2, 2, 0.0, 100.0),
   (1, 5, 1, 0.0, 50.0),
   (2, 1, 1, 0.1, 90.0),
   (3, 3, 3, 0.0, 150.0),
   (3, 4, 2, 0.2, 80.0),
   (4, 6, 1, 0.0, 200.0),
   (5, 9, 1, 0.05, 190.0),
   (6, 7, 1, 0.0, 75.0),
   (7, 10, 2, 0.1, 180.0),
   (8, 8, 1, 0.0, 100.0),
   (9, 2, 3, 0.05, 142.5),
   (10, 1, 1, 0.0, 100.0); 

-- Payment data
INSERT INTO
   Payment (SaleID, payer, value, payment_method, date)
VALUES
   (1, 'John Doe', 250.0, 1, '2022-12-20'),
   (2, 'Jane Doe', 180.0, 2, '2023-02-28'),
   (3, 'Bob Smith', 430.0, 3, '2022-11-10'),
   (3, 'Bob Smith', 80.0, 4, '2022-11-11'),
   (4, 'Alice Johnson', 200.0, 1, '2023-03-15'),
   (5, 'Charlie Brown', 190.0, 1, '2022-12-05'),
   (6, 'Ellen Page', 200.0, 1, '2022-10-01'),
   (7, 'Peter Parker', 540.0, 2, '2023-04-10'),
   (8, 'Sam Jones', 100.0, 3, '2022-09-18'); 

INSERT INTO
   "User" (
      name,
      cpf,
      login,
      email,
      password,
      permission_level
   )
VALUES
   (
      'John Doe',
      '123.456.789-10',
      'johndoe',
      'johndoe@example.com',
      'password123',
      1
   ),
   (
      'Jane Smith',
      '987.654.321-00',
      'janesmith',
      'janesmith@example.com',
      'letmein',
      2
   ),
   (
      'Bob Johnson',
      '555.555.555-55',
      'bobjohnson',
      'bobjohnson@example.com',
      'password',
      1
   ),
   (
      'Mary Adams',
      '111.111.111-11',
      'maryadams',
      'maryadams@example.com',
      'p@ssw0rd',
      2
   ),
   (
      'Tom Lee',
      '222.222.222-22',
      'tomlee',
      'tomlee@example.com',
      'abc123',
      1
   ),
   (
      'Samantha Lee',
      '333.333.333-33',
      'samlee',
      'samlee@example.com',
      'qwerty',
      1
   ),
   (
      'Alex Chen',
      '444.444.444-44',
      'alexchen',
      'alexchen@example.com',
      'password123',
      2
   ),
   (
      'Jessica Kim',
      '666.666.666-66',
      'jessicakim',
      'jessicakim@example.com',
      'letmein',
      1
   ),
   (
      'David Brown',
      '777.777.777-77',
      'davidbrown',
      'davidbrown@example.com',
      'password',
      2
   ),
   (
      'Emily Davis',
      '888.888.888-88',
      'emilydavis',
      'emilydavis@example.com',
      'p@ssw0rd',
      1
   ); 


