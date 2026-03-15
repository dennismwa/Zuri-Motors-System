<?php
/**
 * Zuri Motors - Extended Functions & Business Logic
 * Query helpers, data fetching, and business operations
 */

require_once __DIR__ . '/config.php';

// ============================================================
// CAR FUNCTIONS
// ============================================================

/**
 * Get cars with filters and pagination
 */
function getCars(array $filters = [], int $limit = 12, int $offset = 0, string $orderBy = 'created_at DESC'): array {
    $where = ['c.status = ?'];
    $params = ['active'];

    if (!empty($filters['brand_id'])) {
        $where[] = 'c.brand_id = ?';
        $params[] = (int)$filters['brand_id'];
    }
    if (!empty($filters['category_id'])) {
        $where[] = 'c.category_id = ?';
        $params[] = (int)$filters['category_id'];
    }
    if (!empty($filters['condition_type'])) {
        $where[] = 'c.condition_type = ?';
        $params[] = $filters['condition_type'];
    }
    if (!empty($filters['fuel_type'])) {
        $where[] = 'c.fuel_type = ?';
        $params[] = $filters['fuel_type'];
    }
    if (!empty($filters['transmission'])) {
        $where[] = 'c.transmission = ?';
        $params[] = $filters['transmission'];
    }
    if (!empty($filters['location'])) {
        $where[] = 'c.location LIKE ?';
        $params[] = '%' . $filters['location'] . '%';
    }
    if (!empty($filters['min_price'])) {
        $where[] = 'c.price >= ?';
        $params[] = (float)$filters['min_price'];
    }
    if (!empty($filters['max_price'])) {
        $where[] = 'c.price <= ?';
        $params[] = (float)$filters['max_price'];
    }
    if (!empty($filters['min_year'])) {
        $where[] = 'c.year >= ?';
        $params[] = (int)$filters['min_year'];
    }
    if (!empty($filters['max_year'])) {
        $where[] = 'c.year <= ?';
        $params[] = (int)$filters['max_year'];
    }
    if (!empty($filters['min_mileage'])) {
        $where[] = 'c.mileage >= ?';
        $params[] = (int)$filters['min_mileage'];
    }
    if (!empty($filters['max_mileage'])) {
        $where[] = 'c.mileage <= ?';
        $params[] = (int)$filters['max_mileage'];
    }
    if (!empty($filters['is_featured'])) {
        $where[] = 'c.is_featured = 1';
    }
    if (!empty($filters['is_offer'])) {
        $where[] = 'c.is_offer = 1';
    }
    if (!empty($filters['is_hot_deal'])) {
        $where[] = 'c.is_hot_deal = 1';
    }
    if (!empty($filters['user_id'])) {
        $where[] = 'c.user_id = ?';
        $params[] = (int)$filters['user_id'];
    }
    if (!empty($filters['search'])) {
        $where[] = 'MATCH(c.title, c.description, c.model, c.location) AGAINST(? IN BOOLEAN MODE)';
        $params[] = $filters['search'] . '*';
    }
    if (!empty($filters['body_type'])) {
        $where[] = 'c.body_type = ?';
        $params[] = $filters['body_type'];
    }
    if (!empty($filters['color'])) {
        $where[] = 'c.color = ?';
        $params[] = $filters['color'];
    }
    if (!empty($filters['drivetrain'])) {
        $where[] = 'c.drivetrain = ?';
        $params[] = $filters['drivetrain'];
    }
    if (!empty($filters['brand_slug'])) {
        $where[] = 'b.slug = ?';
        $params[] = $filters['brand_slug'];
    }
    if (!empty($filters['category_slug'])) {
        $where[] = 'cat.slug = ?';
        $params[] = $filters['category_slug'];
    }
    if (!empty($filters['exclude_id'])) {
        $where[] = 'c.id != ?';
        $params[] = (int)$filters['exclude_id'];
    }

    // Allowed order options
    $allowedOrders = [
        'newest'     => 'c.created_at DESC',
        'oldest'     => 'c.created_at ASC',
        'price_low'  => 'c.price ASC',
        'price_high' => 'c.price DESC',
        'year_new'   => 'c.year DESC',
        'year_old'   => 'c.year ASC',
        'mileage'    => 'c.mileage ASC',
        'popular'    => 'c.views_count DESC',
        'featured'   => 'c.is_featured DESC, c.created_at DESC',
    ];

    if (!empty($filters['sort']) && isset($allowedOrders[$filters['sort']])) {
        $orderBy = $allowedOrders[$filters['sort']];
    }

    $whereClause = implode(' AND ', $where);

    $sql = "SELECT c.*, b.name AS brand_name, b.slug AS brand_slug, b.logo AS brand_logo,
                   cat.name AS category_name, cat.slug AS category_slug,
                   u.first_name AS seller_first_name, u.last_name AS seller_last_name, u.company_name AS seller_company,
                   (SELECT image_url FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) AS primary_image,
                   (SELECT COUNT(*) FROM car_images WHERE car_id = c.id) AS image_count
            FROM cars c
            LEFT JOIN brands b ON c.brand_id = b.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            LEFT JOIN users u ON c.user_id = u.id
            WHERE {$whereClause}
            ORDER BY {$orderBy}
            LIMIT {$limit} OFFSET {$offset}";

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Count cars with filters
 */
function countCars(array $filters = []): int {
    $where = ['c.status = ?'];
    $params = ['active'];

    if (!empty($filters['brand_id'])) { $where[] = 'c.brand_id = ?'; $params[] = (int)$filters['brand_id']; }
    if (!empty($filters['category_id'])) { $where[] = 'c.category_id = ?'; $params[] = (int)$filters['category_id']; }
    if (!empty($filters['condition_type'])) { $where[] = 'c.condition_type = ?'; $params[] = $filters['condition_type']; }
    if (!empty($filters['fuel_type'])) { $where[] = 'c.fuel_type = ?'; $params[] = $filters['fuel_type']; }
    if (!empty($filters['transmission'])) { $where[] = 'c.transmission = ?'; $params[] = $filters['transmission']; }
    if (!empty($filters['location'])) { $where[] = 'c.location LIKE ?'; $params[] = '%' . $filters['location'] . '%'; }
    if (!empty($filters['min_price'])) { $where[] = 'c.price >= ?'; $params[] = (float)$filters['min_price']; }
    if (!empty($filters['max_price'])) { $where[] = 'c.price <= ?'; $params[] = (float)$filters['max_price']; }
    if (!empty($filters['min_year'])) { $where[] = 'c.year >= ?'; $params[] = (int)$filters['min_year']; }
    if (!empty($filters['max_year'])) { $where[] = 'c.year <= ?'; $params[] = (int)$filters['max_year']; }
    if (!empty($filters['min_mileage'])) { $where[] = 'c.mileage >= ?'; $params[] = (int)$filters['min_mileage']; }
    if (!empty($filters['max_mileage'])) { $where[] = 'c.mileage <= ?'; $params[] = (int)$filters['max_mileage']; }
    if (!empty($filters['is_featured'])) { $where[] = 'c.is_featured = 1'; }
    if (!empty($filters['is_offer'])) { $where[] = 'c.is_offer = 1'; }
    if (!empty($filters['is_hot_deal'])) { $where[] = 'c.is_hot_deal = 1'; }
    if (!empty($filters['user_id'])) { $where[] = 'c.user_id = ?'; $params[] = (int)$filters['user_id']; }
    if (!empty($filters['search'])) { $where[] = 'MATCH(c.title, c.description, c.model, c.location) AGAINST(? IN BOOLEAN MODE)'; $params[] = $filters['search'] . '*'; }
    if (!empty($filters['brand_slug'])) { $where[] = 'b.slug = ?'; $params[] = $filters['brand_slug']; }
    if (!empty($filters['category_slug'])) { $where[] = 'cat.slug = ?'; $params[] = $filters['category_slug']; }
    if (!empty($filters['exclude_id'])) { $where[] = 'c.id != ?'; $params[] = (int)$filters['exclude_id']; }

    $whereClause = implode(' AND ', $where);
    $sql = "SELECT COUNT(*) FROM cars c 
            LEFT JOIN brands b ON c.brand_id = b.id 
            LEFT JOIN categories cat ON c.category_id = cat.id 
            WHERE {$whereClause}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

/**
 * Get single car by slug or ID
 */
function getCar($identifier): ?array {
    $field = is_numeric($identifier) ? 'c.id' : 'c.slug';
    $sql = "SELECT c.*, b.name AS brand_name, b.slug AS brand_slug, b.logo AS brand_logo,
                   cat.name AS category_name, cat.slug AS category_slug,
                   u.first_name AS seller_first_name, u.last_name AS seller_last_name, 
                   u.company_name AS seller_company, u.phone AS seller_phone, u.email AS seller_email,
                   u.whatsapp AS seller_whatsapp, u.avatar AS seller_avatar, u.id AS seller_id
            FROM cars c
            LEFT JOIN brands b ON c.brand_id = b.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            LEFT JOIN users u ON c.user_id = u.id
            WHERE {$field} = ?";
    $stmt = db()->prepare($sql);
    $stmt->execute([$identifier]);
    $car = $stmt->fetch();
    return $car ?: null;
}

/**
 * Get car images
 */
function getCarImages(int $carId): array {
    $stmt = db()->prepare("SELECT * FROM car_images WHERE car_id = ? ORDER BY is_primary DESC, sort_order ASC");
    $stmt->execute([$carId]);
    return $stmt->fetchAll();
}

/**
 * Get car features
 */
function getCarFeatures(int $carId): array {
    $sql = "SELECT cf.*, f.name, f.icon, f.category AS feature_category 
            FROM car_features cf 
            JOIN features f ON cf.feature_id = f.id 
            WHERE cf.car_id = ? 
            ORDER BY f.category, f.sort_order";
    $stmt = db()->prepare($sql);
    $stmt->execute([$carId]);
    return $stmt->fetchAll();
}

/**
 * Increment car views
 */
function incrementCarViews(int $carId): void {
    $sessionKey = 'car_viewed_' . $carId;
    if (empty($_SESSION[$sessionKey])) {
        db()->prepare("UPDATE cars SET views_count = views_count + 1 WHERE id = ?")->execute([$carId]);
        $_SESSION[$sessionKey] = true;
    }
}

/**
 * Get similar cars
 */
function getSimilarCars(array $car, int $limit = 4): array {
    return getCars([
        'brand_id'   => $car['brand_id'],
        'exclude_id' => $car['id'],
    ], $limit);
}

/**
 * Save car listing (create or update)
 */
function saveCar(array $data, ?int $id = null): array {
    try {
        $fields = [
            'user_id', 'title', 'slug', 'brand_id', 'category_id', 'model', 'year', 'mileage',
            'mileage_unit', 'fuel_type', 'transmission', 'engine_size', 'horsepower', 'body_type',
            'color', 'interior_color', 'doors', 'seats', 'drivetrain', 'price', 'old_price',
            'price_negotiable', 'condition_type', 'location', 'latitude', 'longitude', 'description',
            'excerpt', 'registration_number', 'vin_number', 'registration_year', 'is_featured',
            'is_offer', 'is_hot_deal', 'offer_label', 'status', 'meta_title', 'meta_description',
            'deposit_amount', 'monthly_installment', 'installment_months'
        ];

        // Generate slug
        if (empty($data['slug'])) {
            $data['slug'] = uniqueSlug(slugify($data['title']), 'cars', $id);
        }

        // Auto-generate excerpt
        if (empty($data['excerpt']) && !empty($data['description'])) {
            $data['excerpt'] = excerpt(strip_tags($data['description']), 300);
        }

        // Auto-generate meta
        if (empty($data['meta_title'])) {
            $data['meta_title'] = $data['title'] . ' - ' . Settings::get('company_name');
        }
        if (empty($data['meta_description'])) {
            $data['meta_description'] = $data['excerpt'] ?? excerpt(strip_tags($data['description'] ?? ''), 160);
        }

        $filteredData = [];
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $filteredData[$field] = $data[$field] === '' ? null : $data[$field];
            }
        }

        if ($id) {
            // Update
            $sets = [];
            $params = [];
            foreach ($filteredData as $key => $value) {
                $sets[] = "`{$key}` = ?";
                $params[] = $value;
            }
            $params[] = $id;
            $sql = "UPDATE cars SET " . implode(', ', $sets) . ", updated_at = NOW() WHERE id = ?";
            db()->prepare($sql)->execute($params);
            Auth::logActivity('car_updated', 'car', $id, "Updated car: {$data['title']}");
            return ['success' => true, 'id' => $id, 'message' => 'Car listing updated successfully.'];
        } else {
            // Create
            $columns = implode(', ', array_map(fn($k) => "`{$k}`", array_keys($filteredData)));
            $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));
            $sql = "INSERT INTO cars ({$columns}) VALUES ({$placeholders})";
            db()->prepare($sql)->execute(array_values($filteredData));
            $newId = (int)db()->lastInsertId();
            Auth::logActivity('car_created', 'car', $newId, "Created car: {$data['title']}");
            return ['success' => true, 'id' => $newId, 'message' => 'Car listing created successfully.'];
        }
    } catch (PDOException $e) {
        error_log("Save Car Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Failed to save car listing: ' . $e->getMessage()];
    }
}

/**
 * Delete car
 */
function deleteCar(int $id): bool {
    try {
        // Get images to delete files
        $images = getCarImages($id);
        foreach ($images as $img) {
            ImageUpload::delete($img['image_url']);
            if ($img['thumbnail_url']) ImageUpload::delete($img['thumbnail_url']);
        }
        db()->prepare("DELETE FROM cars WHERE id = ?")->execute([$id]);
        Auth::logActivity('car_deleted', 'car', $id, "Deleted car ID: {$id}");
        return true;
    } catch (PDOException $e) {
        error_log("Delete Car Error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update car status
 */
function updateCarStatus(int $id, string $status): bool {
    try {
        db()->prepare("UPDATE cars SET status = ? WHERE id = ?")->execute([$status, $id]);
        Auth::logActivity('car_status_changed', 'car', $id, "Changed status to: {$status}");
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// ============================================================
// BRAND FUNCTIONS
// ============================================================

function getBrands(bool $activeOnly = true, bool $popularFirst = false): array {
    $sql = "SELECT b.*, (SELECT COUNT(*) FROM cars WHERE brand_id = b.id AND status = 'active') AS car_count 
            FROM brands b";
    if ($activeOnly) $sql .= " WHERE b.is_active = 1";
    if ($popularFirst) {
        $sql .= " ORDER BY b.is_popular DESC, b.sort_order ASC, b.name ASC";
    } else {
        $sql .= " ORDER BY b.sort_order ASC, b.name ASC";
    }
    return db()->query($sql)->fetchAll();
}

function getBrand($identifier): ?array {
    $field = is_numeric($identifier) ? 'id' : 'slug';
    $stmt = db()->prepare("SELECT * FROM brands WHERE {$field} = ?");
    $stmt->execute([$identifier]);
    return $stmt->fetch() ?: null;
}

function saveBrand(array $data, ?int $id = null): array {
    try {
        if (empty($data['slug'])) $data['slug'] = uniqueSlug(slugify($data['name']), 'brands', $id);
        if ($id) {
            $stmt = db()->prepare("UPDATE brands SET name=?, slug=?, logo=?, description=?, is_popular=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$data['name'], $data['slug'], $data['logo'] ?? null, $data['description'] ?? null, $data['is_popular'] ?? 0, $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id]);
        } else {
            $stmt = db()->prepare("INSERT INTO brands (name, slug, logo, description, is_popular, sort_order, is_active) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$data['name'], $data['slug'], $data['logo'] ?? null, $data['description'] ?? null, $data['is_popular'] ?? 0, $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
            $id = (int)db()->lastInsertId();
        }
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function deleteBrand(int $id): bool {
    try {
        db()->prepare("DELETE FROM brands WHERE id = ?")->execute([$id]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// CATEGORY FUNCTIONS
// ============================================================

function getCategories(bool $activeOnly = true): array {
    $sql = "SELECT cat.*, (SELECT COUNT(*) FROM cars WHERE category_id = cat.id AND status = 'active') AS car_count 
            FROM categories cat";
    if ($activeOnly) $sql .= " WHERE cat.is_active = 1";
    $sql .= " ORDER BY cat.sort_order ASC, cat.name ASC";
    return db()->query($sql)->fetchAll();
}

function getCategory($identifier): ?array {
    $field = is_numeric($identifier) ? 'id' : 'slug';
    $stmt = db()->prepare("SELECT * FROM categories WHERE {$field} = ?");
    $stmt->execute([$identifier]);
    return $stmt->fetch() ?: null;
}

function saveCategory(array $data, ?int $id = null): array {
    try {
        if (empty($data['slug'])) $data['slug'] = uniqueSlug(slugify($data['name']), 'categories', $id);
        if ($id) {
            $stmt = db()->prepare("UPDATE categories SET name=?, slug=?, icon=?, image=?, description=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$data['name'], $data['slug'], $data['icon'] ?? null, $data['image'] ?? null, $data['description'] ?? null, $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id]);
        } else {
            $stmt = db()->prepare("INSERT INTO categories (name, slug, icon, image, description, sort_order, is_active) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$data['name'], $data['slug'], $data['icon'] ?? null, $data['image'] ?? null, $data['description'] ?? null, $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
            $id = (int)db()->lastInsertId();
        }
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function deleteCategory(int $id): bool {
    try {
        db()->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// FEATURES FUNCTIONS
// ============================================================

function getFeatures(bool $activeOnly = true): array {
    $sql = "SELECT * FROM features";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY category, sort_order ASC";
    return db()->query($sql)->fetchAll();
}

function getFeaturesGrouped(bool $activeOnly = true): array {
    $features = getFeatures($activeOnly);
    $grouped = [];
    foreach ($features as $f) {
        $grouped[$f['category']][] = $f;
    }
    return $grouped;
}

function saveFeature(array $data, ?int $id = null): array {
    try {
        if ($id) {
            $stmt = db()->prepare("UPDATE features SET name=?, icon=?, category=?, sort_order=?, is_active=? WHERE id=?");
            $stmt->execute([$data['name'], $data['icon'] ?? 'check', $data['category'] ?? 'comfort', $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id]);
        } else {
            $stmt = db()->prepare("INSERT INTO features (name, icon, category, sort_order, is_active) VALUES (?,?,?,?,?)");
            $stmt->execute([$data['name'], $data['icon'] ?? 'check', $data['category'] ?? 'comfort', $data['sort_order'] ?? 0, $data['is_active'] ?? 1]);
            $id = (int)db()->lastInsertId();
        }
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function deleteFeature(int $id): bool {
    try {
        db()->prepare("DELETE FROM features WHERE id = ?")->execute([$id]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// INQUIRY / LEAD FUNCTIONS
// ============================================================

function getInquiries(array $filters = [], int $limit = 20, int $offset = 0): array {
    $where = ['1=1'];
    $params = [];

    if (!empty($filters['status'])) { $where[] = 'i.status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['type'])) { $where[] = 'i.type = ?'; $params[] = $filters['type']; }
    if (!empty($filters['assigned_to'])) { $where[] = 'i.assigned_to = ?'; $params[] = (int)$filters['assigned_to']; }
    if (!empty($filters['priority'])) { $where[] = 'i.priority = ?'; $params[] = $filters['priority']; }
    if (!empty($filters['car_id'])) { $where[] = 'i.car_id = ?'; $params[] = (int)$filters['car_id']; }
    if (!empty($filters['search'])) {
        $where[] = '(i.name LIKE ? OR i.email LIKE ? OR i.phone LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    if (!empty($filters['vendor_id'])) {
        $where[] = 'c.user_id = ?';
        $params[] = (int)$filters['vendor_id'];
    }

    $whereClause = implode(' AND ', $where);
    $sql = "SELECT i.*, c.title AS car_title, c.slug AS car_slug, c.price AS car_price,
                   u.first_name AS assigned_first, u.last_name AS assigned_last
            FROM inquiries i
            LEFT JOIN cars c ON i.car_id = c.id
            LEFT JOIN users u ON i.assigned_to = u.id
            WHERE {$whereClause}
            ORDER BY 
                CASE i.priority WHEN 'urgent' THEN 0 WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END,
                i.created_at DESC
            LIMIT {$limit} OFFSET {$offset}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countInquiries(array $filters = []): int {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'i.status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['type'])) { $where[] = 'i.type = ?'; $params[] = $filters['type']; }
    if (!empty($filters['assigned_to'])) { $where[] = 'i.assigned_to = ?'; $params[] = (int)$filters['assigned_to']; }
    if (!empty($filters['priority'])) { $where[] = 'i.priority = ?'; $params[] = $filters['priority']; }
    if (!empty($filters['vendor_id'])) { $where[] = 'c.user_id = ?'; $params[] = (int)$filters['vendor_id']; }
    if (!empty($filters['search'])) {
        $where[] = '(i.name LIKE ? OR i.email LIKE ? OR i.phone LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $sql = "SELECT COUNT(*) FROM inquiries i LEFT JOIN cars c ON i.car_id = c.id WHERE {$whereClause}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getInquiry(int $id): ?array {
    $sql = "SELECT i.*, c.title AS car_title, c.slug AS car_slug, c.price AS car_price,
                   c.brand_id, c.model, c.year AS car_year,
                   u.first_name AS assigned_first, u.last_name AS assigned_last, u.email AS assigned_email
            FROM inquiries i
            LEFT JOIN cars c ON i.car_id = c.id
            LEFT JOIN users u ON i.assigned_to = u.id
            WHERE i.id = ?";
    $stmt = db()->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getInquiryNotes(int $inquiryId): array {
    $sql = "SELECT n.*, u.first_name, u.last_name, u.avatar 
            FROM inquiry_notes n 
            JOIN users u ON n.user_id = u.id 
            WHERE n.inquiry_id = ? ORDER BY n.created_at DESC";
    $stmt = db()->prepare($sql);
    $stmt->execute([$inquiryId]);
    return $stmt->fetchAll();
}

function saveInquiry(array $data, ?int $id = null): array {
    try {
        if ($id) {
            $stmt = db()->prepare("UPDATE inquiries SET status=?, priority=?, assigned_to=?, updated_at=NOW() WHERE id=?");
            $stmt->execute([$data['status'] ?? 'new', $data['priority'] ?? 'medium', $data['assigned_to'] ?? null, $id]);
            Auth::logActivity('inquiry_updated', 'inquiry', $id, "Updated inquiry #{$id}");
        } else {
            $stmt = db()->prepare("INSERT INTO inquiries (car_id, type, name, email, phone, message, car_model_sell, price_expectation, year_sell, source, ip_address, user_agent) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $data['car_id'] ?? null, $data['type'] ?? 'general', $data['name'], $data['email'] ?? null,
                $data['phone'], $data['message'] ?? null, $data['car_model_sell'] ?? null,
                $data['price_expectation'] ?? null, $data['year_sell'] ?? null,
                $data['source'] ?? 'website', $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500)
            ]);
            $id = (int)db()->lastInsertId();

            // Increment inquiry count on car
            if (!empty($data['car_id'])) {
                db()->prepare("UPDATE cars SET inquiries_count = inquiries_count + 1 WHERE id = ?")->execute([$data['car_id']]);
            }
        }
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function addInquiryNote(int $inquiryId, int $userId, string $note, bool $isInternal = true): bool {
    try {
        $stmt = db()->prepare("INSERT INTO inquiry_notes (inquiry_id, user_id, note, is_internal) VALUES (?,?,?,?)");
        $stmt->execute([$inquiryId, $userId, $note, $isInternal ? 1 : 0]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// USER FUNCTIONS
// ============================================================

function getUsers(array $filters = [], int $limit = 20, int $offset = 0): array {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['role'])) { $where[] = 'role = ?'; $params[] = $filters['role']; }
    if (!empty($filters['is_active'])) { $where[] = 'is_active = ?'; $params[] = (int)$filters['is_active']; }
    if (!empty($filters['search'])) {
        $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR company_name LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $sql = "SELECT * FROM users WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $users = $stmt->fetchAll();
    foreach ($users as &$u) unset($u['password']);
    return $users;
}

function countUsers(array $filters = []): int {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['role'])) { $where[] = 'role = ?'; $params[] = $filters['role']; }
    if (!empty($filters['is_active'])) { $where[] = 'is_active = ?'; $params[] = (int)$filters['is_active']; }
    if (!empty($filters['search'])) {
        $where[] = '(first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR company_name LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE {$whereClause}");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getUser(int $id): ?array {
    $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    if ($user) unset($user['password']);
    return $user ?: null;
}

function saveUser(array $data, ?int $id = null): array {
    try {
        if ($id) {
            $fields = "first_name=?, last_name=?, email=?, phone=?, role=?, company_name=?, address=?, city=?, country=?, whatsapp=?, is_active=?, is_verified=?, notes=?";
            $params = [$data['first_name'], $data['last_name'], $data['email'], $data['phone'] ?? null, $data['role'] ?? 'customer', $data['company_name'] ?? null, $data['address'] ?? null, $data['city'] ?? null, $data['country'] ?? 'Kenya', $data['whatsapp'] ?? null, $data['is_active'] ?? 1, $data['is_verified'] ?? 0, $data['notes'] ?? null];
            if (!empty($data['password'])) {
                $fields .= ", password=?";
                $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            $params[] = $id;
            db()->prepare("UPDATE users SET {$fields} WHERE id = ?")->execute($params);
        } else {
            // Check email uniqueness
            $stmt = db()->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$data['email']]);
            if ($stmt->fetch()) return ['success' => false, 'message' => 'Email already exists.'];

            $password = password_hash($data['password'] ?? 'changeme123', PASSWORD_DEFAULT);
            $stmt = db()->prepare("INSERT INTO users (first_name, last_name, email, phone, password, role, company_name, address, city, country, whatsapp, is_active, is_verified, notes) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$data['first_name'], $data['last_name'], $data['email'], $data['phone'] ?? null, $password, $data['role'] ?? 'customer', $data['company_name'] ?? null, $data['address'] ?? null, $data['city'] ?? null, $data['country'] ?? 'Kenya', $data['whatsapp'] ?? null, $data['is_active'] ?? 1, $data['is_verified'] ?? 0, $data['notes'] ?? null]);
            $id = (int)db()->lastInsertId();
        }
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function deleteUser(int $id): bool {
    try {
        if ($id == 1) return false; // Protect main admin
        db()->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        return true;
    } catch (PDOException $e) { return false; }
}

function getStaffUsers(): array {
    return db()->query("SELECT id, first_name, last_name, email, role FROM users WHERE role IN ('admin','staff') AND is_active = 1 ORDER BY first_name")->fetchAll();
}

// ============================================================
// INVOICE FUNCTIONS
// ============================================================

function getInvoices(array $filters = [], int $limit = 20, int $offset = 0): array {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'inv.status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['customer_id'])) { $where[] = 'inv.customer_id = ?'; $params[] = (int)$filters['customer_id']; }
    if (!empty($filters['vendor_id'])) { $where[] = 'inv.vendor_id = ?'; $params[] = (int)$filters['vendor_id']; }
    if (!empty($filters['search'])) {
        $where[] = '(inv.invoice_number LIKE ? OR inv.customer_name LIKE ? OR inv.customer_email LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $sql = "SELECT inv.*, c.title AS car_title, c.slug AS car_slug,
                   u.first_name AS creator_first, u.last_name AS creator_last
            FROM invoices inv
            LEFT JOIN cars c ON inv.car_id = c.id
            LEFT JOIN users u ON inv.created_by = u.id
            WHERE {$whereClause}
            ORDER BY inv.created_at DESC
            LIMIT {$limit} OFFSET {$offset}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countInvoices(array $filters = []): int {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['customer_id'])) { $where[] = 'customer_id = ?'; $params[] = (int)$filters['customer_id']; }
    if (!empty($filters['vendor_id'])) { $where[] = 'vendor_id = ?'; $params[] = (int)$filters['vendor_id']; }
    if (!empty($filters['search'])) {
        $where[] = '(invoice_number LIKE ? OR customer_name LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $stmt = db()->prepare("SELECT COUNT(*) FROM invoices WHERE {$whereClause}");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function getInvoice(int $id): ?array {
    $sql = "SELECT inv.*, c.title AS car_title, c.slug AS car_slug
            FROM invoices inv
            LEFT JOIN cars c ON inv.car_id = c.id
            WHERE inv.id = ?";
    $stmt = db()->prepare($sql);
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

function getInvoiceItems(int $invoiceId): array {
    $stmt = db()->prepare("SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY sort_order");
    $stmt->execute([$invoiceId]);
    return $stmt->fetchAll();
}

function saveInvoice(array $data, array $items = [], ?int $id = null): array {
    try {
        db()->beginTransaction();

        if ($id) {
            $stmt = db()->prepare("UPDATE invoices SET car_id=?, customer_id=?, vendor_id=?, inquiry_id=?, customer_name=?, customer_email=?, customer_phone=?, customer_address=?, subtotal=?, tax_amount=?, discount_amount=?, total_amount=?, status=?, due_date=?, notes=?, terms=? WHERE id=?");
            $stmt->execute([$data['car_id'] ?? null, $data['customer_id'] ?? null, $data['vendor_id'] ?? null, $data['inquiry_id'] ?? null, $data['customer_name'], $data['customer_email'] ?? null, $data['customer_phone'] ?? null, $data['customer_address'] ?? null, $data['subtotal'], $data['tax_amount'] ?? 0, $data['discount_amount'] ?? 0, $data['total_amount'], $data['status'] ?? 'draft', $data['due_date'] ?? null, $data['notes'] ?? null, $data['terms'] ?? null, $id]);
            // Delete old items and re-insert
            db()->prepare("DELETE FROM invoice_items WHERE invoice_id = ?")->execute([$id]);
        } else {
            $invoiceNumber = generateInvoiceNumber();
            $stmt = db()->prepare("INSERT INTO invoices (invoice_number, car_id, customer_id, vendor_id, inquiry_id, customer_name, customer_email, customer_phone, customer_address, subtotal, tax_amount, discount_amount, total_amount, balance_due, status, due_date, notes, terms, created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([$invoiceNumber, $data['car_id'] ?? null, $data['customer_id'] ?? null, $data['vendor_id'] ?? null, $data['inquiry_id'] ?? null, $data['customer_name'], $data['customer_email'] ?? null, $data['customer_phone'] ?? null, $data['customer_address'] ?? null, $data['subtotal'], $data['tax_amount'] ?? 0, $data['discount_amount'] ?? 0, $data['total_amount'], $data['total_amount'], $data['status'] ?? 'draft', $data['due_date'] ?? null, $data['notes'] ?? null, $data['terms'] ?? null, Auth::id()]);
            $id = (int)db()->lastInsertId();
        }

        // Insert items
        foreach ($items as $index => $item) {
            $stmt = db()->prepare("INSERT INTO invoice_items (invoice_id, description, quantity, unit_price, total_price, sort_order) VALUES (?,?,?,?,?,?)");
            $stmt->execute([$id, $item['description'], $item['quantity'] ?? 1, $item['unit_price'], $item['total_price'], $index]);
        }

        // Recalculate balance
        updateInvoiceBalance($id);

        db()->commit();
        return ['success' => true, 'id' => $id];
    } catch (PDOException $e) {
        db()->rollBack();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function updateInvoiceBalance(int $invoiceId): void {
    $stmt = db()->prepare("SELECT total_amount, (SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id = ? AND status = 'completed') AS paid FROM invoices WHERE id = ?");
    $stmt->execute([$invoiceId, $invoiceId]);
    $row = $stmt->fetch();
    if ($row) {
        $balance = $row['total_amount'] - $row['paid'];
        $paid = $row['paid'];
        $status = 'draft';
        if ($balance <= 0) $status = 'paid';
        elseif ($paid > 0) $status = 'partially_paid';

        db()->prepare("UPDATE invoices SET paid_amount = ?, balance_due = ?, status = CASE WHEN status IN ('cancelled','refunded') THEN status ELSE ? END WHERE id = ?")->execute([$paid, max(0, $balance), $status, $invoiceId]);
    }
}

function deleteInvoice(int $id): bool {
    try {
        db()->prepare("DELETE FROM invoices WHERE id = ?")->execute([$id]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// PAYMENT FUNCTIONS
// ============================================================

function getPayments(array $filters = [], int $limit = 20, int $offset = 0): array {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'p.status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['invoice_id'])) { $where[] = 'p.invoice_id = ?'; $params[] = (int)$filters['invoice_id']; }
    if (!empty($filters['customer_id'])) { $where[] = 'p.customer_id = ?'; $params[] = (int)$filters['customer_id']; }
    if (!empty($filters['payment_method_id'])) { $where[] = 'p.payment_method_id = ?'; $params[] = (int)$filters['payment_method_id']; }
    if (!empty($filters['search'])) {
        $where[] = '(p.transaction_id LIKE ? OR p.reference_number LIKE ? OR inv.invoice_number LIKE ?)';
        $s = '%' . $filters['search'] . '%';
        $params = array_merge($params, [$s, $s, $s]);
    }
    $whereClause = implode(' AND ', $where);
    $sql = "SELECT p.*, inv.invoice_number, inv.customer_name, pm.name AS method_name, pm.icon AS method_icon,
                   u.first_name AS recorder_first, u.last_name AS recorder_last
            FROM payments p
            LEFT JOIN invoices inv ON p.invoice_id = inv.id
            LEFT JOIN payment_methods pm ON p.payment_method_id = pm.id
            LEFT JOIN users u ON p.recorded_by = u.id
            WHERE {$whereClause}
            ORDER BY p.created_at DESC
            LIMIT {$limit} OFFSET {$offset}";
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function countPayments(array $filters = []): int {
    $where = ['1=1'];
    $params = [];
    if (!empty($filters['status'])) { $where[] = 'status = ?'; $params[] = $filters['status']; }
    if (!empty($filters['invoice_id'])) { $where[] = 'invoice_id = ?'; $params[] = (int)$filters['invoice_id']; }
    $whereClause = implode(' AND ', $where);
    $stmt = db()->prepare("SELECT COUNT(*) FROM payments WHERE {$whereClause}");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

function savePayment(array $data): array {
    try {
        $stmt = db()->prepare("INSERT INTO payments (invoice_id, car_id, customer_id, payment_method_id, transaction_id, amount, currency, status, payment_date, reference_number, notes, recorded_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute([
            $data['invoice_id'] ?? null, $data['car_id'] ?? null, $data['customer_id'] ?? null,
            $data['payment_method_id'] ?? null, $data['transaction_id'] ?? null, $data['amount'],
            $data['currency'] ?? Settings::get('currency_code', 'KES'), $data['status'] ?? 'completed',
            $data['payment_date'] ?? date('Y-m-d'), $data['reference_number'] ?? null,
            $data['notes'] ?? null, Auth::id()
        ]);
        $paymentId = (int)db()->lastInsertId();

        // Update invoice balance if linked
        if (!empty($data['invoice_id'])) {
            updateInvoiceBalance((int)$data['invoice_id']);
        }

        Auth::logActivity('payment_recorded', 'payment', $paymentId, "Payment of " . formatPrice($data['amount']) . " recorded");
        return ['success' => true, 'id' => $paymentId];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getPaymentMethods(bool $activeOnly = true): array {
    $sql = "SELECT * FROM payment_methods";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY sort_order";
    return db()->query($sql)->fetchAll();
}

// ============================================================
// TESTIMONIAL FUNCTIONS
// ============================================================

function getTestimonials(bool $activeOnly = true, int $limit = 10): array {
    $sql = "SELECT * FROM testimonials";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY sort_order ASC LIMIT {$limit}";
    return db()->query($sql)->fetchAll();
}

// ============================================================
// LOCATION FUNCTIONS
// ============================================================

function getLocations(bool $activeOnly = true): array {
    $sql = "SELECT * FROM locations";
    if ($activeOnly) $sql .= " WHERE is_active = 1";
    $sql .= " ORDER BY sort_order ASC, name ASC";
    return db()->query($sql)->fetchAll();
}

// ============================================================
// DASHBOARD STATISTICS
// ============================================================

function getDashboardStats(?int $vendorId = null): array {
    $vendorWhere = $vendorId ? " AND user_id = {$vendorId}" : '';
    $vendorWhereC = $vendorId ? " AND c.user_id = {$vendorId}" : '';

    $stats = [];

    // Car counts
    $stmt = db()->query("SELECT 
        COUNT(*) AS total_cars,
        SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) AS active_cars,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_cars,
        SUM(CASE WHEN status = 'sold' THEN 1 ELSE 0 END) AS sold_cars,
        SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) AS featured_cars,
        SUM(views_count) AS total_views
        FROM cars WHERE 1=1 {$vendorWhere}");
    $stats['cars'] = $stmt->fetch();

    // Inquiry counts
    $stmt = db()->query("SELECT 
        COUNT(*) AS total_inquiries,
        SUM(CASE WHEN i.status = 'new' THEN 1 ELSE 0 END) AS new_inquiries,
        SUM(CASE WHEN i.status = 'in_progress' THEN 1 ELSE 0 END) AS active_inquiries,
        SUM(CASE WHEN i.status = 'converted' THEN 1 ELSE 0 END) AS converted_inquiries
        FROM inquiries i LEFT JOIN cars c ON i.car_id = c.id WHERE 1=1 {$vendorWhereC}");
    $stats['inquiries'] = $stmt->fetch();

    // Revenue
    $stmt = db()->query("SELECT 
        COALESCE(SUM(amount), 0) AS total_revenue,
        COUNT(*) AS total_payments
        FROM payments WHERE status = 'completed'" . ($vendorId ? " AND customer_id = {$vendorId}" : ''));
    $stats['revenue'] = $stmt->fetch();

    // Invoices
    $stmt = db()->query("SELECT 
        COUNT(*) AS total_invoices,
        COALESCE(SUM(balance_due), 0) AS total_outstanding,
        SUM(CASE WHEN status = 'overdue' THEN 1 ELSE 0 END) AS overdue_invoices
        FROM invoices WHERE 1=1" . ($vendorId ? " AND vendor_id = {$vendorId}" : ''));
    $stats['invoices'] = $stmt->fetch();

    if (!$vendorId) {
        // User counts (admin only)
        $stmt = db()->query("SELECT 
            COUNT(*) AS total_users,
            SUM(CASE WHEN role = 'vendor' THEN 1 ELSE 0 END) AS total_vendors,
            SUM(CASE WHEN role = 'customer' THEN 1 ELSE 0 END) AS total_customers
            FROM users WHERE is_active = 1");
        $stats['users'] = $stmt->fetch();
    }

    return $stats;
}

/**
 * Get recent activity
 */
function getRecentActivity(int $limit = 15, ?int $userId = null): array {
    $where = $userId ? "WHERE user_id = {$userId}" : '';
    $sql = "SELECT a.*, u.first_name, u.last_name, u.avatar 
            FROM activity_log a 
            LEFT JOIN users u ON a.user_id = u.id 
            {$where}
            ORDER BY a.created_at DESC LIMIT {$limit}";
    return db()->query($sql)->fetchAll();
}

/**
 * Monthly stats for charts
 */
function getMonthlyStats(int $months = 12): array {
    $stats = [];
    for ($i = $months - 1; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-{$i} months"));
        $monthStart = $date . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $label = date('M Y', strtotime($monthStart));

        $stmt = db()->prepare("SELECT COUNT(*) FROM cars WHERE created_at BETWEEN ? AND ?");
        $stmt->execute([$monthStart, $monthEnd . ' 23:59:59']);
        $carsCount = $stmt->fetchColumn();

        $stmt = db()->prepare("SELECT COUNT(*) FROM inquiries WHERE created_at BETWEEN ? AND ?");
        $stmt->execute([$monthStart, $monthEnd . ' 23:59:59']);
        $inquiriesCount = $stmt->fetchColumn();

        $stmt = db()->prepare("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed' AND payment_date BETWEEN ? AND ?");
        $stmt->execute([$monthStart, $monthEnd]);
        $revenue = $stmt->fetchColumn();

        $stats[] = [
            'label'     => $label,
            'cars'      => (int)$carsCount,
            'inquiries' => (int)$inquiriesCount,
            'revenue'   => (float)$revenue
        ];
    }
    return $stats;
}

// ============================================================
// COMPARE FUNCTIONS
// ============================================================

function getCompareCarIds(): array {
    $sessionId = session_id();
    $stmt = db()->prepare("SELECT car_id FROM compare_sessions WHERE session_id = ?");
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function addToCompare(int $carId): array {
    $sessionId = session_id();
    $maxCompare = (int)Settings::get('max_compare_cars', 4);
    $current = getCompareCarIds();

    if (in_array($carId, $current)) {
        return ['success' => false, 'message' => 'Car already in compare list.'];
    }
    if (count($current) >= $maxCompare) {
        return ['success' => false, 'message' => "Maximum {$maxCompare} cars can be compared at once."];
    }

    try {
        db()->prepare("INSERT INTO compare_sessions (session_id, car_id) VALUES (?, ?)")->execute([$sessionId, $carId]);
        return ['success' => true, 'count' => count($current) + 1];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to add car to compare.'];
    }
}

function removeFromCompare(int $carId): bool {
    $sessionId = session_id();
    db()->prepare("DELETE FROM compare_sessions WHERE session_id = ? AND car_id = ?")->execute([$sessionId, $carId]);
    return true;
}

function clearCompare(): bool {
    $sessionId = session_id();
    db()->prepare("DELETE FROM compare_sessions WHERE session_id = ?")->execute([$sessionId]);
    return true;
}

function getCompareCars(): array {
    $carIds = getCompareCarIds();
    if (empty($carIds)) return [];
    $placeholders = implode(',', array_fill(0, count($carIds), '?'));
    $sql = "SELECT c.*, b.name AS brand_name, cat.name AS category_name,
                   (SELECT image_url FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM cars c
            LEFT JOIN brands b ON c.brand_id = b.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE c.id IN ({$placeholders})";
    $stmt = db()->prepare($sql);
    $stmt->execute($carIds);
    return $stmt->fetchAll();
}

// ============================================================
// CHAT FUNCTIONS
// ============================================================

function getChatSessions(int $limit = 50): array {
    $sql = "SELECT session_id, sender_name, sender_email, MAX(created_at) AS last_message, 
                   COUNT(*) AS message_count, SUM(CASE WHEN is_read = 0 AND sender_type = 'visitor' THEN 1 ELSE 0 END) AS unread
            FROM chat_messages 
            GROUP BY session_id, sender_name, sender_email 
            ORDER BY last_message DESC 
            LIMIT {$limit}";
    return db()->query($sql)->fetchAll();
}

function getChatMessages(string $sessionId): array {
    $stmt = db()->prepare("SELECT * FROM chat_messages WHERE session_id = ? ORDER BY created_at ASC");
    $stmt->execute([$sessionId]);
    return $stmt->fetchAll();
}

function sendChatMessage(string $sessionId, string $message, string $senderType = 'visitor', ?string $name = null, ?string $email = null, ?int $senderId = null, ?int $carId = null): bool {
    try {
        $stmt = db()->prepare("INSERT INTO chat_messages (session_id, sender_type, sender_id, sender_name, sender_email, message, car_id) VALUES (?,?,?,?,?,?,?)");
        $stmt->execute([$sessionId, $senderType, $senderId, $name, $email, $message, $carId]);
        return true;
    } catch (PDOException $e) { return false; }
}

function markChatRead(string $sessionId): void {
    db()->prepare("UPDATE chat_messages SET is_read = 1 WHERE session_id = ? AND sender_type = 'visitor'")->execute([$sessionId]);
}

function getUnreadChatCount(): int {
    return (int)db()->query("SELECT COUNT(DISTINCT session_id) FROM chat_messages WHERE is_read = 0 AND sender_type = 'visitor'")->fetchColumn();
}

// ============================================================
// FAVORITES FUNCTIONS
// ============================================================

function toggleFavorite(int $carId, ?int $userId = null): array {
    if (!$userId) return ['success' => false, 'message' => 'Please login to save favorites.'];
    try {
        $stmt = db()->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
        $stmt->execute([$userId, $carId]);
        if ($stmt->fetch()) {
            db()->prepare("DELETE FROM favorites WHERE user_id = ? AND car_id = ?")->execute([$userId, $carId]);
            return ['success' => true, 'action' => 'removed'];
        } else {
            db()->prepare("INSERT INTO favorites (user_id, car_id) VALUES (?, ?)")->execute([$userId, $carId]);
            return ['success' => true, 'action' => 'added'];
        }
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Failed to update favorites.'];
    }
}

function isFavorite(int $carId, ?int $userId = null): bool {
    if (!$userId) return false;
    $stmt = db()->prepare("SELECT id FROM favorites WHERE user_id = ? AND car_id = ?");
    $stmt->execute([$userId, $carId]);
    return (bool)$stmt->fetch();
}

function getUserFavorites(int $userId, int $limit = 50): array {
    $sql = "SELECT c.*, b.name AS brand_name, cat.name AS category_name,
                   (SELECT image_url FROM car_images WHERE car_id = c.id AND is_primary = 1 LIMIT 1) AS primary_image
            FROM favorites fav
            JOIN cars c ON fav.car_id = c.id
            LEFT JOIN brands b ON c.brand_id = b.id
            LEFT JOIN categories cat ON c.category_id = cat.id
            WHERE fav.user_id = ?
            ORDER BY fav.created_at DESC LIMIT {$limit}";
    $stmt = db()->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetchAll();
}

// ============================================================
// PAGE FUNCTIONS
// ============================================================

function getPage(string $slug): ?array {
    $stmt = db()->prepare("SELECT * FROM pages WHERE slug = ? AND is_active = 1");
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function savePage(array $data, int $id): bool {
    try {
        $stmt = db()->prepare("UPDATE pages SET title=?, content=?, meta_title=?, meta_description=?, is_active=?, updated_at=NOW() WHERE id=?");
        $stmt->execute([$data['title'], $data['content'], $data['meta_title'] ?? null, $data['meta_description'] ?? null, $data['is_active'] ?? 1, $id]);
        return true;
    } catch (PDOException $e) { return false; }
}

// ============================================================
// RENDER HELPERS
// ============================================================

/**
 * Render car card HTML
 */
function renderCarCard(array $car, string $class = ''): string {
    $image = $car['primary_image'] ? resolveUrl($car['primary_image']) : BASE_URL . '/assets/images/car-placeholder.jpg';
    $url = BASE_URL . '/car/' . $car['slug'];
    $price = formatPrice($car['price']);
    $oldPrice = $car['old_price'] ? '<span class="text-sm text-gray-400 line-through ml-2">' . formatPrice($car['old_price']) . '</span>' : '';
    $isFav = Auth::check() ? isFavorite($car['id'], Auth::id()) : false;

    $badges = '';
    if ($car['condition_type'] === 'new') $badges .= '<span class="car-badge badge-new">New</span>';
    if ($car['condition_type'] === 'used') $badges .= '<span class="car-badge badge-used">Used</span>';
    if ($car['condition_type'] === 'certified') $badges .= '<span class="car-badge badge-certified">Certified</span>';
    if ($car['is_featured']) $badges .= '<span class="car-badge badge-featured">Featured</span>';
    if ($car['is_offer']) $badges .= '<span class="car-badge badge-offer">' . ($car['offer_label'] ?: 'Special Offer') . '</span>';
    if ($car['is_hot_deal']) $badges .= '<span class="car-badge badge-hot">Hot Deal</span>';

    $html = <<<HTML
    <article class="car-card group {$class}" data-car-id="{$car['id']}">
        <div class="car-card-image">
            <a href="{$url}" aria-label="View {$car['title']}">
                <img src="{$image}" alt="{$car['title']}" loading="lazy" class="w-full h-56 object-cover transition-transform duration-500 group-hover:scale-105">
            </a>
            <div class="car-badges">{$badges}</div>
            <div class="car-price-tag">{$price}{$oldPrice}</div>
            <div class="car-card-actions">
                <button onclick="toggleFavorite({$car['id']}, this)" class="car-action-btn" title="Save" data-fav="{$isFav}">
                    <i data-lucide="heart" class="w-4 h-4"></i>
                </button>
                <button onclick="addToCompare({$car['id']})" class="car-action-btn" title="Compare">
                    <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                </button>
            </div>
            <span class="car-image-count"><i data-lucide="camera" class="w-3.5 h-3.5"></i> {$car['image_count']}</span>
        </div>
        <div class="car-card-body">
            <h3 class="car-card-title">
                <a href="{$url}">{$car['title']}</a>
            </h3>
            <p class="car-card-excerpt">{$car['excerpt']}</p>
            <div class="car-card-location">
                <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                <span>{$car['location']}</span>
            </div>
            <div class="car-card-specs">
                <span title="Year"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> {$car['year']}</span>
                <span title="Mileage"><i data-lucide="gauge" class="w-3.5 h-3.5"></i> {$car['mileage']} {$car['mileage_unit']}</span>
                <span title="Fuel"><i data-lucide="fuel" class="w-3.5 h-3.5"></i> {$car['fuel_type']}</span>
                <span title="Transmission"><i data-lucide="settings-2" class="w-3.5 h-3.5"></i> {$car['transmission']}</span>
            </div>
        </div>
    </article>
    HTML;

    return $html;
}

/**
 * Render empty state
 */
function renderEmptyState(string $title = 'No results found', string $description = '', string $icon = 'inbox'): string {
    return <<<HTML
    <div class="text-center py-16">
        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-lucide="{$icon}" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">{$title}</h3>
        <p class="text-gray-500">{$description}</p>
    </div>
    HTML;
}
