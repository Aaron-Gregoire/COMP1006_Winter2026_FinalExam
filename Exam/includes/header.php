<?php

if(!isset($pageTitle)){
    $pageTitle = "image gallery";
}
?>

<!DOCKTYPE html>
<html lang = "en">
<head>
    <meta charset="UTF-8">
    <meta name = "viewport" content = "width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($pageTitle); ?> | image gallery</title>

</head>

<body class="bg-light">   

<!-- nav bar for every page -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        

        <!-- menu botton -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- menu items -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">  
                <li class="nav-item">
                    <a class="nav-link" href="index.php">All images</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="create.php">+ add image</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout (<?= htmlspecialchars($userEmail 
                    ?? 'User') ?>)</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    
