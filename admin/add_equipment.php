<?php include "../auth/auth.php"; ?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Equipment</title>
  <link rel="stylesheet" href="../assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
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
      <a href="backup_restore.php">Backup & Restore</a>
      
  
  




  </aside>

  <div class="main">
    <div class="navbar">
  <span>Add Equipment</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>

    <div class="content">

      <!-- Selection -->
      <div class="card" id="selectionCard">
        <h2 class="center red">Select Equipment Type</h2>

        <div class="product-grid">
          <div class="product" onclick="openForm('cpu')"><i class="fa-solid fa-computer"></i><span>CPU</span></div>
          <div class="product" onclick="openForm('monitor')"><i class="fa-solid fa-desktop"></i><span>Monitor</span></div>
          <div class="product" onclick="openForm('keyboard')"><i class="fa-solid fa-keyboard"></i><span>Keyboard</span></div>
          <div class="product" onclick="openForm('mouse')"><i class="fa-solid fa-mouse"></i><span>Mouse</span></div>
          <div class="product" onclick="openForm('combo')"><i class="fa-solid fa-layer-group"></i><span>Combo Set</span></div>
        <div class="product" onclick="addNewEquipment()">
<i class="fa-solid fa-plus"></i>
<span>Add New</span>
</div>
        </div>
      </div>

      <!-- Form -->
      <div class="card" id="formCard" style="display:none;">
        <button class="back-btn" onclick="goBack()">Back</button>
        
        <h3 id="formTitle"></h3>

        <form id="equipmentForm" method="POST" action="save_equipment.php" enctype="multipart/form-data">
          <input type="hidden" name="type" id="equipmentType">
          <div id="specificFields"></div>
          <button type="submit" class="btn-primary">Save Equipment</button>
        </form>
      </div>

    </div>
  </div>
</div>
<script>
function addNewEquipment(){
  window.location.href = "manage_equipment.php";
}
function openForm(type) {

  document.getElementById("equipmentType").value = type;

  document.getElementById("selectionCard").style.display = "none";
  document.getElementById("formCard").style.display = "block";
  document.getElementById("formTitle").innerText = "Add " + type.toUpperCase();

  let fields = "";

  // ================= CPU =================
  if (type === "cpu") {
  fields = `
    

    <label>Vendor Name</label>
    <input type="text" name="vendor" required>

<label>Company</label>
<input type="text" name="company" required>

    <label>Processor</label>
    <select name="processor" required>
      <option value="">Select Processor</option>
      <option>i3</option>
      <option>i5</option>
      <option>i7</option>
      <option>i9</option>
    </select>

    <label>Generation</label>
    <select name="generation" required>
      <option value="">Select Generation</option>
      <option>1</option><option>2</option><option>3</option>
      <option>4</option><option>5</option><option>6</option>
      <option>7</option><option>8</option><option>9</option><option>10</option><option>11</option><option>12</option>
      <option>13</option><option>14</option><option>15</option><option>16</option><option>17</option><option>18</option>
      <option>19</option><option>20</option>
    </select>

    <label>RAM</label>
    <select name="ram" required>
      <option value="">Select RAM</option>
      <option>4 GB</option>
      <option>8 GB</option>
      <option>16 GB</option>
      <option>32 GB</option>
      <option>64 GB</option>
      <option>128 GB</option>
      <option>256 GB</option>
      <option>512 GB</option>
      <option>1 TB</option>
      <option>1.5 TB</option>
    </select>

    <label>Storage</label>
    <select name="storage" required>
      <option value="">Select Storage</option>
      <option>128 GB</option>
      <option>256 GB</option>
      <option>512 GB</option>
      <option>1 TB</option>
      <option>2 TB</option>
    </select>

    <label>LAN-MAC</label>
    <input type="text" name="lan_mac">

    <label>Wi-Fi Make</label>
    <input type="text" name="wifi_card" required>

    <label>Wi-Fi MAC</label>
    <input type="text" name="wifi_mac">

    <label>Upload Bill</label>
    <input type="file" name="bill" required>

    <label>Price</label>
    <input type="number" id="cpuPrice" name="price" required>

    <label>Quantity</label>
    <input type="number" id="cpuQty" name="quantity" required>

    <label>Total Price</label>
   <input type="number" id="cpuTotal" name="total" readonly>

    <label>Purchase Date</label>
    <input type="date" id="cpuPurchase" name="purchase_date" required>

    <label>Warranty (Months)</label>
    <input type="number" id="cpuWarranty" name="warranty" required>

    <label>Expiry Date</label>
    <input type="date" name="expiry_date" id="cpuExpiry" readonly>


    <label>Alert Date</label>
    <input type="date"  id="cpuAlert" readonly>
  `;
}
  // ================= MONITOR =================
  if (type === "monitor") {
    fields = `
    

      <label>Vendor Name</label>
      <input type="text" name="vendor" required>

      <label>Model</label>
      <input type="text" name="model" required>

      <label>Company</label>
      <input type="text" name="company" required>


      <label>Bill Upload</label>
      <input type="file" name="bill" required>

      <label>Price</label>
      <input type="number" id="monitorPrice" name="price" required>

      <label>Quantity</label>
      <input type="number" id="monitorQty" name="quantity" required>

      <label>Total</label>
      <input type="number" id="monitorTotal" name="total" readonly>

      <label>Purchase Date</label>
      <input type="date" id="monitorPurchase" name="purchase_date" required>

      <label>Warranty</label>
      <input type="number" id="monitorWarranty" name="warranty" required>

      <label>Expiry</label>
      <input type="date" name="expiry_date" id="monitorExpiry" readonly>

      <label>Alert</label>
      <input type="date" id="monitorAlert" readonly>

    `;
  }

  // ================= KEYBOARD =================
  if (type === "keyboard") {
    fields = `
    

      <label>Model</label>
      <input type="text" name="model" required>

      <label>Vendor Name</label>
      <input type="text" name="vendor" required>

      <label>Company</label>
      <input type="text" name="company" required>

      <label>Bill Upload</label>
      <input type="file" name="bill" required>

      <label>Price</label>
      <input type="number" id="kbPrice" name="price" required>

      <label>Quantity</label>
      <input type="number" id="kbQty" name="quantity" required>

      <label>Total</label>
      <input type="number" id="kbTotal" name="total" readonly>

      <label>Purchase Date</label>
      <input type="date" id="kbPurchase" name="purchase_date" required>

      <label>Warranty</label>
      <input type="number" id="kbWarranty" name="warranty" required>

      <label>Expiry</label>
      <input type="date" name="expiry_date" id="kbExpiry" readonly>


      <label>Alert</label>
      <input type="date"  id="kbAlert" readonly>
    `;
  }

  // ================= MOUSE =================
  if (type === "mouse") {
    fields = `
    

      <label>Model</label>
      <input type="text" name="model" required>

      <label>Vendor Name</label>
      <input type="text" name="vendor" required>

      <label>Company</label>
      <input type="text" name="company" required>

      <label>Bill Upload</label>
      <input type="file" name="bill" required>

      <label>Price</label>
      <input type="number" id="mousePrice" name="price" required>

      <label>Quantity</label>
      <input type="number" id="mouseQty" name="quantity" required>

      <label>Total</label>
      <input type="number" id="mouseTotal" name="total" readonly>

      <label>Purchase Date</label>
      <input type="date" id="mousePurchase" name="purchase_date" required>

      <label>Warranty</label>
      <input type="number" id="mouseWarranty" name="warranty" required>

      <label>Expiry</label>
      <input type="date" name="expiry_date" id="mouseExpiry" readonly>


      <label>Alert</label>
      <input type="date"  id="mouseAlert" readonly>
    `;
  }

  // ================= COMBO =================
  if (type === "combo") {
  fields = `
    <label>Vendor Name</label>
    <input type="text" name="vendor" required>

    <h4>Keyboard</h4>

    <label>Keyboard Model</label>
    <input type="text" name="kb_model" required>

    <label>Keyboard Company</label>
    <input type="text" name="kb_company" required>

 

    <hr>

    <h4>Mouse</h4>

    <label>Mouse Model</label>
    <input type="text" name="mouse_model" required>

    <label>Mouse Company</label>
    <input type="text" name="mouse_company" required>

   

    <label>Upload Bill</label>
    <input type="file" name="bill" required>

    <label>Price</label>
    <input type="number" id="comboPrice" name="price" required>

    <label>Quantity</label>
    <input type="number" id="comboQty" name="quantity" required>

    <label>Total</label>
    <input type="number" id="comboTotal" readonly>

    <label>Purchase Date</label>
    <input type="date" id="comboPurchase" name="purchase_date" required>

    <label>Warranty</label>
    <input type="number" id="comboWarranty" name="warranty" required>

    <label>Expiry</label>
    <input type="date" name="expiry_date" id="comboExpiry" readonly>


    <label>Alert</label>
    <input type="date"  id="comboAlert" readonly>
  `;
}

  document.getElementById("specificFields").innerHTML = fields;
  attachLogic();
}

function goBack() {
  document.getElementById("formCard").style.display = "none";
  document.getElementById("selectionCard").style.display = "block";
}

function attachLogic() {

  calc("cpuPrice","cpuQty","cpuTotal");
  calc("monitorPrice","monitorQty","monitorTotal");
  calc("kbPrice","kbQty","kbTotal");
  calc("mousePrice","mouseQty","mouseTotal");
  calc("comboPrice","comboQty","comboTotal");

  warranty("cpuPurchase","cpuWarranty","cpuExpiry","cpuAlert");
  warranty("monitorPurchase","monitorWarranty","monitorExpiry","monitorAlert");
  warranty("kbPurchase","kbWarranty","kbExpiry","kbAlert");
  warranty("mousePurchase","mouseWarranty","mouseExpiry","mouseAlert");
  warranty("comboPurchase","comboWarranty","comboExpiry","comboAlert");
}

function calc(priceId, qtyId, totalId) {
  const p = document.getElementById(priceId);
  const q = document.getElementById(qtyId);
  const t = document.getElementById(totalId);

  if(p && q && t){
    function update(){ t.value = (p.value * q.value) || ""; }
    p.addEventListener("input", update);
    q.addEventListener("input", update);
  }
}

function warranty(purchaseId, monthsId, expiryId, alertId) {
  const purchase = document.getElementById(purchaseId);
  const months   = document.getElementById(monthsId);
  const expiry   = document.getElementById(expiryId);
  const alert    = document.getElementById(alertId);

  if(purchase && months && expiry && alert){
    function update(){
      if(purchase.value && months.value){
        let d = new Date(purchase.value);
        d.setMonth(d.getMonth() + parseInt(months.value));
        d.setDate(d.getDate() - 1);
        expiry.value = d.toISOString().split("T")[0];

        let a = new Date(d);
        a.setDate(a.getDate() - 5);
        alert.value = a.toISOString().split("T")[0];
      }
    }
    purchase.addEventListener("change", update);
    months.addEventListener("input", update);
  }
}

/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted ) {
        window.location.reload();
    }
});
</script>

</body>
</html>