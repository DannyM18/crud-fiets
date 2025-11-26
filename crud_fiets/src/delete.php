<?php
// auteur: Vul hier je naam in
// functie: verwijder een fiets op basis van de id

require_once 'config.php';
require_once 'Database.php';
require_once 'FietsenRepository.php';

// Haal fiets uit de database
if(isset($_GET['id'])){
    $db = new Database(SERVERNAME, USERNAME, PASSWORD, DATABASE);
    $repository = new FietsenRepository($db);

    // test of verwijderen gelukt is
    if($repository->delete($_GET['id']) == true){
        echo '<script>alert("Fietscode: ' . $_GET['id'] . ' is verwijderd")</script>';
        echo "<script> location.replace('index.php'); </script>";
    } else {
        echo '<script>alert("Fiets is NIET verwijderd")</script>';
    }
}
?>

