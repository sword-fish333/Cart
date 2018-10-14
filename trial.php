<?php
session_start();
$product_ids = array();
//session_destroy();

//check if button to add to cart was submitted
if(filter_input(INPUT_POST, 'add_to_cart')){
    if(isset($_SESSION['shopping_cart'])){
        
        //how many products are
        $count = count($_SESSION['shopping_cart']);
        
        //create sequantial array for matching array keys to products id's
        $product_ids = array_column($_SESSION['shopping_cart'], 'id');
        
        if (!in_array(filter_input(INPUT_GET, 'id'), $product_ids)){
        $_SESSION['shopping_cart'][$count] = array
            (
                'id' => filter_input(INPUT_GET, 'id'),
                'name' => filter_input(INPUT_POST, 'name'),
                'price' => filter_input(INPUT_POST, 'price'),
                'quantity' => filter_input(INPUT_POST, 'quantity')
            );   
        }
        else { //product already exists, increase quantity
            //match array key to id of the product being added to the cart
            for ($i = 0; $i < count($product_ids); $i++){
                if ($product_ids[$i] == filter_input(INPUT_GET, 'id')){
                    //add item quantity to the existing product in the array
                    $_SESSION['shopping_cart'][$i]['quantity'] += filter_input(INPUT_POST, 'quantity');
                }
            }
        }
        
    }
    else { 
        //create array using submitted form data, start from key 0 and fill it with values
        $_SESSION['shopping_cart'][0] = array
        (
            'id' => filter_input(INPUT_GET, 'id'),
            'name' => filter_input(INPUT_POST, 'name'),
            'price' => filter_input(INPUT_POST, 'price'),
            'quantity' => filter_input(INPUT_POST, 'quantity')
        );
    }
}

if(filter_input(INPUT_GET, 'action') == 'delete'){
    //loop through products to see if it matches
    foreach($_SESSION['shopping_cart'] as $key => $product){
        if ($product['id'] == filter_input(INPUT_GET, 'id')){
            //remove product when it matches id
            unset($_SESSION['shopping_cart'][$key]);
        }
    }
    //reset session array keys 
    $_SESSION['shopping_cart'] = array_values($_SESSION['shopping_cart']);
}

//pre_r($_SESSION);

function pre_r($array){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}
?>

<html>
<head>
	<title>Shopping Cart</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Aldrich' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Antic Slab' rel='stylesheet'>
</head>
<body class="parallax">
	 <div class="containner ">


	 	<!--connect to database-->
			<?php

					$connect=mysqli_connect('localhost','root','','cart');

					if(mysqli_connect_errno()):
						echo "Failed to connect to MySql:".mysqli_connect_error();
					endif;
			?>
			<header>
			<img src="Images/logo.png" id="logo">
					<h1 class="title">Shop IT</h1>
				</header>
				<?php
					$query='SELECT * FROM products ORDER BY id ASC';

					$result=mysqli_query($connect,$query);

					if($result):
						if(mysqli_num_rows($result)>0):
							while($product=mysqli_fetch_assoc($result)):
						

					?>
<div style="margin-left: 60px; margin-right: 60px;">
			<div class="col-sm-4 col-md-3" >	

			<form method="post" action="trial.php?action=add&id=<?php echo $product['id']; ?>">
				<div class="products">
					<img src="Images/<?php echo $product['image']; ?>" class="img-responsive img-thumbnail" style="height: 250px; width: auto; margin:auto;">
					<h4 class="text-info"><?php echo $product['name'] ?></h4>
					<h4><?php echo $product['price'];?>LEI</h4>
					<label>Quantity</label>
					<input type="text" name="quantity" class="form-control" value="1" style="width: 40px;">
					<input type="hidden" name="name" value="<?php echo $product['name']; ?>">
					<input type="hidden" name="price" value="<?php echo $product['price']; ?>">
					<input type="submit" name="add_to_cart" class="btn btn-info" value="Add to cart" style="margin-top: 10px;">
				</div>

			</form>
			</div>
		</div>
		<?php

			endwhile;
		endif;
	endif;
?>

<div style="clear:both"></div>  
        <br />  
        <div class="table-responsive">  
        <table class="table" >  
            <tr><th colspan="5"><h3>Order Details</h3></th></tr>   
        <tr>  
             <th width="40%">Product Name</th>  
             <th width="10%">Quantity</th>  
             <th width="20%">Price</th>  
             <th width="15%">Total</th>  
             <th width="5%">Action</th>  
        </tr>  
        <?php   
        if(!empty($_SESSION['shopping_cart'])):  
            
             $total = 0;  
        
             foreach($_SESSION['shopping_cart'] as $key => $product): 
        ?>  
        <tr>  
           <td><?php echo $product['name']; ?></td>  
           <td><?php echo $product['quantity']; ?></td>  
           <td> <?php echo $product['price']; ?> LEI</td>  
           <td> <?php echo number_format($product['quantity'] * $product['price'], 2); ?> LEI</td>  
           <td>
               <a href="trial.php?action=delete&id=<?php echo $product['id']; ?>">
                    <div class="btn btn-danger">Remove</div>
               </a>
           </td>  
        </tr>  
        <?php  
                  $total = $total + ($product['quantity'] * $product['price']);  
             endforeach;  
        ?>  
        <tr>  
             <td colspan="3" align="right">Total</td>  
             <td align="right"> <?php echo number_format($total, 2); ?> LEI</td>  
             <td></td>  
        </tr>  
        <tr>
            <!-- Show checkout button only if the shopping cart is not empty -->
            <td colspan="5">
             <?php 
                if (isset($_SESSION['shopping_cart'])):
                if (count($_SESSION['shopping_cart']) > 0):
             ?>
                <a href="#" class="btn btn-primary" style="width: 150px; margin: 30px;">Checkout</a>
             <?php endif; endif; ?>
            </td>
        </tr>
        <?php  
        endif;
        ?>  
        </table>  
         </div>
        </div>

         <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
    </body>
</html>
