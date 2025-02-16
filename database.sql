-- Users Table
CREATE TABLE Users (
    UserID INT PRIMARY KEY AUTO_INCREMENT,
    Preferences TEXT,
    DietaryRestrictions TEXT,
    Username VARCHAR(255) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    Email VARCHAR(255) UNIQUE NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Restaurants Table
CREATE TABLE Restaurants (
    RestaurantID INT PRIMARY KEY AUTO_INCREMENT,
    Description TEXT,
    Location VARCHAR(255), -- เปลี่ยนจาก POINT เป็น VARCHAR
    AverageRating DECIMAL(4,2), -- รองรับค่าสูงสุด 99.99
    PopularityScore INT,
    RestaurantName VARCHAR(255) NOT NULL,
    Address VARCHAR(255),
    Phone VARCHAR(20),
    Website VARCHAR(255),
    CuisineType VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Rooms Table
CREATE TABLE Rooms (
    RoomID INT PRIMARY KEY AUTO_INCREMENT,
    CreatedBy INT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    RoomCode VARCHAR(20) UNIQUE NOT NULL,
    FOREIGN KEY (CreatedBy) REFERENCES Users(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- RoomMembers Table
CREATE TABLE RoomMembers (
    RoomID INT,
    UserID INT,
    JoinedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (RoomID, UserID),
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Votes Table
CREATE TABLE Votes (
    UserID INT,
    RoomID INT,
    RestaurantID INT,
    VoteTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (UserID, RoomID, RestaurantID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (RoomID) REFERENCES Rooms(RoomID) ON DELETE CASCADE,
    FOREIGN KEY (RestaurantID) REFERENCES Restaurants(RestaurantID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Reviews Table
CREATE TABLE Reviews (
    ReviewID INT PRIMARY KEY AUTO_INCREMENT,
    UserID INT,
    RestaurantID INT,
    Rating INT CHECK (Rating BETWEEN 1 AND 5),
    Comment TEXT,
    ReviewTime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (RestaurantID) REFERENCES Restaurants(RestaurantID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MenuItems Table
CREATE TABLE MenuItems (
    ItemID INT PRIMARY KEY AUTO_INCREMENT,
    RestaurantID INT,
    Description TEXT,
    Price DECIMAL(10,2),
    ItemName VARCHAR(255) NOT NULL,
    ImageURL VARCHAR(255),
    FOREIGN KEY (RestaurantID) REFERENCES Restaurants(RestaurantID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- MenuPreferences Table
CREATE TABLE MenuPreferences (
    UserID INT,
    ItemID INT,
    PreferenceType TINYINT NOT NULL CHECK (PreferenceType IN (-1, 0, 1)), -- -1 คือไม่ชอบ, 0 คือเฉยๆปานกลาง, 1 คือ ชอบ
    PRIMARY KEY (UserID, ItemID),
    FOREIGN KEY (UserID) REFERENCES Users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (ItemID) REFERENCES MenuItems(ItemID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
