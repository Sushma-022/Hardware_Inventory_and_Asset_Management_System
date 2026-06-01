<?php
include "../auth/auth.php";
include "../db.php";

$items = $_POST['items'] ?? [];

if (empty($items)) { die("No items selected!"); }
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; padding: 40px; background: #f4f4f4; }
        .confirm-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 800px; margin: auto; }
        .warning { color: #dc3545; font-weight: bold; font-size: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <div class="confirm-box">
        <h2 class="warning">⚠️ Confirm Deletion</h2>
        <p>You are about to delete the following <strong><?= count($items) ?></strong> items permanently:</p>
        
        <form action="delete_multiple.php" method="POST">
            <table>
                <tr><th>Type</th><th>Serial Number</th></tr>
                <?php foreach($items as $item): 
                    list($type, $serial) = explode('|', $item); ?>
                    <tr>
                        <td><?= $type ?></td>
                        <td><?= $serial ?></td>
                        <input type="hidden" name="items[]" value="<?= $item ?>">
                    </tr>
                <?php endforeach; ?>
            </table>
            
            <div style="margin-top: 20px; display: flex; gap: 10px;">
                <button type="submit" style="background: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">YES, DELETE PERMANENTLY</button>
                <a href="view_equipment.php" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; text-decoration: none;">CANCEL</a>
            </div>
        </form>
    </div>
</body>
</html>