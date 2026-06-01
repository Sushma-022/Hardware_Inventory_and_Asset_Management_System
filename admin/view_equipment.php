<?php
include "../auth/auth.php";
include "../db.php";

/* PAGINATION */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

function addFilter($baseQuery, $search, $range) {
    $where = " WHERE 1=1";

    if (!empty($search)) {
        $where .= " AND (serial_number LIKE '%$search%' OR vendor LIKE '%$search%')";
    }

    if (!empty($range)) {
        if (strpos($range, '-') !== false) {
            $parts = explode('-', $range);
            $start_full = trim($parts[0]);
            $end_num = trim($parts[1]);

            $last_slash = strrpos($start_full, '/');

            if ($last_slash !== false) {
                $prefix = substr($start_full, 0, $last_slash + 1);
                $start_num = substr($start_full, $last_slash + 1);

                $where .= " AND serial_number LIKE '$prefix%' 
                AND CAST(SUBSTRING_INDEX(serial_number, '/', -1) AS UNSIGNED) 
                BETWEEN $start_num AND $end_num";
            }
        } else {
            $where .= " AND serial_number = '$range'";
        }
    }

    return $baseQuery . $where;
}

$search = $_GET['search'] ?? "";
$range_search = $_GET['range_search'] ?? "";
/* ====================================================================
   1. BUILD THE MASTER LIST (MUCH FASTER & CLEANER!)
==================================================================== */
$union_query = "
    SELECT equipment_type AS type, serial_number, vendor, company, model AS display_model, price, quantity, bill, purchase_date, expiry_date, created_at FROM configurations
    UNION ALL
    SELECT 'CPU', serial_number, vendor, company, CONCAT(processor,' (',ram,' RAM, ',storage,')'), price, quantity, bill, purchase_date, expiry_date, created_at FROM cpu
    UNION ALL
    SELECT 'MONITOR', serial_number, vendor, company, model, price, quantity, bill, purchase_date, expiry_date, created_at FROM monitor
    UNION ALL
    SELECT 'KEYBOARD', serial_number, vendor, company, model, price, quantity, bill, purchase_date, expiry_date, created_at FROM keyboard
    UNION ALL
    SELECT 'MOUSE', serial_number, vendor, company, model, price, quantity, bill, purchase_date, expiry_date, created_at FROM mouse
    UNION ALL
    SELECT 'COMBO', serial_number, vendor, company, CONCAT('KB: ', kb_serial, ' | Mouse: ', mouse_serial), price, quantity, bill, purchase_date, expiry_date, created_at FROM combo_set
";

/* ====================================================================
   2. SECURE FILTERS (FIXES THE PRINTER CRASH)
==================================================================== */
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : "";
$range_search = isset($_GET['range_search']) ? $conn->real_escape_string($_GET['range_search']) : "";

$where = " WHERE 1=1";

if (!empty($search)) {
    // Now searches Type, Serial, AND Vendor safely!
    $where .= " AND (serial_number LIKE '%$search%' OR vendor LIKE '%$search%' OR type LIKE '%$search%')";
}

if (!empty($range_search)) {
    if (strpos($range_search, '-') !== false) {
        $parts = explode('-', $range_search);
        $start_full = trim($parts[0]);
        $end_num = (int)trim($parts[1]); // Force integer for safety
        $last_slash = strrpos($start_full, '/');
        
        if ($last_slash !== false) {
            $prefix = substr($start_full, 0, $last_slash + 1);
            $start_num = (int)substr($start_full, $last_slash + 1);

            $where .= " AND serial_number LIKE '$prefix%' 
                        AND CAST(SUBSTRING_INDEX(serial_number, '/', -1) AS UNSIGNED) 
                        BETWEEN $start_num AND $end_num";
        }
    } else {
        // Single quotes here prevent the 'Printer' column crash!
        $where .= " AND serial_number = '$range_search'";
    }
}

/* ====================================================================
   3. COUNT AND FETCH FINAL DATA
==================================================================== */
$count_query = "SELECT COUNT(*) as total FROM ($union_query) as master_list $where";
$count_result = $conn->query($count_query);
$total_records = $count_result->fetch_assoc()['total'];

$total_pages = ceil($total_records / $limit);

$combined_query = "SELECT * FROM ($union_query) as master_list 
                   $where 
                   ORDER BY created_at DESC, serial_number DESC 
                   LIMIT $limit OFFSET $offset";

$equipment = $conn->query($combined_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Equipment</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .btn-edit { background: #0d6efd; color: white; border: none; padding: 6px; border-radius: 4px; cursor: pointer; font-size: 13px; margin-bottom: 2px;}
        .btn-delete { background: #dc3545; color: white; border: none; padding: 6px; border-radius: 4px; text-decoration: none; text-align: center; font-size: 13px; display: block; margin: 2px 0; }
        .btn-details { background: #198754; color: white; border: none; padding: 6px; border-radius: 4px; cursor: pointer; font-size: 13px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 10px;}
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .action-btns { display: flex; flex-direction: column; width: 100px; }
    </style>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <h2>Hardware Admin</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="add_equipment.php">Add Equipment</a>
        <a class="active" href="view_equipment.php">View Equipment</a>
        <a href="issue_equipment_employee.php">Issue Equipment</a>
        <a href="view_issued.php">View Issued</a>
        <a href="upload_employees.php">Upload Employees</a>
        <a href="stock_available.php">Stock Available</a>
        <a href="backup_restore.php">Backup & Restore</a>
    </aside>

    <div class="main">
        
        <div class="navbar">
  <span>View Equipment</span>

  <div class="nav-right">
    <a href="../auth/logout.php" class="logout-btn"
    onclick="return confirm('Are you sure you want to Logout?')">Logout</a>
  </div>
</div>
        <div class="content">
            
            <form method="GET" action="view_equipment.php" style="margin-bottom:20px; padding: 15px; background: #e9ecef; border: 1px solid #ced4da; border-radius: 8px;">
                <h4 style="margin-top: 0;">🔍 Search / Preview Range</h4>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <input type="text" name="range_search" placeholder="e.g., VFSTR/TD/HP/CPU/001 - 015" 
                           style="flex-grow: 1; padding: 10px; border: 1px solid #adb5bd; border-radius: 4px;" 
                           value="<?= htmlspecialchars($range_search) ?>">
                    <button type="submit" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">Display Range</button>
                    <?php if(!empty($range_search)): ?>
                        <a href="view_equipment.php" style="text-decoration: none; color: #dc3545; font-weight: bold;">Clear Filter</a>
                    <?php endif; ?>
                </div>
            </form>

            <div class="card">
                <h3>Equipment List</h3>
                
                <form method="POST" action="delete_multiple.php" id="deleteForm">
                    <div style="display: flex; justify-content: flex-end; align-items: center; gap: 15px; margin-bottom: 20px; padding: 15px; background: #fff; border: 1px solid #ddd; border-radius: 8px;">
                        <div id="deleteWarning" style="display: none; color: #dc3545; font-weight: bold; font-size: 14px;">
                            ⚠️ <span id="selectedCount">0</span> items selected.
                        </div>
                        <button type="button" id="bulkDeleteBtn" style="background: #dc3545; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">Delete Selected</button>
                        <button type="submit" id="finalDeleteBtn" style="display: none; background: #8b0000; color: white; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer;">Confirm Permanent Deletion</button>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Type</th>
                                <th>Serial Number</th>
                                <th>Vendor</th>
                                <th>Model / Config</th>
                                <th>Price</th>
                                <th>Bill</th>
                                <th>Purchase Date</th>
                                <th>Expiry Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($equipment && $equipment->num_rows > 0): ?>
                                <?php while($row = $equipment->fetch_assoc()): 
                                    $purchase = !empty($row['purchase_date']) && $row['purchase_date'] != '0000-00-00' ? date("d-m-Y", strtotime($row['purchase_date'])) : 'Not Set';
                                ?>
                                <tr>
                                    <td><input type="checkbox" name="items[]" value="<?= $row['type'].'|'.$row['serial_number'] ?>"></td>
                                    <td><?= $row['type'] ?></td>
                                    <td><?= $row['serial_number'] ?></td>
                                    <td><?= $row['vendor'] ?></td>
                                    <td><?= $row['display_model'] ?></td>
                                    <td>₹<?= number_format($row['price'], 2) ?></td>
<td>
    <?php if (!empty($row['bill'])): ?>
        <?php 
            // Check if the file is a Word doc or PDF
            $file_ext = strtolower(pathinfo($row['bill'], PATHINFO_EXTENSION));
            $is_word = in_array($file_ext, ['doc', 'docx']);
        ?>
        <a href="/hardware/uploads/<?= rawurlencode($row['bill']) ?>" 
           target="_blank" 
           <?= $is_word ? '' : 'rel="noopener noreferrer"' ?>>
           <?= $is_word ? 'Download Bill (Word)' : 'View Bill (PDF/Img)' ?>
        </a>
    <?php else: ?> 
        <span style="color: gray;">-</span> 
    <?php endif; ?>
</td>
                                    <td><?= $purchase ?></td>
                                    <td>
                                        <?php
                                        if (empty($row['expiry_date']) || $row['expiry_date'] == '0000-00-00') {
                                            echo "<span style='color:gray;'>Not Set</span>";
                                        } else {
                                            $exp_ts = strtotime($row['expiry_date']);
                                            echo ($exp_ts <= time()) ? "<span style='color:red; font-weight:bold;'>" . date('d-m-Y', $exp_ts) . "</span>" : date('d-m-Y', $exp_ts);
                                        }
                                        ?>
                                    </td>
                                   <td>
    <div class="action-btns">
        <button type="button" class="btn-edit" onclick="editDetails('<?= strtoupper($row['type']) ?>','<?= urlencode($row['serial_number']) ?>')">Edit</button>
        
        <a href="delete_equipment.php?type=<?= urlencode($row['type']) ?>&serial=<?= urlencode($row['serial_number']) ?>" 
           class="btn-delete" 
           onclick="return confirm('Delete this item?')">Delete</a>
        
        <button type="button" class="btn-details" onclick="viewDetails('<?= strtoupper($row['type']) ?>','<?= urlencode($row['serial_number']) ?>')">Details</button>
    </div>
</td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="10" style="text-align:center;">No equipment found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table> 
               <div style="margin-top: 25px; display:flex; justify-content:center; align-items:center; gap:8px; flex-wrap:wrap;">

    <?php if($page > 1): ?>
        <a href="?page=<?= $page-1 ?>&range_search=<?= urlencode($range_search) ?>" 
           class="page-btn">&laquo;</a>
    <?php endif; ?>

    <?php
    $start = max(1, $page - 2);
    $end = min($total_pages, $page + 2);

    for($i = $start; $i <= $end; $i++):
    ?>
        <a href="?page=<?= $i ?>&range_search=<?= urlencode($range_search) ?>" 
           class="page-btn <?= ($i == $page) ? 'active-page' : '' ?>">
            <?= $i ?>
        </a>
    <?php endfor; ?>

    <?php if($page < $total_pages): ?>
        <a href="?page=<?= $page+1 ?>&range_search=<?= urlencode($range_search) ?>" 
           class="page-btn">&raquo;</a>
    <?php endif; ?>

</div>
                </form> 
            </div>
        </div>
    </div>
</div>

<div id="equipmentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; align-items:center; justify-content:center;">
    <div style="background:#fff; padding:10px; border-radius:12px; width:650px; max-width:95%; height:auto; max-height:95vh; overflow:hidden; position:relative;">
        <button onclick="closeModal()" style="position:absolute; top:10px; right:15px; background:#dc3545; color:white; border:none; padding:5px 10px; border-radius:4px; cursor:pointer; font-weight:bold; z-index:1001;">X</button>
        <iframe id="modalFrame" src="" style="width:100%; height:80vh; border:none; border-radius:8px; display:block;"></iframe>
    </div>
</div>

<script>
// --- SELECTION LOGIC ---
function updateSelectionCount() {
    let checkboxes = document.querySelectorAll('input[name="items[]"]:checked');
    let count = checkboxes.length;
    let warningDiv = document.getElementById('deleteWarning');
    let selectedSpan = document.getElementById('selectedCount');
    let bulkBtn = document.getElementById('bulkDeleteBtn');
    let finalBtn = document.getElementById('finalDeleteBtn');
    
    if (count > 0) {
        warningDiv.style.display = "block";
        selectedSpan.innerText = count;
    } else {
        warningDiv.style.display = "none";
        finalBtn.style.display = "none";
        bulkBtn.style.display = "block";
    }
}

document.getElementById("selectAll").onclick = function(){
    let boxes = document.getElementsByName("items[]");
    for(let b of boxes){ b.checked = this.checked; }
    updateSelectionCount();
}

document.addEventListener('change', function(e) {
    if (e.target.name === 'items[]') { updateSelectionCount(); }
});

document.getElementById('bulkDeleteBtn').onclick = function() {
    let checkboxes = document.querySelectorAll('input[name="items[]"]:checked');
    if (checkboxes.length === 0) {
        alert("Select at least one item first!");
        return;
    }
    this.style.display = "none";
    document.getElementById('finalDeleteBtn').style.display = "block";
};

// --- MODAL FUNCTIONS ---
function editDetails(type, serial) {
    // serial is already encoded from the PHP call
    document.getElementById('modalFrame').src = "edit_equipment.php?type=" + type + "&serial=" + serial;
    document.getElementById('equipmentModal').style.display = "flex";
}

function viewDetails(type, serial) {
    let cleanType = type.toLowerCase();
    // Use the specific file if it's a main component
    let targetFile = (["cpu","monitor","keyboard","mouse"].includes(cleanType)) ? "details_" + cleanType + ".php" : "details_equipment.php";
    document.getElementById('modalFrame').src = targetFile + "?type=" + type + "&serial=" + serial;
    document.getElementById('equipmentModal').style.display = "flex";
}

function closeModal() {
    document.getElementById('equipmentModal').style.display = "none";
    document.getElementById('modalFrame').src = ""; 
}

// --- AUTO-SELECT ON RANGE SEARCH ---
window.onload = function() {
    <?php if(!empty($range_search)): ?>
        let boxes = document.getElementsByName("items[]");
        if(boxes.length > 0) {
            document.getElementById('selectAll').checked = true;
            for(let b of boxes){ b.checked = true; }
            updateSelectionCount();
            document.getElementById('bulkDeleteBtn').click();
        }
    <?php endif; ?>
};

/* Force reload if page comes from browser cache (back button) */
window.addEventListener("pageshow", function (event) {
    if (event.persisted) {
        window.location.reload();
    }
});

</script>
</body>
</html>