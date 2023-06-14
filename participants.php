<!DOCTYPE html>
<html>
<head>
    <title>Lista de Participantes</title>
    <style>
        .blue {
            color: blue;
        }
        .bold {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Lista de Participantes</h1>
    <ul>
        <?php
            require_once "includes/db.php";
            $db = DB::get_instance();
            $participants = $db->get_assoliments();
            foreach ($participants as $participant) {
                echo '<li>';
                echo $participant['participant'];
                $routes = $participant['routes'];

                if (!empty($routes)) {
                    echo '<ul>';
                    foreach ($routes as $route) {
                        $via = $route['via']; 
                        $encadenat = $route['encadenat'];
                        $primer = $route['primer'];
                        if ($primer) {
                            echo '<li><strong>' . $via . '</strong></li>';
                        } elseif ($encadenat) { 
                            echo '<li><span class="blue">' . $via . '</span></li>';
                        } else {
                            echo '<li>' . $via . '</li>';
                        }
                    }
                    echo '</ul>';
                }
                echo '</li>';
            }
        ?>
    </ul>
</body>
</html>
