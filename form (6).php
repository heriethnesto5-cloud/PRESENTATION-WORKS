<?php
require_once "connection.php";

$students = mysqli_query($connection, "SELECT * FROM students ORDER BY id ASC");
if (!$students) {
	die("Could not load student records: " . mysqli_error($connection));
}
$displayId = 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Student Management System</title>
	<style>
		body {
			margin: 0;
			padding: 32px 16px;
			background: #f2f2f2;
			color: #222;
			font-family: Arial, sans-serif;
		}
		main {
			max-width: 1000px;
			margin: 0 auto;
		}
		h1 {
			margin: 0 0 24px;
			text-align: center;
			font-size: 28px;
		}
		section {
			margin-bottom: 24px;
			padding: 24px;
			background: #fff;
			border: 1px solid #d5d5d5;
		}
		h2 {
			margin: 0 0 18px;
			font-size: 20px;
		}
		.form-grid {
			display: grid;
			grid-template-columns: repeat(2, minmax(0, 1fr));
			gap: 16px;
		}
		label {
			display: block;
			margin-bottom: 6px;
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
		button, .action-link {
			display: inline-block;
			padding: 8px 12px;
			border: 1px solid #777;
			border-radius: 2px;
			background: #eee;
			color: #222;
			font: inherit;
			text-decoration: none;
			cursor: pointer;
		}
		button:hover, .action-link:hover {
			background: #ddd;
		}
		.submit-row {
			margin-top: 16px;
		}
		.table-wrap {
			overflow-x: auto;
		}
		table {
			width: 100%;
			border-collapse: collapse;
			text-align: left;
		}
		th, td {
			padding: 10px;
			border: 1px solid #ccc;
			vertical-align: top;
		}
		th {
			background: #eee;
		}
		.actions {
			white-space: nowrap;
		}
		@media (max-width: 600px) {
			.form-grid {
				grid-template-columns: 1fr;
			}
			section {
				padding: 16px;
			}
		}
	</style>
</head>
<body>
<main>
	<h1>STUDENT MANAGEMENT SYSTEM</h1>

	<section>
		<h2>Student Registration Form</h2>
		<form action="process.php" method="POST">
			<input type="hidden" name="action" value="insert">
			<div class="form-grid">
				<div>
					<label for="full_name">Full Name</label>
					<input type="text" id="full_name" name="full_name" maxlength="100" required>
				</div>
				<div>
					<label for="email">Email</label>
					<input type="email" id="email" name="email" maxlength="100" required>
				</div>
				<div>
					<label for="phone">Phone</label>
					<input type="tel" id="phone" name="phone" maxlength="20" required>
				</div>
				<div>
					<label for="course">Course</label>
					<input type="text" id="course" name="course" maxlength="100" required>
				</div>
			</div>
			<div class="submit-row">
				<button type="submit">Submit Data</button>
			</div>
		</form>
	</section>

	<section>
		<h2>Submitted Students</h2>
		<div class="table-wrap">
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Full Name</th>
						<th>Email</th>
						<th>Phone</th>
						<th>Course</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php if (mysqli_num_rows($students) > 0): ?>
						<?php while ($student = mysqli_fetch_assoc($students)): ?>
							<tr>
								<td><?php echo $displayId++; ?></td>
								<td><?php echo htmlspecialchars($student["full_name"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($student["email"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($student["phone"], ENT_QUOTES, "UTF-8"); ?></td>
								<td><?php echo htmlspecialchars($student["course"], ENT_QUOTES, "UTF-8"); ?></td>
								<td class="actions">
									<a class="action-link" href="process.php?action=view&amp;id=<?php echo (int) $student["id"]; ?>">View</a>
									<a class="action-link" href="process.php?action=edit&amp;id=<?php echo (int) $student["id"]; ?>">Edit</a>
									<a class="action-link" href="process.php?action=delete&amp;id=<?php echo (int) $student["id"]; ?>">Delete</a>
								</td>
							</tr>
						<?php endwhile; ?>
					<?php else: ?>
						<tr>
							<td colspan="6">No student records have been submitted yet.</td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</section>
</main>
</body>
</html>
