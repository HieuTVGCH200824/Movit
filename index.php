<?php
require_once 'view/header.php';
require_once 'controller/SheetsController.php';
require_once 'vendor/autoload.php';
session_start();
?>

<?php
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
$controller = new SheetsController();
?>

<form Action="index.php" class="form" Method="POST" enctype="multipart/form-data">
    <label for="inputTag">
        <input id="inputTag" type="file" name="file" accept=".csv">
    </label>
    <button class="button" type="submit" name="import">Import</button>
</form>

<form class="form" Method="POST">
    <button class="button" type="submit" name="clear">Clear All</button>
</form>

<?php
if (isset($_POST['import'])) {
    $file = $_FILES['file']['tmp_name'];
    $controller->execute($file);
} ?>

<?php
if (isset($_POST['export'])) {
    $controller->export();
}
?>

<?php
if (isset($_POST['delete'])) {
    $no = $_POST['number'];
    $controller->delete($no);
}
?>

<?php
if (isset($_POST['add'])) {
    $origin = $_POST['origin'];
    $destination = $_POST['destination'];
    $prices = $_POST['prices'];
    $controller->add($origin, $destination, $prices);
}
?>

<?php
if (isset($_POST['clear'])) {
    session_destroy();
}
?>

<form class="form" Method="POST">
    <input type='text' name='origin' />
    <input type='text' name='destination' />
    <input type='text' name='prices' />
    <button class="button" type="submit" name="add">Add</button>
</form>
<form class="form" Method="POST">
    <button class="button" type="submit" name="export">Export</button>
</form>

<?php require_once('view/footer.php'); ?>