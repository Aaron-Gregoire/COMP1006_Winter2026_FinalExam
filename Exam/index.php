<?php

require_once 'includes/auth.php';
require_once 'includes/connect.php';

$pageTitle = 'All images';

//only show images belonging to the logged in user
$sql = "SELECT * FROM tasks WHERE user_id = :user_id";
$stmt = $pdo->prepare($sql);
$stmt -> bindParam(':user_id', $_SESSION['user_id']);
$stmt->execute();
$tasks = $stmt->fetchAll();


$totalimages = count($images);
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">

        
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">All images</h4>
                <a href="add.php" class="btn btn-light btn-sm">+ Add image</a>
            </div>
          
            <div class="card-body">
                <div class="row g-3 mb-4 text-center">
                    <div class="col-6 col-md-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h3 class="text-primary mb-1"><?php echo $totalimages; ?></h3>
                                <p class="text-muted small mb-0">Total images</p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <?php if (empty($images)) { ?>

                    <div class="text-center py-5">
                        <h5 class="text-muted mb-3">No images yet</h5>
                        <a href="create.php" class="btn btn-primary">Add Your First image</a>
                    </div>

                <?php } else { ?>

                   
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered align-middle">
                            <thead class="table-primary">
                                <tr>
                                    <th>#</th>
                                    <th>image Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <!--edit link -->
                                        <a href="update.php?id=<?php echo $images['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                        
                                        <!-- delete -->
                                        <form action="delete.php" method="POST" class="d-inline" 
                                              onsubmit="return confirmDelete();">
                                            <input type="hidden" name="id" value="<?php echo $task['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php $rowNum++; } ?>
                            </tbody>
                        </table>
                    </div>

                

                <?php } ?>

            </div> 
        </div> 
    </div>
</div>

<?php include 'includes/footer.php'; ?>

