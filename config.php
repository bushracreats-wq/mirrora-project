<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "mirrora"; // Yahan apne database ka naam likhein

$conn = mysqli_connect($host, $username, $password, $database);

// Connection check karne ke liye
if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

/**
 * Helper function to retrieve environment variables from system env, $_ENV, $_SERVER, or .env file
 */
if (!function_exists('get_env_var')) {
    function get_env_var($key, $default = null) {
        // 1. System environment
        $val = getenv($key);
        if ($val !== false && $val !== '') return $val;
        
        // 2. $_ENV array
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') return $_ENV[$key];
        
        // 3. $_SERVER array
        if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') return $_SERVER[$key];

        // 4. Defined constant
        if (defined($key)) return constant($key);
        
        // 5. Parse .env file if it exists
        static $env_parsed = null;
        if ($env_parsed === null) {
            $env_parsed = [];
            $env_file = __DIR__ . '/.env';
            if (file_exists($env_file)) {
                $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line) || $line[0] === '#') continue;
                    if (strpos($line, '=') !== false) {
                        list($name, $value) = explode('=', $line, 2);
                        $name = trim($name);
                        $value = trim($value, " \t\n\r\0\x0B\"'");
                        $env_parsed[$name] = $value;
                    }
                }
            }
        }

        if (isset($env_parsed[$key]) && $env_parsed[$key] !== '') {
            return $env_parsed[$key];
        }

        return $default;
    }
}
?>