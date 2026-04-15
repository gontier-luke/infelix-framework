<?php

namespace Classes;

use Exceptions\RouteException;

class Parser {
    public const ROUTE_COMMENT = 'Route';
    public const INLINE_COMMENT = '#';
    public const ROUTE_METHOD = 'public function';

    /**
     * Parse all routes from controllers
     * 
     * @return array<string, array<string, string>>
     */
    public static function parseRoutes(): array
    {
        $routes = [];
        $controllersPath = BASE_PATH.'/controllers/';
        $controllerFiles = scandir($controllersPath);
        foreach ($controllerFiles as $file) {
            if ($file !== '.' && $file !== '..' && !str_contains($file, 'index.php')) {
                $filePath = $controllersPath . $file;
                $fileContent = self::splitFileContent($filePath);
                
                if(str_contains(implode('', $fileContent), self::ROUTE_COMMENT)){
                    $inlineComment = false;
                    $routeFound = false;
                    $routeChar = 0;
                    $getParams = false;
                    $getMethod = false;
                    $currentLine = 1;
                    $methodeChar = 0;
                    $method = '';
                    $char = array_shift($fileContent);
                    $routes[$file] = [];
                    $param = '';
                    $unused = false;
                    $unusedChar = 0;
                    $filePathsCpt = 0;
                    while (!empty($fileContent)) {
                        if ($char === "\n") {
                            $currentLine++;
                            $unused = false;
                        }
                        if ($char === '/') {
                            ++$unusedChar;
                            if ($unusedChar >= 2) {
                                $unused = true;
                            }
                        }
                        if (!$unused) {
                            switch (true){
                                case $char === self::INLINE_COMMENT:
                                    if (!$inlineComment) {
                                        $inlineComment = true;
                                        break;
                                    }
                                case $inlineComment:
                                    switch ($char) {
                                        case $getMethod:
                                            if ($methodeChar >= strlen(self::ROUTE_METHOD)) {
                                                switch ($char) {
                                                    case '(':
                                                        $getMethod = false;
                                                        $methodeChar = 0;
                                                        break;
                                                    case ' ':
                                                        break;
                                                    default:
                                                        $method .= $char;
                                                        break;
                                                }
                                                if (!$getMethod) {
                                                    $routes[$file][count($routes[$file])-1]['method'] = $method;
                                                    $inlineComment = false;
                                                    $method = '';
                                                }
                                                break;
                                            }
                                            switch ($char) {
                                                case self::ROUTE_METHOD[$methodeChar]:
                                                    if(!($fileContent[1] === ' ' && $char == ' ')){
                                                        $methodeChar++;
                                                    }
                                                    break;
                                                    
                                                case "]":
                                                case "\n":
                                                case ' ':
                                                    break;
                                                default:
                                                    break;
                                            }
                                            
                                            break;
                                        case $getParams:
                                            switch ($char) {
                                                case '\'':
                                                    $char = array_shift($fileContent);
                                                    while ($char !== '\'') {
                                                        $param .= $char;
                                                        $char = array_shift($fileContent);
                                                        if (in_array($char, ['"', "\n", "\r", "\t", "\0", "\x0B", ')', ' ', ','])) {
                                                            throw new RouteException('\' is missing here : '. $filePath.':'.$currentLine);
                                                        }
                                                    }
                                                    if (!empty($routes[$file][$filePathsCpt]) && !str_contains($param, '{')) {
                                                        ++$filePathsCpt;
                                                        $routes[$file][count($routes[$file])-1]['name'] = $param;
                                                        $param = '';
                                                        break;
                                                    }
                                                    $routes[$file][$filePathsCpt] = ['path' => $param];
                                                    $param = '';
                                                    break;
                                                case ' ':
                                                    break;
                                                case ',':
                                                    if(empty($routes[$file][count($routes[$file])-1]['path'])){
                                                        throw new RouteException('Route path is missing there : '. $filePath.':'.$currentLine);
                                                    }
                                                    
                                                    break;
                                                default:
                                                    $getParams = false;
                                                    $getMethod = true;
                                                    break;
                                            }
                                            break;
                                        case $routeFound:
                                            $routeChar++;
                                            if ($routeChar >= strlen(self::ROUTE_COMMENT)){
                                                switch ($char) {
                                                    case '(':
                                                        $getParams = true;
                                                        $routeFound = false;
                                                        $routeChar = 0;
                                                        break;
                                                    case ' ':
                                                        break;
                                                    default:
                                                        throw new RouteException('"(" is missing here : '. $filePath.':'.$currentLine);
                                                        break;
                                                }
                                                break;
                                            }
                                            if ($char !== self::ROUTE_COMMENT[$routeChar]) {
                                                $routeFound = false;
                                                $routeChar = 0;
                                                $inlineComment = false;
                                                break;
                                            }
                                            break;
                                        case self::ROUTE_COMMENT[0]:
                                            $routeFound = true;
                                            break;
                                        case "\n":
                                            $inlineComment = false;
                                            break;
                                        default:
                                            break;
                                    }
                                    break;
                                default:
                                    break;
                            }
                        }
                        
                        $char = array_shift($fileContent);
                    }
                }
            }
        }    
        return $routes;
    }

    /**
     * split character file content
     * 
     * @return array<string>
     */
    public static function splitFileContent(string $filePath): array
    {
        $fileContent = file_get_contents($filePath);
        return str_split($fileContent);
    }
    
}