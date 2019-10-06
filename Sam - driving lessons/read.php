<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Upcoming Lessons</title>
		<link rel="stylesheet"  href="style2.css"> 
	</head>
	<body>
		<div class="container tall">
			<header>
				<img src="color_logo2.png" alt="logo" class="logo">
				<nav>
					<ul>
						<li><a href="index.php">Home</a></li>
						<li><a href="create.php">Book A Lesson</a></li>
						<li><a href="read.php">Check Bookings</a></li>
					</ul>
				</nav>	
			</header>

			<div class="heading">
				<h2>Upcoming Lessons</h2>
			</div>

			<?php
				session_start();

				if(! isset($_SESSION['user_id'])){
					header ("Location: index.php");
				}

				// Read Records from the Database
				readFromDb(htmlentities($_SESSION['user_id']));

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
				function readFromDb($user_id) {
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
				
						// Select data from lesson_table
						$sql =
							"SELECT
								*
							FROM
								lesson_table
							WHERE
								user_id = '{$mysqli->real_escape_string($user_id)}'
							OR
								instructor_id IN (
									SELECT instructor_id
									FROM instructor_table
									WHERE user_id = '{$mysqli->real_escape_string($user_id)}'
								)";

					// execute the query 
						$result = $mysqli->query($sql);

						// Check for an error
						if ($mysqli->error){
							die("error: {$mysqli->errno} : {$mysqli->error}");
						} else {

							$rowcount=mysqli_num_rows($result);

						if ($rowcount > 0) {
							// Output header
							echo '<div class="booking-title">';
							echo '<div class="entry sml">Permit</div>';
							echo '<div class="entry med">Name</div>';
							echo '<div class="entry lrg">Address</div>';
							echo '<div class="entry med">Phone</div>';
							echo '<div class="entry med">Transmission</div>';
							echo '<div class="entry med">Time</div>';
							echo '<div class="entry med">Date</div>';
							echo '<div class="entry sml"></div>';
							echo '<div class="entry sml"></div>';
							echo '</div>';
							
							// Set result object
							while ($row = $result->fetch_assoc()) {
									echo '<booking>';
									echo	'<div class="entry sml">' . '<img class="bandpic" height="40px" width="40px" src="data:image/jpeg;base64,'. base64_encode( $row['image'] ) . '"/>' . '</div>';
									echo	'<div class="entry med">' . $row["name"] . '</div>';
									echo	'<div class="entry lrg">' . $row["address"] . '</div>';
									echo	'<div class="entry med">' . $row["phone"] . '</div>';
									echo	'<div class="entry med">' . $row["transmission"] . '</div>';
									echo	'<div class="entry med">' . $row["lessontime"] . '</div>';
									echo	'<div class="entry med">' . $row["lessondate"] . '</div>';
									if ($row["user_id"] != $_SESSION['user_id'])
									{
										echo	'<div class="entry med">STUDENT</div>';
									}
									else {
										echo	'<div class="entry sml">' . '<a href="update.php?id=' .$row["lesson_id"]. '">' . 'Update Booking'.  '</a></div>';
										echo	'<div class="entry sml">' . '<a href="delete.php?id=' .$row["lesson_id"]. '">' . 'Cancel Booking'.  '</a></div>';
									}	
									echo '</booking>';
								}
						} else {
							echo '<div class="direction">You have no upcoming lessons, would you like to book one?</div>';
						}			
					}

						// Close the connection
						$mysqli->close();
					}
				}
			?>

			<div>&nbsp;</div>
		</div>
	
	</body>
</html>

