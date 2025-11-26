<?php
// auteur: Vul hier je naam in
// functie: FietsenRepository - Data Access Object

require_once 'Fiets.php';

class FietsenRepository {
    private $db;
    private $table = 'fietsen';

    public function __construct(Database $db) {
        $this->db = $db;
    }

    // Haal alle fietsen op
    public function getAll(): array {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM " . $this->table;
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll();
        
        return $result ? $result : [];
    }

    // Haal één fiets op basis van ID
    public function getById($id): ?Fiets {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->execute([':id' => $id]);
        $row = $query->fetch();

        if ($row) {
            return new Fiets($row['merk'], $row['type'], $row['prijs'], $row['id']);
        }
        return null;
    }

    // Voeg fiets toe
    public function insert(Fiets $fiets): bool {
        try {
            $conn = $this->db->getConnection();
            $sql = "INSERT INTO " . $this->table . " (merk, type, prijs) 
                    VALUES (:merk, :type, :prijs)";
            
            $values = [
                ':merk' => $fiets->getMerk(),
                ':type' => $fiets->getType(),
                ':prijs' => $fiets->getPrijs()
            ];

            $stmt = $conn->prepare($sql);
            $stmt->execute($values);

            return $stmt->rowCount() == 1;
        } catch (PDOException $e) {
            $this->handleError($e, $sql, $values ?? []);
            return false;
        }
    }

    // Update fiets
    public function update(Fiets $fiets): bool {
        try {
            $conn = $this->db->getConnection();
            $sql = "UPDATE " . $this->table . " 
                    SET merk = :merk, type = :type, prijs = :prijs 
                    WHERE id = :id";

            $values = [
                ':merk' => $fiets->getMerk(),
                ':type' => $fiets->getType(),
                ':prijs' => $fiets->getPrijs(),
                ':id' => $fiets->getId()
            ];

            $stmt = $conn->prepare($sql);
            $stmt->execute($values);

            return $stmt->rowCount() == 1;
        } catch (PDOException $e) {
            $this->handleError($e, $sql, $values ?? []);
            return false;
        }
    }

    // Verwijder fiets
    public function delete($id): bool {
        try {
            $conn = $this->db->getConnection();
            $sql = "DELETE FROM " . $this->table . " WHERE id = :id";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $id]);

            return $stmt->rowCount() == 1;
        } catch (PDOException $e) {
            $this->handleError($e, $sql, [':id' => $id]);
            return false;
        }
    }

    // Foutafhandeling
    private function handleError(PDOException $e, string $sql, array $values): void {
        $err = "
        <h2>Foutmelding</h2>
        Fout op bestand: " . $e->getFile() . " op regel " . $e->getLine() . "<br>" .
        "SQL-fout: " . $e->getMessage() . "<br>" .
        "Foute SQL: " . $sql . "<br>" .
        "Opgegeven waarden: " . print_r($values, true) . "<br><br>";
        echo $err;
    }
}
?>
