<?php
require "db.php"; // připojení k SQLite

// vytvoření tabulky (pokud neexistuje)
$db->exec("CREATE TABLE IF NOT EXISTS interests (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT
)");

// ZPRACOVÁNÍ FORMULÁŘŮ
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // PŘIDÁNÍ
    if (isset($_POST["add"])) {
        $name = $_POST["name"];

        $stmt = $db->prepare("INSERT INTO interests (name) VALUES (:name)");
        $stmt->bindValue(":name", $name);
        $stmt->execute();

        header("Location: ?page=interests&msg=added");
        exit;
    }

    // MAZÁNÍ
    if (isset($_POST["delete"])) {
        $id = $_POST["id"];

        $stmt = $db->prepare("DELETE FROM interests WHERE id = :id");
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        header("Location: ?page=interests&msg=deleted");
        exit;
    }

    // EDITACE
    if (isset($_POST["edit"])) {
        $id = $_POST["id"];
        $name = $_POST["name"];

        $stmt = $db->prepare("UPDATE interests SET name = :name WHERE id = :id");
        $stmt->bindValue(":name", $name);
        $stmt->bindValue(":id", $id);
        $stmt->execute();

        header("Location: ?page=interests&msg=updated");
        exit;
    }
}

// NAČTENÍ DAT
$interests = $db->query("SELECT * FROM interests")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1>Zájmy</h1>

<!-- HLÁŠKY -->
<?php if (isset($_GET["msg"])): ?>
    <p style="color: green;">
        <?php
        if ($_GET["msg"] === "added") echo "Zájem přidán ✔";
        if ($_GET["msg"] === "deleted") echo "Zájem smazán ✔";
        if ($_GET["msg"] === "updated") echo "Zájem upraven ✔";
        ?>
    </p>
<?php endif; ?>

<!-- FORMULÁŘ NA PŘIDÁNÍ -->
<h2>Přidat zájem</h2>
<form method="post">
    <input type="text" name="name" required>
    <button type="submit" name="add">Přidat</button>
</form>

<hr>

<!-- VÝPIS -->
<h2>Seznam zájmů</h2>

<ul>
<?php foreach ($interests as $item): ?>
    <li>
        <!-- EDITACE -->
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $item["id"] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($item["name"]) ?>">
            <button name="edit">Uložit</button>
        </form>

        <!-- MAZÁNÍ -->
        <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $item["id"] ?>">
            <button name="delete" onclick="return confirm('Opravdu smazat?')">Smazat</button>
        </form>
    </li>
<?php endforeach; ?>
</ul>
