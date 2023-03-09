<?php
session_start();
?>

<html>
<head>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <style>
        .cart-container {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            width: 300px;
            padding: 1rem;
            background-color: #f8f9fa;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            overflow-y: auto;
        }

        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
        }

        .cart-item img {
            max-height: 50px;
        }

        .cart-item h6 {
            margin: 0;
        }

        .cart-item .remove-btn {
            background-color: #dc3545;
            border: none;
            color: #fff;
            padding: 0.5rem;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <h3>Categories</h3>
                <div class="list-group">
                    <?php
                    
                    // Initialize cart array if not already set
                    if (!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = array();
                    }
                    // Check if product was added to cart
                    if (isset($_POST['add-to-cart'])) {
                        $id = $_POST['id'];
                        $name = $_POST['name'];
                        $price = $_POST['price'];
                        $quantity = $_POST['quantity'];

                        // Check if product already exists in cart
                        if (isset($_SESSION['cart'][$id])) {
                        // Update quantity and price of existing item
                        $_SESSION['cart'][$id]['quantity'] += $quantity;
                        $_SESSION['cart'][$id]['price'] = $price * $_SESSION['cart'][$id]['quantity'];
                        } else {
                        // Add new item to cart
                        $_SESSION['cart'][$id] = array(
                            'name' => $name,
                            'price' => $price * $quantity,
                            'quantity' => $quantity
                        );
                        }        
                    }

                    // Check if product was removed from cart
                    if (isset($_POST['remove-from-cart'])) {
                        $id = $_POST['id'];
                        unset($_SESSION['cart'][$id]);
                        }
                    // Check if cart was cleared  
                    if (isset($_POST['clear-cart'])) {
                        unset($_SESSION['cart']);   
                        }
                        // Fetch product data
                        $host = "localhost";
                        $user = "root";
                        $password = "";
                        $dbname = "inventory_system";

                        $conn = mysqli_connect($host, $user, $password, $dbname);

                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }

                    $sql = "SELECT id, name, sale_price FROM products";
                    $result = mysqli_query($conn, $sql);

                    $result = mysqli_query($conn, "SELECT * FROM categories");
                    while ($row = mysqli_fetch_array($result)) {
                        echo '<a href="?category='.$row['id'].'" class="list-group-item">'.$row['name'].'</a>';
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row">
                    <?php
                    if (isset($_GET['category'])) {
                        $category_id = $_GET['category'];
                        $result = mysqli_query($conn, "SELECT * FROM products WHERE categorie_id = $category_id");
                        while ($row = mysqli_fetch_array($result)) {
                            $id = isset($row['id']) ? $row['id'] : '';
                            $name = isset($row['name']) ? $row['name'] : '';
                            $salePrice = isset($row['sale_price']) ? $row['sale_price'] : '';
                            echo '<div class="col-md-4">';
                            echo '<div class="card">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">'.$row['name'].'</h5>';                            
                            echo '<h6 class="card-subtitle mb-2 text-muted">'.$row['sale_price'].'</h6>';
                            echo '<button type="submit" class="btn btn-outline-primary add-to-cart" name="add-to-cart"><i class="bi bi-cart-plus"></i></button>';
                            echo '<input type="number" name="quantity" value="1" min="1" style="width: 40px;">';
                            echo '</div>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="cart-container" id="cart">
        <h3>Cart</h3>
        <?php if (!empty($_SESSION['cart'])): ?>
          <?php
          $totalPrice = 0;
          foreach($_SESSION['cart'] as $id => $product) {
            $totalPrice += $product['price'] * $product['quantity'];
          }
          ?>
          <table class="table">
            <thead>
              <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($_SESSION['cart'] as $id => $product): ?>
              <tr>
                <td>
                  <?php echo $product['name']; ?>
                </td>
                <td>
                  <?php echo $product['price']; ?>
                </td>
                <td>
                  <?php echo $product['quantity']; ?>
                </td>

                <td>
                  <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-danger" class="remove-from-cart" name="remove-from-cart"><i
                        class="bi bi-trash"></i></button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              <tr>
                <td colspan="3" class="text-end"><strong>Total Price:</strong></td>
                <td><strong>
                    <?php echo $totalPrice; ?>
                  </strong></td>
              </tr>
            </tbody>
          </table>
          <div class="text-end">
            <form action="" method="POST" class="d-inline-block">
              <button type="submit" class="btn btn-danger" class="clear-cart" name="clear-cart">Clear Cart</button>
            </form>
            <form action="checkout.php" method="POST" class="d-inline-block">
              <button type="submit" class="btn btn-primary" name="checkout">Checkout</button>
            </form>
          </div>
          <?php else: ?>
          <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

    <!-- Add Bootstrap JS at the bottom of the page -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript code to handle the "Add to Cart" button click -->
    <script>
  $('.add-to-cart').click(function (event) {
  event.preventDefault(); // prevent default action
  // rest of the code here
});

  $(document).ready(function () {
  // Define an empty cart array
  var cart = [];

  // Handle add to cart button click
  $('.add-to-cart').click(function () {
    // Get the product id
    var id = $(this).data('id');

    // Check if the product is already in the cart
    var cartItem = cart.find(item => item.id === id);
    if (cartItem) {
      // If the product is already in the cart, increment its quantity
      cartItem.quantity++;
    } else {
      // If the product is not in the cart, add it
      cart.push({ id: id, quantity: 1 });
    }

    // Update the cart UI
    updateCartUI();

    // Send the data to the server using AJAX
    $.ajax({
      url: '/add-to-cart',
      type: 'POST',
      data: { id: id },
      success: function (data) {
        // Handle the server response if needed
      },
      error: function () {
        // Handle the error if needed
      }
    });
  });

  // Handle remove from cart button click
  $('.remove-from-cart').click(function () {
    // Get the product id
    var id = $(this).data('id');

    // Find the index of the product in the cart
    var index = cart.findIndex(item => item.id === id);

    // If the product is in the cart, remove it
    if (index !== -1) {
      cart.splice(index, 1);
    }

    // Update the cart UI
    updateCartUI();

    // Send the data to the server using AJAX
    $.ajax({
      url: '/remove-from-cart',
      type: 'POST',
      data: { id: id },
      success: function (data) {
        // Handle the server response if needed
      },
      error: function () {
        // Handle the error if needed
      }
    });
  });

  // Function to update the cart UI
  function updateCartUI() {
    // Get the cart items element
    var cartItemsElement = $('#cart-items');

    // Clear the cart items element
    cartItemsElement.empty();

    // Iterate over the cart items and add them to the cart UI
    cart.forEach(function (item) {
      var product = getProductById(item.id);
      var li = $('<li></li>');
      li.text(product.name + ' x ' + item.quantity + ' = $' + (product.price * item.quantity));
      cartItemsElement.append(li);
    });
  }

  // Function to get a product by its id
  function getProductById(id) {
    // Find the product in the products array
    var product = products.find(p => p.id === id);

    // Return the product if found, otherwise null
    return product || null;
  }

});

</script>
</body>
</html>
