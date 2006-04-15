<?php
class OdinErrorHandler {
    private $current = 0;
    private $errors = array (
            1    => "Php Error",
            2    => "Php Warning",
            4    => "Parsing Error",
            8    => "Php Notice",
            16   => "Core Error",
            32   => "Core Warning",
            64   => "Compile Error",
            128  => "Compile Warning",
            256  => "Php User Error",
            512  => "Php User Warning",
            1024 => "Php User Notice",
            2048 => ""
    );
    
    public function handle($number, $string, $file, $line, $context) {
        if (! ($number & error_reporting())) {
            return;
        }
        print '<h3><a href="javascript:toggleV(\'error-'. $this->current . '\')">' . $this->errors[$number] . ' ' . $string .'</a></h3>' . "\n";
        print '<div id="error-' . $this->current . '" style="display: block;">' . "\n";
        print'<ul>
                    <li><strong>Message: </strong>' . $string . '</li>
                    <li><strong>File: </strong>' . basename($file) . '/' . $line;
        print '</ul>';
        $trace = debug_backtrace();
        array_shift($trace);
        array_shift($trace);
        foreach ($trace as $s => $step) {
            // [class::]method
            print '<li><span class="Symbol">';
            if (isset($step['class']))
                printf('%s::', $step['class']);
            printf('%s', $step['function']);
            print '</span>';

            // print parameters
            print '<span class="Parameters">(';
            for ($i = 0; $i < @count(@$step['args']); $i++) {
                printf('<a href="javascript:toggleErrorVisibility(\'bc_error_backtrace_%d_step_%d_parameter_%d\')">%s</a>',
                    $this->current, $s, $i, gettype($step['args'][$i]));

                if ($i < count($step['args']) - 1)
                    echo ', ';
            }
            print ')</span>'; echo "\n";

            printf('<a href="javascript:toggleErrorVisibility(\'bc_error_backtrace_%d_step_%d_source\');">[toggle source]</a>', $this->current, $s);

            print '<ul class="Location">'; echo "\n";
            printf('<li><strong>file:</strong> %s</li>', $step['file']); echo "\n";
            printf('<li><strong>line:</strong> %d</li>', $step['line']); echo "\n";
            print '</ul>'; echo "\n";

            // print parameter details
            for ($i = 0; $i < @count(@$step['args']); $i++) {
                printf('<pre class="Parameter" id="bc_error_backtrace_%d_step_%d_parameter_%d" style="display:none;">[param %d] %s</pre>',
                    $this->current, $s, $i, $i, htmlentities(value_dump($step['args'][$i], true, 4)));
            }

            // print source context
            printf('<pre id="bc_error_backtrace_%d_step_%d_source"  class="SourceContext" style="display:none;">', $this->current, $s); echo "\n";
            print $this->getSourceContext($step['file'], $step['line']);
            print '</pre>';

            print '</li>'; echo "\n";
        }
        print '</div>' . "\n";


        $this->current++;
    }
    
    function getSourceContext($file, $line, $before = 10, $after = 10) {
        $data = file($file);
        $count = count($data) - 1;

        //count which lines to display
        $start = $line - $before;
        if ($start < 1) {
            $start = 1;
        }
        $end = $line + $after;
        if ($end > $count) {
            $end = $count + 1;
        }

        $result = '';
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $line)
                $result .= '<span class="ErrorLineNumber">';
            else
                $result .= '<span class="LineNumber">';
            $result .= str_repeat('0', strlen($end) - strlen($i) + 1) . $i;
            $result .= '</span> ';

            $result .= htmlentities($data[$i - 1]);
        }

        return $result;
    }
}

function value_dump($var, $ret = true, $max_scope = 10, $indent = "\n") {
    $indent .= "    ";
    $result = "";

    /* Get value's text representation.
     */
    if (is_null($var)) {
        $result .= "(null)";
    } else if (is_object($var)) {
        $result .= "(object) {" . get_class($var) . "} (";

        if ($max_scope != 0) {
            $properties = array_keys(get_object_vars($var));
            foreach ($properties as $name) {
                // key
                $result .= $indent . "\$" . $name. ' = ';
                // value
                $result .= value_dump($var->$name, true, $max_scope - 1, $indent) . ",";
            }
        } else {
            $result .= $indent . '/* ... */';
        }

        $result .= $indent . ")";
    }
    elseif (is_array($var)) {
        $result .= "array(";

        if ($max_scope != 0) {
            foreach ($var as $key => $value) {
                if (is_string($key))
                    $key = "'" . $key . "'";
                // key
                $result .= $indent . $key . " => ";
                // value
                $result .= value_dump($value, true, $max_scope - 1, $indent) . ",";
            }
        } else {
            $result .= $indent . '/* ... */';
        }

        $result .= $indent . ")";
    }
    elseif (is_string($var)) {
        $result .= '(string) "'  . htmlspecialchars($var) . '"';
    }
    elseif (is_bool($var)) {
        $result .= '(boolean) ' . (($var) ? 'true' : 'false');
    }
    else {
        $result .= '(' . gettype($var) . ') ' . $var;
    }

    /* Return or print value.
     */
    if ($ret) {
        return $result;
    } else {
        echo $result;
    }
}

?>
