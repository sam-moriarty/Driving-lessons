<!DOCTYPE html>
<?php

	session_start();

	$isDuplicate = false;

	main();
	/**
	 * Main funcation
	 */
	function main() {
		if(empty($_SESSION['user_id'])== 1){
			header ("Location: index.php");
		}
		else{

			// Execute if a post request is being made
			if (! empty($_POST)){
				$image = $_FILES['image']['tmp_name'];
				$img = addslashes(file_get_contents($image));
				
				

				// Write to database
				writeToDb(htmlentities($_SESSION['user_id']), $img, htmlentities($_POST['name']), htmlentities($_POST['address']),htmlentities($_POST['phone']), htmlentities($_POST['transmission']), htmlentities($_POST['lessontime']), htmlentities($_POST['lessondate']), htmlentities($_POST['instructor']));

			}
			// Execute if a get request is being made
			else {

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
	 * Function that reads from database 
	 */
	function readFromDb() {
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
	
			// Select data from instructor_table
			$sql = "SELECT * FROM instructor_table";

			// execute the query 
			$result = $mysqli->query($sql);

			// Check for an error
			if ($mysqli->error){
				die("error: {$mysqli->errno} : {$mysqli->error}");
			} else {
				$rowcount=mysqli_num_rows($result);
				if ($rowcount > 0) {
					
					// Set result object
					while ($row = $result->fetch_assoc()) {
							// Output header
							echo '<option value="' . $row["instructor_id"] . '">' . $row["name"] . '</option>';
						}
				}		
			}

			// Close the connection
			$mysqli->close();
		}
	}

	/**
	 * Function that writes data to the database
	 */
	function writeToDb($user_id, $image, $name, $address, $phone, $transmission,  $lessontime, $lessondate, $instructor) {
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
			$sql = "INSERT INTO lesson_table (user_id, image, name, address, phone, transmission, lessontime, lessondate, instructor_id) VALUES ('{$mysqli->real_escape_string($user_id)}', '{$image}', '{$mysqli->real_escape_string($name)}', '{$mysqli->real_escape_string($address)}', '{$mysqli->real_escape_string($phone)}', '{$mysqli->real_escape_string($transmission)}','{$mysqli->real_escape_string($lessontime)}','{$mysqli->real_escape_string($lessondate)}','{$mysqli->real_escape_string($instructor)}')";

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
				// Redirect to read.php
				echo "<script type='text/javascript'>
				alert('Your lesson has been booked!');
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
	<title>Book A Lesson</title>
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
	<form action="create.php" method="post" enctype="multipart/form-data">
		<div class="signup-form">
			<div class="front">
				<h1>Please fill out form for booking</h1>
				<?php
					if($isDuplicate){
						echo '<p style="color: orange; width:100%;">Time Has Already Been Booked!</p>';
					}
				?>
				<p class="direction">Please Upload Learner's Permit</p>
				
				<label class="form__label" id= "img" for="img"></label> 
				<input class="form__text" id="img" type="file" placeholder="Image" name="image" required value="image" id="fileToUpload"checked>

				<label class="form__label" id= "Name" for="Name"></label> 
				<input class="form__text" id="Name" type="text" pattern=".*[a-zA-Z]" placeholder="First and Last name"name="name" required value="" >

				<label class="form__label" id= "Address" for="Address"></label> 
				<input class="form__text" id="Address" type="text" pattern=".*[1-9a-zA-Z]"  placeholder="Pick up Location" name="address" required value="" >

				<label class="form__label" id= "Phone" for="Phone"></label> 
				<input class="form__text" id="Phone" type="text" pattern=".*[1-9]"  placeholder="Phone" name="phone" required value="" >

				<p class="direction">Please select preferred transmission</p>
				<label class="form__label" id= "Transmission" for="Transmission"></label> 
				<select class="form__text" id="Transmission"type="text"  placeholder="Automatic or Manual?" name="transmission" value=""  checked> 
					<option value="Automatic">Automatic</option>
					<option value="Manual">Manual</option>
				</select>

				<p class="direction">Please select an instructor</p>
				<label class="form__label" id= "InstructorLabel" for="Instructor"></label> 
				<select class="form__text" id="Instructor" type="text"  placeholder="Who?" name="instructor" value=""  checked> 
					<?php
						// Read Records from the Database
						readFromDb();
					?>
				</select>

				<p class="direction">Please select a date & time</p>
				<label class="form__label" id= "Lessontime" for="Lessontime">
				<select class="form__text" id="Lessontimne" placeholder="Select Time" type="text"  name="lessontime" value="" required="">
					<option value="9:00am">9:00am</option>
					<option value="10:00am">10:00am</option>
					<option value="11:00am">11:00am</option>
					<option value="12:00pm">12:00pm</option>
					<option value="1:00pm">1:00pm</option>
					<option value="2:00pm">2:00pm</option>
					<option value="3:00pm">3:00pm</option>
					<option value="4:00pm">4:00pm</option>
					<option value="5:00pm">5:00pm</option>
					<option value="6:00pm">6:00pm</option>
				</select>

				<label class="form__label" id= "Lessondate" for="Lessondate">
				<input class="form__text" id="Lessondate" type="date"  name="lessondate" value="" required="">

				<input type="submit" style="background: #fce74c; color: black;" class="form__text button" type="button" name="submit" value="Book">
				<input type="button" style="background: #c0c0c0; color: black;" class="form__text button" type="button" name="Cancel" value="Go Back" onclick="document.location='read.php'" >
			
		</div>



</body>
</html>