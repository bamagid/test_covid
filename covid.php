<?php
// Démarrer la session
session_start();
if (!isset($_SESSION['historique'])) {
    $_SESSION['historique'] = [];
}
if (!isset($_SESSION['filtrage'])) {
    $_SESSION['filtrage'] = [];
}
function calcul_score(){
    // Collection des données du formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $poids = floatval($_POST['poids']);
    $tranche_age = $_POST['tranche_age'];
    $temperature =floatval($_POST['temperature']);
    $maux_tete = $_POST['maux_tete'];
    $diarrhee = $_POST['diarrhee'];
    $toux = $_POST['toux'];
    $perte_odorat = $_POST['perte_odorat'];
    $date=date('j F Y');
    $heure=date('H:i:s');
    if(!preg_match("/^[a-zA-Z]+$/", $nom) || strlen($nom) <2 || strlen($nom) >50) {
        echo "<p>Veuillez saisir un nom valide </p>";
        return false ;
    }elseif (!preg_match("/^[a-zA-Z\s']+$/", $prenom) || strlen($prenom) <2 || strlen($prenom) >50) {
        echo "<p>Veuillez saisir un prénom valide </p>";
        return false;
    }elseif ($poids < 8 || $poids >150) {
        echo "<p> Veuillez saisir un poids valide entre 8 et 150 kg </p>";
        return false ;
    }elseif ($temperature < 35 || $temperature > 42) {
        echo "<p>Veuillez saisir une température valide entre 35 et 42 degrés Celsius </p>" ;
        return false ;
    }elseif(empty($_POST['maux_tete'])||empty($_POST['diarrhee'])||empty($_POST['toux'])||empty($_POST['perte_odorat'])){
        echo "<p>Veuillez cocher 'oui' ou 'non' dans toutes les cages  </p>" ;
        return false;
    }else{ 
        $score=0;
         if ($_POST['tranche_age']=== '2-14' || $_POST['tranche_age']=== '60 et plus') {
            $score = 0;
            if ($maux_tete === 'oui') $score += 20;
            if ($diarrhee === 'oui') $score += 15;
            if ($toux === 'oui') $score += 30;
            if ($perte_odorat === 'oui') $score += 15;
            if (!($temperature>= 36.1) && !($temperature>= 37.2)){$score+=10;}
            if ($maux_tete === 'non') $score += 5;
            if ($diarrhee === 'non') $score += 3;
            if ($toux === 'non') $score += 4;
            if ($perte_odorat === 'non') $score += 5;
        }elseif ($_POST['tranche_age']=== '15-30' || $_POST['tranche_age']=== '30-60') {
            $score = 0;
            if ($maux_tete === 'oui') $score += 30;
            if ($diarrhee === 'oui'){$score+=10;}
            if ($toux === 'oui') $score += 20;
            if ($perte_odorat === 'oui') $score += 25;
            if (!($temperature>= 36.1 ) && !($temperature>= 37.2)){$score+=10;}
            if ($maux_tete === 'non') $score += 2;
            if ($diarrhee === 'non') $score += 3;
            if ($toux === 'non') $score += 4;
            if ($perte_odorat === 'non') $score += 5;
        }
        if ($_POST['tranche_age']=== '2-14') {
            if(!($_POST['poids'] < 10 || $_POST['poids'] > 50)){$score+=10;}
            }elseif($_POST['tranche_age']=== '15-30') {
            if(!($_POST['poids'] < 45 || $_POST['poids'] > 80)){$score+=10;}
            }elseif($_POST['tranche_age']=== '30-60') {
            if(!($_POST['poids'] < 50 || $_POST['poids'] > 85)){$score+=10;}
            }elseif($_POST['tranche_age']=== '60 et plus') {
            if(!($_POST['poids'] < 60 || $_POST['poids'] > 100)){$score+=10;}}
    if ($score >= 70) {
        $categorie = 'votre etat est Critique veuillez consulter un medecin en urgence';
    } 
    elseif($score >= 45 && $score <= 69) {
        $categorie = "Vous etes susceptible d'avoir le covid veuillez consulter un medecin pour plus de details ";
    } else {
        $categorie = 'vous etes sain.es prenez soins de vous et respecter les recommandations des medecins ';
    }
    $nouvelEnvoi =[
        'nom' => $nom,
        'prenom' => $prenom,
        'poids' => $poids,
        'tranche_age' => $tranche_age,
        'temperature' => $temperature,
        'maux_tete' => $maux_tete,
        'diarrhee' => $diarrhee,
        'toux' => $toux,
        'perte_odorat' => $perte_odorat,
        'score' => $score,
        'categorie' => $categorie,
        'date' => $date,
        'heure' =>$heure
    ];
    $_SESSION['historique'][] = $nouvelEnvoi;
   return $nouvelEnvoi;
   }
} 
if (isset($_POST['envoie'])) {
    $resultat = calcul_score($_POST['envoie']);
}
if (isset($_POST['filtre']) && isset($_POST['date'])) {
    $datefiltrer = date('j F Y',strtotime($_POST['date']));
    $_SESSION['filtrage'] = [];
    foreach ($_SESSION['historique'] as $historique_filtrer) {
        if ($datefiltrer == $historique_filtrer['date']) {
            $_SESSION['filtrage'][] = $historique_filtrer;
        }
    }
}
if (isset($_POST['supprimer'])) {
    $dateASupprimer = $_POST['date_a_supprimer'];
    foreach ($_SESSION['historique'] as $supprime => $historique) {
        $dateAffichee=$historique['date'];
        if ($dateAffichee === $dateASupprimer) {
            unset($_SESSION['historique'][$supprime]);
        }
    }
    $_SESSION['historique'] = array_values($_SESSION['historique']);
}
if (isset($_POST['reset'])) {
    session_destroy();
    $_SESSION['historique'] =[];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test COVID-19</title>
    <link rel="stylesheet" href="covid.css">
</head>
<body>
    <form action="covid.php" method="POST" class="left">
                <label for="nom">Nom :</label>
                <input type="text" autocomplete="off" class="texte" name="nom" placeholder="Enrer votre nom" required><br>
                <label for="prenom">Prénom :</label>
                <input type="text" class="texte" autocomplete="off" name="prenom"  placeholder="Entrer votre prenom" required><br>
                <label for="poids">Quel est votre poids (kg) :</label>
                <input type="number" class="number" name="poids" placeholder="exemple 50.10 ou 50" step="0.01" required><br>
            <div class="age">
                <label for="tranche_age">Choisissez votre tranche dâge :</label>
                <select class="tranche_age" name="tranche_age">
                        <option value="2-14">2-14</option>
                        <option value="15-30">15-30</option>
                        <option value="30-60">30-60</option>
                        <option value="70-plus">60 et plus</option>
                </select><br>
            </div>
                <label for="temperature">Quelle est votre température corporelle (°C) :</label>
                <input type="number" class="number" name="temperature" placeholder="exemple 25.10 ou 25" step="0.01" required><br>
                    <label>Avez vous des maux de tête :</label>
                <div class="mt">
                <span>
                    <input type="radio" class="radio" name="maux_tete" value="oui" required>
                    <label for="mt_oui">Oui</label>
                </span>
                <span>
                    <input type="radio" class="radio" name="maux_tete" value="non" required>
                    <label for="mt_non">Non</label><br>
                </span>
                </div>
                
                    <label>Avez vous de la iarrhée :</label>
                <div class="diarhee">
                <span>
                    <input type="radio" class="radio" name="diarrhee" value="oui" required>
                    <label for="diarrhee_oui">Oui</label>
                </span>
                <span>
                    <input type="radio" class="radio" name="diarrhee" value="non" required>
                    <label for="diarrhee_non">Non</label><br>
                </span>
                </div>

                    <label>Toussez vous beaucoup :</label>
                <div class="toux">
                <span>
                    <input type="radio" class="toux_oui" name="toux" value="oui" required>
                    <label for="toux_oui">Oui</label>
                </span>
                <span>
                    <input type="radio" class="toux_non" name="toux" value="non" required>
                    <label for="toux_non">Non</label><br>
                </span>
                </div>
                    <label>Perte de l'odorat :</label>
                <div class="po">
                <span>
                    <input type="radio" class="perte_odorat_oui" name="perte_odorat" value="oui" required>
                    <label for="perte_odorat_oui">Oui</label>
                </span>
                <span>
                    <input type="radio" class="perte_odorat_non" name="perte_odorat" value="non" required>
                    <label for="perte_odorat_non">Non</label><br>
                </span>
                </div>
        
            <button type="submit" name="envoie">Envoyer</button>
    </form>
    <form action="covid.php" method="POST" class="right">
    <button type="submit" name="reset" class="reset">Réinitialiser</button>
    <div class="historique">
            <h2>Historique des test :</h2>
            <ol>
                <?php
                $dateAffichee = '';
                foreach ($_SESSION['historique'] as $historique){
                    if ($dateAffichee !== $historique['date']) {
                        $dateAffichee=$historique['date'];
                        echo "<h3>Date : " . $dateAffichee . "</h3>";
                        echo '<form action="form.php" method="POST">';
                        echo '<input type="hidden" name="date_a_supprimer" value="' . $historique['date'] . '">';
                        echo ' <button type="submit" name="supprimer" class="supprimer">Supprimer</button>';
                        echo '</form>';
                    }
                
                    ?>
                    <li>
                    <?php 
                       
                       echo "<h2>Résultat du test COVID-19 pour " .$historique['prenom']." ".$historique['nom'] . ":</h2>";
                       echo "<p>heure : ". $historique['heure'] . "</p>";
                       echo "<p>poids : ". $historique['poids'] ."Kg"."</p>";
                       echo "<p>Tranche d'age : " . $historique['tranche_age'] ."</p>";
                       echo "<p>temperature corporelle : ". $historique['temperature']."°c". "</p>";
                       echo "<p>diarrhee : ". $historique['diarrhee'] . "</p>";
                       echo "<p>toux : ". $historique['toux'] ."</p>";
                       echo "<p>perte de l'odorat : ". $historique['perte_odorat'] . "</p>";
                       echo "<p>Score : ". $historique['score']."%". "</p>";
                       echo "<p>Notice : ". $historique['categorie'] ."</p>";
                      ?>
                    </li>
                <?php }?>
            </ol>
        </div>
    </form>

    <form action="covid.php" method="POST" class="filter">
        <input type="date" name="date" class="filtre" required>
        <button type="submit" name="filtre" class="filtre">FILTRER</button>
        <div class="historique_filtrer">
            <ol>
            <?php
           $historique = isset($_SESSION['filtrage']) ? $_SESSION['filtrage'] : $_SESSION['historique'];
           if (!empty($historique)) {
            echo "<h3>Date : " . $historique[0]['date'] . "</h3>";
            echo "<ol>";
            foreach ($historique as $test) :
            ?>
                <li>
                    <?php 
                    echo "<h2>Résultat du test COVID-19 pour " . $test['prenom'] . " " . $test['nom'] . ":</h2>";
                    echo "<p>heure : " . $test['heure'] . "</p>";
                    echo "<p>poids : " . $test['poids'] . "Kg</p>";
                    echo "<p>Tranche d'âge : " . $test['tranche_age'] . "</p>";
                    echo "<p>temperature corporelle : " . $test['temperature'] . "°c</p>";
                    echo "<p>diarrhee : " . $test['diarrhee'] . "</p>";
                    echo "<p>toux : " . $test['toux'] . "</p>";
                    echo "<p>perte de l'odorat : " . $test['perte_odorat'] . "</p>";
                    echo "<p>Score : " . $test['score'] . "%</p>";
                    echo "<p>Catégorie : " . $test['categorie'] . "</p>";
                    ?>
                </li>
            <?php endforeach;
            echo "</ol>";
        } 
        else{
            echo "Aucun résultat trouvé pour la date sélectionnée.";
        }
            ?>
            </ol>
        </div>
    </form>
</body>
</html>