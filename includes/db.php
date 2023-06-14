<?php
class DB
{
    private static $instance = null;
    private ?PDO $dbh;
    private bool $connected = false;

    /**
     * Constructor privat (Singelton)
     */
    private function __construct()
    {
        try {
            error_log("try");
            $username = 'paolo';
            $password = 'patata';
            $this->dbh = new PDO('mysql:host=localhost;dbname=escalada', $username, $password);
            $this->connected = true;
        } catch (Exception $e) {
            error_log("catch");
            error_log($e->getMessage());
        }
    }

    /**
     * Mètode per agafar la instància sempre activa (Singelton)
     * @return DB
     */
    public static function get_instance(): DB
    {
        if (self::$instance == null) {
            self::$instance = new DB();
        }

        return self::$instance;
    }

    /**
     * Comprova la connexió amb la base de dades.
     * @return bool
     */
    public function connected() : bool
    {
        return $this->connected;
    }

 
    public function get_participants(): array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare("SELECT nom, cognom, email FROM participant");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];
        return $arr;

    }
    public function get_via(): array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare("SELECT nom, sector, grau FROM via");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];
        return $arr;

    }

    public function get_assoliments() : array
    {
        if(!$this->connected) return [];
        $stmt = $this->dbh->prepare(
            "SELECT a.participant, a.via, a.encadenat, a.primer FROM assoliment a 
            INNER JOIN participant p ON a.participant = p.email
            GROUP BY a.participant, a.via
            ORDER BY p.nom ASC;
            "
        );
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        if(!$success)
            return [];

        $participants = [];
            foreach ($arr as $row) {
                $participant = $row['participant'];
                $via = $row['via'];
                $encadenat = $row['encadenat'];
                $primer = $row['primer'];
        
                if (!isset($participants[$participant])) {
                    $participants[$participant] = [
                        'participant' => $participant,
                        'routes' => []
                    ];
                }
        
                $participants[$participant]['routes'][] = [
                    'via' => $via,
                    'encadenat' => $encadenat,
                    'primer' => $primer
                ];
            }
        
            return array_values($participants);
    }
    public function introduir_sectors_vies() {
        if (!$this->connected) return false;
    
        $sectors = array(
            "Collegats - La pedrera",
            "Sadernes - El diable"
        );
        
        $stmt = $this->dbh->prepare("INSERT INTO sector (nom) VALUES (:nom)");
        foreach ($sectors as $sector) {
            $stmt->bindValue(':nom', $sector);
            $stmt->execute();
        }
        $stmt = null;
    
        $vies = array(
            array("Nom" => "Rollo Love", "Sector" => "Collegats - La pedrera", "Grau" => "8a"),
            array("Nom" => "Rollo Javalí", "Sector" => "Collegats - La pedrera", "Grau" => "8a+"),
            array("Nom" => "Bioactiva", "Sector" => "Collegats - La pedrera", "Grau" => "7c+"),
            array("Nom" => "L’arcada del dimoni", "Sector" => "Sadernes - El diable", "Grau" => "7b"),
            array("Nom" => "Bruixots", "Sector" => "Sadernes - El diable", "Grau" => "6c+")
        );
    
        $stmt = $this->dbh->prepare("INSERT INTO via (nom, sector, grau) VALUES (:nom, :sector, :grau)");
        foreach ($vies as $via) {
            $stmt->bindValue(':nom', $via['Nom']);
            $stmt->bindValue(':sector', $via['Sector']);
            $stmt->bindValue(':grau', $via['Grau']);
            $stmt->execute();
        }
        $stmt = null;
    
        return true;
    }
    
    /**
     * Retorna una llista dels escons aconseguits per cada partit
     * Les claus de l'array de sortida són els partits i els valors els escons
     *
     * @return array
     */
    public function delete_assoliments() {
        if (!$this->connected) return false;
        
        $stmt = $this->dbh->prepare("DELETE FROM assoliment");
        $success = $stmt->execute();
        $stmt = null;

        return $success;
    }

    public function delete_participants() {
        if (!$this->connected) return false;
        
        $stmt = $this->dbh->prepare("DELETE FROM participant");
        $success = $stmt->execute();
        $stmt = null;

        return $success;
    }

    public function delete_vies_sectors() {
        if (!$this->connected) return false;
        
        $stmt = $this->dbh->prepare("DELETE FROM via");
        $success = $stmt->execute();
        $stmt = null;

        $stmt = $this->dbh->prepare("DELETE FROM sector");
        $success = $stmt->execute();
        $stmt = null;

        return $success;
    }



    public function set_participant(string $nom, string $cognom, string $email ) : bool
    {
        if(!$this->connected) return false;

        try {
            $stmtInsert = $this->dbh->prepare("INSERT INTO participant (nom, cognom, email) VALUES (?, ?, ?)");
            $stmtInsert->execute([$nom, $cognom, $email]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }    
    }
    public function set_assoliment(string $participant, string $via, int $intent, string $data, bool $encadenat, bool $primer, string $assegurador ) : bool
    {
        if(!$this->connected) return false;

        try {
            $stmtInsert = $this->dbh->prepare("INSERT INTO assoliment (participant, via, intent, data, encadenat, primer, assegurador) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtInsert->execute([$participant, $via, $intent, $data, $encadenat, $primer, $assegurador]);
            return true;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }    
    }
}
