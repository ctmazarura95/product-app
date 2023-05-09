<?php
// Connect to the database server
$dbhost = 'localhost';
$dbuser = 'admin';
$dbpass = 'password';
$dbname = 'products';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Get the action parameter from the URL
$action = $_GET['action'] ?? 'list';

// Perform different actions based on the value of action
switch ($action) {
  case 'list':
    // List all products
    $sql = "SELECT * FROM products";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      echo "<h1>Product List</h1>";
      echo "<table border='1'>";
      echo "<tr><th>ID</th><th>Name</th><th>Price</th><th>Actions</th></tr>";
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['name'] . "</td>";
        echo "<td>" . $row['price'] . "</td>";
        echo "<td>";
        echo "<a href='?action=show&id=" . $row['id'] . "'>Show</a> | ";
        echo "<a href='?action=edit&id=" . $row['id'] . "'>Edit</a> | ";
        echo "<a href='?action=delete&id=" . $row['id'] . "'>Delete</a>";
        echo "</td>";
        echo "</tr>";
      }
      echo "</table>";
      mysqli_free_result($result);
    } else {
      echo "Error: " . mysqli_error($conn);
    }
    break;
  case 'show':
    // Show a single product
    $id = $_GET['id'] ?? 0;
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
      if ($row = mysqli_fetch_assoc($result)) {
        echo "<h1>Product Details</h1>";
        echo "<p>ID: " . $row['id'] . "</p>";
        echo "<p>Name: " . $row['name'] . "</p>";
        echo "<p>Price: " . $row['price'] . "</p>";
        echo "<p><a href='?action=list'>Back to list</a></p>";
      } else {
        echo "Product not found.";
      }
      mysqli_free_result($result);
    } else {
      echo "Error: " . mysqli_error($conn);
    }
    break;
  case 'edit':
    // Edit a product
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Update the product in the database
      $id = $_POST['id'];
      $name = $_POST['name'];
      $price = $_POST['price'];
      $sql = "UPDATE products SET name = '$name', price = '$price' WHERE id = $id";
      if (mysqli_query($conn, $sql)) {
        header("Location: ?action=list");
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    } else {
      // Show the edit form
      $id = $_GET['id'] ?? 0;
      $sql = "SELECT * FROM products WHERE id = $id";
      $result = mysqli_query($conn, $sql);
      if ($result) {
        if ($row = mysqli_fetch_assoc($result)) {
          echo "<h1>Edit Product</h1>";
          echo "<form method='post'>";
          echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
          echo "<p>Name: <input type='text' name='name' value='" . $row['name'] . "'></p>";
          echo "<p>Price: <input type='number' name='price' value='" . $row['price'] . "'></p>";
          echo "<p><input type='submit' value='Save'></p>";
          echo "</form>";
          echo "<p><a href='?action=list'>Cancel</a></p>";
        } else {
          echo "Product not found.";
        }
        mysqli_free_result($result);
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    }
    break;
  case 'delete':
    // Delete a product
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      // Delete the product from the database
      $id = $_POST['id'];
      $sql = "DELETE FROM products WHERE id = $id";
      if (mysqli_query($conn, $sql)) {
        header("Location: ?action=list");
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    } else {
      // Show the confirmation form
      $id = $_GET['id'] ?? 0;
      $sql = "SELECT * FROM products WHERE id = $id";
      $result = mysqli_query($conn, $sql);
      if ($result) {
        if ($row = mysqli_fetch_assoc($result)) {
          echo "<h1>Delete Product</h1>";
          echo "<form method='post'>";
          echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
          echo "<p>Are you sure you want to delete this product?</p>";
          echo "<p>ID: " . $row['id'] . "</p>";
          echo "<p>Name: " . $row['name'] . "</p>";
          echo "<p>Price: " . $row['price'] . "</p>";
          echo "<p><input type='submit' value='Delete'></p>";
          echo "</form>";
          echo "<p><a href='?action=list'>Cancel</a></p>";
        } else {
          echo "Product not found.";
        }
        mysqli_free_result($result);
      } else {
        echo "Error: " . mysqli_error($conn);
      }
    }
    break;
  default:
    // Invalid action
    echo "Invalid action.";
}

// Close the database connection
mysqli_close($conn);

?>
