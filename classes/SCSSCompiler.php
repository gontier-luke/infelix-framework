<?php

class SCSSCompiler
{
    private $scssFile;
    private $cssFile;
    private $variables = []; // Tableau pour stocker les variables SCSS
    private $mixins = [];    // Tableau pour stocker les mixins SCSS
    private $scssMaps = []; // Un tableau pour stocker les maps SCSS


    public function __construct($scssFile, $cssFile)
    {
        $this->scssFile = $scssFile;
        $this->cssFile = $cssFile;
    }

    public function compile()
    {
        // Lire le contenu du fichier SCSS
        $scssContent = file_get_contents($this->scssFile);
        if ($scssContent === false) {
            throw new Exception("Impossible de lire le fichier SCSS.");
        }

        // Traiter les imports avant de parser le SCSS
        $scssContent = $this->processImports($scssContent);

        // Analyser le contenu SCSS
        $cssContent = $this->parseSCSS($scssContent);

        // Écrire le contenu CSS dans le fichier
        $result = file_put_contents($this->cssFile, $cssContent);
        if ($result === false) {
            throw new Exception("Impossible d'écrire dans le fichier CSS.");
        }
    }

    private function parseSCSS($scss)
    {
        // Suppression des commentaires
        $scss = preg_replace('!/\*.*?\*/!s', '', $scss);
        $scss = preg_replace('/\n\s*\n/', "\n", $scss);

        // Extraction des variables et mixins
        $scss = $this->processVariables($scss);
        $scss = $this->processMixins($scss);

        // Séparer le contenu en lignes
        $lines = explode("\n", $scss);
        $css = '';

        $indentStack = [];       // Pile pour les sélecteurs imbriqués
        $mediaQueries = [];      // Pile pour les media queries
        $currentMediaQuery = ''; // Indique si on est dans une media query

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Gérer les fermetures de blocs (sélecteurs ou media queries)
            if ($line === '}') {
                if ($currentMediaQuery) {
                    $currentMediaQuery = ''; // Fin de media query
                } else {
                    array_pop($indentStack); // Fin de sélecteur imbriqué
                }
                continue;
            }

            // Gérer l'ouverture d'un bloc @media
            if ($this->isMediaQuery($line)) {
                $currentMediaQuery = $this->extractMediaQuery($line);
                if (!isset($mediaQueries[$currentMediaQuery])) {
                    $mediaQueries[$currentMediaQuery] = [];
                }
                continue;
            }

            // Gérer l'ouverture d'un sélecteur
            if ($this->isSelector($line)) {
                $selector = $this->extractSelector($line);
                array_push($indentStack, $selector);

                if ($currentMediaQuery) {
                    $mediaQueries[$currentMediaQuery][$this->processSelectors($indentStack)] = [];
                }
                continue;
            }

            // Ajouter les règles dans le bon contexte
            $rule = $this->processRule($line);
            if ($currentMediaQuery) {
                $mediaQueries[$currentMediaQuery][$this->processSelectors($indentStack)][] = $rule;
            } else if (!empty($indentStack)) {
                $css .= $this->generateCSS($this->processSelectors($indentStack), [$rule]);
            }
        }

        // Générer les blocs de media queries
        $css .= $this->generateMediaQueries($mediaQueries);

        return $css;
    }

    private function isMediaQuery($line)
    {
        return preg_match('/^@media\s+(.*)\{$/', $line);
    }

    private function extractMediaQuery($line)
    {
        preg_match('/^@media\s+(.*)\{$/', $line, $matches);
        return trim($matches[1]);
    }

    private function isSelector($line)
    {
        return preg_match('/^(.+)\s*\{$/', $line);
    }

    private function extractSelector($line)
    {
        preg_match('/^(.+)\s*\{$/', $line, $matches);
        return trim($matches[1]);
    }

    private function processRule($line)
    {
        // Remplacer les inclusions de mixins et les calculs
        $line = $this->processIncludes($line);
        $line = $this->evaluateExpressions($line);
        return $line;
    }

    private function generateCSS($selector, $rules)
    {
        return $selector . ' { ' . implode(' ', $rules) . ' } ';
    }

    private function generateMediaQueries($mediaQueries)
    {
        $css = '';
        foreach ($mediaQueries as $mediaQuery => $selectors) {
            $css .= '@media ' . $mediaQuery . ' { ';
            foreach ($selectors as $selector => $rules) {
                $css .= $this->generateCSS($selector, $rules);
            }
            $css .= ' } ';
        }
        return $css;
    }

    private function processMaps($scss)
    {
        // On cherche les maps SCSS du type $map: (key1: value1, key2: value2, ...);
        preg_match_all('/\$(\w+):\s*\((.*?)\)\s*;/', $scss, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $mapName = $match[1]; // Nom de la map
            $mapValues = $match[2]; // Contenu de la map

            // On extrait les paires clé/valeur dans la map
            $mapArray = [];
            preg_match_all('/(\w+):\s*([^,]+)\s*,?/', $mapValues, $pairs, PREG_SET_ORDER);
            foreach ($pairs as $pair) {
                $key = trim($pair[1]);
                $value = trim($pair[2]);
                $mapArray[$key] = $value; // Stockage des paires clé/valeur
            }

            // Stocker la map entière dans $scssMaps
            $this->scssMaps[$mapName] = $mapArray;
        }

        return $scss;
    }

    private function getMapValue($map, $key)
    {
        if (preg_match('/\$(\w+)-\[(\w+)\]/', $map, $matches)) {
            $mapName = $matches[1]; // Nom de la map
            $mapKey = $matches[2];  // Clé de la map

            // Vérifier si la map existe et contient la clé recherchée
            if (isset($this->scssMaps[$mapName]) && isset($this->scssMaps[$mapName][$mapKey])) {
                return $this->scssMaps[$mapName][$mapKey]; // Retourner la valeur trouvée
            }
        }

        // Si la variable ou la map n'existe pas, retourner une valeur vide ou par défaut
        return '';
    }


    // Modifie la fonction qui traite les media queries et variables
    private function processVariables($scss)
    {
        $lines = explode("\n", $scss);
        foreach ($lines as $line) {
            if (preg_match('/^\s*\$(\w+)\s*:\s*(.+);$/', trim($line), $matches)) {
                dump($matches);
                $variableName = $matches[1];
                $variableValue = $matches[2];
                $this->variables[$variableName] = $this->evaluateExpressions($variableValue);
                dump($matches);
            }
        }
dd($this->variables);
        foreach ($this->variables as $name => $value) {
            dump($name);
            $scss = preg_replace('/(' . $name . ')/', $value, $scss);
        }
        die();
        return $scss;
    }

    private function processMixins($scss)
    {
        // Détection des mixins avec paramètres et stockage
        preg_match_all('/@mixin\s+(\w+)\s*\(([^)]*)\)\s*\{([^}]+)\}/', $scss, $matches, PREG_SET_ORDER);
        foreach ($matches as $mixin) {
            $mixinName = $mixin[1];
            $params = array_map('trim', explode(',', $mixin[2])); // Extraction des paramètres
            $content = trim($mixin[3]);

            // Stockage du mixin avec ses paramètres et son contenu
            $this->mixins[$mixinName] = [
                'params' => $params,
                'content' => $content
            ];

            // Supprimer le mixin du SCSS original après l'avoir stocké
            $scss = str_replace($mixin[0], '', $scss);
        }

        return $scss;
    }

    private function processIncludes($line)
    {
        // Détection et substitution des inclusions de mixins
        if (preg_match('/@include\s+(\w+)\s*\(([^)]*)\);/', $line, $matches)) {
            $mixinName = $matches[1];
            $args = array_map('trim', explode(',', $matches[2])); // Extraction des arguments

            if (isset($this->mixins[$mixinName])) {
                $mixin = $this->mixins[$mixinName];
                $mixinContent = $mixin['content'];

                // Remplacement des paramètres par leurs arguments
                foreach ($mixin['params'] as $index => $param) {
                    $paramName = trim($param, '$ ');
                    $argValue = $args[$index] ?? '';
                    $mixinContent = str_replace('$' . $paramName, $argValue, $mixinContent);
                }

                // Remplacer l'include par le contenu du mixin avec les arguments
                $line = str_replace($matches[0], $mixinContent, $line);
            }
        }

        return $line;
    }

    private function processImports($scss)
    {
        // Détection et traitement des instructions @import
        preg_match_all('/@import\s+["\']([^"\']+)["\'];/', $scss, $matches, PREG_SET_ORDER);
        foreach ($matches as $import) {
            $importFile = trim($import[1]);

            // Construire le chemin du fichier SCSS à importer
            $importPath = dirname($this->scssFile) . '/' . $importFile . '.scss';
            if (file_exists($importPath)) {
                // Lire et intégrer le contenu du fichier importé
                $importContent = file_get_contents($importPath);
                if ($importContent !== false) {
                    $scss = str_replace($import[0], $importContent, $scss);
                } else {
                    throw new Exception("Impossible de lire le fichier importé : $importPath.");
                }
            } else {
                throw new Exception("Le fichier importé n'existe pas : $importPath.");
            }
        }

        return $scss;
    }

    private function evaluateExpressions($value)
    {
        // Remplacement des fonctions simples comme darken() et lighten()
        $value = preg_replace_callback('/(darken|lighten)\(([^,]+),\s*([^)]+)\)/', function ($matches) {
            $function = $matches[1];
            $color = trim($matches[2]);
            $amount = floatval(trim($matches[3]));

            // Pour simplification, ajustement direct de la luminosité pour darken/lighten
            $adjustment = ($function === 'darken') ? -$amount : $amount;
            return $this->adjustColorBrightness($color, $adjustment);
        }, $value);

        // Évaluer les opérations mathématiques simples
        $value = preg_replace_callback('/\b(\d+)(\s*[\+\-\*\/]\s*)(\d+)\b/', function ($matches) {
            $expression = $matches[0];
            // Utiliser eval pour calculer les expressions mathématiques
            return eval('return ' . $expression . ';');
        }, $value);

        return $value;
    }

    private function adjustColorBrightness($color, $amount)
    {
        // Fonction simple d'ajustement de la luminosité des couleurs (simulant darken/lighten)
        if (preg_match('/^#([a-fA-F0-9]{6})$/', $color, $matches)) {
            $hex = $matches[1];
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));

            // Calcul de la nouvelle valeur des couleurs en ajustant la luminosité
            $r = max(0, min(255, $r + ($amount * 2.55)));
            $g = max(0, min(255, $g + ($amount * 2.55)));
            $b = max(0, min(255, $b + ($amount * 2.55)));

            // Retourner la couleur ajustée sous forme hexadécimale
            return sprintf('#%02x%02x%02x', $r, $g, $b);
        }

        // Retourner la couleur inchangée si le format n'est pas pris en charge
        return $color;
    }

    private function processSelectors($indentStack)
    {
        $currentSelector = implode(' ', $indentStack);
        // Remplacer les références parentales (&) par le sélecteur complet
        if (strpos($currentSelector, '&') !== false) {
            $lastSelector = array_pop($indentStack);
            $currentSelector = implode(' ', $indentStack) . str_replace('&', '', $lastSelector);
        }

        // Assurer que le sélecteur est bien formaté
        return trim($currentSelector);
    }

}

if($_ENV['MODE'] == 'dev') {
    // Exemple d'utilisation
    try {
        $compiler = new SCSSCompiler(BASE_PATH.'assets/css/base.scss', BASE_PATH.'build/css/styles.css');
        $compiler->compile();
        echo "# Compilation terminée avec succès.";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}

?>