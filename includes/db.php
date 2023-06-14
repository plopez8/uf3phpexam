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
            echo("noooo1");
            $this->dbh = new PDO('mysql:host=localhost;dbname=escalada', $username, $password);
            echo("noooo2");
            $this->connected = true;
            echo("noooo3");
        } catch (Exception $e) {
            echo("noooo");
            echo($e);
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

    /**
     * Retorna un array amb les comarques
     * @return array
     */
    public function get_comarques(): array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare("SELECT nom FROM comarques");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];

        // Converteix a array pla
        $comarques = [];
        foreach ($arr as $row){
            $comarques[] = $row["nom"];
        }

        return $comarques;
    }

    /**
     * Retorna un array amb tots els muncipis on cada element és un array (població, comarca i demarcació)
     * @return array
     */
    public function get_municipis(): array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare("SELECT poblacio, comarca, demarcacio FROM poblacions");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];

        return $arr;

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

    /**
     * Retorna un array amb les demarcacions
     * @return array
     */
    public function get_demarcacions(): array
    {
        if(!$this->connected) return [];
    
        $stmt = $this->dbh->prepare("SELECT nom FROM demarcacions");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
    
        if (!$success) {
            return [];
        }
    
        $demarcacions = [];
        foreach ($arr as $row){
            $demarcacions[] = $row["nom"];
        }

        return $demarcacions;
    }

    /**
     * Retorna un array tots els partits, cada element és un array (nom, color i curt)
     * On curt és el nom abreviat del partit
     * @return array
     */
    public function get_all_partits(): array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare("SELECT nom, color, curt FROM partits");
        $success = $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];

        return $arr;
    }

    /**
     * Retorna tots els partits amb candidatures a una demarcació, cada element és un array (nom, color i curt)
     * On curt és el nom abreviat del partit
     * @param $demarcacio
     * @return array
     */
    public function get_partits($demarcacio): array
    {
        if(!$this->connected) return [];
        try {
            $stmt = $this->dbh->prepare(
                "SELECT DISTINCT p.nom, p.color, p.curt FROM partits p
                INNER JOIN candidatures c ON p.curt = c.partit
                INNER JOIN poblacions po ON c.demarcacio = po.demarcacio
                WHERE UPPER(po.demarcacio) = UPPER(?);"
            );
            $success = $stmt->execute([$demarcacio]);
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stmt = null;

            if(!$success)
                return [];

            return $arr;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    /**
     * Comprova si existeix una demarcació donada
     * @param $demarcacio
     * @return bool
     */
    public function find_demarcacio($demarcacio): bool
    {
        if(!$this->connected) return false;

        $stmt = $this->dbh->prepare("SELECT nom FROM demarcacions WHERE UPPER(nom) =UPPER(?);");
        $success = $stmt->execute([$demarcacio]);
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return false;

        return true; // return $trobat;
    }

    /**
     * Retorna el nombre d'escons destinats a una demarcació
     * @param string $demarcacio
     * @return int
     */
    public function get_num_escons(string $demarcacio): int
    {
        if(!$this->connected) return 0;

        $stmt = $this->dbh->prepare(
            "SELECT escons FROM demarcacions WHERE UPPER(nom)=UPPER(?);"
        );
        $success = $stmt->execute([$demarcacio]);
        $arr = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success || count($arr) < 1)
            return 0;

        return $arr["escons"];
    }

    /**
     * Retorna una llista dels vots de cada partit donada una demarcació
     * Les claus de l'array de sortida són els partits i els valors els vots
     *
     * @param string $demarcacio
     * @return array
     */
    public function get_vots(string $demarcacio) : array
    {
        if(!$this->connected) return [];
        $stmt = $this->dbh->prepare(
            "SELECT DISTINCT v.partit, v.vots FROM vots v
            INNER JOIN poblacions p ON v.poblacio = p.poblacio
            WHERE UPPER(p.demarcacio) = UPPER(?);"
        );
        $success = $stmt->execute([$demarcacio]);
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];

        $vots = [];
        foreach ($arr as $row){
            $vots[$row["partit"]] = $row["vots"];
        }

        return $vots;
    }

    /**
     * Retorna una llista dels escons de cada partit donada una demarcació
     * Les claus de l'array de sortida són els partits i els valors els escons
     *
     * @param string $demarcacio
     * @return array
     */
    public function get_escons(string $demarcacio) : array
    {
        if(!$this->connected) return [];

        $stmt = $this->dbh->prepare(
            "SELECT partit, escons FROM escons
            WHERE UPPER(demarcacio) = UPPER(?)"
        );
        $success = $stmt->execute([$demarcacio]);
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return [];

        // Converteix al format [partits (clau), escons(valors)]
        $vots = [];
        foreach ($arr as $row){
            $vots[$row["partit"]] = $row["escons"];
        }

        return $vots;
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
        print_r($arr);
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

    /**
     * Retorna el nombre de demarcacions que ja tenen escons assignats
     *
     * @return int
     */
    public function count_demarcacio_with_escons() : int
    {
        if(!$this->connected) return false;

        $stmt = $this->dbh->prepare("SELECT COUNT(DISTINCT demarcacio) AS num_demarcacions FROM escons");
        $success = $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = null;

        if(!$success)
            return 0;

        return $result['num_demarcacions'];
    }

    /**
     * Assigna un array de partits (clau) i escons (valor) a una demarcació
     *
     * @param string $demarcacio
     * @param array $assignacio_escons
     * @return bool
     */
    public function set_escons(string $demarcacio, array $assignacio_escons) : bool
    {
        if(!$this->connected) return false;

        try {
            $this->dbh->beginTransaction();
    
            $stmtDelete = $this->dbh->prepare("DELETE FROM escons WHERE UPPER(demarcacio) = UPPER(?)");
            $stmtDelete->execute([$demarcacio]);
    
            $stmtInsert = $this->dbh->prepare("INSERT INTO escons (partit, demarcacio, escons) VALUES (?, ?, ?)");
            foreach ($assignacio_escons as $assignacio) {
                $partit = $assignacio['partit'];
                $numEscons = $assignacio['escons'];
                $stmtInsert->execute([$partit, $demarcacio, $numEscons]);
            }
    
            $this->dbh->commit();
    
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollBack();
            return false;
        }
    }

    /**
     * Assigna un array de partits (clau) i vots (valor) a una població
     *
     * @param string $poblacio
     * @param array $vots_partits
     * @return bool
     */
    public function set_vots(string $poblacio, array $vots_partits) : bool
    {
        if(!$this->connected) return false;

        try {
            $this->dbh->beginTransaction();
    
            $stmtDelete = $this->dbh->prepare("DELETE FROM vots WHERE UPPER(poblacio) = UPPER(?)");
            $stmtDelete->execute([$poblacio]);
    
            $stmtInsert = $this->dbh->prepare("INSERT INTO vots (partit, poblacio, vots) VALUES (?, ?, ?)");
            foreach ($vots_partits as $partit => $vots) {
                $partit = $partit;
                $votos = $vots;
                $stmtInsert->execute([$partit, $poblacio, $votos]);
            }
    
            $this->dbh->commit();
    
            return true;
        } catch (PDOException $e) {
            $this->dbh->rollBack();
            return false;
        }    
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
