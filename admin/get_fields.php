<?php
include "../auth/auth.php";
include "../db.php";

if(isset($_GET['equipment_type_id'])){

$equipment_type_id = intval($_GET['equipment_type_id']);

$result = $conn->query("
SELECT id, field_name 
FROM equipment_fields 
WHERE equipment_id=$equipment_type_id
");

while($row = $result->fetch_assoc()){

echo "<label>".$row['field_name']."</label>";

echo "<input type='text' name='custom[".$row['id']."]' required>";

}

}
?>