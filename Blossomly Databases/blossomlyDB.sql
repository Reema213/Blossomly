-- ============================================
--   Blossomly — Flower Shop Database
--   CIS 311: Web-based Systems
--   BB Group: 1
-- 	 Student names: 
-- 		Reema Shaya Aljaber  		2240004900
-- 		Layan Mohammed Alharthi		2240005643
--      Sharifah Alyousef			2240004904
-- 		Haneen Alsaflan				2250040171 
--      Juri Sulayhim 				2250040086
--   Term 2 2025-26
-- ============================================

CREATE DATABASE blossomly;
USE Blossomly;

-- ============================================
-- TABLE 1: products 
-- ============================================
CREATE TABLE products (
    product_id    INT            AUTO_INCREMENT PRIMARY KEY,
    category_id   INT NOT NULL,
    name          VARCHAR(100)   NOT NULL,
    description   TEXT,
    price         DECIMAL(10,2)  NOT NULL,
    stock         INT            NOT NULL DEFAULT 0,
    picture       VARCHAR(255)   NOT NULL       
);

-- ============================================
-- TABLE 2: admins
-- ============================================
CREATE TABLE admins (
    admin_id  INT          AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL               
);

-- ============================================
--  DATA ENTRIES
-- ============================================

-- Insert into Admins 
INSERT INTO admins (username, password) VALUES
('admin1','pass'),
('admin2','pass');

-- Insert into Products 
INSERT INTO products (category_id ,name, description, price, stock, picture) VALUES
-- Bouquets (category_id = 1)
(1, 'Red Roses Bouquet',      'A classic bouquet of 12 hand-picked red roses, beautifully wrapped with ribbon.',       85.00,  24, 'red_roses.jpg'),
(1, 'Sunflower Bunch',        'A cheerful bunch of 10 fresh sunflowers, perfect for brightening any room.',            55.00,  18, 'sunflowers.jpg'),
(1, 'Mixed Spring Bouquet',   'A vibrant mix of tulips, daisies, and lilies in a colorful arrangement.',              120.00,  10, 'mixed_spring.jpg'),
(1, 'White Lily Bouquet',     'Elegant white lilies arranged in a beautiful bouquet, ideal for special occasions.',    95.00,  15, 'white_lily.jpg'),
(1, 'Pink Peony Bouquet',     'Soft and romantic pink peonies bundled together in a lush bouquet.',                   110.00,  12, 'pink_peony.jpg'),
-- Singles (category_id = 2)
(2, 'Single Red Rose',        'One perfect long-stemmed red rose, a timeless romantic gesture.',                       15.00,  50, 'single_rose.jpg'),
(2, 'Single Sunflower',       'A single bright sunflower to spread happiness.',                                        10.00,  40, 'single_sunflower.jpg'),
(2, 'Lavender Stem',          'A fresh lavender stem with a calming and beautiful fragrance.',                         12.00,  35, 'lavender_stem.jpg'),
(2, 'Single White Lily',      'A single elegant white lily, graceful and long-lasting.',                               18.00,  30, 'single_lily.jpg'),
-- Plants (category_id = 3)
(3, 'Lavender Pot Plant',     'A potted lavender plant that blooms beautifully and fills the room with fragrance.',    40.00,  20, 'lavender_pot.jpg'),
(3, 'Peace Lily Plant',       'An elegant indoor peace lily plant, easy to care for and long-lasting.',                65.00,  14, 'peace_lily.jpg'),
(3, 'Succulent Garden',       'A small decorative pot with three assorted succulents, perfect for any desk.',          35.00,  25, 'succulent.jpg'),
(3, 'Jasmine Pot Plant',      'A fragrant jasmine plant in a ceramic pot, beautiful indoors or on a balcony.',         50.00,  16, 'jasmine_pot.jpg'),
-- Arrangements (category_id = 4)
(4, 'Roses and Baby Breath',  'A romantic arrangement of red roses complemented by delicate baby breath flowers.',    130.00,   8, 'roses_babys_breath.jpg'),
(4, 'Tropical Arrangement',   'An exotic arrangement of birds of paradise, anthuriums, and tropical greenery.',       150.00,   6, 'tropical.jpg'),
(4, 'Pastel Dream Vase',      'A dreamy pastel arrangement of soft pink, peach, and white flowers in a glass vase.',  140.00,   9, 'pastel_dream.jpg');





