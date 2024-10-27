<?php
include 'config.php';

function generateBarcode() {
    return substr(str_shuffle(str_repeat($x='0123456789', ceil(12/strlen($x)) )),1,12);
}

function addOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $user_id) {
    global $conn;
    $barcode = generateBarcode();

    while (true) {
        $response = file_get_contents('http://localhost/mock_api.php/book', false, stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type: application/json',
                'content' => json_encode(array(
                    'event_id' => $event_id,
                    'event_date' => $event_date,
                    'ticket_adult_price' => $ticket_adult_price,
                    'ticket_adult_quantity' => $ticket_adult_quantity,
                    'ticket_kid_price' => $ticket_kid_price,
                    'ticket_kid_quantity' => $ticket_kid_quantity,
                    'barcode' => $barcode
                ))
            )
        )));
        $data = json_decode($response, true);
        if ($data['message'] == 'order successfully booked') {
            break;
        } elseif ($data['error'] == 'barcode already exists') {
            $barcode = generateBarcode();
        } else {
            throw new Exception("Unexpected error: " . json_encode($data));
        }
    }

    $response = file_get_contents('http://localhost/mock_api.php/approve', false, stream_context_create(array(
        'http' => array(
            'method' => 'POST',
            'header' => 'Content-type: application/json',
            'content' => json_encode(array('barcode' => $barcode))
        )
    )));
    $data = json_decode($response, true);
    
    if ($data['message'] != 'order successfully approved') {
        throw new Exception("Approval failed: " . json_encode($data));
    }

    $equal_price = $ticket_adult_price * $ticket_adult_quantity + $ticket_kid_price * $ticket_kid_quantity;
    $stmt = $conn->prepare("INSERT INTO orders (event_id, event_date, ticket_adult_price, ticket_adult_quantity, ticket_kid_price, ticket_kid_quantity, barcode, user_id, equal_price, created) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isiiiiiii", $event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $barcode, $user_id, $equal_price);
    $stmt->execute();
    $stmt->close();
}

try {
    addOrder(1, '2021-08-21 13:00:00', 700, 1, 450, 0, 1);
    echo "Order added successfully!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
