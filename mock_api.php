<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    header('Content-Type: application/json');
    
    $input = json_decode(file_get_contents('php://input'), true);

    file_put_contents('log.txt', print_r($input, true)); // تسجيل البيانات الواردة في ملف log

    if ($path === '/mock_api.php/book') {
        if (isset($input['event_id'], $input['event_date'], $input['ticket_adult_price'], $input['ticket_adult_quantity'], $input['ticket_kid_price'], $input['ticket_kid_quantity'], $input['barcode'])) {
            $response = ['message' => 'order successfully booked'];
            echo json_encode($response);
        } else {
            $response = ['error' => 'barcode already exists'];
            echo json_encode($response);
        }
    } elseif ($path === '/mock_api.php/approve') {
        if (isset($input['barcode'])) {
            $response = ['message' => 'order successfully approved'];
            echo json_encode($response);
        } else {
            $response = ['error' => 'event cancelled'];
            echo json_encode($response);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} else {
    // رسالة تأكيد عند الوصول إلى الملف عبر GET
    echo "Mock API is working!";
}
?>
