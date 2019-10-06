<!DOCTYPE html>
<?php

session_start();

	$isDuplicate = false;

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
				$image = $_FILES['image']['tmp_name'];
				$img = addslashes(file_get_contents($image));
				// Write to database
				writeToDb(htmlentities($_SESSION['user_id']), $img, htmlentities($_POST['name']), htmlentities($_POST['address']),htmlentities($_POST['phone']), htmlentities($_POST['transmission']),  htmlentities($_POST['lessontime']), htmlentities($_POST['lessondate']), htmlentities($_POST['lesson_id']), htmlentities($_POST['instructor'])); 

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
	 * Function that writes data to the database
	 */
	function writeToDb($user_id, $image, $name, $address, $phone, $transmission,  $lessontime, $lessondate, $lesson_id, $instructor) {
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

			// Create our update sql
			$sql = "UPDATE lesson_table SET user_id = {$mysqli->real_escape_string($user_id)}, image = '{$image}', name = '{$mysqli->real_escape_string($name)}', address ='{$mysqli->real_escape_string($address)}', phone = '{$mysqli->real_escape_string($phone)}', transmission = '{$mysqli->real_escape_string($transmission)}', instructor_id = '{$mysqli->real_escape_string($instructor)}', lessontime = '{$mysqli->real_escape_string($lessontime)}', lessondate = '{$mysqli->real_escape_string($lessondate)}' WHERE lesson_id = '{$mysqli->real_escape_string($lesson_id)}' AND user_id = '{$mysqli->real_escape_string($user_id)}'" ;

			// updating the database
			$update = $mysqli->query($sql);

			//Check for an error
			if ($mysqli->errno == 1062) {
				// Look for a duplicate error
				$GLOBALS['isDuplicate'] = true;
				$_GET["id"] = $lesson_id;
			}
			else if ($mysqli->error){
				die("error: {$mysqli->errno} : {$mysqli->error}");

			} else {
				// Redirect to read.php
				echo "<script type='text/javascript'>
				alert('Thank you for updating your lesson!');
				location='read.php';</script>";

			}

			// Close the connection
			$mysqli->close();
		}
	}

	/**
	 * Function that reads all the instructors from the database
	 */
	function readInstructorsFromDb() {
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

			// Select data from instructor_table
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
					echo '<h1>Update your booking details</h1>';
					if($GLOBALS['isDuplicate']){
						echo '<p style="color: orange; width:100%;">Time Has Already Been Booked!</p>';
					}
					echo '<img class="form__label" src="data:image/jpeg;base64,'.base64_encode( $row['image'] ) . '"/>';
					echo '<input class="form__text" type="file" placeholder="Image" name="image" required checked>';
					echo '<input class="form__text" type="text" placeholder="Name" name="name" value="' . $row["name"] . '">';
					echo '<input class="form__text" type="text" placeholder="Address" name="address" value="' . $row["address"] . '">';
					echo '<input class="form__text" type="text" pattern=".*[1-9]" placeholder="Phone" name="phone" value="' . $row["phone"] . '">';
					echo '<select class="form__text" id="Transmission"type="text"  placeholder="Automatic or Manual?" name="transmission" value="' . $row["transmission"] . '"  checked>';
						echo '<option value="Automatic">Automatic</option>';
						echo '<option value="Manual">Manual</option>';
					echo '</select>';				
					echo '<select class="form__text" id="Instructor" type="text"  placeholder="Who?" name="instructor" value="' . $row["instructor_id"] . '"  checked>';
					// write out our optionns
					readInstructorsFromDb();
					echo '</select>	';
					echo '<select class="form__text" id="Lessontimne" placeholder="Select Time" type="text"  name="lessontime" value="' . $row["lessontime"] . '" required="">';
						echo '<option value="9:00am">9:00am</option>';
						echo '<option value="10:00am">10:00am</option>';
						echo '<option value="11:00am">11:00am</option>';
						echo '<option value="12:00pm">12:00pm</option>';
						echo '<option value="1:00pm">1:00pm</option>';
						echo '<option value="2:00pm">2:00pm</option>';
						echo '<option value="3:00pm">3:00pm</option>';
						echo '<option value="4:00pm">4:00pm</option>';
						echo '<option value="5:00pm">5:00pm</option>';
						echo '<option value="6:00pm">6:00pm</option>';
					echo '</select>';
					echo '<input class="form__text" type="date" placeholder="Lessondate" name="lessondate" required value="' . $row["lessondate"] . '">';
					echo '<input  type="hidden" name="lesson_id" value="'. $row["lesson_id"]. '">';	
				}
			}

			// Close the connection
			$mysqli->close();
		}
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Update Lesson</title>
	<link rel="stylesheet"  href="style2.css"> 
</head>
<body>
	<form  class="form" action="update.php" method="post" enctype="multipart/form-data">

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
		<?php

			// Read Records from the Database
			readFromDb(htmlentities($_GET["id"]), (htmlentities($_SESSION["user_id"])));

		?>
			
			
			<input type="submit" style="background: #fce74c; color: black;" class="form__text button" type="button" name="submit" value="Update">
			<input type="button" style="background: #c0c0c0; color: black;" class="form__text button" type="button" name="cancel" value="Cancel" onclick="document.location='read.php'" >

		</form>

	</div>

</body>
</html>