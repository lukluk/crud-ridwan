<?php
// MySQL connection configuration
$servername = "localhost";
$username = "root";
$password = "password";
$database = "ridwan";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to create a new order
function createOrder($customerName, $productId, $address, $paymentMethod, $status) {
    global $conn;
    
    $customerName = $conn->real_escape_string($customerName);
    $address = $conn->real_escape_string($address);
    $paymentMethod = $conn->real_escape_string($paymentMethod);
    $status = $conn->real_escape_string($status);
    
    $sql = "INSERT INTO `order` (customer_name, id_product, address, payment_method, status)
            VALUES ('$customerName', $productId, '$address', '$paymentMethod', '$status')";
    
    if ($conn->query($sql) === true) {
        return json_encode(array("status" => "success"));
    } else {
        return json_encode(array("status" => "error", "message" => $conn->error));
    }
}

// Function to retrieve all orders
function getAllOrders() {
    global $conn;
    
    $sql = "SELECT * FROM `order`";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $orders = array();
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return json_encode($orders);
    } else {
        return json_encode(array());
    }
}

// Function to retrieve an order by its ID
function getOrderById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT * FROM `order` WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        return json_encode($order);
    } else {
        return json_encode(array());
    }
}

// Function to update an order
function updateOrder($id, $customerName, $productId, $address, $paymentMethod, $status) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $customerName = $conn->real_escape_string($customerName);
    $address = $conn->real_escape_string($address);
    $paymentMethod = $conn->real_escape_string($paymentMethod);
    $status = $conn->real_escape_string($status);
    
    $sql = "UPDATE `order` SET customer_name = '$customerName', id_product = $productId, 
            address = '$address', payment_method = '$paymentMethod', status = '$status' WHERE id = $id";
    
    if ($conn->query($sql) === true) {
        return json_encode(array("status" => "success"));
    } else {
        return json_encode(array("status" => "error", "message" => $conn->error));
    }
}

// Function to delete an order
function deleteOrder($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    $sql = "DELETE FROM `order` WHERE id = $id";
    
    if ($conn->query($sql) === true) {
        return json_encode(array("status" => "success"));
    } else {
        return json_encode(array("status" => "error", "message" => $conn->error));
    }
}

// Check the request method
$method = $_SERVER['REQUEST_METHOD'];

// Perform the requested CRUD operation based on the method
switch ($method) {
    case 'POST':
        // Create a new order
        $customerName = $_POST['customer_name'];
        $productId = $_POST['id_product'];
        $address = $_POST['address'];
        $paymentMethod = $_POST['payment_method'];
        $status = $_POST['status'];

        echo createOrder($customerName, $productId, $address, $paymentMethod, $status);
        break;
    case 'GET':
        // Retrieve all orders or an order by ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo getOrderById($id);
        } else {
            echo getAllOrders();
        }
        break;
    case 'PUT':
        // Update an order
        parse_str(file_get_contents("php://input"), $putParams);
        $id = $putParams['id'];
        $customerName = $putParams['customer_name'];
        $productId = $putParams['id_product'];
        $address = $putParams['address'];
        $paymentMethod = $putParams['payment_method'];
        $status = $putParams['status'];

        echo updateOrder($id, $customerName, $productId, $address, $paymentMethod, $status);
        break;
    case 'DELETE':
        // Delete an order
        parse_str(file_get_contents("php://input"), $deleteParams);
        $id = $deleteParams['id'];

        echo deleteOrder($id);
        break;
    default:
        // Invalid request method
        echo json_encode(array("status" => "error", "message" => "Invalid request method."));
        break;
}

// Close the database connection
$conn->close();
?>
