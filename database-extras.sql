-- ============================================================
-- ZURI MOTORS - Additional SQL: Views, Triggers, Procedures
-- Run AFTER database.sql
-- ============================================================

USE `zuri_motors`;

-- ============================================================
-- USEFUL VIEWS
-- ============================================================

-- Car listings view with all joined data
CREATE OR REPLACE VIEW `v_car_listings` AS
SELECT c.*, 
    b.name AS brand_name, b.slug AS brand_slug,
    cat.name AS category_name, cat.slug AS category_slug,
    u.first_name AS seller_first, u.last_name AS seller_last, u.company_name AS seller_company,
    u.phone AS seller_phone, u.whatsapp AS seller_whatsapp,
    (SELECT image_url FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) AS primary_image,
    (SELECT COUNT(*) FROM car_images WHERE car_id = c.id) AS image_count,
    (SELECT COUNT(*) FROM car_features WHERE car_id = c.id) AS feature_count,
    (SELECT COUNT(*) FROM inquiries WHERE car_id = c.id) AS inquiry_count
FROM cars c
LEFT JOIN brands b ON c.brand_id = b.id
LEFT JOIN categories cat ON c.category_id = cat.id
LEFT JOIN users u ON c.user_id = u.id;

-- Dashboard summary view
CREATE OR REPLACE VIEW `v_dashboard_summary` AS
SELECT
    (SELECT COUNT(*) FROM cars WHERE status = 'active') AS active_cars,
    (SELECT COUNT(*) FROM cars WHERE status = 'pending') AS pending_cars,
    (SELECT COUNT(*) FROM cars WHERE status = 'sold') AS sold_cars,
    (SELECT COUNT(*) FROM inquiries WHERE status = 'new') AS new_inquiries,
    (SELECT COUNT(*) FROM inquiries WHERE DATE(created_at) = CURDATE()) AS today_inquiries,
    (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed') AS total_revenue,
    (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND MONTH(payment_date) = MONTH(CURDATE()) AND YEAR(payment_date) = YEAR(CURDATE())) AS month_revenue,
    (SELECT COUNT(*) FROM users WHERE role = 'vendor' AND is_active = 1) AS active_vendors,
    (SELECT COUNT(*) FROM users WHERE is_active = 1) AS total_users,
    (SELECT COUNT(DISTINCT session_id) FROM chat_messages WHERE is_read = 0 AND sender_type = 'visitor') AS unread_chats;

-- Invoice summary view
CREATE OR REPLACE VIEW `v_invoice_summary` AS
SELECT inv.*,
    c.title AS car_title,
    c.slug AS car_slug,
    (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = inv.id AND status = 'completed') AS calculated_paid,
    u.first_name AS creator_first, u.last_name AS creator_last
FROM invoices inv
LEFT JOIN cars c ON inv.car_id = c.id
LEFT JOIN users u ON inv.created_by = u.id;

-- ============================================================
-- TRIGGERS
-- ============================================================

-- Auto-update invoice balance when payment is inserted
DELIMITER //
CREATE TRIGGER `after_payment_insert` AFTER INSERT ON `payments`
FOR EACH ROW
BEGIN
    IF NEW.invoice_id IS NOT NULL AND NEW.status = 'completed' THEN
        UPDATE invoices SET 
            paid_amount = (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = NEW.invoice_id AND status = 'completed'),
            balance_due = total_amount - (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = NEW.invoice_id AND status = 'completed'),
            status = CASE 
                WHEN (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = NEW.invoice_id AND status = 'completed') >= total_amount THEN 'paid'
                WHEN (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE invoice_id = NEW.invoice_id AND status = 'completed') > 0 THEN 'partially_paid'
                ELSE status
            END
        WHERE id = NEW.invoice_id AND status NOT IN ('cancelled', 'refunded');
    END IF;
END//

-- Auto-update inquiry count on car when inquiry is created
CREATE TRIGGER `after_inquiry_insert` AFTER INSERT ON `inquiries`
FOR EACH ROW
BEGIN
    IF NEW.car_id IS NOT NULL THEN
        UPDATE cars SET inquiries_count = (SELECT COUNT(*) FROM inquiries WHERE car_id = NEW.car_id) WHERE id = NEW.car_id;
    END IF;
END//

-- Check overdue invoices daily (can be called via cron)
CREATE PROCEDURE `check_overdue_invoices`()
BEGIN
    UPDATE invoices SET status = 'overdue' 
    WHERE due_date < CURDATE() AND status IN ('draft', 'sent', 'partially_paid') AND balance_due > 0;
END//

-- Generate monthly report
CREATE PROCEDURE `get_monthly_report`(IN report_month DATE)
BEGIN
    SELECT 
        DATE_FORMAT(report_month, '%Y-%m') AS month,
        (SELECT COUNT(*) FROM cars WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(report_month, '%Y-%m')) AS new_cars,
        (SELECT COUNT(*) FROM cars WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(report_month, '%Y-%m') AND status = 'sold') AS cars_sold,
        (SELECT COUNT(*) FROM inquiries WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(report_month, '%Y-%m')) AS new_inquiries,
        (SELECT COUNT(*) FROM users WHERE DATE_FORMAT(created_at, '%Y-%m') = DATE_FORMAT(report_month, '%Y-%m')) AS new_users,
        (SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND DATE_FORMAT(payment_date, '%Y-%m') = DATE_FORMAT(report_month, '%Y-%m')) AS revenue;
END//

DELIMITER ;

-- ============================================================
-- ADDITIONAL INDEXES FOR PERFORMANCE
-- ============================================================
CREATE INDEX IF NOT EXISTS `idx_cars_created` ON `cars` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_cars_user_status` ON `cars` (`user_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_inquiries_created` ON `inquiries` (`created_at`);
CREATE INDEX IF NOT EXISTS `idx_payments_date` ON `payments` (`payment_date`);
CREATE INDEX IF NOT EXISTS `idx_payments_invoice_status` ON `payments` (`invoice_id`, `status`);
CREATE INDEX IF NOT EXISTS `idx_chat_session_read` ON `chat_messages` (`session_id`, `is_read`);
CREATE INDEX IF NOT EXISTS `idx_activity_created` ON `activity_log` (`created_at`);

-- ============================================================
-- CLEAN OLD SESSIONS (run via cron daily)
-- ============================================================
DELIMITER //
CREATE PROCEDURE `cleanup_old_data`()
BEGIN
    -- Remove old compare sessions (older than 7 days)
    DELETE FROM compare_sessions WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);
    -- Remove old activity logs (older than 90 days)  
    DELETE FROM activity_log WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);
    -- Remove old chat messages from anonymous visitors (older than 30 days)
    DELETE FROM chat_messages WHERE sender_id IS NULL AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY);
END//
DELIMITER ;
