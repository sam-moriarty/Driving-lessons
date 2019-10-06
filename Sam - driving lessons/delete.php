<!DOCTYPE html>
<?php

session_start();

	main();
	/**
	 * Main funcation
	 */
	function main() {
		if(!isset($_SESSION['user_id'])){
			header ("Location: index.php"); 
		}
		else{

			// Execute if a post request is being made
			if (! empty($_POST)){

				// Remove from database
				removeFromDb(htmlentities($_POST['lesson_id']), htmlentities($_SESSION['user_id']));

			}
			// Execute if a get request is being made
			else {
				if(!isset($_GET["id"])){
					header("Location: read.php");
				}	
			}
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
	 * Function that remove data from the database
	 */
	function removeFromDb($lesson_id, $user_id) {
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

			// Create our delete sql
			$sql = "DELETE FROM lesson_table WHERE lesson_id = '{$mysqli->real_escape_string($lesson_id)}' AND user_id = '{$mysqli->real_escape_string($user_id)}'";

			// Removing from database
			$deleted = $mysqli->query($sql);

			// Check for an error
			if ($mysqli->error){
				die("error: {$mysqli->errno} : {$mysqli->error}");
			} else {
				// Redirect to read.php
				echo "<script type='text/javascript'>
				alert('Your lesson has been cancelled');
				location='read.php';</script>";
			}

			// Close the connection
			$mysqli->close();
		}
	}
?>	

<!DOCTYPE html>
<html>
<head>
	<title>Cancel Lesson</title>
	<link rel="stylesheet"  href="style2.css"> 
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

	<form action="delete.php" method="post" enctype="multipart/form-data">
		<?php
			
			// Read Records from the Database
			readFromDb(htmlentities($_GET["id"]), htmlentities($_SESSION["user_id"]));

			/**
			 * Function that reads from database 
			 */
			function readFromDb($lesson_id, $user_id) {
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

					// Select data from lesson_tablew
					$sql = "SELECT * FROM lesson_table WHERE lesson_id = '{$mysqli->real_escape_string($lesson_id)}' AND user_id = '{$mysqli->real_escape_string($user_id)}'";
					// execute the query 
					$result = $mysqli->query($sql);

					// Check for an error
					if ($mysqli->error){
						die("error: {$mysqli->errno} : {$mysqli->error}");
					} else {
						// Set result object
						while ($row = $result->fetch_assoc()) {

							echo '<div class="signup-form">';
							echo '<div class="front">';
							echo '<h1>Cancel Your Booking</h1>';
					
							echo '<img class="form__label" aria-hidden="true" src="data:image/jpeg;base64,'.base64_encode( $row['image'] ) . '"/>';
							echo '<input class="form__text" aria-hidden="true" type="text" placeholder="Name" name="name" disabled value="' . $row["name"] . '">';
							echo '<input class="form__text" aria-hidden="true" type="text" placeholder="Address" name="address" disabled value="' . $row["address"] . '">';	
							echo '<input class="form__text" aria-hidden="true" type="text" placeholder="Phone" name="phone" disabled value="' . $row["phone"] . '">';
							echo '<input class="form__text" aria-hidden="true" type="text" placeholder="Transmission" name="transmission" disabled value="' . $row["transmission"] . '">';
							echo '<input class="form__text" aria-hidden="true" type="text" placeholder="Lessontime" name="lessontime" disabled value="' . $row["lessontime"] . '">';
							echo '<input class="form__text" aria-hidden="true" type="date" placeholder="Lessondate" name="lessondate" disabled value="' . $row["lessondate"] . '">';
						
							echo '<input type="hidden" id="lesson_id" name="lesson_id" value="'. $row["lesson_id"]. '">';	
						}
					}

					// Close the connection
					$mysqli->close();
				}
			}
			?>
		
			<input type="submit" style="background: #fce74c; color: black;" class="form__text button" type="button" name="submit" value="Cancel Booking">
			<input type="button" style="background: #c0c0c0; color: black;" class="form__text button" type="button" name="cancel" value="Go Back" onclick="document.location='read.php'" >
			
			</form>
	
		</div>

</body>
</html>
