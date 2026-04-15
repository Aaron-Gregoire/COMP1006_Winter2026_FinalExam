<?php
// shows form, validates data, saves new task to the db, goes back to index
require_once 'includes/auth.php';
require_once 'includes/connect.php';


$pageTitle = "Add image";

$errors   = [];
$formData = [];
$imagePath = null;

//chack if submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $formData['image_title']  = trim($_POST['image_title'] ?? '');
 
    //validation
    if (empty($formData['image_title'])) {
        $errors['image_title'] = 'image name is required.';
    }
//file uploading
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
            $errors['image_title'] = "failed to save file";
        }
    } else {
        $errors['image_title'] = "only jpg and png files under 5mb are allowed";
    }
}

    //save to db
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO images (image_title, user_id, image_path)
                        VALUES (:image_title, :user_id, :image_path)";

            $stmt = $pdo->prepare($sql);

            $stmt->bindParam(':image_title',  $formData['image_title']);
            $stmt->bindParam(':user_id',    $_SESSION['user_id']);
            $stmt->bindParam(':image_path', $imagePath);


            $stmt->execute();

            //back to main if sucess
            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $errors['db'] = "Database error occurred.";
        }
    }
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Add New image</h4>
            </div>
            <div class="card-body">

                <!-- show db error if it broke -->
                <?php if (!empty($errors['db'])) { ?>
                    <div class="alert alert-danger"><?php echo $errors['db']; ?></div>
                <?php } ?>

                <!-- running the js and php validation on the form-->
                 <form action="create.php" method="POST" enctype="multipart/form-data" onsubmit="return validateTaskForm()" novalidate>

                    <div class="mb-3">
                        <label for="image_title" class="form-label">image name <span class="text-danger">*</span></label>
                        <input type="" class="form-control <?php if(isset($errors['image_title'])) echo 'is-invalid'; ?>"
                               id="image_title" name="image_title" value="<?php echo htmlspecialchars($formData['image_title'] ?? ''); ?>">
                        <?php if(isset($errors['image_title'])) { ?>
                            <div class="invalid-feedback"><?php echo $errors['image_title']; ?></div>
                        <?php } ?>
                    </div>
                     <div class="mb-3">
                        <label for="image_path" class="form-label">image</label>
                        <input type="file" class="form-control" id="image_path" name="image_path" 
                               accept=".jpg,.jpeg,.png.">
                        <small class="form-text">Max 5MB.</small>
                        <?php if(isset($errors['image_path'])) { ?>
                            <div class="invalid-feedback d-block"><?php echo $errors['image_path']; ?></div>
                        <?php } ?>
                    </div>
                    <!--buttons-->
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">Add Task</button>
                        <a href="index.php" class="btn btn-outline-secondary">Cancel</a>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>