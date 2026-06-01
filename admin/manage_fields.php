<?php
include "../auth/auth.php";
include "../db.php";

$equipment_id = $_GET['equipment_id'];
?>

<!DOCTYPE html>
<html>

<head>

<title>Manage Equipment Fields</title>

<link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="layout">

  <aside class="sidebar">
    <h2>Hardware Admin</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a class="active" href="add_equipment.php">Add Equipment</a>
    <a href="view_equipment.php">View Equipment</a>
      <a href="issue_equipment_employee.php">Issue Equipment</a>
      <a href="view_issued.php">View issued</a>
      <a href="upload_employees.php">Upload Employees</a>
      <a href="stock_available.php">Stock Available</a>
<a href="manage_equipment.php">Manage Equipment</a>
<a href="add_configuration.php">Add Configuration</a>





  </aside>
<div class="layout">

<div class="main">

<div class="navbar">
Manage Equipment Fields
</div>

<div class="content">

<div class="card">

<h3>Common Fields</h3>

<form action="save_configuration.php" method="POST" enctype="multipart/form-data">

<input type="hidden" name="equipment_id" value="<?= $equipment_id ?>">

<label>Vendor Name</label>
<input type="text" id="vendor" name="vendor">

<label>Equipment_type</label>
<input type="text" name="equipment_type">

<label>Model</label>
<input type="text" name="model">

<label>Company</label>
<input type="text" name="company">

<label>Bill Upload</label>
<input type="file" id="bill" name="bill">

<label>Price</label>
<input type="number" id="price" name="price">

<label>Quantity</label>
<input type="number" id="quantity" name="quantity">

<label>Total</label>
<input type="number" id="total" name="total" readonly>
<label>Purchase Date</lable>
<input type="date" id="purchase_date" name="purchase_date">

<label>Warranty (Months)</lable>
<input type="number" id="warranty" name="warranty">

<label>Expiry Date</label>
<input type="date" id="expiry_date" name="expiry_date" readonly>


<hr>

<h3>Custom Fields</h3>

<div id="customFields">

<label>Field Name</label>
<input type="text" name="field_name[]">

<label>Field Value</label>
<input type="text" name="field_value[]">

</div>

<br>

<button type="button" onclick="addField()">
Add Custom Field
</button>

<br><br>

<button type="submit" class="btn-primary">
Save Configuration
</button>

</form>

</div>

</div>

</div>

</div>

<script>

function addField(){

let div = document.createElement("div");

div.innerHTML = `
<br>
<label>Field Name</label>
<input type="text" name="field_name[]">

<label>Field Value</label>
<input type="text" name="field_value[]">
`;

document.getElementById("customFields").appendChild(div);

}

</script>
<script>

// TOTAL PRICE CALCULATION
function calcTotal() {
    let price = document.getElementById("price").value || 0;
    let qty = document.getElementById("quantity").value || 0;
    document.getElementById("total").value = price * qty;
}

function calculateExpiry() {

    let purchaseDate = document.getElementById("purchase_date").value;
    let warrantyMonths = document.getElementById("warranty").value;

    if (purchaseDate && warrantyMonths) {

        let date = new Date(purchaseDate);

        date.setMonth(date.getMonth() + parseInt(warrantyMonths));

        date.setDate(date.getDate() - 1);

        let yyyy = date.getFullYear();
        let mm = String(date.getMonth() + 1).padStart(2,'0');
        let dd = String(date.getDate()).padStart(2,'0');

        document.getElementById("expiry_date").value = `${yyyy}-${mm}-${dd}`;
    }
}

document.getElementById("purchase_date").addEventListener("change", calculateExpiry);
document.getElementById("warranty").addEventListener("input", calculateExpiry);

// EVENT LISTENERS
document.getElementById("price").addEventListener("input", calcTotal);
document.getElementById("quantity").addEventListener("input", calcTotal);

document.getElementById("purchase_date").addEventListener("change", calculateExpiry);
document.getElementById("warranty_months").addEventListener("input", calculateExpiry);

</script>
</body>

</html>