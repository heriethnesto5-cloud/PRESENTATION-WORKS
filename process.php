<?php
require_once "connection.php";

$action = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$action = isset($_POST["action"]) ? $_POST["action"] : "";
} else {
	$action = isset($_GET["action"]) ? $_GET["action"] : "";
}

// INSERT DATA
if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "insert") {
	$fullNameInput = trim(isset($_POST["full_name"]) ? $_POST["full_name"] : "");
	$emailInput = trim(isset($_POST["email"]) ? $_POST["email"] : "");
	$phoneInput = trim(isset($_POST["phone"]) ? $_POST["phone"] : "");
	$courseInput = trim(isset($_POST["course"]) ? $_POST["course"] : "");

	if ($fullNameInput === "" || $emailInput === "" || $phoneInput === "" || $courseInput === "" || !filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
		die("Please enter all student details and a valid email address.");
	}

	$fullName = mysqli_real_escape_string($connection, $fullNameInput);
	$email = mysqli_real_escape_string($connection, $emailInput);
	$phone = mysqli_real_escape_string($connection, $phoneInput);
	$course = mysqli_real_escape_string($connection, $courseInput);

	$sql = "INSERT INTO students (full_name, email, phone, course)
			VALUES ('$fullName', '$email', '$phone', '$course')";

	if (!mysqli_query($connection, $sql)) {
		die("Could not save the student: " . mysqli_error($connection));
	}

	header("Location: form.php");
	exit;
}

// UPDATE DATA
if ($_SERVER["REQUEST_METHOD"] === "POST" && $action === "update") {
	$studentId = isset($_POST["id"]) ? (int) $_POST["id"] : 0;
	$fullNameInput = trim(isset($_POST["full_name"]) ? $_POST["full_name"] : "");
	$emailInput = trim(isset($_POST["email"]) ? $_POST["email"] : "");
	$phoneInput = trim(isset($_POST["phone"]) ? $_POST["phone"] : "");
	$courseInput = trim(isset($_POST["course"]) ? $_POST["course"] : "");

	if ($studentId <= 0 || $fullNameInput === "" || $emailInput === "" || $phoneInput === "" || $courseInput === "" || !filter_var($emailInput, FILTER_VALIDATE_EMAIL)) {
		die("Please enter all student details and a valid email address.");
	}

	$fullName = mysqli_real_escape_string($connection, $fullNameInput);
	$email = mysqli_real_escape_string($connection, $emailInput);
	$phone = mysqli_real_escape_string($connection, $phoneInput);
	$course = mysqli_real_escape_string($connection, $courseInput);

	$sql = "UPDATE students
			SET full_name = '$fullName', email = '$email', phone = '$phone', course = '$course'
			WHERE id = $studentId";

	if (!mysqli_query($connection, $sql)) {
		die("Could not update the student: " . mysqli_error($connection));
	}

	header("Location: form.php");
	exit;
}

$studentId = isset($_GET["id"]) ? (int) $_GET["id"] : 0;
if ($studentId <= 0) {
	header("Location: form.php");
	exit;
}

// DELETE DATA
if ($action === "delete") {
	if (!mysqli_query($connection, "DELETE FROM students WHERE id = $studentId")) {
		die("Could not delete the student: " . mysqli_error($connection));
	}

	header("Location: form.php");
	exit;
}

// VIEW DATA
if ($action === "view" || $action === "edit") {
	$result = mysqli_query($connection, "SELECT * FROM students WHERE id = $studentId");
	if (!$result) {
		die("Could not load the student: " . mysqli_error($connection));
	}

	$student = mysqli_fetch_assoc($result);
	if (!$student) {
		header("Location: form.php");
		exit;
	}
	// EDIT DATA
} else {
	header("Location: form.php");
	exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo $action === "view" ? "View Student" : "Edit Student"; ?></title>
	<style>
		body {
			margin: 0;
			padding: 32px 16px;
			background: #f2f2f2;
			color: #222;
			font-family: Arial, sans-serif;
		}
		main {
			max-width: 650px;
			margin: 0 auto;
			padding: 24px;
			background: #fff;
			border: 1px solid #d5d5d5;
		}
		h1 {
			margin-top: 0;
			font-size: 24px;
		}
		.detail {
			padding: 10px 0;
			border-bottom: 1px solid #ddd;
		}
		label {
			display: block;
			margin: 14px 0 6px;
			font-size: 14px;
		}
		input {
			box-sizing: border-box;
			width: 100%;
			padding: 10px;
			border: 1px solid #aaa;
			border-radius: 2px;
			font: inherit;
		}
		button, a {
			display: inline-block;
			margin-top: 18px;
			padding: 8px 12px;
			border: 1px solid #777;
			border-radius: 2px;
			background: #eee;
			color: #222;
			font: inherit;
			text-decoration: none;
			cursor: pointer;
		}
		button:hover, a:hover {
			background: #ddd;
		}
	</style>
</head>
<body>
<main>
	<?php if ($action === "view"): ?>
		<h1>Student Details</h1>
		<div class="detail"><strong>Full Name:</strong> <?php echo htmlspecialchars($student["full_name"], ENT_QUOTES, "UTF-8"); ?></div>
		<div class="detail"><strong>Email:</strong> <?php echo htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8"); ?></div>
		<div class="detail"><strong>Phone:</strong> <?php echo htmlspecialchars($student["phone"], ENT_QUOTES, "UTF-8"); ?></div>
		<div class="detail"><strong>Course:</strong> <?php echo htmlspecialchars($student["course"], ENT_QUOTES, "UTF-8"); ?></div>
		<a href="form.php">Back to Records</a>
	<?php else: ?>
		<h1>Edit Student</h1>
		<form action="process.php" method="POST">
			<input type="hidden" name="action" value="update">
			<input type="hidden" name="id" value="<?php echo (int) $student["id"]; ?>">

			<label for="full_name">Full Name</label>
			<input type="text" id="full_name" name="full_name" maxlength="100" value="<?php echo htmlspecialchars($student["full_name"], ENT_QUOTES, "UTF-8"); ?>" required>

			<label for="email">Email</label>
			<input type="email" id="email" name="email" maxlength="100" value="<?php echo htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8"); ?>" required>

			<label for="phone">Phone</label>
			<input type="tel" id="phone" name="phone" maxlength="20" value="<?php echo htmlspecialchars($student["phone"], ENT_QUOTES, "UTF-8"); ?>" required>

			<label for="course">Course</label>
			<input type="text" id="course" name="course" maxlength="100" value="<?php echo htmlspecialchars($student["course"], ENT_QUOTES, "UTF-8"); ?>" required>

			<button type="submit">Update Data</button>
			<a href="form.php">Back to Records</a>
		</form>
	<?php endif; ?>
</main>
</body>
</html>
