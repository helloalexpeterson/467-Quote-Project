INSERT INTO Employees (Email, EmpName, Title, PwHash, PwText)
VALUES ('z1912480@niu.edu', 'Alex Peterson', 'Sales Associate', '1234', '1234');

INSERT INTO Employees(Email, EmpName, Title, PwHash)
VALUES ('bighead@aol.com', 'John Smith', 'Headquarters', '5678');

INSERT INTO Employees(Email, EmpName, Title, PwHash)
VALUES ('me@me.com', 'Jane Doe', 'Administrator', 'abcd');

INSERT INTO Employees(Email, EmpName, Title, PwHash)
VALUES ('megamind@gmail.com', 'Megamind', 'Superuser', 'efgh');

INSERT INTO Employees (Email, EmpName, Title, PwHash, PwText)
VALUES ('z1912480@niu.edu', 'sales', 'Sales Associate', '$2y$10$AlFq6Ds2T8sUfknHb2MLu.gAxBg/6yNxpAUqtshqnIf/33.R76nQu ', '1234');

INSERT INTO Employees (Email, EmpName, Title, PwHash, PwText)
VALUES ('z1912480@niu.edu', 'hq', 'Headquarters', '$2y$10$M5sPpmx8ZCWULyhJIVrmnOTbIpTQDpEKKChSujzXHOW2X04CjDS6S', '1234');

INSERT INTO Employees (Email, EmpName, Title, PwHash, PwText)
VALUES ('z1912480@niu.edu', 'admin', 'Administrator', '$2y$10$wk5uu0XMwkT8WuoQsRdPr.afIrp5BM1I2hQ.UjiHyKnLxFT.B7QOy', '1234');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('1','IBM Corporation', 'Armonk, NY', 'The IBM Way', '1-800-CALL-IBM', 'ibm@ibm.ibm', '1', 'open', '5', '1000');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'finalized', '8', '500');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'sanctioned', '4', '2500');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'open', '5', '3700');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('4','Insight Technologies Group','St. Louis, MO','Hollenberg Drive West, Suite 203','info@insight-tech.com', 'info@insight-tech.com','2','ordered', '10', '69.69');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('6','Bell South','Atlanta, GA','Braves Parkway','1-305-970-BELL', 'bell@bell.com','1','sanctioned', '7', '2000');

INSERT INTO PurchaseOrders(QuoteID , EmployeeID , CustomerID , OrderTotal , CustomerName , CommissionTotal)
VALUES ('1','1','1','1000','IBM Corporation', '5' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','500','1','New roof' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','400','1','New floor' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','100','1','Gumball machine' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('2','300','1','New dog' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('2','200','1','New sandwich' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('2','50','1','Lobotomy' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('3','200','1','New cat' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('3','500','1','New ice cream' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('3','780','1','Heart transplant' );

INSERT INTO Notes (QuoteID, Note)
VALUES ('1', "Customer is very rude");

INSERT INTO Notes (QuoteID, Note)
VALUES ('1', "IBM is the literal worst");

INSERT INTO Notes (QuoteID, Note)
VALUES ('1', "Do not call back");