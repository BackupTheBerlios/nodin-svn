<?php
error_reporting(E_ALL|E_STRICT);
function get_microtime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

global $fileContext;
$fileContext = array();
$start = get_microtime();
iterateDir('.'/*dirname(__FILE__)*/);

function iterateDir($path) {
    $di = new DirectoryIterator($path);
    foreach ($di as $k => $v) {
        // get extension
        $ext = end(explode('.', $v->getPathname()));
        // is svn dir
        $name = $v->getFilename();
        $first = substr($name, 0, 1);
        $first == '.' ? $dupa = false : $dupa = true;

        // if is dir and not svn dir, iterate this dir ;]
        if($v->isDir() && $dupa) {
            iterateDir($v->getPath() . '/' . $v->getFilename());
        }
        // print filename with path, with php extenstion
        if($dupa && $v->isFile() && $ext == 'php' && $v->getFilename() != 'tokenizer.php' && $v->getFilename() != 'test.php' && $v->getFilename() != 'config.php' && $v->getFilename() != 'dupa.php') {
            $GLOBALS['fileContext'][] = array($v->getPathname(), token_get_all(file_get_contents($v->getPathname())));
            print $v->getPathname() . "<br />\n";
        }
    }

}

//dump($fileContext);
$classes = array();
$key = 0;
foreach($fileContext as $file) {
    static $i;
    foreach ($file as $c) {
        if(is_array($c)) {
            $a = false;
            foreach ($c as $k => $v) {
                if(is_array($v)) {
                    if($v[0] == T_CLASS || $v[0] == T_INTERFACE) {
                        $a = true;
                    } elseif ($a && $v[0] == T_STRING) {
                        $classes[] = array($v[1], substr($file[0], 2));
                        $a = false;
                    }
                }
                else
                {
                    //print("$v<br/>\n");
                }
            }
        }

    }

}

dump($classes);

$phpCode  = '<?php function __autoload($name) {' . "\n";
$phpCode .= 'static $map = array(' . "\n";
foreach ($classes as $k => $v) {
    $phpCode .= '\'' . $v[0] . '\' => \'' . $v[1] . '\','. "\n";
}
$phpCode .= ');' . "\n";
$phpCode .= 'require_once($map[$name]);' . "\n}\n";
$phpCode .= '?>';
file_put_contents('odin.framework/autoload.php', $phpCode);
//dump($fileContext);
function dump($dump) {
    print('<pre>');
    var_dump($dump);
    print('</pre>');
}
print ($end = get_microtime() - $start . '<br />');
?>
