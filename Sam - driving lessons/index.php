<!DOCTYPE html>
<?php

	
	$hasLoginFailed = false;
main();
	
	/**
	 * Main funcation
	 */
	function main() {

		// Execute if a post request is being made
		if (! empty($_POST)){
			// read from database
			readFromDb(htmlentities($_POST['email']), encryptPassword(htmlentities($_POST['password'])));

		}
		// Execute if a get request is being made
		else {

		// Nothing to see here!

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
	 * Function that reads from database 
	 */
	function readFromDb($email, $password) {
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
	
		// Check for existence of existing record
		
			$sql = "SELECT * FROM login_table WHERE email = '{$mysqli->real_escape_string($email)}' AND password = '{$mysqli->real_escape_string($password)}'";
			
			// execute the query 
			$result = $mysqli->query($sql);
			// Check for an error
			if ($result->num_rows < 1) {
				// Look for a duplicate error
				$GLOBALS['hasLoginFailed'] = true;
			}
			else if ($mysqli->error){
				die("error: {$mysqli->errno} : {$mysqli->error}");

			} else {
				//starting session after connect
				session_start();
                while ($row = $result->fetch_assoc()) {
                	$_SESSION['user_id'] = $row['user_id'];
                }
				// Redirect to read.php
				echo "<script type='text/javascript'>
				alert('You are now logged in');
				location='read.php';</script>";

			}

			$result->close();

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
		 <meta charset="UTF-8">
		<title>Value Driving School</title>
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


	
		<form action ="" method="post">

			<div class="signup-form">
			<div class="front">
			<h1 class="title">Login To Book A Lesson!</h1>

		<form class="form" action = "account.php" method="post">
		
			
				<label class="form__label" id="Email" for="email">Your Email <span class="form__tooltip" data-tooltip="Please enter your email address">?</span></label>
				<input class="form__text" type="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$" value="" name="email" required>
				<label class="form__label" id= "Passwordlabel" for="password">Password <span class="form__tooltip" data-tooltip="Minimum 12 characters, at least one capital and one number">?</span></label>
				<input class="form__text" type="password" id="password"  value="" name="password" required="">
				<input style="background: #fce74c; color: black;"type="submit" class="button" type="button" value="Login">
				<a class ="links "href="account.php">Create a account?</a>
				<br>
				<img class ="form__label2" src="driver.png" alt="logo" >

					<?php
					if($hasLoginFailed)
					{
						error_log("Incorrect Login", 3, "logs/errors.log");
						echo "Incorrect Login!";
					}
					?>
		</form>
</div>

			<dir class="back">
			<h2 class="subheading"></h2>
			<ul class="list list--unstyled">
				<h1>Book a lesson today</h1>
				<P> 
					At Value Driving School, we pride ourselves on our high standard of tuition. By learning with us, you are ensuring the best chance of success and many years of safe driving! The secret of becoming a confident and competent driver is to choose a driving school and a driving instructor who understands your individual needs – whose expertise, patience, and guidance will turn an otherwise stressful occasion into a positive and rewarding experience.
				</P>
			</ul>

		</dir>

	</body>

</html>