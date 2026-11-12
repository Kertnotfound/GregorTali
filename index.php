<?php

require "connection.php";

$search = $_GET['search'] ?? '';

$stmt = $pdo->prepare('SELECT id, title FROM books WHERE is_deleted = 0 AND title LIKE :search ORDER BY title');
$stmt->execute(['search' => '%' . $search . '%']);
$books = $stmt->fetchAll();

function escape($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raamatupood</title>
</head>
<body>
    <h1>Raamatud</h1>

    <form method="get" action="index.php">
        <label for="search">Otsi raamatu nime järgi:</label>
        <input type="text" id="search" name="search" value="<?= escape($search) ?>">
        <button type="submit">Otsi</button>
    </form>

    <?php if (count($books) > 0) { ?>
        <ul>
            <?php foreach ($books as $book) { ?>
                <li>
                    <a href="book.php?id=<?= (int) $book['id'] ?>"><?= escape($book['title']) ?></a>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Raamatuid ei leitud.</p>
    <?php } ?>
</body>
</html>
