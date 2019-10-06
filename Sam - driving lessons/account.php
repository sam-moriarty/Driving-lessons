
<!DOCTYPE html>
<?php

	$isDuplicate = false;

	main();
	
	/**
	 * Main funcation
	 */
	function main() {

		// Execute if a post request is being made
		if (! empty($_POST)){

			// Write to database
			writeToDb($_POST['email'], encryptPassword($_POST['password']));

		}
		// Execute if a get request is being made
		else {

		}

	}


	/**
	 * Function that connects to the database
	 */
	function connectToDb() {

		// Database connection information
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "valuedatabase";

		// Create connection
		$mysqli = new mysqli($servername, $username, $password, $dbname);

		// Return the database object
		return $mysqli;
	}

	/**
	 * Function that writes data to the database
	 */
	function writeToDb($email, $password) {
		// Obtain the mysql database connection object
		$mysqli = connectToDb();

		// Check for a connection error
		if ($mysqli->connect_error) 
		{
			// Close the connection on an error
			die("Connection failed: " . $mysqli->connect_error);
		}
		// Continue with the operation
		else {
	

			// Create our insert sql
			$sql = "INSERT INTO login_table ( email, password) VALUES ('{$mysqli->real_escape_string($email)}', '{$mysqli->real_escape_string($password)}')";

			// insert it into the database
			$insert = $mysqli->query($sql);

			// Check for an error
			if ($mysqli->errno == 1062) {
				// Look for a duplicate error
				$GLOBALS['isDuplicate'] = true;
			}
			else if ($mysqli->error){
				die("error: {$mysqli->errno} : {$mysqli->error}");
				

			} else {
				// Redirect to login.php
				echo "<script type='text/javascript'>
				alert('Account has been created');
				location='index.php';</script>";


			}

			// Close the connection
			$mysqli->close();
		}
	}

	/**
	 * Function that encrypts the password
	 */
	function encryptPassword($password) {
		return base64_encode(openssl_encrypt($password, "AES-256-CBC", 'Key' , 0, '16characterslong'));
	}


	?>

	<!DOCTYPE html>
	<html>
	<head>
		<title>Create Account</title>
		<link rel="stylesheet" href="style2.css">
	</head>
	<body>

		<header>
			<div class="container">
				<img src="color_logo2.png" alt="logo" class="logo">
				<nav>
			<ul>
			<li><a href="index.php">Home</a></li>
			<li><a href="create.php">Book A Lesson</a></li>
			<li><a href="read.php">Check Bookings</a></li>
			</ul>
			</nav>	
			</div>		
		</header>

	<div class="signup-form">
			
			<div class="front">

				<h1 class="title">Create Account</h1>
		
		<form class="form" action = "account.php" method="post">

				<label class="form__label" id="Email" for="email">Your Email <span class="form__tooltip" 		data-tooltip="Please enter your email address">?
				</span>
				</label>
				<input class="form__text" type="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="" name="email" required>	
				<label class="form__label" id= "Password" for="Password">Password <span class="form__tooltip" data-tooltip="Minimum 12 characters, at least one capital and one number">?
				</span>
				</label>
				<input class="form__text" type="password" id="password"  value="" name="password" required="">
				<label class="form__label" id="Confpassword" for="Confpassword">Re-enter password</label>
				<input class="form__text" type="password" id="confpassword" name="confpassword" required  			value=""> 
				<img class="recaptcha" src="https://s3-us-west-2.amazonaws.com/s.cdpn.io/308367/recaptcha-dark.png" alt="">
				</p>
				<a href="index.php" class="fineprint">I already have an account</a>   
				<input style="background: #fce74c; color: black;"type="submit" class="button" type="button" value="Create">
				
				<?php
				if($isDuplicate)
				{
					error_log("Account Already Exists", 3, "logs/errors.log");
					echo "Account Already Exists!";
				}
				?>
		</form>

	</div>

		<dir class="back">
			<h2 class="subheading">What we offer</h2>
			<ul class="list list--unstyled">
				<li>At Value Driving School we take 
					pride in teaching YOU how to drive.
					<br>
					<br>
					We have a number of modern manual and 
					automatic vehicles. All fully insured and Dual Controlled for 
					your safety.  </li>
				
			</ul>
	</dir>

		<!-- Login Area End -->

		<script type="text/javascript">
			// Set up a variable for the password and confirm password form items
			var password = document.getElementById('password');
			var confpassword = document.getElementById('confpassword');

			/**
			 * Function that checks if the password matches
			 */
			function checkpassword() {
				
				if (password.value != confpassword.value) {

					confpassword.setCustomValidity("Passwords don't match!");

				} else
				{
					confpassword.setCustomValidity("");
				}
			}

			// Set up a delegate 
			password.onchange = checkpassword;
			confpassword.onkeyup = checkpassword;
		</script>
	</body>
</html>
