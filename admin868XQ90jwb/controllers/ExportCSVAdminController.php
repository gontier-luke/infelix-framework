<?php

class ExportCSVAdminController extends AdminControllerCore
{
    public function __construct()
    {
        $this->template = 'index.php';
    }

    public function run(): bool
    {
        //On récupère les possible anciens fichiers csv, si il y en a, on les supprime
        $listeAncienFichier = glob("../exportCSV/*.csv");
        if(is_array($listeAncienFichier)){
            foreach ($listeAncienFichier as $ancienFichier){
                unlink($ancienFichier);
            }
        }

        $date = date("d-m-Y");
        $this->exportResultsToCsv('Resultat_IMSIC_' . $date . '.csv');
        return true;
    }

    public function exportResultsToCsv($filename = 'resultat.csv') {
        list($questions, $userResults) = (new ExportCSVmodel())->getAllUserResult();

        // ouvrir fichier en écriture
        $file = fopen($filename, 'w');

        // On met les premières lignes (id et libelle question)
        $questionIds = array_keys($questions);
        fputcsv($file, array_merge(['ID', 'DATE'], $questionIds));

        $questionLabels = array_values($questions);
        fputcsv($file, array_merge(['', ''], $questionLabels));

        // ecriture des résultats
        foreach ($userResults as $userResult) {
            $row = [$userResult['ID'], $userResult['DATE']];
            foreach ($questionIds as $questionId) {
                $row[] = isset($userResult['REPONSE'][$questionId]) ? $userResult['REPONSE'][$questionId] : '';
            }
            fputcsv($file, $row);
        }

        fclose($file);

        header('Location: '.$filename);
    }
}