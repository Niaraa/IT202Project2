
CREATE TABLE IF NOT EXISTS MovingRepresentatives (
    RepID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    RepPassword VARCHAR(255) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    Email VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS Clients (
    ClientID INT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS ClientPersonalInfo (
    ClientID INT PRIMARY KEY,
    StreetNumber VARCHAR(20) NOT NULL,
    StreetName VARCHAR(100) NOT NULL,
    City VARCHAR(50) NOT NULL,
    State VARCHAR(2) NOT NULL,
    ZipCode VARCHAR(10) NOT NULL,
    Phone VARCHAR(20) NOT NULL,
    FOREIGN KEY (ClientID) REFERENCES Clients(ClientID)
);

CREATE TABLE IF NOT EXISTS MovingEvents (
    MovingID INT PRIMARY KEY,
    ClientID INT NOT NULL,
    StreetNumber VARCHAR(20) NOT NULL,
    StreetName VARCHAR(100) NOT NULL,
    City VARCHAR(50) NOT NULL,
    State VARCHAR(2) NOT NULL,
    ZipCode VARCHAR(10) NOT NULL,
    MovingDate DATE NOT NULL,
    TotalWeight DECIMAL(10,2) NOT NULL,
    Miles DECIMAL(10,2) NOT NULL,
    TotalCost DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (ClientID) REFERENCES Clients(ClientID)
);

CREATE TABLE IF NOT EXISTS SpecialInstructions (
    MovingID INT PRIMARY KEY,
    Instructions TEXT NOT NULL,
    FOREIGN KEY (MovingID) REFERENCES MovingEvents(MovingID)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS RepresentativeClients (
    RepID INT NOT NULL,
    ClientID INT NOT NULL,
    PRIMARY KEY (RepID, ClientID),
    FOREIGN KEY (RepID) REFERENCES MovingRepresentatives(RepID),
    FOREIGN KEY (ClientID) REFERENCES Clients(ClientID)
);



INSERT INTO MovingRepresentatives
(RepID, FirstName, LastName, RepPassword, Phone, Email)
VALUES
(1001, 'James', 'Wilson', 'Move1001!', '2015550101', 'james.wilson@example.com'),
(1002, 'Mary', 'Johnson', 'Move1002!', '2015550102', 'mary.johnson@example.com'),
(1003, 'Robert', 'Brown', 'Move1003!', '2015550103', 'robert.brown@example.com'),
(1004, 'Lisa', 'Davis', 'Move1004!', '2015550104', 'lisa.davis@example.com'),
(1005, 'Michael', 'Miller', 'Move1005!', '2015550105', 'michael.miller@example.com'),
(1006, 'Sarah', 'Moore', 'Move1006!', '2015550106', 'sarah.moore@example.com'),
(1007, 'David', 'Taylor', 'Move1007!', '2015550107', 'david.taylor@example.com'),
(1008, 'Anna', 'Anderson', 'Move1008!', '2015550108', 'anna.anderson@example.com'),
(1009, 'Daniel', 'Thomas', 'Move1009!', '2015550109', 'daniel.thomas@example.com'),
(1010, 'Emily', 'Jackson', 'Move1010!', '2015550110', 'emily.jackson@example.com');



-- Add 10 clients
INSERT INTO Clients (ClientID, FirstName, LastName)
VALUES
(2001, 'Olivia', 'Smith'),
(2002, 'Noah', 'Williams'),
(2003, 'Emma', 'Jones'),
(2004, 'Liam', 'Garcia'),
(2005, 'Ava', 'Martinez'),
(2006, 'Ethan', 'Robinson'),
(2007, 'Sophia', 'Clark'),
(2008, 'Mason', 'Lewis'),
(2009, 'Isabella', 'Walker'),
(2010, 'Lucas', 'Hall');

-- Add personal information
INSERT INTO ClientPersonalInfo
(ClientID, StreetNumber, StreetName, City, State, ZipCode, Phone)
VALUES
(2001, '101', 'Main Street', 'Newark', 'NJ', '07102', '2015550201'),
(2002, '202', 'Broad Street', 'Jersey City', 'NJ', '07302', '2015550202'),
(2003, '303', 'Park Avenue', 'Hoboken', 'NJ', '07030', '2015550203'),
(2004, '404', 'Oak Street', 'Edison', 'NJ', '08817', '2015550204'),
(2005, '505', 'Maple Avenue', 'Elizabeth', 'NJ', '07201', '2015550205'),
(2006, '606', 'Pine Street', 'Paterson', 'NJ', '07501', '2015550206'),
(2007, '707', 'Cedar Avenue', 'Clifton', 'NJ', '07011', '2015550207'),
(2008, '808', 'Washington Street', 'Bloomfield', 'NJ', '07003', '2015550208'),
(2009, '909', 'Market Street', 'New Brunswick', 'NJ', '08901', '2015550209'),
(2010, '110', 'River Road', 'Hackensack', 'NJ', '07601', '2015550210');

-- Assign one client to each representative
INSERT INTO RepresentativeClients (RepID, ClientID)
VALUES
(1001, 2001),
(1002, 2002),
(1003, 2003),
(1004, 2004),
(1005, 2005),
(1006, 2006),
(1007, 2007),
(1008, 2008),
(1009, 2009),
(1010, 2010);





-- Add moving events for the 10 clients
INSERT INTO MovingEvents
(MovingID, ClientID, StreetNumber, StreetName,
 City, State, ZipCode, MovingDate,
 TotalWeight, Miles, TotalCost)
VALUES
(3001, 2001, '15', 'Oak Street', 'Newark', 'NJ', '07103', '2026-12-01', 1200, 15, 450),
(3002, 2002, '25', 'Pine Avenue', 'Jersey City', 'NJ', '07304', '2026-12-02', 1500, 20, 550),
(3003, 2003, '35', 'Maple Street', 'Hoboken', 'NJ', '07030', '2026-12-03', 1800, 25, 650),
(3004, 2004, '45', 'Cedar Road', 'Edison', 'NJ', '08820', '2026-12-04', 2000, 30, 750),
(3005, 2005, '55', 'Elm Street', 'Elizabeth', 'NJ', '07202', '2026-12-05', 1100, 10, 400),
(3006, 2006, '65', 'River Road', 'Paterson', 'NJ', '07502', '2026-12-06', 1600, 35, 700),
(3007, 2007, '75', 'Park Avenue', 'Clifton', 'NJ', '07012', '2026-12-07', 1400, 18, 500),
(3008, 2008, '85', 'Broad Street', 'Bloomfield', 'NJ', '07003', '2026-12-08', 1900, 28, 680),
(3009, 2009, '95', 'Main Street', 'New Brunswick', 'NJ', '08901', '2026-12-09', 2200, 40, 850),
(3010, 2010, '105', 'Washington Avenue', 'Hackensack', 'NJ', '07602', '2026-12-10', 1300, 22, 480);





-- Add special instructions for each move
INSERT INTO SpecialInstructions
(MovingID, Instructions)
VALUES
(3001, 'Handle glass furniture carefully.'),
(3002, 'Use the back entrance for loading.'),
(3003, 'Fragile kitchen items need extra padding.'),
(3004, 'Call the client 30 minutes before arrival.'),
(3005, 'Use protective covers for all furniture.'),
(3006, 'Large piano requires special equipment.'),
(3007, 'Elevator reservation required.'),
(3008, 'Keep electronics in separate boxes.'),
(3009, 'Two movers needed for heavy appliances.'),
(3010, 'Deliver all boxes to the second floor.');
