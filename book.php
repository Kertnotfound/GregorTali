<?php

require "connection.php";

function escape($value)

{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$id = $_GET['id'] ?? '';
$book = false;
$authors = [];

if (is_numeric($id)) {
    $stmt = $pdo->prepare('SELECT id, title, release_date, cover_path, language, summary, price, stock_saldo, pages, type FROM books WHERE id = :id AND is_deleted = 0');
    $stmt->execute(['id' => $id]);
    $book = $stmt->fetch();

    if ($book) {
        $stmt = $pdo->prepare('
            SELECT authors.first_name, authors.last_name
            FROM authors
            JOIN book_authors ON authors.id = book_authors.author_id
            WHERE book_authors.book_id = :id
        ');
        $stmt->execute(['id' => $id]);
        $authors = $stmt->fetchAll();
    }
}

?>

<!DOCTYPE html>
<html lang="et">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $book ? escape($book['title']) : 'Raamatut ei leitud' ?></title>
</head>
<body>
    <?php if (!$book) { ?>
        <h1>Raamatut ei leitud</h1>
        <p>Selle ID-ga aktiivset raamatut ei ole.</p>
        <a href="index.php">Tagasi raamatute nimekirja</a>
    <?php } else { ?>
        <p><a href="index.php">Tagasi raamatute nimekirja</a></p>
        <h1><?= escape($book['title']) ?></h1>

        <?php if ($book['cover_path']) { ?>
            <p><img src="<?= escape($book['cover_path']) ?>" alt="<?= escape($book['title']) ?> kaanepilt" width="180"></p>
        <?php } else { ?>
            <p>Kaanepilt puudub.</p>
        <?php } ?>

        <p><strong>Ilmumisaasta:</strong> <?= escape($book['release_date']) ?></p>
        <p><strong>Keel:</strong> <?= escape($book['language']) ?></p>
        <p><strong>Hind:</strong> <?= number_format((float) $book['price'], 2, ',', ' ') ?> €</p>
        <p><strong>Lehekülgi:</strong> <?= (int) $book['pages'] ?></p>
        <p><strong>Raamatu tüüp:</strong> <?= escape($book['type']) ?></p>
        <p><strong>Laos:</strong> <?= escape($book['stock_saldo']) ?></p>
        <p><strong>Kirjeldus:</strong> <?= escape($book['summary']) ?></p>

        <h2>Autorid</h2>
        <?php if (count($authors) > 0) { ?>
            <ul>
                <?php foreach ($authors as $author) { ?>
                    <li><?= escape($author['first_name']) ?> <?= escape($author['last_name']) ?></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p>Sellel raamatul pole autoreid lisatud.</p>
        <?php } ?>

        <p><a href="edit.php?id=<?= (int) $book['id'] ?>">Muuda raamatut</a></p>
    <?php } ?>
</body>
</html>
