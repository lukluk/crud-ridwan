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

// Function to create a new product
function createProduct($name, $price, $color, $brand, $year, $spec) {
    global $conn;
    
    $name = $conn->real_escape_string($name);
    $color = $conn->real_escape_string($color);
    $brand = $conn->real_escape_string($brand);
    $year = $conn->real_escape_string($year);
    $spec = $conn->real_escape_string($spec);
    
    $sql = "INSERT INTO `product` (name, price, color, brand, year, spec)
            VALUES ('$name', $price, '$color', '$brand', '$year', '$spec')";
    
    if ($conn->query($sql) === true) {
        return json_encode(array("status" => "success"));
    } else {
        return json_encode(array("status" => "error", "message" => $conn->error));
    }
}

// Function to retrieve all products
function getAllProducts() {
    global $conn;
    
    $sql = "SELECT * FROM `product`";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $products = array();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        return json_encode($products);
    } else {
        return json_encode(array());
    }
}

// Function to retrieve a product by its ID
function getProductById($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    $sql = "SELECT * FROM `product` WHERE id = $id";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        return json_encode($product);
    } else {
        return json_encode(array());
    }
}

// Function to update a product
function updateProduct($id, $name, $price, $color, $brand, $year, $spec) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    $name = $conn->real_escape_string($name);
    $color = $conn->real_escape_string($color);
    $brand = $conn->real_escape_string($brand);
    $year = $conn->real_escape_string($year);
    $spec = $conn->real_escape_string($spec);
    
    $sql = "UPDATE `product` SET name = '$name', price = $price, 
            color = '$color', brand = '$brand', year = '$year', spec = '$spec' WHERE id = $id";
    
    if ($conn->query($sql) === true) {
        return json_encode(array("status" => "success"));
    } else {
        return json_encode(array("status" => "error", "message" => $conn->error));
    }
}

// Function to delete a product
function deleteProduct($id) {
    global $conn;
    
    $id = $conn->real_escape_string($id);
    
    $sql = "DELETE FROM `product` WHERE id = $id";
    
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
        // Create a new product
        $name = $_POST['name'];
        $price = $_POST['price'];
        $color = $_POST['color'];
        $brand = $_POST['brand'];
        $year = $_POST['year'];
        $spec = $_POST['spec'];

        echo createProduct($name, $price, $color, $brand, $year, $spec);
        break;
    case 'GET':
        // Retrieve all products or a product by ID
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            echo getProductById($id);
        } else {
            echo getAllProducts();
        }
        break;
    case 'PUT':
        // Update a product
        parse_str(file_get_contents("php://input"), $putParams);
        $id = $putParams['id'];
        $name = $putParams['name'];
        $price = $putParams['price'];
        $color = $putParams['color'];
        $brand = $putParams['brand'];
        $year = $putParams['year'];
        $spec = $putParams['spec'];

        echo updateProduct($id, $name, $price, $color, $brand, $year, $spec);
        break;
    case 'DELETE':
        // Delete a product
        parse_str(file_get_contents("php://input"), $deleteParams);
        $id = $deleteParams['id'];

        echo deleteProduct($id);
        break;
    default:
        // Invalid request method
        echo json_encode(array("status" => "error", "message" => "Invalid request method."));
        break;
}

// Close the database connection
$conn->close();
?>
