INSERT INTO Employees (Email, EmpName, Title, PwHash)
VALUES ('z1912480@niu.edu', 'Alex Peterson', 'Sales Associate', '1234');

INSERT INTO Employees(Email, EmpName, Title, PwHash)
VALUES ('bighead@aol.com', 'John Smith', 'Headquarters', '5678');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('1','IBM Corporation', 'Armonk, NY', 'The IBM Way', '1-800-CALL-IBM', 'ibm@ibm.ibm', '1', 'open', '5', '1000');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'finalized', '8', '500');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal)
VALUES ('4','Insight Technologies Group','St. Louis, MO','Hollenberg Drive West, Suite 203','info@insight-tech.com', 'info@insight-tech.com','2','ordered', '10', '69.69');

INSERT INTO PurchaseOrders(QuoteID , EmployeeID , CustomerID , OrderTotal , CustomerName , CommissionTotal)
VALUES ('1','1','1','1000','IBM Corporation', '5' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','500','1','New roof' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','400','1','New floor' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc)
VALUES ('1','100','1','Gumball machine' );