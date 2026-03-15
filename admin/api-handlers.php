<?php
/**
 * Admin API Handlers - AJAX endpoints for admin panel
 */
require_once dirname(__DIR__) . '/functions.php';
header('Content-Type: application/json');

if (!Auth::isAdmin() && !Auth::isStaff()) {
    jsonResponse(['success' => false, 'message' => 'Unauthorized'], 403);
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

switch ($action) {
    case 'update_car_status':
        $carId = (int)($input['car_id'] ?? 0);
        $status = $input['status'] ?? '';
        if ($carId && $status) {
            updateCarStatus($carId, $status);
            jsonResponse(['success' => true, 'message' => 'Status updated']);
        }
        jsonResponse(['success' => false, 'message' => 'Invalid data'], 400);
        break;

    case 'update_inquiry_status':
        $id = (int)($input['id'] ?? 0);
        $status = $input['status'] ?? '';
        if ($id && $status) {
            saveInquiry(['status' => $status, 'priority' => $input['priority'] ?? 'medium', 'assigned_to' => $input['assigned_to'] ?? null], $id);
            jsonResponse(['success' => true]);
        }
        jsonResponse(['success' => false], 400);
        break;

    case 'assign_inquiry':
        $id = (int)($input['id'] ?? 0);
        $assignTo = (int)($input['assigned_to'] ?? 0);
        if ($id) {
            db()->prepare("UPDATE inquiries SET assigned_to=? WHERE id=?")->execute([$assignTo ?: null, $id]);
            Auth::logActivity('inquiry_assigned', 'inquiry', $id, "Assigned to user #{$assignTo}");
            jsonResponse(['success' => true]);
        }
        jsonResponse(['success' => false], 400);
        break;

    case 'bulk_car_action':
        $ids = $input['ids'] ?? [];
        $bulkAction = $input['bulk_action'] ?? '';
        if (!empty($ids) && $bulkAction) {
            foreach ($ids as $id) {
                switch ($bulkAction) {
                    case 'activate': updateCarStatus((int)$id, 'active'); break;
                    case 'deactivate': updateCarStatus((int)$id, 'draft'); break;
                    case 'delete': deleteCar((int)$id); break;
                    case 'feature': db()->prepare("UPDATE cars SET is_featured=1 WHERE id=?")->execute([(int)$id]); break;
                    case 'unfeature': db()->prepare("UPDATE cars SET is_featured=0 WHERE id=?")->execute([(int)$id]); break;
                }
            }
            jsonResponse(['success' => true, 'message' => count($ids) . ' cars updated']);
        }
        jsonResponse(['success' => false], 400);
        break;

    case 'get_dashboard_stats':
        jsonResponse(['success' => true, 'data' => getDashboardStats()]);
        break;

    case 'delete_payment':
        $id = (int)($input['id'] ?? 0);
        if ($id) {
            $payment = db()->prepare("SELECT invoice_id FROM payments WHERE id=?")->execute([$id]);
            $p = $payment->fetch();
            db()->prepare("DELETE FROM payments WHERE id=?")->execute([$id]);
            if ($p && $p['invoice_id']) updateInvoiceBalance((int)$p['invoice_id']);
            jsonResponse(['success' => true]);
        }
        jsonResponse(['success' => false], 400);
        break;

    case 'update_invoice_status':
        $id = (int)($input['id'] ?? 0);
        $status = $input['status'] ?? '';
        if ($id && $status) {
            db()->prepare("UPDATE invoices SET status=? WHERE id=?")->execute([$status, $id]);
            jsonResponse(['success' => true]);
        }
        jsonResponse(['success' => false], 400);
        break;

    case 'export_cars':
        // Generate CSV export
        $cars = getCars([], 10000);
        $csv = "ID,Title,Brand,Model,Year,Price,Condition,Status,Location,Mileage,Views,Created\n";
        foreach ($cars as $c) {
            $csv .= "{$c['id']},\"{$c['title']}\",\"{$c['brand_name']}\",\"{$c['model']}\",{$c['year']},{$c['price']},{$c['condition_type']},{$c['status']},\"{$c['location']}\",{$c['mileage']},{$c['views_count']},{$c['created_at']}\n";
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="cars_export_'.date('Y-m-d').'.csv"');
        echo $csv;
        exit;

    case 'export_inquiries':
        $inquiries = getInquiries([], 10000);
        $csv = "ID,Name,Email,Phone,Type,Car,Status,Priority,Date\n";
        foreach ($inquiries as $i) {
            $csv .= "{$i['id']},\"{$i['name']}\",\"{$i['email']}\",\"{$i['phone']}\",{$i['type']},\"{$i['car_title']}\",{$i['status']},{$i['priority']},{$i['created_at']}\n";
        }
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="inquiries_export_'.date('Y-m-d').'.csv"');
        echo $csv;
        exit;

    default:
        jsonResponse(['success' => false, 'message' => 'Unknown action'], 400);
}
