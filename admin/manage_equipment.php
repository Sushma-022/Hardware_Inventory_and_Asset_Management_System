<?php
include "../auth/auth.php";
include "../db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Equipment</title>
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
    </aside>

    <div class="main">
        <div class="navbar">
            Manage Equipment Types
        </div>

        <div class="content">

            <div class="card">
                <h3>Add New Equipment</h3>
                <form method="POST" action="save_equipment_type.php" onsubmit="return validateForm();">
                    
                    <label>Equipment Name</label>
                    <input type="text" name="equipment_name" id="equipment_name" required>

                    <br><br>

                    <button type="submit" class="btn-primary">
                        Save Equipment
                    </button>
                </form>
            </div>

            <div class="card">
                <h3>Existing Equipment</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr>
                            <th style="padding:10px; border-bottom:2px solid #ddd;">ID</th>
                            <th style="padding:10px; border-bottom:2px solid #ddd;">Equipment</th>
                            <th style="padding:10px; border-bottom:2px solid #ddd;">Fields</th>
                            <th style="padding:10px; border-bottom:2px solid #ddd;">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $result = $conn->query("
                            SELECT id, UPPER(name) as name 
                            FROM equipment_types 
                            GROUP BY UPPER(name) 
                            ORDER BY id DESC
                        ");

                        while($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td style="padding:10px; border-bottom:1px solid #eee; text-align:center;">
                                <?= $i++ ?>
                            </td>
                            <td style="padding:10px; border-bottom:1px solid #eee; text-align:center;">
                                <?= htmlspecialchars($row['name']) ?>
                            </td>
                            <td style="padding:10px; border-bottom:1px solid #eee; text-align:center;">
                                <a href="manage_fields.php?equipment_id=<?= $row['id'] ?>" class="details-btn">
                                    Manage Fields
                                </a>
                            </td>
                           <td style="padding:10px; border-bottom:1px solid #eee; text-align:center;">
    <a href="delete_equipment.php?delete_name=<?= urlencode($row['name']) ?>" 
       onclick="return confirm('Delete this equipment?')" 
       style="color:red; font-weight:bold;">
        Delete
    </a>
</td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
function validateForm(){
    let name = document.getElementById("equipment_name").value;

    if(name.trim() === ""){
        alert("Please enter equipment name");
        return false;
    }
    return true;
}
</script>
</body>
</html>