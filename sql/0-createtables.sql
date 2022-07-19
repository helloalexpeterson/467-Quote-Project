-- Put this at the top
DROP TABLE IF EXISTS PurchaseOrders, LineItems, Notes, Quotes, Employees;

--creates tables
CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    Email VARCHAR(32) NOT NULL,
    Name VARCHAR(32) NOT NULL,
    Title ENUM('Sales Associate', 'Headquarters', 'Administrator'),
    Password VARCHAR(32) NOT NULL,
    CommissionTotal DOUBLE(8,2) DEFAULT 0.00
);

CREATE TABLE Quotes (
	QuoteID INT PRIMARY KEY AUTO_INCREMENT,
	CustomerID INT NOT NULL,
    EmployeeID INT NOT NULL,
	Email VARCHAR(64) NOT NULL,
    CustomerName VARCHAR(32) NOT NULL,
    Addr VARCHAR(32) NOT NULL,
    Contact VARCHAR(32) NOT NULL,
    OrderStatus ENUM('open', 'finalized', 'sanctioned', 'ordered'),
    CommissionRate INT(2),

    FOREIGN KEY(EmployeeID) REFERENCES Employees
);

CREATE TABLE Notes (
	NoteID INT PRIMARY KEY AUTO_INCREMENT,
    QuoteID INT, 
	Note VARCHAR(128),
    
    FOREIGN KEY(QuoteID) REFERENCES Quotes 
);

CREATE TABLE LineItems (
    LineID INT PRIMARY KEY AUTO_INCREMENT,
    QuoteID INT,
    Cost DECIMAL(7,2),
    Quantity INT,
    ServiceDesc VARCHAR(64),

    FOREIGN KEY(QuoteID) REFERENCES Quotes
);

CREATE TABLE PurchaseOrders(
    PurchaseID INT NOT NULL AUTO_INCREMENT,
    QuoteID INT,
    EmployeeID INT,
    CustomerID INT,
    OrderTotal DECIMAL(10, 2) NOT NULL,
    CustomerName VARCHAR(32),
    ProcessDate DATE NOT NULL,
    CommissionTotal DECIMAL(8,2) NOT NULL,
    OrderTime TIMESTAMP NOT NULL,

    PRIMARY KEY (PurchaseID),
    FOREIGN KEY (QuoteID) REFERENCES Quotes (QuoteID),
    FOREIGN KEY (EmployeeID) REFERENCES Employees (EmployeeID)
    
    -- FOREIGN KEY (CustomerID) must match legacy db 
);