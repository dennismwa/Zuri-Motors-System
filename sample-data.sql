-- ============================================================
-- ZURI MOTORS - Complete Sample Data with Real Images
-- Run AFTER database.sql and database-extras.sql
-- All images are real URLs that display properly
-- Everything is editable from Admin panel
-- ============================================================

USE `zuri_motors`;

-- ============================================================
-- HERO & SITE IMAGES (changeable in Admin > Settings)
-- ============================================================
UPDATE settings SET setting_value = 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=1920&q=80' WHERE setting_key = 'hero_image';

-- ============================================================
-- BRAND LOGOS (SVG logos - transparent bg, changeable in Admin > Brands)
-- ============================================================
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/toyota-logo-2020-europe.png' WHERE slug = 'toyota';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/mercedes-benz-logo-2011.png' WHERE slug = 'mercedes-benz';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/bmw-logo-2020.png' WHERE slug = 'bmw';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/audi-logo-2016.png' WHERE slug = 'audi';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/land-rover-logo.png' WHERE slug = 'land-rover';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/nissan-logo-2020.png' WHERE slug = 'nissan';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/honda-logo-2000.png' WHERE slug = 'honda';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/volkswagen-logo-2019.png' WHERE slug = 'volkswagen';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/subaru-logo-2019.png' WHERE slug = 'subaru';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/mazda-logo-2018.png' WHERE slug = 'mazda';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/mitsubishi-logo-2020.png' WHERE slug = 'mitsubishi';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/hyundai-logo-2011.png' WHERE slug = 'hyundai';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/kia-logo-2021.png' WHERE slug = 'kia';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/ford-logo-2017.png' WHERE slug = 'ford';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/lexus-logo-2013.png' WHERE slug = 'lexus';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/porsche-logo-2014.png' WHERE slug = 'porsche';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/jaguar-logo-2012.png' WHERE slug = 'jaguar';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/volvo-logo-2014.png' WHERE slug = 'volvo';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/jeep-logo-2022.png' WHERE slug = 'jeep';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/peugeot-logo-2010.png' WHERE slug = 'peugeot';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/suzuki-logo-2009.png' WHERE slug = 'suzuki';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/isuzu-logo-1991.png' WHERE slug = 'isuzu';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/tesla-logo-2003.png' WHERE slug = 'tesla';
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/chevrolet-logo-2013.png' WHERE slug = 'chevrolet';

-- Fix Audi logo (better quality)
UPDATE brands SET logo = 'https://www.carlogos.org/car-logos/audi-logo-2016.png' WHERE slug = 'audi';

-- ============================================================
-- CATEGORY IMAGES - REAL CAR PHOTOS (not icons!)
-- Circular cropped real vehicle photos for each type
-- Changeable in Admin > Categories > Edit
-- ============================================================
UPDATE categories SET image = 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=300&h=300&fit=crop' WHERE slug = 'suv';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=300&h=300&fit=crop' WHERE slug = 'sedan';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=300&h=300&fit=crop' WHERE slug = 'pickup';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=300&h=300&fit=crop' WHERE slug = 'hatchback';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=300&h=300&fit=crop' WHERE slug = 'coupe';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1507136566006-cfc505b114fc?w=300&h=300&fit=crop' WHERE slug = 'convertible';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?w=300&h=300&fit=crop' WHERE slug = 'van';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1612544448445-b8232cff3b6c?w=300&h=300&fit=crop' WHERE slug = 'wagon';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=300&h=300&fit=crop' WHERE slug = 'electric';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1563720223185-11003d516935?w=300&h=300&fit=crop' WHERE slug = 'luxury';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=300&h=300&fit=crop' WHERE slug = 'sports';
UPDATE categories SET image = 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=300&h=300&fit=crop' WHERE slug = 'crossover';

-- ============================================================
-- HOMEPAGE SECTION IMAGES (all editable in Admin > Settings)
-- ============================================================
INSERT INTO settings (setting_key, setting_value, setting_group) VALUES
('promo1_image', 'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=900&q=80', 'homepage'),
('promo2_image', 'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=900&q=80', 'homepage'),
('why_us_image', 'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=900&q=80', 'homepage'),
('sell_cta_image', 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=900&q=80', 'homepage')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ============================================================
-- SAMPLE CAR LISTINGS WITH REAL UNSPLASH IMAGES
-- Each brand gets 3+ cars, every field populated
-- ============================================================

-- ===== TOYOTA (id=1, cat SUV=1, Pickup=3, Crossover=12) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, old_price, price_negotiable, condition_type, location, description, excerpt, is_featured, is_offer, is_hot_deal, offer_label, status, views_count, created_at) VALUES
(1,'Toyota Land Cruiser V8 2021','toyota-land-cruiser-v8-2021',1,1,'Land Cruiser V8',2021,45000,'km','diesel','automatic','4.5L V8',268,'SUV','Pearl White','Beige Leather',5,7,'4wd',12500000,14000000,1,'used','Nairobi','Immaculate Toyota Land Cruiser V8 diesel with full leather interior, sunroof, 360-degree camera, cooled seats, rear entertainment, and JBL sound system. Full service history at Toyota Kenya. Single owner.','Pristine LC V8 diesel, full specs, sunroof, 360 cam, leather, Toyota service history.',1,1,0,'Save KSh 1.5M','active',342,NOW()-INTERVAL 5 DAY),
(1,'Toyota Prado TX-L 2022','toyota-prado-txl-2022',1,1,'Prado TX-L',2022,28000,'km','diesel','automatic','2.8L',204,'SUV','Black','Black Leather',5,7,'4wd',8900000,NULL,1,'used','Nairobi','Nearly new Prado TX-L with premium leather, KDSS suspension, multi-terrain select, crawl control, and Toyota Safety Sense. Ideal family SUV with legendary reliability.','Low mileage Prado TX-L, KDSS, leather, Safety Sense, ideal family SUV.',1,0,0,NULL,'active',256,NOW()-INTERVAL 3 DAY),
(1,'Toyota Hilux Double Cab 2023','toyota-hilux-double-cab-2023',1,3,'Hilux Double Cab',2023,15000,'km','diesel','automatic','2.8L GD-6',204,'Pickup','Grey Metallic','Black',4,5,'4wd',6200000,NULL,0,'used','Mombasa','Hilux 2.8 GD-6 4x4 with diff lock, downhill assist, LED headlights, 18-inch alloys, Apple CarPlay, leather-trimmed seats. Perfect workhorse.','Powerful Hilux GD-6 4x4, loaded features, low mileage, Kenya workhorse.',0,0,1,'Hot Deal','active',189,NOW()-INTERVAL 7 DAY),
(1,'Toyota RAV4 2020','toyota-rav4-2020',1,12,'RAV4',2020,52000,'km','petrol','automatic','2.0L',170,'Crossover','Red','Black',5,5,'awd',4200000,4600000,1,'used','Nairobi','RAV4 Adventure grade AWD, 8-inch touchscreen, wireless charging, panoramic moonroof, and safety suite. Fuel efficient Nairobi daily driver.','RAV4 Adventure AWD, panoramic roof, safety suite, reliable daily driver.',0,1,0,'Save 400K','active',178,NOW()-INTERVAL 2 DAY);

-- Images for Toyota cars
INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='toyota-land-cruiser-v8-2021'),'https://images.unsplash.com/photo-1594611396760-9e5c3b517a43?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='toyota-land-cruiser-v8-2021'),'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='toyota-land-cruiser-v8-2021'),'https://images.unsplash.com/photo-1581235720704-06d3acfcb36f?w=800&q=80',0,2),
((SELECT id FROM cars WHERE slug='toyota-prado-txl-2022'),'https://images.unsplash.com/photo-1625231334401-ff978fa77e38?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='toyota-prado-txl-2022'),'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='toyota-hilux-double-cab-2023'),'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='toyota-hilux-double-cab-2023'),'https://images.unsplash.com/photo-1612544448445-b8232cff3b6c?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='toyota-rav4-2020'),'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='toyota-rav4-2020'),'https://images.unsplash.com/photo-1568844293986-8d0400f4f36b?w=800&q=80',0,1);

-- ===== MERCEDES-BENZ (id=2, cat Sedan=2, SUV=1, Luxury=10) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, old_price, price_negotiable, condition_type, location, description, excerpt, is_featured, is_offer, status, views_count, created_at) VALUES
(1,'Mercedes-Benz GLE 300d 2022','mercedes-gle-300d-2022',2,1,'GLE 300d',2022,32000,'km','diesel','automatic','2.0L Turbo',245,'SUV','Obsidian Black','Saddle Brown Leather',5,5,'awd',11800000,NULL,1,'used','Nairobi','GLE 300d 4MATIC AMG Line with Burmester sound, panoramic sunroof, MBUX, 360 camera, air suspension, heated and ventilated seats. MB Kenya service history.','GLE 300d AMG Line, Burmester, air suspension, panoramic roof, serviced.',1,0,'active',298,NOW()-INTERVAL 4 DAY),
(1,'Mercedes-Benz C200 AMG Line 2021','mercedes-c200-amg-2021',2,2,'C200 AMG Line',2021,41000,'km','petrol','automatic','1.5L Turbo',184,'Sedan','Polar White','Black AMG Leather',4,5,'rwd',5800000,6200000,0,'used','Nairobi','C200 AMG Line with 9G-TRONIC, MBUX augmented reality navigation, 64-color ambient lighting, digital cluster, wireless Apple CarPlay. Executive sedan.','C200 AMG Line, MBUX, ambient lighting, 9G-TRONIC, executive sedan.',1,1,'active',234,NOW()-INTERVAL 6 DAY),
(1,'Mercedes-Benz E250 2019','mercedes-e250-2019',2,10,'E250',2019,68000,'km','petrol','automatic','2.0L Turbo',211,'Sedan','Silver','Black Leather',4,5,'rwd',4500000,NULL,1,'used','Kisumu','E-Class with Comand infotainment, 12.3-inch widescreen cockpit, Parktronic, active parking assist. Complete service history. Smooth highway cruiser.','E250 widescreen cockpit, active parking, full history, ultimate comfort.',0,0,'active',156,NOW()-INTERVAL 10 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='mercedes-gle-300d-2022'),'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mercedes-gle-300d-2022'),'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='mercedes-c200-amg-2021'),'https://images.unsplash.com/photo-1617531653332-bd46c24f2068?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mercedes-c200-amg-2021'),'https://images.unsplash.com/photo-1563720223185-11003d516935?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='mercedes-e250-2019'),'https://images.unsplash.com/photo-1553440569-bcc63803a83d?w=800&q=80',1,0);

-- ===== BMW (id=3) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, old_price, price_negotiable, condition_type, location, description, excerpt, is_featured, is_offer, status, views_count, created_at) VALUES
(1,'BMW X5 xDrive40i 2022','bmw-x5-xdrive40i-2022',3,1,'X5 xDrive40i',2022,25000,'km','petrol','automatic','3.0L Turbo I6',340,'SUV','Alpine White','Cognac Leather',5,5,'awd',10500000,NULL,1,'used','Nairobi','X5 xDrive40i M Sport with inline-6 turbo, Vernasca leather, panoramic glass roof, Harman Kardon, gesture control, wireless charging, head-up display, Live Cockpit Professional.','X5 M Sport, 340hp turbo I6, HUD, panoramic roof, Harman Kardon.',1,0,'active',267,NOW()-INTERVAL 3 DAY),
(1,'BMW 320i M Sport 2021','bmw-320i-m-sport-2021',3,2,'320i M Sport',2021,38000,'km','petrol','automatic','2.0L Turbo',184,'Sedan','Black Sapphire','Black Dakota Leather',4,5,'rwd',5200000,5600000,0,'used','Nairobi','320i M Sport with aerodynamic body kit, M suspension, 18-inch M alloys, iDrive 7.0, ambient lighting, wireless Apple CarPlay, parking assistant.','320i M Sport, dynamic handling, iDrive 7, ambient lighting, sports sedan.',0,1,'active',201,NOW()-INTERVAL 8 DAY),
(1,'BMW X3 xDrive20d 2020','bmw-x3-xdrive20d-2020',3,1,'X3 xDrive20d',2020,55000,'km','diesel','automatic','2.0L Turbo',190,'SUV','Phytonic Blue','Black Leather',5,5,'awd',5800000,NULL,1,'used','Nakuru','X3 diesel with xDrive AWD, sport seats, navigation, reversing camera, LED headlights, three-zone climate. Ideal mid-size luxury SUV for Kenyan families.','X3 diesel AWD, efficient yet powerful, perfect family luxury SUV.',0,0,'active',145,NOW()-INTERVAL 12 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='bmw-x5-xdrive40i-2022'),'https://images.unsplash.com/photo-1556189250-72ba954cfc2b?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='bmw-x5-xdrive40i-2022'),'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='bmw-320i-m-sport-2021'),'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='bmw-320i-m-sport-2021'),'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='bmw-x3-xdrive20d-2020'),'https://images.unsplash.com/photo-1617814076367-b759c7d7e738?w=800&q=80',1,0);

-- ===== NISSAN (id=6) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, old_price, price_negotiable, condition_type, location, description, excerpt, is_featured, is_offer, is_hot_deal, status, views_count, created_at) VALUES
(1,'Nissan X-Trail 2021','nissan-xtrail-2021',6,1,'X-Trail',2021,42000,'km','petrol','automatic','2.5L',181,'SUV','Diamond Black','Black Leather',5,7,'awd',3800000,4200000,1,'used','Nairobi','X-Trail with ProPilot assist, 7-seat, panoramic moonroof, around view monitor, emergency braking. Best value SUV in Kenya.','X-Trail 7-seater, ProPilot, panoramic roof, intelligent safety, great value.',1,1,0,'active',198,NOW()-INTERVAL 2 DAY),
(1,'Nissan Note e-Power 2020','nissan-note-epower-2020',6,4,'Note e-Power',2020,35000,'km','hybrid','automatic','1.2L + Electric',109,'Hatchback','White Pearl','Black',5,5,'fwd',1650000,NULL,0,'used','Nairobi','Note e-Power hybrid with electric motor drive, smooth and quiet driving. Perfect city car with excellent fuel economy for Nairobi traffic.','Note e-Power, incredibly fuel efficient, smooth electric drive, city car.',0,0,1,'active',167,NOW()-INTERVAL 9 DAY),
(1,'Nissan Navara NP300 2022','nissan-navara-np300-2022',6,3,'Navara NP300',2022,20000,'km','diesel','automatic','2.3L dCi',190,'Pickup','Forged Copper','Black',4,5,'4wd',4500000,NULL,1,'used','Mombasa','Navara double cab twin-turbo diesel, 7-speed auto, intelligent 4WD. Around view monitor, LED headlamps, 18-inch alloys. Class-leading payload.','Navara twin-turbo diesel, intelligent 4WD, SUV comfort with pickup capability.',0,0,0,'active',134,NOW()-INTERVAL 6 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='nissan-xtrail-2021'),'https://images.unsplash.com/photo-1609521263047-f8f205293f24?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='nissan-note-epower-2020'),'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='nissan-navara-np300-2022'),'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=800&q=80',1,0);

-- ===== SUBARU (id=9) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Subaru Forester 2.0i-S 2021','subaru-forester-20is-2021',9,1,'Forester 2.0i-S',2021,40000,'km','petrol','cvt','2.0L Boxer',156,'SUV','Crystal White Pearl','Black',5,5,'awd',3900000,'used','Nairobi','Forester with Symmetrical AWD, EyeSight driver assist, X-Mode, heated seats, powered tailgate, Starlink infotainment. Kenya all-terrain favorite.','Forester AWD, EyeSight, X-Mode, heated seats, all-terrain favorite.',1,'active',212,NOW()-INTERVAL 4 DAY),
(1,'Subaru Outback 2.5i 2020','subaru-outback-25i-2020',9,8,'Outback 2.5i Premium',2020,48000,'km','petrol','cvt','2.5L Boxer',182,'Wagon','Magnetite Grey','Java Brown',5,5,'awd',3400000,'used','Nairobi','Outback with class-leading ground clearance, Nappa leather, 11.6-inch touchscreen, EyeSight, wireless CarPlay. Perfect for adventures.','Outback Premium, Nappa leather, huge screen, EyeSight, adventure wagon.',0,'active',143,NOW()-INTERVAL 11 DAY),
(1,'Subaru XV 2.0i-S 2021','subaru-xv-20is-2021',9,12,'XV 2.0i-S',2021,33000,'km','petrol','cvt','2.0L Boxer',156,'Crossover','Sunshine Orange','Black',5,5,'awd',3200000,'used','Thika','Compact XV with Symmetrical AWD, EyeSight, great ground clearance. Perfect urban crossover for Nairobi potholes and weekend adventures.','XV AWD crossover, EyeSight, handles city and off-road perfectly.',0,'active',98,NOW()-INTERVAL 15 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='subaru-forester-20is-2021'),'https://images.unsplash.com/photo-1619767886558-efdc259cde1a?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='subaru-outback-25i-2020'),'https://images.unsplash.com/photo-1612544448445-b8232cff3b6c?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='subaru-xv-20is-2021'),'https://images.unsplash.com/photo-1568844293986-8d0400f4f36b?w=800&q=80',1,0);

-- ===== HONDA (id=7) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Honda CR-V 2021','honda-crv-2021',7,1,'CR-V',2021,38000,'km','petrol','cvt','1.5L VTEC Turbo',190,'SUV','Platinum White','Black Leather',5,5,'awd',4100000,'used','Nairobi','CR-V with VTEC turbo, Honda Sensing safety suite, hands-free tailgate, wireless charging, premium leather. Exceptional reliability and resale value.','CR-V Turbo, Honda Sensing, spacious, reliable, excellent resale value.',1,'active',187,NOW()-INTERVAL 5 DAY),
(1,'Honda Vezel Hybrid 2020','honda-vezel-hybrid-2020',7,12,'Vezel Hybrid',2020,45000,'km','hybrid','automatic','1.5L + Electric',152,'Crossover','Premium Crystal Red','Black',5,5,'fwd',2600000,'used','Nairobi','Vezel hybrid with Sport Hybrid i-DCD, Magic Seats, Honda Sensing, LED headlights, 8-inch touchscreen. Best-selling compact SUV.','Vezel Hybrid, great fuel economy, Magic Seats, Honda Sensing, popular SUV.',0,'active',213,NOW()-INTERVAL 7 DAY),
(1,'Honda Civic RS Turbo 2022','honda-civic-rs-turbo-2022',7,2,'Civic RS Turbo',2022,22000,'km','petrol','cvt','1.5L VTEC Turbo',182,'Sedan','Rallye Red','Black',4,5,'fwd',3800000,'used','Nairobi','11th gen Civic RS with VTEC Turbo, Bose premium audio, wireless CarPlay, Honda Sensing, sport suspension. The most exciting Civic ever.','New gen Civic RS Turbo, Bose audio, Honda Sensing, bold sporty sedan.',0,'active',145,NOW()-INTERVAL 3 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='honda-crv-2021'),'https://images.unsplash.com/photo-1568844293986-8d0400f4f36b?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='honda-vezel-hybrid-2020'),'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='honda-civic-rs-turbo-2022'),'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&q=80',1,0);

-- ===== LAND ROVER (id=5) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, old_price, condition_type, location, description, excerpt, is_featured, is_offer, status, views_count, created_at) VALUES
(1,'Range Rover Vogue 2021','range-rover-vogue-2021',5,10,'Range Rover Vogue',2021,35000,'km','diesel','automatic','3.0L V6 TDi',300,'SUV','Santorini Black','Ivory Leather',5,5,'4wd',18500000,NULL,'used','Nairobi','The ultimate luxury SUV. Meridian surround sound, massage seats, four-zone climate, panoramic roof, wade sensing, terrain response 2, pixel LED headlights.','Range Rover Vogue, Meridian, massage seats, wade sensing, ultimate luxury.',1,0,'active',389,NOW()-INTERVAL 2 DAY),
(1,'Land Rover Discovery 5 HSE 2020','land-rover-discovery-5-hse-2020',5,1,'Discovery 5 HSE',2020,52000,'km','diesel','automatic','3.0L TDV6',258,'SUV','Fuji White','Tan Leather',5,7,'4wd',8900000,9500000,'used','Nairobi','Discovery 5 HSE with 7-seat intelligence, terrain response, wade sensing, powered tailgate, meridian sound, tow assist. Family adventure vehicle.','Discovery 5 HSE, 7 seats, terrain response, wade sensing, family adventurer.',0,1,'active',234,NOW()-INTERVAL 8 DAY),
(1,'Range Rover Evoque 2022','range-rover-evoque-2022',5,12,'Range Rover Evoque',2022,18000,'km','petrol','automatic','2.0L Turbo',249,'Crossover','Seoul Pearl Silver','Ebony Leather',5,5,'awd',7200000,NULL,'used','Nairobi','Evoque with PIVI Pro infotainment, ClearSight Ground View, Meridian audio, wireless charging. Luxury crossover for Nairobi lifestyle.','Evoque, stunning design, PIVI Pro, ClearSight, luxury crossover.',0,0,'active',178,NOW()-INTERVAL 5 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='range-rover-vogue-2021'),'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='range-rover-vogue-2021'),'https://images.unsplash.com/photo-1519245659620-e859806a8d3b?w=800&q=80',0,1),
((SELECT id FROM cars WHERE slug='land-rover-discovery-5-hse-2020'),'https://images.unsplash.com/photo-1625231334401-ff978fa77e38?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='range-rover-evoque-2022'),'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',1,0);

-- ===== HYUNDAI (id=12) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Hyundai Tucson 2022','hyundai-tucson-2022',12,1,'Tucson',2022,22000,'km','petrol','automatic','2.5L',190,'SUV','Amazon Grey','Black',5,5,'awd',4200000,'used','Nairobi','All-new Tucson parametric design, dual 10.25-inch screens, Bose audio, ventilated seats, blind spot monitoring, SmartSense safety.','New Tucson, bold design, dual screens, Bose, SmartSense, feature-packed.',1,'active',198,NOW()-INTERVAL 1 DAY),
(1,'Hyundai Creta 2023','hyundai-creta-2023',12,12,'Creta',2023,12000,'km','petrol','automatic','1.5L',115,'Crossover','Polar White','Black',5,5,'fwd',2800000,'new','Nairobi','Creta SX(O) with panoramic sunroof, ventilated seats, Bose audio, ADAS Level 2. Best-selling compact SUV in Kenya, unbeatable value.','Creta SX(O), panoramic sunroof, Bose, ADAS, best value compact SUV.',0,'active',156,NOW()-INTERVAL 4 DAY),
(1,'Hyundai Sonata 2021','hyundai-sonata-2021',12,2,'Sonata',2021,35000,'km','petrol','automatic','2.5L',191,'Sedan','Midnight Black Pearl','Beige',4,5,'fwd',3200000,'used','Nairobi','Sonata with coupe-like design, digital key, 12.3-inch cluster, Bose 12-speaker audio, ventilated seats, blind spot view monitor. Sophisticated sedan.','Sonata, coupe design, digital key, Bose audio, sophisticated sedan.',0,'active',89,NOW()-INTERVAL 14 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='hyundai-tucson-2022'),'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='hyundai-creta-2023'),'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='hyundai-sonata-2021'),'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&q=80',1,0);

-- ===== ISUZU (id=22) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, is_hot_deal, status, views_count, created_at) VALUES
(1,'Isuzu D-Max 2022','isuzu-dmax-2022',22,3,'D-Max',2022,25000,'km','diesel','automatic','3.0L DDi',190,'Pickup','Splash White','Black',4,5,'4wd',4800000,'used','Nairobi','All-new D-Max, 3.0L turbo diesel, terrain command 4WD, 9-inch infotainment, multi-collision brake. Built for Kenya roads.','New D-Max, 3.0L diesel, terrain command 4WD, built for Kenya.',1,1,'active',234,NOW()-INTERVAL 3 DAY),
(1,'Isuzu MU-X 2021','isuzu-mux-2021',22,1,'MU-X',2021,40000,'km','diesel','automatic','3.0L DDi',190,'SUV','Obsidian Grey','Tan Leather',5,7,'4wd',5200000,'used','Nairobi','MU-X 7-seater, 3.0L turbo diesel, bi-LED headlights, 9-inch screen, multi-terrain select, ADAS. Family adventure SUV.','MU-X 7-seater, 3.0L diesel, 4WD, ADAS, family adventure SUV.',0,0,'active',178,NOW()-INTERVAL 7 DAY),
(1,'Isuzu D-Max Single Cab 2023','isuzu-dmax-single-2023',22,3,'D-Max Single Cab',2023,8000,'km','diesel','manual','1.9L DDi',150,'Pickup','Silver','Grey',2,3,'rwd',2800000,'new','Mombasa','Brand new D-Max single cab workhorse, 1.9L turbo diesel, 6-speed manual, 1-tonne payload, legendary durability.','D-Max single cab, new, 1.9L diesel, ultimate Kenya workhorse.',0,0,'active',112,NOW()-INTERVAL 1 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='isuzu-dmax-2022'),'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='isuzu-mux-2021'),'https://images.unsplash.com/photo-1625231334401-ff978fa77e38?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='isuzu-dmax-single-2023'),'https://images.unsplash.com/photo-1612544448445-b8232cff3b6c?w=800&q=80',1,0);

-- ===== MAZDA (id=10) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Mazda CX-5 2021','mazda-cx5-2021',10,1,'CX-5',2021,39000,'km','diesel','automatic','2.2L SkyActiv-D',184,'SUV','Soul Red Crystal','Parchment Leather',5,5,'awd',3600000,'used','Nairobi','CX-5 Soul Red, SkyActiv diesel, Bose 10-speaker, head-up display, 360 view, i-Activsense. Premium European feel, Japanese reliability.','CX-5 Soul Red, SkyActiv diesel, Bose, HUD, premium with Japanese reliability.',1,'active',176,NOW()-INTERVAL 6 DAY),
(1,'Mazda 3 2020','mazda-3-2020',10,2,'Mazda 3',2020,48000,'km','petrol','automatic','2.0L SkyActiv-G',165,'Sedan','Machine Grey','Black Leather',4,5,'fwd',2400000,'used','Nairobi','Award-winning Mazda 3 Kodo design, SkyActiv technology, G-Vectoring Control Plus, Bose audio, HUD, i-Activsense. Premium interior rivals luxury brands.','Mazda 3, stunning Kodo design, Bose, HUD, premium award-winning sedan.',0,'active',134,NOW()-INTERVAL 13 DAY),
(1,'Mazda CX-30 2022','mazda-cx30-2022',10,12,'CX-30',2022,25000,'km','petrol','automatic','2.0L SkyActiv-G',186,'Crossover','Polymetal Grey','Greige',5,5,'awd',3200000,'used','Nairobi','CX-30 bridging CX-3 and CX-5. Bose 12-speaker, 8.8-inch display, wireless CarPlay, i-Activsense, AWD. Perfect size for Nairobi.','CX-30, perfect size crossover, Bose, AWD, premium Mazda quality.',0,'active',112,NOW()-INTERVAL 9 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='mazda-cx5-2021'),'https://images.unsplash.com/photo-1580273916550-e323be2ae537?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mazda-3-2020'),'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mazda-cx30-2022'),'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&q=80',1,0);

-- ===== LEXUS (id=15) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Lexus RX 350 2021','lexus-rx350-2021',15,10,'RX 350',2021,30000,'km','petrol','automatic','3.5L V6',295,'SUV','Sonic Titanium','Noble Brown',5,5,'awd',8500000,'used','Nairobi','RX 350 F Sport, Mark Levinson reference surround, triple-beam LED, panoramic moonroof, adaptive suspension. Takumi craftsmanship meets Toyota reliability.','RX 350 F Sport, Mark Levinson, panoramic roof, Takumi luxury, reliability.',1,'active',245,NOW()-INTERVAL 4 DAY),
(1,'Lexus LX 570 2020','lexus-lx570-2020',15,10,'LX 570',2020,48000,'km','petrol','automatic','5.7L V8',383,'SUV','Starfire Pearl','Parchment Leather',5,8,'4wd',15000000,'used','Nairobi','Flagship LX 570 with 5.7L V8, multi-terrain select, crawl control, Mark Levinson 19-speaker, heated/cooled seats, rear entertainment, air suspension.','LX 570, 5.7L V8, Mark Levinson 19-speaker, air suspension, flagship.',1,'active',312,NOW()-INTERVAL 6 DAY),
(1,'Lexus NX 300h 2022','lexus-nx300h-2022',15,12,'NX 300h',2022,18000,'km','hybrid','automatic','2.5L + Electric',194,'Crossover','Graphic Black','Red Leather',5,5,'awd',6800000,'used','Nairobi','NX hybrid with 14-inch touchscreen, Mark Levinson, panoramic moonroof, Lexus Safety System+, wireless charging. Luxury meets efficiency.','NX 300h hybrid, 14-inch screen, Mark Levinson, luxury efficiency.',0,'active',167,NOW()-INTERVAL 10 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='lexus-rx350-2021'),'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='lexus-lx570-2020'),'https://images.unsplash.com/photo-1594611396760-9e5c3b517a43?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='lexus-nx300h-2022'),'https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&q=80',1,0);

-- ===== MITSUBISHI (id=11) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Mitsubishi Pajero Sport 2021','mitsubishi-pajero-sport-2021',11,1,'Pajero Sport',2021,45000,'km','diesel','automatic','2.4L DI-D',181,'SUV','Quartz White','Black Leather',5,7,'4wd',5500000,'used','Nairobi','Pajero Sport Super Select 4WD II, 8-speed auto, multi-around monitor, collision mitigation, leather. Dakar Rally heritage, proven Kenya reliability.','Pajero Sport, Super Select 4WD, 7-seat, Dakar pedigree, Kenya proven.',0,'active',156,NOW()-INTERVAL 8 DAY),
(1,'Mitsubishi Outlander PHEV 2022','mitsubishi-outlander-phev-2022',11,1,'Outlander PHEV',2022,20000,'km','plugin_hybrid','automatic','2.4L + Electric',224,'SUV','Cosmic Blue','Black',5,5,'awd',5800000,'used','Nairobi','Outlander PHEV with 87km electric range, S-AWC, 12.3-inch display, Bose audio. Save on fuel, enjoy SUV capability. Smart eco choice.','Outlander PHEV, 87km electric, S-AWC, Bose, eco-smart SUV.',1,'active',134,NOW()-INTERVAL 5 DAY),
(1,'Mitsubishi L200 Triton 2020','mitsubishi-l200-triton-2020',11,3,'L200 Triton',2020,55000,'km','diesel','automatic','2.4L DI-D',181,'Pickup','Graphite Grey','Black',4,5,'4wd',3200000,'used','Eldoret','L200 Triton Super Select 4WD, off-road mode, hill descent, smartphone link. Trusted by safari operators and businesses across Kenya.','L200 Triton, Super Select 4WD, off-road ready, trusted Kenya pickup.',0,'active',123,NOW()-INTERVAL 12 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='mitsubishi-pajero-sport-2021'),'https://images.unsplash.com/photo-1625231334401-ff978fa77e38?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mitsubishi-outlander-phev-2022'),'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='mitsubishi-l200-triton-2020'),'https://images.unsplash.com/photo-1559416523-140ddc3d238c?w=800&q=80',1,0);

-- ===== VOLKSWAGEN (id=8) =====
INSERT INTO cars (user_id, title, slug, brand_id, category_id, model, year, mileage, mileage_unit, fuel_type, transmission, engine_size, horsepower, body_type, color, interior_color, doors, seats, drivetrain, price, condition_type, location, description, excerpt, is_featured, status, views_count, created_at) VALUES
(1,'Volkswagen Tiguan R-Line 2021','vw-tiguan-rline-2021',8,1,'Tiguan R-Line',2021,30000,'km','petrol','automatic','2.0L TSI',190,'SUV','Oryx White','Black Vienna Leather',5,5,'awd',4800000,'used','Nairobi','Tiguan R-Line with Digital Cockpit Pro, Discover Pro nav, DCC adaptive suspension, Park Assist, IQ.Drive safety. German engineering precision.','Tiguan R-Line, Digital Cockpit, DCC, IQ.Drive, German engineering SUV.',1,'active',167,NOW()-INTERVAL 3 DAY),
(1,'Volkswagen Golf GTI 2021','vw-golf-gti-2021',8,4,'Golf GTI',2021,28000,'km','petrol','automatic','2.0L TSI',245,'Hatchback','Kings Red','Vienna Leather Tartan',5,5,'fwd',4200000,'used','Nairobi','Iconic Golf GTI Mk8, 245hp, DCC adaptive dampers, Digital Cockpit Pro, Harman Kardon, IQ.Light matrix LED. Performance and practicality.','Golf GTI Mk8, 245hp, DCC, Harman Kardon, iconic hot hatch.',0,'active',145,NOW()-INTERVAL 9 DAY),
(1,'Volkswagen Polo 2022','vw-polo-2022',8,4,'Polo',2022,18000,'km','petrol','automatic','1.0L TSI',110,'Hatchback','Reflex Silver','Black',5,5,'fwd',2100000,'used','Nairobi','Polo TSI, 8-inch touchscreen, Digital Cockpit, parking sensors, LED headlights, cruise control. German quality, fuel efficient, daily commuter.','Polo TSI, Digital Cockpit, solid build, fuel efficient, daily commuter.',0,'active',98,NOW()-INTERVAL 16 DAY);

INSERT INTO car_images (car_id, image_url, is_primary, sort_order) VALUES
((SELECT id FROM cars WHERE slug='vw-tiguan-rline-2021'),'https://images.unsplash.com/photo-1606016159991-dfe4f2746ad5?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='vw-golf-gti-2021'),'https://images.unsplash.com/photo-1590362891991-f776e747a588?w=800&q=80',1,0),
((SELECT id FROM cars WHERE slug='vw-polo-2022'),'https://images.unsplash.com/photo-1549317661-bd32c8ce0afa?w=800&q=80',1,0);

-- ============================================================
-- ADD FEATURES TO ALL CARS (bulk)
-- ============================================================
INSERT IGNORE INTO car_features (car_id, feature_id)
SELECT c.id, f.id FROM cars c CROSS JOIN features f
WHERE f.id IN (1,2,8,9,11,12,14,15,19,21,24,25,29,30) AND c.price > 3000000;

INSERT IGNORE INTO car_features (car_id, feature_id)
SELECT c.id, f.id FROM cars c CROSS JOIN features f
WHERE f.id IN (1,11,15,19,24,25,29) AND c.price <= 3000000;

INSERT IGNORE INTO car_features (car_id, feature_id)
SELECT c.id, f.id FROM cars c CROSS JOIN features f
WHERE f.id IN (3,4,5,6,7,10,13,16,17,18,20,22,23,26,27,28,34,36) AND c.price > 7000000;

-- ============================================================
-- TESTIMONIALS WITH DETAILS
-- ============================================================
DELETE FROM testimonials;
INSERT INTO testimonials (name, role, content, rating, is_active, sort_order) VALUES
('James Mwangi','Business Owner, Nairobi','Found my dream Land Cruiser through Zuri Motors. The process was smooth, transparent, and the team was incredibly helpful throughout. Highly recommend!',5,1,1),
('Sarah Kimani','Marketing Director','Purchased a Mercedes C200 and the experience was exceptional. The car was exactly as described. The financing options made it very affordable.',5,1,2),
('David Ochieng','Safari Tour Operator, Mombasa','We bought 4 vehicles for our safari fleet. Their 4x4 selection is unmatched, and they understand Kenyan business needs. Outstanding service.',5,1,3),
('Angela Wanjiru','Software Engineer','As a first-time car buyer, I was nervous. Zuri Motors guided me every step. I now drive a beautiful Honda Vezel and could not be happier.',4,1,4),
('Peter Kamau','Entrepreneur, Nakuru','The comparison feature helped me choose between the Forester and RAV4. Ended up with the Forester and it is perfect for Nakuru roads!',5,1,5),
('Lucy Njeri','Doctor, Nairobi','Sold my old car and bought a new one through the same platform. They handled all paperwork. Truly hassle-free. Will use Zuri Motors again.',5,1,6);

-- ============================================================
-- SAMPLE INQUIRIES
-- ============================================================
INSERT INTO inquiries (car_id, type, name, email, phone, message, status, priority, source, created_at) VALUES
((SELECT id FROM cars WHERE slug='toyota-land-cruiser-v8-2021' LIMIT 1),'general','John Mutua','john.mutua@gmail.com','+254712345678','Interested in the Land Cruiser V8. Is it still available? I want to view this weekend.','new','high','website',NOW()-INTERVAL 2 HOUR),
((SELECT id FROM cars WHERE slug='mercedes-c200-amg-2021' LIMIT 1),'test_drive','Mary Wambui','mary.w@outlook.com','+254723456789','Can I book a test drive for the C200 AMG Line? Available Saturday morning.','new','medium','website',NOW()-INTERVAL 5 HOUR),
((SELECT id FROM cars WHERE slug='bmw-x5-xdrive40i-2022' LIMIT 1),'financing','Robert Otieno','robert.o@yahoo.com','+254734567890','What financing options for the BMW X5? I can put down 30% deposit.','contacted','high','whatsapp',NOW()-INTERVAL 1 DAY),
(NULL,'sell_car','Grace Akinyi','grace.a@gmail.com','+254745678901','Want to sell my 2019 Toyota Harrier. 60,000km, excellent condition.','new','medium','website',NOW()-INTERVAL 3 HOUR),
((SELECT id FROM cars WHERE slug='hyundai-tucson-2022' LIMIT 1),'price_quote','Samuel Kiprop','sam.kiprop@gmail.com','+254756789012','What is the best cash price for the Tucson? Ready to buy this week.','in_progress','urgent','website',NOW()-INTERVAL 8 HOUR);
