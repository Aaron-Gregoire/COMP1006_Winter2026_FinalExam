<?php
// pre fills form, validates server side, updates the db, goes back to index
require_once 'includes/auth.php';
require_once 'includes/connect.php';

$pageTitle = "Edit Task";

$errors   = [];
$formData = [];

//getting task id
$imageId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// if the id is 0 or negative back to the main page 
if ($imageId <= 0) {
    header("Location: index.php");
    exit;
}

// form submission handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formData['image_title']  = trim($_POST['image_title'] ?? '');

    // validation
    if (empty($formData['image_title'])) {
        $errors['image_title'] = 'image name is required.';
    }
    
    //file upload same as add
    $attachmentPath = null; 
    if (!empty($_FILES['image_path']['name']) && empty($errors)) {
        $file = $_FILES['image_path'];
        $maxSize = 5 * 1024 * 1024; 
        $allowedExt = ['jpg', 'jpeg', 'png'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($file['error'] === 0 && $file['size'] <= $maxSize && in_array($ext, $allowedExt)) {
            
            $newName = 'image_' . uniqid() . '.' . $ext;
            $uploadDir = 'uploads/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $targetPath = $uploadDir . $newName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $attachmentPath = $targetPath;
            } else {
                $errors['image_path'] = "failed to save file";
            }
        } else {
            $errors['image_path'] = "only jpg and png files under 5mb are allowed";
        }
    }

    // if it has no errors update the db
    if (empty($errors)) {
        try {
            $sql = "UPDATE tasks 
                    SET image_title  = :image_title,
                        image_path = COALESCE(:image_path, image_path)  
                    WHERE id = :id AND user_id = :user_id";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':image_title',  $formData['image_title']);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->bindParam(':id',         $imageId);
            $stmt->bindParam(':user_id', $_SESSION['user_id']);

            $stmt->execute();

            // back to the list
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            //db problem
            $errors['db'] = "Database error occurred.";
        }
    }
} else {
    //load an existing task
    $sql = "SELECT * FROM images WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $taskId);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $task = $stmt->fetch();

    // non existing task
    if (!$task) {
        header("Location: index.php");
        exit;
    }

    //pre fill form
    $formData = $images;
}


include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Edit images: <?php echo htmlspecialchars($formData['image_title'] ?? 'Unknown'); ?></h4>
            </div>
            <div class="card-body">

                <!-- show the db error -->
                <?php if (!empty($errors['db'])) { ?>
                    <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
                <?php } ?>

                <form action="update.php?id=<?php echo $taskId; ?>" method="POST" 
                      onsubmit="return validateTaskForm()" novalidate>

                    <div class="mb-3">
                        <label for="image_title" class="form-label">image Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control <?php if(isset($errors['image_title'])) echo 'is-invalid'; ?>"
                               id="image_title" name="image_title" 
                               value="<?php echo htmlspecialchars($formData['image_title'] ?? ''); ?>">
                        <?php if(isset($errors['image_title'])) { ?>
                            <div class="invalid-feedback"><?php echo $errors['image_title']; ?></div>
                        <?php } ?>
                        </div>

                    <!-- file upload field  -->
                    <div class="mb-3">
                        <label for="image_path" class="form-label">New image</label>
                        <input type="file" class="form-control" id="image_path" name="image_path" 
                               accept=".jpg,.jpeg,.png">
                        <small class="form-text">Max 5MB. Leave blank to keep current file.</small>
                        <?php if(isset($errors['image_path'])) { ?>
                            <div class="invalid-feedback d-block"><?php echo $errors['image_path']; ?></div>
                        <?php } ?>
                    </div>

                    <!-- show current attachment if exists -->
                    <?php if (!empty($formData['image_path'])): ?>
                    <div class="mb-3">
                        <p class="form-text">
                            Current file: 
                            <a href="<?php echo htmlspecialchars($formData['image_path']); ?>" target="_blank">View Current image</a>
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- save and cancel buttons -->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-warning text-dark">Save Changes</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>