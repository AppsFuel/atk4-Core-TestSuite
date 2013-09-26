#!/usr/bin/php
<?php

function format($className, $method) {
return "
    /**
     * <code>
     * // doctest: $className::$method
     * \$testCase = \$this->add('$className');
     * \$testCase->$method();
     * echo 1;
     * // expects:
     * // 1
     * </code>
     */";
}

function generateDocTest($file) {
    $content = file_get_contents($file);
    preg_match('/class ([^\s]+) extends TestCase\s*\{/', $content, $matches);
    $className = $matches[1];
    preg_match_all('/function\s+test([^\(]*)\(\)/', $content, $matches);
    $methods = $matches[1];
    $doctests = array();
    foreach($methods as $method) {
        $doctests[] = format($className, 'test' . $method);
    }
    $content = "<?php\n";
    $content .= "// [[$className]]\n";
    $content .= implode("\n", $doctests);
    $file = __DIR__ . '/htdocs/lib/' . strtolower($className) . '.php';
    file_put_contents($file, $content);
    chmod($file, 0775);
}

function findAllTestCases($dir) {
    $files = array();
    foreach (scandir($dir) as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $f = $dir . '/' . $file;
        if (is_dir($f)) {
            $files = array_merge($files, findAllTestCases($f));
            continue;
        }
        if (file_exists($f)) {
            if (preg_match('/class ([^\s]+) extends TestCase\s*\{/', file_get_contents($f))) {
                $files[] = $f;
            }
        }
    }
    return $files;
}

$file = realpath($argv[1]);
if (is_dir($file)) {
    $files = findAllTestCases($file);
} else {
    $files = array($file);
}

foreach ($files as $file) {
    echo "Generating $file doctests...\n";
    generateDocTest($file);
    echo "Generated\n";
}