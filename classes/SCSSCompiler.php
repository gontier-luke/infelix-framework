<?php

namespace Classes;

class SCSSCompiler
{
    /** @var string $inputFile Chemin vers le fichier SCSS d'entrée */
    private string $inputFile;
    /** @var string $outputFile Chemin vers le fichier CSS de sortie */
    private string $outputFile;
    /** @var string $currentDirectoryStack Pile des répertoires courants pour la gestion des imports */
    private string $currentDirectoryStack = '';

    /** @var array<string, mixed|object> $variables Variables SCSS */
    private array $variables = [];

    /** @var string $parentSelector Sélecteur parent pour l'imbrication */
    private string $parentSelector = '';

    /**
     * Constructeur
     * 
     * @param string $inputFile Chemin vers le fichier SCSS d'entrée
     * @param string $outputFile Chemin vers le fichier CSS de sortie
     * @throws \Exception Si le fichier d'entrée n'existe pas
     */
    public function __construct(string $inputFile, string $outputFile)
    {
        if (!file_exists($inputFile)) {
            throw new \Exception("Input SCSS file does not exist: $inputFile");
        }
        $this->inputFile = $inputFile;
        $this->outputFile = $outputFile;
        $this->currentDirectoryStack = dirname(realpath($inputFile));
    }

    /**
     * Compile le SCSS du fichier d'entrée vers le fichier de sortie.
     * Prend en charge :
     * - @media blocks
     * - @for $i de A à B { ... }
     * - css selectors imbriqués
     * - ${var} variables simples
     * - @import
     * - création de fonctions simples
     * - mixins
     * - @if ... @else
     * - commentaires /* ... * /
     * - commentaires // ...
     * - opérations arithmétiques de base dans les valeurs
     * - gestion des unités (%, px, em, rem, vw, vh, ch)
     * - gestion des nombres flottants
     * - variables globales et locales
     * - variables par défaut (!default)
     * - variable complexes (listes, maps)
     * - & sélecteurs parent
     * 
     * @function compile(): string
     * @return string Le CSS compilé
     */
    public function compile(): string
    {
        $scss = file_get_contents($this->inputFile);

        // Normalize newlines
        $scss = str_replace("\r\n", "\n", $scss);
        $scss = $this->stripOuterWhitespace($scss);

        // First, expand all at-rule blocks (@media, @supports, etc.) recursively.
        // We do this with a brace-matching scanner to avoid brittle regex pitfalls.
        $scss = $this->compileBlock($scss);
        file_put_contents($this->outputFile, $scss);
        return $scss;
    }

    /** Compile un bloc de SCSS, redirigeant vers les fonctions appropriées :
     * - @import
     * - @for ... de ... à/jusqu'à ...
     * - mixins
     * - @if ... @else
     * - gestion des variables simples #{$var} et $var
     * - gestion des unités (%, px, em, rem, vw, vh, ch)
     * - gestion des nombres flottants
     * - variables globales et locales
     * - variable complexes (listes, maps)
     * - imbrication de sélecteurs
     * - règles css simple
     * - commentaires /* ... * /
     * - commentaires // ...
     * - & sélecteurs parent : l'objectif est de remplacer '&' par le sélecteur parent courant et de le sortir de l'imbrication une fois le traitement du parent terminé
     * 
     * @param string $src Le code SCSS à compiler
     * @return string Le code CSS compilé
     */
    private function compileBlock(string $src): string
    {
        $out = '';
        $pos = 0;
        $len = strlen($src);
        while ($pos < $len) {
            dump('Position actuelle : '. $pos . ' / ' . substr($src, $pos, 30));

            switch(true) {
                case (in_array($src[$pos], ["\t", "\n", ";", " ", "\r", ''])):
                    // Espaces / caractères invisibles
                    ++$pos;
                    break;
                case ($src[$pos] === '$'):
                    // Définition de Variable simple ou complexe (list, map)
                    if (preg_match('/^\$([a-zA-Z_-]*)\s*:\s*([^;]+);/', substr($src, $pos), $m)) {
                        $varName = $m[1];
                        $varValue = trim($m[2]);
                        // Supporte !default
                        $this->variables[$varName] = $varValue;
                        $pos += strlen($m[0]);
                        break;
                    } 
                    // Variable mal formée, on avance d'un caractère
                    $out .= $src[$pos];
                    ++$pos;
                    break;
                case (substr($src, $pos, 2) === '/*'):
                    // Commentaire /* ... */
                    $endComment = strpos($src, '*/', $pos + 2);
                    if ($endComment === false) {
                        // Pas de fin de commentaire, on prend le reste
                        $out .= substr($src, $pos);
                        $pos = $len;
                        break;
                    }
                    // On prend jusqu'à la fin du commentaire
                    $pos = $endComment + 2;
                    break;
                case (substr($src, $pos, 2) === '//'):
                    // Commentaire // ...
                    $endComment = strpos($src, "\n", $pos + 2);
                    if ($endComment === false) {
                        // Pas de fin de commentaire, on prend le reste
                        $out .= substr($src, $pos);
                        $pos = $len;
                        break;
                    } 
                    // On prend jusqu'à la fin du commentaire
                    $pos = $endComment;
                    break;
                case ($src[$pos] === '@'):
                    // Règle @...
                    $control = $this->readUntilBraceOrSemicolon($src, pos: $pos);
                    if ($control === null) {
                        // Erreur de lecture, on sort
                        $out .= substr($src, $pos);
                        $pos = $len;
                        break;
                    }
                    if ($control['type'] === 'semicolon') {
                        // Règles simples @charset, @import, etc.
                        $rule = trim($control['text']);
                        // Règle @import : ne prend pas en charge les @import url(...)
                        if (preg_match('/^@import\s+(url\()?["\']([^"\']*)["\']\s*\)?\s*;$/', $rule, $m)) {
                            // Import avec url() - on l'ignore pour l'instant
                            if(str_contains($rule, 'url(')) {
                                dump('Import avec url() non supporté : ' . $rule);
                                $out .= $rule . "\n";
                                $pos += strlen(string: $control['text']);
                                break;
                            }
                            dump('Compile @import ' . $m[2]);
                            $importedContent = $this->handleImport($m[2]);
                            $out .= $importedContent . "\n";
                            $pos += strlen(string: $control['text']);
                            break;
                        }
                        if(preg_match('/^@mixin\s+([a-zA-Z_]\w*)\s*(\(([^)]*)\))?\s*{/', $rule, $m)) {
                            // Définition de mixin
                            // Non implémenté pour l'instant
                            $pos += $this->readBraceBlock($src,$this->readUntilBrace($src, $control['pos'])['pos'])['pos'];
                            break;
                        }
                        $pos += strlen($control['text']);
                        dump($rule);
                        break;
                    }
                    if ($control['type'] === 'brace') {
                        // Règles de bloc, e.g. @media, @for, @supports
                        $header = trim($control['text']);
                        $bracePos = $control['pos'];
                        $block = $this->readBraceBlock($src, $bracePos);
                        $inner = $block['inner'];
                        $pos = $block['pos'];

                        if (preg_match('/^@for\s+\$[a-zA-Z_]\w*\s+from\s+-?\d+(?:\.\d+)?\s+(to|through)\s+-?\d+(?:\.\d+)?/i', subject: $header)) {
                            // Règle @for
                            $expanded = $this->expandForLoop($header, $inner);
                            $out .= $expanded . "\n";
                            break;
                        }
                        
                        if(preg_match('/^@media/', subject: $header)) {
                            // Règle @media sans bloc, e.g. @media print;
                            $out .= $header . "\n";
                            dd('$out', $out);
                            $pos += strlen($control['text']);
                            break;
                        }
                        break;
                    }

                default:
                    // Dans le cas d'une simple règle CSS ou d'un sélecteur imbriqué
                    $nextControlPos = $this->findNextControl($src, $pos);
                    if ($nextControlPos === null) {
                        // Pas d'autre instruction, on prend le reste
                        $text = substr($src, $pos);
                        $processed = $this->processImbricationAndSelectors($text);
                        $out .= $processed;
                        $pos = $len;
                        break;
                    }
                    // On prend jusqu'à la prochaine instruction @
                    $text = substr($src, $pos, $nextControlPos - $pos);
                    $processed = $this->processImbricationAndSelectors($text);
                    dd('Texte à traiter : ' . $processed);
                    $out .= $processed;
                    $pos = $nextControlPos;
                    break;
            }
        }
        return str_replace("\n", '', $out);
    }

    /**
     * Gère une instruction @import en lisant le fichier importé et en compilant son contenu.
     * Retourne le contenu compilé à insérer à la place de l'instruction @import.
     * 
     * @param string $src Le chemin du fichier à importer
     * @return string Le contenu compilé du fichier importé
     */
    private function handleImport(string $src): string
    {
        // Récupérer le répertoire courant et renseigner le chemin complet du fichier importé
        $importPath = $this->currentDirectoryStack . '/' . trim($src);
        if(substr($importPath, -5) !== '.scss') {
            $importPath .= '.scss'; // Ajouter l'extension si absente
        }
        dump('Importing file: ' . $importPath);
        $previousDir = $this->currentDirectoryStack;
        $nextDir = dirname(realpath(path: $importPath));
        if($nextDir !== $previousDir) {
            // On change de répertoire, on empile l'ancien
            $this->currentDirectoryStack = $nextDir;
        }
        if (!file_exists($importPath)) {
            throw new \Exception("Imported SCSS file does not exist: $importPath");
        }
        $this->currentDirectoryStack = dirname(realpath($importPath));
        $content = $this->compileBlock(file_get_contents($importPath));
        if($nextDir !== $previousDir) {
            $this->currentDirectoryStack = $previousDir; // On revient au répertoire précédent
        }

        return $content;
    }

    /**
     * Trouve la position de la prochaine instruction de contrôle (commençant par '@') dans le texte à partir de la position donnée.
     * Retourne null si aucune instruction n'est trouvée.
     * 
     * @param string $src Le texte source SCSS
     * @param int $pos La position de départ pour la recherche
     * @return int|null La position de la prochaine instruction ou null si non trouvée
     */
    private function findNextControl(string $src, int $pos): ?int
    {
        $at = strpos($src, '@', $pos);
        if ($at === false) return null;
        return $at;
    }

    /**
     * Lit le texte à partir de la position donnée jusqu'à rencontrer une accolade ouvrante '{' ou un point-virgule ';'.
     * Retourne un tableau associatif contenant :
     * - 'type' : 'brace' si une accolade a été trouvée, 'semicolon' si un point-virgule a été trouvé, 'eof' si la fin du texte est atteinte.
     * - 'text' : le texte lu depuis la position initiale jusqu'au caractère trouvé (exclu).
     * - 'pos' : la position du caractère suivant celui trouvé (ou la fin du texte).
     * 
     * @param string $src Le texte source SCSS
     * @param int $pos La position de départ pour la lecture
     * @return array<string, string|int> Associatif avec les clés 'type', 'text', et 'pos'
     */
    private function readUntilBraceOrSemicolon(string $src, int $pos): array
    {
        // pos points to '@'
        $i = $pos;
        $len = strlen($src);
        while ($i < $len) {
            $ch = $src[$i];
            if ($ch === ';') {
                // simple at-rule @charset etc.
                return ['type' => 'semicolon', 'text' => substr($src, $pos, $i - $pos + 1), 'pos' => $i + 1];
            }
            if ($ch === '{') {
                // block at-rule
                return ['type' => 'brace', 'text' => trim(substr($src, $pos, $i - $pos)), 'pos' => $i];
            }
            $i++;
        }
        // fallback: until end
        return ['type' => 'eof', 'text' => substr($src, $pos), 'pos' => $len];
    }

    /**
     * Lit le texte à partir de la position donnée jusqu'à rencontrer une accolade ouvrante '{'.
     * Retourne un tableau associatif contenant :
     * - 'text' : le texte lu depuis la position initiale jusqu'à l'accolade (exclue).
     * - 'pos' : la position de l'accolade trouvée (ou la fin du texte).
     * 
     * @param string $src Le texte source SCSS
     * @param int $pos La position de départ pour la lecture
     * @return array<string, string|int> Associatif avec les clés 'text' et 'pos'
     */
    private function readUntilBrace(string $src, int $pos): array
    {
        $i = $pos;
        $len = strlen($src);
        while ($i < $len && $src[$i] !== '{') $i++;
        return ['text' => substr($src, $pos, $i - $pos), 'pos' => $i];
    }

    private function readBraceBlock(string $src, int $bracePos): array
    {
        // expects $src[$bracePos] === '{'
        $depth = 0;
        $i = $bracePos;
        $len = strlen($src);
        for (; $i < $len; $i++) {
            $ch = $src[$i];
            if ($ch === '{') {
                $depth++;
            } elseif ($ch === '}') {
                $depth--;
                if ($depth === 0) {
                    // block is from bracePos+1 to i-1
                    $inner = substr($src, $bracePos + 1, $i - $bracePos - 1);
                    return ['inner' => $inner, 'pos' => $i + 1];
                }
            }
        }
        // Unbalanced braces; return rest
        $inner = substr($src, $bracePos + 1);
        return ['inner' => $inner, 'pos' => $len];
    }

    /** 
     * Développe une boucle @for $i from A to/through B { ... }
     * en répétant le corps de la boucle avec $i remplacé par les valeurs successives.
     * Supporte les modes "to" (exclusif) et "through" (inclusif).
     * Gère les nombres négatifs et flottants.
     */
    private function expandForLoop(string $header, string $body): string
    {
        // Expect patterns: @for $i from 1 through 12  OR  @for $i from 1 to 12
        if (!preg_match('/^@for\s+\$(?P<var>[a-zA-Z_]\w*)\s+from\s+(?P<start>-?\d+(?:\.\d+)?)\s+(?P<mode>to|through)\s+(?P<end>-?\d+(?:\.\d+)?)/', $header, $m)) {
            // Invalid syntax, return body as is
            return $body;
        }

        $varName = $m['var'];
        $start = (float)$m['start'];
        $mode = $m['mode'];
        $end = (float)$m['end'];
        $inclusive = ($mode === 'through');
        $result = '';

        $direction = '<';
        if ($start > $end) {
            $direction = '>';
        }
        $step = ($direction === '<') ? 1 : -1;
        $current = $start;
        $condition = function($cur, $end) use ($direction, $inclusive) {
            if ($direction === '<') {
                return $inclusive ? ($cur <= $end) : ($cur < $end);
            }
            return $inclusive ? ($cur >= $end) : ($cur > $end);
        };

        while ($condition($current, $end)) {
            $result .= $this->compileBlock($this->replaceVar($body, $varName, $current)) . "\n";
            $current += $step;
        }
        return $result;
    }

    private function replaceVar(string $text, string $name, $value): string
    {
        // Replace #{$name}
        $text = preg_replace_callback('/#\{\s*\$' . preg_quote($name, '/') . '\s*\}/', function() use ($value) {
            // Keep integers clean
            if (is_numeric($value) && (int)$value == $value) return (string)intval($value);
            return (string)$value;
        }, $text);

        // Replace $name in simple arithmetic like: $i * 8.333333%
        // We'll try a basic arithmetic resolver for expressions involving the loop var.
        $text = $this->resolveSimpleExpressions($text, $name, $value);

        // Replace bare $name in values/selectors (safe fallback)
        $text = preg_replace('/\$(' . preg_quote($name, '/') . ')\b/', (string)$value, $text);

        return $text;
    }

    private function resolveSimpleExpressions(string $text, string $var, $value): string
    {
        // Very small evaluator for patterns like: ($i * 10px), ($i*8.3333%), $i * 2rem, 2 * $i
        // It does not aim to be complete – just to cover common grid patterns.
        $pattern = '/(?P<expr>(?:\$\b' . preg_quote($var, '/') . '\b|\d+(?:\.\d+)?)(?:\s*[\*\/\+\-]\s*(?:\$\b' . preg_quote($var, '/') . '\b|\d+(?:\.\d+)?))+\s*(?:%|px|rem|em|vw|vh|ch)?)/';
        return preg_replace_callback($pattern, function($m) use ($var, $value) {
            $expr = $m['expr'];

            // Capture trailing unit if any
            if (preg_match('/(%|px|rem|em|vw|vh|ch)\s*$/', $expr, $um)) {
                $unit = $um[1];
                $exprCore = substr($expr, 0, -strlen($um[0]));
            } else {
                $unit = '';
                $exprCore = $expr;
            }

            // Replace $var with numeric value
            $phpExpr = preg_replace('/\$\b' . preg_quote($var, '/') . '\b/', (string)$value, $exprCore);

            // Evaluate safely: allow only numbers, spaces, and operators
            if (!preg_match('/^[0-9\.\s\+\-\*\/]+$/', $phpExpr)) {
                return $expr; // fallback
            }
            // Evaluate
            $result = 0;
            try {
                // Use eval in a very constrained way
                $result = eval('return ' . $phpExpr . ';');
            } catch (\Throwable $e) {
                return $expr;
            }
            // Normalize small floats
            if (is_float($result)) {
                if (abs($result - round($result)) < 1e-9) {
                    $result = (string)round($result);
                } else {
                    $result = rtrim(rtrim(number_format($result, 6, '.', ''), '0'), '.');
                }
            }
            return $result . $unit;
        }, $text);
    }

    /**
     * Interpole les variables simples de la forme ${name} dans le texte donné.
     * Ne gère pas les variables complexes (listes, maps) ni les fonctions.
     */
    private function interpolateSimpleVariables(string $text, array $vars): string
    {
        return preg_replace_callback('/#\{\s*\$([a-zA-Z_]\w*)\s*\}/', function($m) use ($vars) {
            $name = $m[1];
            return isset($vars[$name]) ? $vars[$name] : '';
        }, $text);
    }

    private function stripOuterWhitespace(string $s): string
    {
        // Keep inner indentation intact
        $lines = explode("\n", $s);
        return implode("\n", $lines);
    }

    /**
     * Applique les traitements communs sur un texte normal (sélecteurs, règles CSS, etc.)
     * ainsi que l'interpolation des variables simples et le caractère parent '&'.
     * Retourne le texte traité.
     * 
     * @param string $text Le texte à traiter
     * @return string Le texte traité
     */
    private function processImbricationAndSelectors(string $text): string
    {
        // Gérer l'imbrication des sélecteurs et le caractère parent '&'
        
        // Sauvegarder le sélecteur présent
        $originalParent = trim($this->parentSelector);
        $selectorResearch = $this->readUntilBrace($text, 0);

        if($selectorResearch === null) {
            return $text; // Erreur de lecture, on retourne le texte tel quel
        }

        if(str_contains($selectorResearch['text'], '&')) {
            if($this->parentSelector === ''){
                throw new \Exception("Utilisation de '&' dans un contexte sans parent défini.");
            }
            $selectorResearch['text'] = str_replace('&', $this->parentSelector, $selectorResearch['text']);
        }
        $this->parentSelector = $originalParent . ' ' . trim($selectorResearch['text']);
        $bodyBlock = $this->readBraceBlock($text, $selectorResearch['pos']);
        $inner = $bodyBlock['inner'];

        $compiledInner = $this->compileBlock($inner);

        $this->parentSelector = $originalParent; // Restaurer le parent

        $childProcessed = '';
        $rules = '';
        $isFirst = true;
        foreach (explode("}", $compiledInner) as $internSelection){
            if(trim($internSelection) === ''){
                continue;
            }
            if(strpos($internSelection, ';' ) !== false && $isFirst){
                // Règle CSS simple
                foreach (explode(";", $internSelection) as $rule){
                    if(trim($rule) === '' || trim($rule) === '{'){
                        continue;
                    }
                    $rules .= $this->interpolateSimpleVariables(trim($rule) . ';', $this->variables) . "\n";
                }
            }
            $innerSelectorResearch = $this->readUntilBrace($internSelection, 0);
            if($innerSelectorResearch === null){
                continue;
            }
            $childSelector = trim($innerSelectorResearch['text']);
            if($childSelector === ''){
                continue;
            }
            $childBodyBlock = $this->readBraceBlock($internSelection, $innerSelectorResearch['pos']);
            $childInner = $childBodyBlock['inner'];
            $childProcessed .= $this->parentSelector . ' ' . str_replace('&', $this->parentSelector, $childSelector) . " {\n" . $this->compileBlock($childInner) . "\n}\n";

        }

        if(trim($rules) !== ''){
            $rules .= $rule;
        }

        return $this->parentSelector . " {\n" . $rules . "\n}\n" . $childProcessed;
    }
}


// if($_ENV['MODE'] == 'dev' && false) { // ne plus utiliser le compilateur pour l'instant
//     // Exemple d'utilisation
//     try {
//         $compiler = new SCSSCompiler(BASE_PATH.'assets/css/base.scss', BASE_PATH.'build/css/styles.css');
//         $compiler->compile();
//     } catch (\Exception $e) {
//         dd( "Erreur : " . $e->getMessage());
//     }
// }

?>