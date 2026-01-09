<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TheoRenaut</title>
    <link rel="stylesheet" href="<?php echo $style ?>">
</head>
<body>
    <nav class="navbar">
        <div class="menu-container">
            <details>
                <summary>
                    <img class="imgBurgerMenu" src="/site-cms/public/image/menu(1).png" alt="menu">
                </summary>
                    <ul class="menu">
                        <a href="/site-galerie/controller/galerie.php" target="_blank" ><li>Galerie</li></a>
                        <a href="/site-galerie/controller/presentation.php" target="_blank"><li>Présentation</li></a>
                        <a href="/site-galerie/controller/contact.php" target="_blank"><li>Contact</li></a>
                        <a href="/site-cms/index.php"><li>CMS</li></a>
                        <a href="/site-cms/index.php?page=toDoList"><li>To Do List</li></a>
                    </ul>
            </details>
        </div>
        <div class="logo"><a href="/site-cms/index.php">Théo Renaut</a></div>
        <ul class="contact">
            <li><a href="/site-cms/index.php"><img src="/site-cms/public/image/contact.png" alt="connexion"></a></li>
        </ul>
    </nav>