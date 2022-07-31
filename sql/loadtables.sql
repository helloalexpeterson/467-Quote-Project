INSERT INTO Employees (Email, EmpName, Title, PwHash, Street) VALUES
    ('z1912480@niu.edu', 'Alex Peterson', 'Sales Associate', '$2y$10$/Jw0MmHmN7XtJ53GdkrjguezqVrgkCdAOoWOPf.ZDR65Syxoc.r1e', '4701 Tator Patch Road'),
    ('bighead@aol.com', 'John Smith', 'Headquarters', '5678', '4466 North Avenue'),
    ('me@me.com', 'Jane Doe', 'Administrator', 'abcd', '4154 Sugar Camp Road'),
    ('megamind@gmail.com', 'Megamind', 'Superuser', 'efgh', '1228 University Drive'),
    ('z1912480@niu.edu', 'sales', 'Sales Associate', '$2y$10$/Jw0MmHmN7XtJ53GdkrjguezqVrgkCdAOoWOPf.ZDR65Syxoc.r1e',  '3182 Leisure Lane'),
    ('z1912480@niu.edu', 'hq', 'Headquarters', '$2y$10$/Jw0MmHmN7XtJ53GdkrjguezqVrgkCdAOoWOPf.ZDR65Syxoc.r1e', '3223 Lunetta Street'),
    ('z1912480@niu.edu', 'admin', 'Administrator', '$2y$10$/Jw0MmHmN7XtJ53GdkrjguezqVrgkCdAOoWOPf.ZDR65Syxoc.r1e', '1516 Green Gate Lane');

INSERT INTO Quotes (CustomerID, CustomerName, City, Street, Contact, Email, EmployeeID, OrderStatus, CommissionRate, OrderTotal, StartDate) VALUES 
 ('1','IBM Corporation', 'Armonk, NY', 'The IBM Way', '1-800-CALL-IBM', 'ibm@ibm.ibm', '1', 'open', '5', '1000', '2022-05-07'),
 ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'finalized', '8', '500', '2019-02-19'),
 ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'sanctioned', '4', '2500',  '2020-12-05'),
 ('2', 'Ege Consulting, Inc.', 'Miami, FL', '14531 SW 76 Street','www.ege.com', 'ege@egeworld.edu','2', 'open', '5', '3700', '2019-11-25'),
 ('4','Insight Technologies Group','St. Louis, MO','Hollenberg Drive West, Suite 203','info@insight-tech.com', 'info@insight-tech.com','2','ordered', '10', '1000', '2017-11-15'),
 ('6','Bell South','Atlanta, GA','Braves Parkway','1-305-970-BELL', 'bell@bell.com','1','sanctioned', '7', '2000', '2022-07-15');

INSERT INTO PurchaseOrders(QuoteID , EmployeeID , CustomerID , OrderTotal , CustomerName , CommissionRate) VALUES
('5','2','4','1000','Insight Technologies Group', '10' );

INSERT INTO LineItems(QuoteID, Cost, Quantity, ServiceDesc) VALUES
('1','500','1','New roof' ),
('1','400','1','New floor' ),
('1','100','1','Gumball machine' ),
('2','300','1','New dog' ),
('2','200','1','New sandwich' ),
('2','50','1','Lobotomy' ),
('3','200','1','New cat' ),
('3','500','1','New ice cream' ),
('3','780','1','Heart transplant' ),
('4','1000','1','Distributor Calibration' ),
('4','100','1','Clean 02 Filter' ),
('5','800','1','Decontamination Station' ),
('5','200','4','Fixed Wiring' ),
('6','125','3','Open Waterways' ),
('6','200','1','Process Data' );

/*These don't have quotes to match


VALUES ('7','540','2','Unlock Manifolds' );


VALUES ('7','8100','1','Reactor Maintenance' );


VALUES ('8','260','1','Repair Drill' );


VALUES ('8','780','1','Stabilize Steering' );

*/
INSERT INTO Notes (QuoteID, Note) VALUES 
('1', "Customer is very rude"),
('1', "IBM is the literal worst"),
('1', "Do not call back");