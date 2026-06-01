<?php
include "../auth/auth.php";
include "../db.php";

$id = intval($_GET['id']);
$equipment_id = intval($_GET['equipment_id']);

$field = $conn->query(
"SELECT * FROM equipment_fields WHERE id=$id"
)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>

<div class="card">

<h3>Edit Field</h3>

<form method="POST" action="update_field.php">

<input type="hidden" name="id" value="<?= $id ?>">
<input type="hidden" name="equipment_id" value="<?= $equipment_id ?>">

<label>Field Name</label>

<input type="text"
name="field_name"
value="<?= $field['field_name'] ?>"
required>

<label>Field Type</label>

<select name="field_type">

<option value="text" <?= $field['field_type']=="text"?"selected":"" ?>>Text</option>
<option value="number" <?= $field['field_type']=="number"?"selected":"" ?>>Number</option>
<option value="date" <?= $field['field_type']=="date"?"selected":"" ?>>Date</option>
<option value="file" <?= $field['field_type']=="file"?"selected":"" ?>>File</option>
<option value="select" <?= $field['field_type']=="select"?"selected":"" ?>>Dropdown</option>

</select>

<label>Dropdown Options</label>

<input type="text"
name="field_options"
value="<?= $field['field_options'] ?>">

<label>

<input type="checkbox"
name="required"
value="1"
<?= $field['is_required']?"checked":"" ?>>

Required

</label>

<br><br>

<button class="btn-primary">

Update Field

</button>

</form>

</div>

</body>
</html>