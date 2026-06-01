<?php
include "../auth/auth.php";
include "../db.php";
 

$types = $conn->query("SELECT * FROM equipment_types ORDER BY name");
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Configuration</title>

<link rel="stylesheet" href="../assets/css/admin.css">

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

<div class="layout">

<aside class="sidebar">

<h2>Hardware Admin</h2>

<a href="admin_dashboard.php">Dashboard</a>
<a href="manage_equipment.php">Manage Equipment</a>
<a class="active" href="add_configuration.php">Add Configuration</a>
<a href="view_configuration.php">View Configuration</a>

</aside>

<div class="main">

<div class="navbar">
Add Equipment Configuration
</div>

<div class="content">

<div class="card">

<form method="POST" action="save_configuration.php" enctype="multipart/form-data">

<input type="hidden" name="equipment_id" value="<?= $equipment_id ?>">

<label>Equipment Type</label>

<select name="equipment_type_id" id="equipmentType" required>

<option value="">Select Equipment</option>

<?php
while($row = $types->fetch_assoc()){
echo "<option value='".$row['id']."'>".$row['name']."</option>";
}
?>

</select>


<h3>Common Fields</h3>

<label>Vendor</label>
<input type="text"  id="vendor" name="vendor" required>

<label>Model</label>
<input type="text" id="model" name="model">

<label>Company</label>
<input type="text" id="company" name="company">

<label>Bill Upload</label>
<input type="file" id="bill" name="bill">

<label>Price</label>
<input type="number" id="price" name="price">

<label>Quantity</label>
<input type="number" id="quantity" name="quantity">

<label>Total</label>
<input type="number" id="total" name="total" readonly>
<label>Purchase Date</lable>
<input type="date" id="purchase_date" name="purchase_date" required>

<label>Warranty (Months)</lable>
<input type="number" id="warranty" name="warranty">

<label>Expiry Date</label>
<input type="date" id="expiry_date" name="expiry_date" readonly>
<h3>Add Custom Field</h3>

  <div id="customFields">
    <!-- Custom fields dynamically loaded here as before -->
  </div>

  <button type="submit" class="btn-primary">Save Configuration</button>

</form>

<script>
function calculateTotal(){
    let price = document.getElementById("price").value;
    let qty = document.getElementById("quantity").value;
    let total = price * qty;
    document.getElementById("total").value = total;

    let purchaseDate = document.querySelector('[name="purchase_date"]').value;
    let warranty = document.querySelector('[name="warranty"]').value;
    if(purchaseDate && warranty){
        let expiryDate = new Date(purchaseDate);
        expiryDate.setFullYear(expiryDate.getFullYear() + parseInt(warranty));
        expiryDate.setDate(expiryDate.getDate() - 1); // before 1 day
        document.querySelector('[name="expiry"]').value = expiryDate.toISOString().split('T')[0];
    }
}
</script>

<h3>Custom Fields</h3>

<div id="customFields">

<p>Select equipment type to load fields</p>

</div>

<br>

<button class="btn-primary">Save Configuration</button>

</form>

</div>

</div>

</div>

</div>

<script>

// TOTAL PRICE CALCULATION
function calcTotal() {
    let price = document.getElementById("price").value || 0;
    let qty = document.getElementById("quantity").value || 0;
    document.getElementById("total").value = price * qty;
}

// EXPIRY DATE CALCULATION
function calculateExpiry() {

    let purchaseDate = document.getElementById("purchase_date").value;
    let warrantyMonths = document.getElementById("warranty_months").value;

    if (purchaseDate && warrantyMonths) {

        let date = new Date(purchaseDate);

        // add warranty months
        date.setMonth(date.getMonth() + parseInt(warrantyMonths));

        // subtract 1 day
        date.setDate(date.getDate() - 1);

        let formatted = date.toISOString().split('T')[0];

        document.getElementById("expiry_date").value = formatted;
    }
}

// EVENT LISTENERS
document.getElementById("price").addEventListener("input", calcTotal);
document.getElementById("quantity").addEventListener("input", calcTotal);

document.getElementById("purchase_date").addEventListener("change", calculateExpiry);
document.getElementById("warranty_months").addEventListener("input", calculateExpiry);

/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted ) {
        window.location.reload();
    }
});

</script>

</body>
</html>