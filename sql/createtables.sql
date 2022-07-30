DROP TABLE IF EXISTS PurchaseOrders, LineItems, Notes, Quotes, Employees;

CREATE TABLE Employees (
    EmployeeID INT PRIMARY KEY AUTO_INCREMENT,
    Email VARCHAR(32) NOT NULL,
    EmpName VARCHAR(32) NOT NULL,
    Title ENUM('Sales Associate', 'Headquarters', 'Administrator', 'Superuser'),
    PwHash VARCHAR(64) NOT NULL,
    PwText VARCHAR(64),
    CommissionTotal DOUBLE(10,2) DEFAULT 0.00,
    Street VARCHAR(64) NOT NULL
);

CREATE TABLE Quotes (
	QuoteID INT PRIMARY KEY AUTO_INCREMENT,
	CustomerID INT NOT NULL,
    CustomerName VARCHAR(64) NOT NULL,
    City VARCHAR(32) NOT NULL,
    Street VARCHAR(32) NOT NULL,
    Contact VARCHAR(32) NOT NULL,
    Email VARCHAR(64) NOT NULL,
    EmployeeID INT NOT NULL,
    OrderStatus ENUM('open', 'finalized', 'sanctioned', 'ordered'),
    CommissionRate INT(2),
    OrderTotal DECIMAL(10, 2) DEFAULT 0.00,
    StartDate  DATE NOT NULL DEFAULT CURRENT_DATE(), 

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
    Cost DECIMAL(10,2),
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
    ProcessDate DATE NOT NULL DEFAULT CURRENT_DATE(),
    CommissionRate VARCHAR(3) NOT NULL,
    OrderTime TIMESTAMP NOT NULL,

    PRIMARY KEY (PurchaseID),
    FOREIGN KEY (QuoteID) REFERENCES Quotes (QuoteID),
    FOREIGN KEY (EmployeeID) REFERENCES Employees (EmployeeID)
    

);

