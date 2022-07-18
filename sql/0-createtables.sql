-- Put this at the top
DROP TABLE IF EXISTS Employees, Quotes, PurchaseOrders, Notes, LineItems;

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
	CustomerID VARCHAR(32) NOT NULL,
    EmployeeID VARCHAR(32) NOT NULL,
	Email VARCHAR(64) NOT NULL,
    Name VARCHAR(32) NOT NULL,
    Addr VARCHAR(32) NOT NULL,
    Phone VARCHAR(16) NOT NULL,
    Status ENUM('open', 'finalized', 'sanctioned', 'ordered'),
    CommissionRate DECIMAL (0,2),

    FOREIGN KEY(EmployeeID) REFERENCES Employees
);

CREATE TABLE Notes (
	NOTEID INT PRIMARY KEY AUTO_INCREMENT,
    QuoteID INT, 
	Note VARCHAR(128),
    
    FOREIGN KEY(QUOTEID) REFERENCES Quotes
);

CREATE TABLE LineItems (
    LineID INT PRIMARY KEY AUTO_INCREMENT;

    QuoteID INT,
    Cost DECIMAL(6,2),
    Quantity INT,
    Service VARCHAR(64),

    FOREIGN KEY(QuoteID) REFERENCES Quotes
);

CREATE TABLE PurchaseOrders(
    PurchaseID INT NOT NULL AUTO_INCREMENT,
    QuoteID INT NOT NULL,
    EmployeeID INT NOT NULL,
    CustomerID INT NOT NULL,
    OrderTotal DECIMAL(10, 2) NOT NULL,
    CustomerName VARCHAR(32) NOT NULL,
    ProcessDate DATE NOT NULL,
    CommissionTotal DECIMAL(8, 2) NOT NULL,
    TimeStamp TIMESTAMP NOT NULL,

    PRIMARY KEY (PurchaseID),
    FOREIGN KEY (QuoteID) REFERENCES Quotes (QuoteID),
    FOREIGN KEY (EmployeeID) REFERENCES Emoloyees (EmployeeID),
    -- FROM LEGACY CUSTOMER DB
    -- FOREIGN KEY (CustomerID) REFERENCES !!!!!LEGACY DB!!!!!
);