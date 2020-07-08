<?php
/**
 * ThreeMan Web Application
 * @author Emre Emir <emre@emreemir.com>
 * @package Functions Helper
 * @copyright  It is protected by the GNU General Public License! All Rights Reserved.
 */

if (!defined('THREEMAN_FUNCTIONS')):

    define('THREEMAN_FUNCTIONS', 1);

    if (!function_exists('getallheaders'))
    {

        /**
         * Get all HTTP header key/values as an associative array for the current request.
         *
         * @return string[string] The HTTP header key/value pairs.
         */
        function getallheaders()
        {
            $headers = array();

            $copy_server = array(
                'CONTENT_TYPE' => 'Content-Type',
                'CONTENT_LENGTH' => 'Content-Length',
                'CONTENT_MD5' => 'Content-Md5',
            );

            foreach ($_SERVER as $key => $value)
            {
                if (substr($key, 0, 5) === 'HTTP_')
                {
                    $key = substr($key, 5);
                    if (!isset($copy_server[$key]) || !isset($_SERVER[$key]))
                    {
                        $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                        $headers[$key] = $value;
                    }
                }
                elseif (isset($copy_server[$key]))
                {
                    $headers[$copy_server[$key]] = $value;
                }
            }

            if (!isset($headers['Authorization']))
            {
                if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']))
                {
                    $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
                }
                elseif (isset($_SERVER['PHP_AUTH_USER']))
                {
                    $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                    $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
                }
                elseif (isset($_SERVER['PHP_AUTH_DIGEST']))
                {
                    $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
                }
            }

            return $headers;
        }

    }

    /**
     * Connection DataBase
     * @return object
     */
    function db()
    {
        return \Core\Database::get();
    }

    /**
     * Client User Interface signature writer
     * @param array|null $arr
     * @return sprintf array
     */
    function copyright_ui(array $arr = NULL)
    {
        $param = '/*!_* %1$s_* @author %2$s <%3$s>_* @package %4$s_* @copyright  %5$s_*/_';
        $param = str_replace('_', PHP_EOL, $param);
        $signature = sprintf($param, $arr['title'], $arr['author'], $arr['mail'], $arr['package'], $arr['licence']);

        return $signature;
    }

    /**
     * is debug controll
     * @return boolean
     */
    function isDebug()
    {
        return (config('app.developer.environment') == 'local') ? true : false;
    }

    /**
     * remove query from request
     * @return variable
     */
    function removeQuery($url = NULL, $char = NULL)
    {
        $url = (empty($url)) ? option('protocol') . option('address') . option('dir') . $_SERVER['QUERY_STRING'] : $url;
        $char = (empty($char)) ? '&' : $char;
        $return = (substr($url, 0, strpos($url, $char))) ? substr($url, 0, strpos($url, $char)) : $url;

        return $return;
    }

    /**
     * is Array value check
     * @param array|null $var
     * @return var array
     */
    if (!function_exists('is_iterable'))
    {
        function is_iterable($var)
        {
            return $var !== null && (is_array($var) || $var instanceof Traversable || $var instanceof Iterator || $var instanceof IteratorAggregate);
        }
    }

    /**
     * is numeric array
     * @param array|null $var
     * @return var array
     */
    function isNumericArray(array $array)
    {
        foreach ($array as $a => $b)
        {
            if (!is_numeric($a))
            {
                return false;
            }
        }
        return true;
    }

    /**
     * is return random pin
     * @param variable
     * @return alphanumeric
     */
    function createPin($len = 10)
    {
        $maxNbrStr = str_repeat('9', $len);
        $maxNbr = intval($maxNbrStr);
        $n = mt_rand(0, $maxNbr);
        $pin = str_pad($n, $len, "0", STR_PAD_LEFT);
        return $pin;
    }

    /**
     * Get Currency
     */
    function Currency($to = 'EUR')
    {
        Currency::run();
        return Currency::rate($to);
    }

    /**
     * Get Currency Convert
     */
    function CurrencyConvert($price = 0.00, $symbol = false, $to = 0)
    {
        Currency::run();
        return Currency::convert($price, $symbol, $to);
    }

    /**
     * Get Currency Rate Convert
     */
    function CurrencyRateConvert($price = 0.00, $from = 1)
    {
        Currency::run();
        return Currency::rateConvert($price, $from);
    }

    /**
     * Get Default Currency
     */
    function DefaultCurrency()
    {
        Currency::run();
        return Currency::getDefaultCurrency();
    }

    /**
     * Get Default Currency Symbol
     */
    function DefaultCurrencySymbol($id = 0)
    {
        Currency::run();
        return Currency::defaultCurrencySymbol($id);
    }

    /**
     * Get Default Currency Name
     */
    function DefaultCurrencyName($id = 0)
    {
        Currency::run();
        return Currency::defaultCurrencyName($id);
    }

    /**
     * Clear cache by key
     */
    function clearCache($name = NULL, $key = NULL, $tpl = NULL)
    {
        $tpl = (empty($tpl)) ? 'page' : tplType();
        $config = config('cache.simple');
        $cache = \Core\Cache::get($config[$tpl]);

        $cache->setCache($name);
        if ($cache->isCached($key))
        {
            return ($cache->erase($key)) ? true : false;
        }
    }

    /**
     * Clear cache by group
     */
    function clearGroupCache($name = NULL, $tpl = NULL)
    {
        $tpl = (empty($tpl)) ? 'page' : tplType();
        $config = config('cache.simple');
        $cache = \Core\Cache::get($config[$tpl]);

        $cache->setCache($name);
        return ($cache->eraseGroup()) ? true : false;
    }

    /**
     * Get Image File
     */
    function Image($img, $path = NULL, $width = NULL, $height = NULL, $quality = NULL)
    {
        return \Helper\Router::image($img, $path, $width, $height, $quality);
    }

    /**
     * Get Image File
     */
    function img($img, $path = NULL, $width = NULL, $height = NULL, $quality = NULL)
    {
        return \Helper\Router::image($img, $path, $width, $height, $quality);
    }

    /**
     * Get Media Image Path
     */
    function media($path = '/')
    {
        return STORAGE . 'media/img/' . $path;
    }

    /**
     * Put Create Path
     */
    function exits_dir($path = '/')
    {
        $return = array();
        $dir_p = vsprintf('%s/%s/', [$path, date('d_m_Y') ]);
        $dir_p = preg_replace('~(^|[^:])//+~', '\\1/', $dir_p);
        $dir_s = vsprintf('%s/%s/%s/', [STORAGE, $path, date('d_m_Y') ]);
        $dir_s = preg_replace('~(^|[^:])//+~', '\\1/', $dir_s);

        $return['dir'] = $dir_s;
        $return['path'] = $dir_p;

        if (!file_exists($return['dir']))
        {
            mkdir($return['dir'], 0777, true);
        }

        return $return;
    }

    /**
     * Price Smash
     * @param $limit
     * @param $post
     * @return array
     */
    function priceSmash($post = 0, $limit = 400)
    {

        $array = [];

        if ($post > $limit):
            $range = range($limit, $post);

            foreach ($range as $key => $value)
            {
                $watch = [$value % $limit];
                $aritmetic = $range[$value % $limit];
                if ($watch[0] == 0)
                {
                    array_push($array, $aritmetic);
                }
            }

            $total = array_sum($array);
            $check = ($post > $total) ? ($post - $total) : $total;
            array_push($array, $check);
        else:
            array_push($array, $post);
        endif;

        return $array;
    }

    /**
     * String to slug var
     * @param string
     * @return function
     */
    function slug(...$vars)
    {
        $db = \Core\Database::get();

        list($title, $split, $unique) = $vars;
        list($table, $column) = array_values($unique);
        $slug = \Support\Str::Slug($title, $split);

        $primaryID = $db->rawQuery("SHOW INDEXES FROM {$table}");
        $primaryID = $primaryID[0]['Column_name'];

        $count = $db->rawQueryValue("
            SELECT 
            COUNT({$column}) 
            FROM 
            {$table} 
            WHERE
            {$column} = '{$slug}'
            OR
            {$column} 
            REGEXP '{$slug}-[0-9]*' 
            ORDER BY LENGTH({$column}) DESC, 
            {$column} DESC
        ");

        if (!empty($count))
        {
            return ($count[0] == '0') ? fsprintf('{{slug}}', ['slug' => $slug]) : fsprintf('{{slug}}{{split}}{{count}}', ['slug' => $slug, 'split' => $split, 'count' => $count[0]]);
        }
        else
        {
            return $slug;
        }
    }

    /**
     * String to limit
     * @param string
     * @return function
     */
    function limit($str, $limit = 0)
    {
        return \Support\Str::limit($str, $limit);
    }

    /**
     * Get config parametre
     * @param array|null $arr
     * @return function
     */
    function config($config = NULL)
    {
        return \Core\Config::get($config);
    }

    /**
     * Get option variable
     * @param array|null $arr
     * @return sprintf function
     */
    function option($param = NULL)
    {
        return \Core\Options::get()->getOption($param);
    }

    /**
     * Get template type
     * @param none
     * @return variable
     */
    function tplType($ttype = NULL)
    {
        if (!empty($ttype)):
            return \Rewrite::requestCurrentPath($ttype);
        endif;
        return \Rewrite::requestGroup();
    }

    /**
     * Get template type for asset
     * @param none
     * @return variable
     */
    function tplTypeAsset($ttype = NULL)
    {
        if (!empty($ttype)):
            return \Rewrite::requestCurrentPath($ttype);
        endif;
        return \Rewrite::requestGroup();
    }

    /**
     * Get tpl settings variable
     * @param array|null $arr
     * @return sprintf function
     */
    function tplSettings($param = NULL, $ttype = NULL)
    {
        $settings = \Core\Options::get();
        $params = sprintf('theme%1$s', $param);
        $type = tplType($ttype);
        $config = $settings->getSiteThemeSettings($type);
        $var = $config[$params];

        return (!empty($var)) ? vsprintf('%s', [$var]) : false;
    }

    /**
     * Get tpl directory
     * @param array|null $arr
     * @return sprintf function
     */
    function tplDirectory($ttype = NULL)
    {
        $type = tplType($ttype);
        $var = tplSettings('Dir', $ttype);

        return sprintf('%1s%2s/', THEMES . $type . '/', $var);
    }

    /**
     * root template path
     * @param none
     * @return fsprintf
     */
    function rootTemplatePath()
    {
        return fsprintf('{{path}}{{group}}/{{dir}}/', ['path' => THEMES, 'group' => \Rewrite::requestGroup() , 'dir' => tplSettings('Dir') ]);
    }

    /**
     * Get tpl path
     * @param array|null $param
     * @return variable
     */
    function tplPath($path = NULL, $url = false)
    {

        $settings = \Core\Options::get();
        $type = tplType();
        $themes = ($url == false) ? THEMES . $type : sprintf('/%s/%s/', 'Themes', $type);
        $dir = tplSettings('Dir');

        $raw = vsprintf('%s/%s/%s/', [$themes, $dir, $path]);

        $return = preg_replace('#/+#', '/', $raw);

        return ($url == true) ? option('host-address') . $return : $return;
    }

    /**
     * Get assets path
     * @param array|null $param
     * @return variable
     */
    function assets($path = NULL, $url = false)
    {
        if (filter_var($path, FILTER_VALIDATE_URL))
        {
            return $path;
        }
        else
        {
            $settings = \Core\Options::get();
            $type = tplType();
            $themes = ($url == false) ? THEMES . $type : sprintf('/%s/%s/', 'Themes', $type);
            $dir = tplSettings('Dir');
            $assets = 'assets';

            $raw = vsprintf('%s/%s/%s/%s', [$themes, $dir, $assets, $path]);

            $return = preg_replace('#/+#', '/', $raw);

            return ($url == true) ? sprintf('%s%s', option('host-address') , $return) : $return;
        }
    }

    /**
     * XSS attack protection
     * @param $variable
     * @return return $param
     */
    function escapeXSS($var)
    {
        $var = htmlentities($var, ENT_QUOTES);

        return $var;
    }

    /**
     * Percent Discount
     * @param $variable
     * @return return $param
     */
    function percentDiscount($number, $percent)
    {
        $number = floatval($number);
        $return = $number - ($number / 100) * $percent;
        return number_format($return, 2, '.', '');
    }

    /**
     * Total Percentage Calculation
     * @param $num1, $num2
     * @return return function
     */
    function percentageCalc($num1, $num2)
    {
        $num1 = floatval($num1);
        $num2 = floatval($num2);
        return ($num1 - $num2) / $num1 * 100;
    }

    /**
     * Get session data
     * @param var|null $var
     * @return function
     */
    function getSession($name = NULL)
    {
        $session = new \Supplement\Session();
        return $session->get($name);
    }

    /**
     * Set session data
     * @param var|null $var
     * @return function
     */
    function setSession($name = NULL, $val = NULL)
    {
        $session = new \Supplement\Session();
        return $session->set($name, $val);
    }

    /**
     * HTML Entity Decode
     * @param $variable
     * @return return $param
     */
    function decodeHTMLEntity($str = NULL)
    {
        return html_entity_decode($str, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * parse pagination url query
     * @param  multiple variable
     * @return array
     */
    function parseQuery($request = NULL, $query = 'p')
    {
        $queryList = [];
        $anchor = array_keys($request) [0];
        $request = array_unique($request);
        $filter = config('http.filter.blacklist');

        foreach ($request as $key => $value)
        {
            $varType = gettype($value);

            if ((!empty($value)) && ($key != $query) && (!in_array($key, $filter)) && ($varType != 'boolean')):
                array_push($queryList, ['key' => $key, 'value' => $value]);
            endif;
        }

        $returnStr = [];
        foreach ($queryList as $key => $list)
        {

            if (count($queryList) > 1)
            {
                reset($queryList);
                if ($key === key($queryList)):
                    array_push($returnStr, sprintf('?%1$s=%2$s', $list['key'], $list['value']));
                else:
                    array_push($returnStr, sprintf('&%1$s=%2$s', $list['key'], $list['value']));
                endif;
            }
            else
            {
                array_push($returnStr, sprintf('?%1$s=%2$s&%3$s=', $list['key'], $list['value'], $query));
            }

            if (count($queryList) > 1)
            {
                end($queryList);
                if ($key === key($queryList)):
                    array_push($returnStr, sprintf('&%1$s=%2$s&%3$s=', $list['key'], $list['value'], $query));
                endif;
            }

        }

        $returnStr = join('', $returnStr);
        preg_match_all("/(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)/", $returnStr, $returnStr);
        $returnStr = array_unique(end($returnStr));
        $returnStr = end($returnStr);
        $returnSTR = implode('&', array_unique(explode('&', $returnStr)));

        if (empty($queryList)):
            $returnSTR = sprintf('?%1$s=', $query);
        endif;

        return $returnSTR;
    }

    /**
     * redirect to url
     * @param  $url
     * @return header location
     */
    function redirect($url = null)
    {
        return \Rewrite::redirect($url);
    }

    /**
     * route to url
     * @param  $url
     * @return header location
     */
    function route($name, array $args = null)
    {
        return \Rewrite::route($name, $args);
    }

    /**
     * return to url
     * @param  array $url
     * @return array to string
     */
    function url($name = NULL, array $args = NULL)
    {
        return \Helper\Router::url($name, $args);
    }

    /**
     * BBCode parser
     * @param  array $str
     * @return replace string
     */
    function BBCode($str = NULL, $core = 'parse')
    {
        return \Helper\BBCode::$core($str);
    }

    /**
     * 18in1 language
     * @param  array
     * @return variable
     */
    function trans($string, $x = NULL, $args = NULL)
    {
        return \Core\Language::trans($string, $x, $args);
    }

    /**
     * Send mail scheme
     * @param  array
     * @return variable
     */
    function sendMail()
    {
        \Helper\Router::setRegister(true);
        $method = func_get_arg(0);
        $args = func_get_args();
        unset($args[0]);
        $vars = array_values($args);
        $vars = base64_encode(json_encode($vars));
        $key = PRIVATE_KEY;
        $url = base64_encode(json_encode(url("?cryptoSendMail={$key}")));

        $command = vsprintf('php threeman -p %s,%s,%s', [$url, $method, $vars]);
        exec($command);
    }

    /**
     * Send mail template
     * @param  array
     * @return variable
     */
    function sendMailManuel()
    {
        $method = func_get_arg(0);
        $args = func_get_args();
        unset($args[0]);
        $vars = array_values($args);
        $send = new \Mail\Send();

        return $send->$method($vars);
    }

    /**
     * Generate encrypt and decrypt key
     *
     * @param  string  $data
     * @param  bool    $strict
     * @return bool
     */
    function encrypt($string)
    {
        $string .= PRIVATE_KEY;
        $key = sha1(PRIVATE_KEY);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $hash = '';
        for ($i = 0;$i < $strLen;$i++)
        {
            $ordStr = ord(substr($string, $i, 1));
            $j = (!empty($j)) ? $j : 0;
            if ($j == $keyLen)
            {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;

            $hash .= strrev(base_convert(dechex($ordStr + $ordKey) , 16, 36));
        }

        return $hash;
    }

    function decrypt($string)
    {
        $key = sha1(PRIVATE_KEY);
        $strLen = strlen($string);
        $keyLen = strlen($key);
        $hash = '';
        for ($i = 0;$i < $strLen;$i += 2)
        {
            $ordStr = hexdec(base_convert(strrev(substr($string, $i, 2)) , 36, 16));
            $j = (!empty($j)) ? $j : 0;
            if ($j == $keyLen)
            {
                $j = 0;
            }
            $ordKey = ord(substr($key, $j, 1));
            $j++;
            $hash .= chr($ordStr - $ordKey);
        }

        return str_replace(PRIVATE_KEY, '', $hash);
    }

    /**
     * Get webapp url address
     * @return sprintf array
     */
    function getURL()
    {
        if ((!empty($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] == 'https') || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443'))
        {
            $_SERVER["REQUEST_SCHEME"] = 'https';
        }
        else
        {
            $_SERVER["REQUEST_SCHEME"] = 'http';
        }

        return sprintf("%1s://%2s", $_SERVER["REQUEST_SCHEME"], $_SERVER['SERVER_NAME']);
    }

    /**
     * Check value to find if it was serialized.
     *
     * @param  string  $data
     * @param  bool    $strict
     * @return bool
     */
    function is_serialized($data, $strict = true)
    {
        if (!is_string($data)) return false;

        $data = trim($data);

        if ('N;' == $data) return true;

        if (strlen($data) < 4) return false;

        if (':' !== $data[1]) return false;

        if ($strict)
        {
            $lastc = substr($data, -1);

            if ((';' !== $lastc) && ('}' !== $lastc))
            {
                return false;
            }
        }
        else
        {
            $semicolon = strpos($data, ';');
            $brace = strpos($data, '}');

            if ((false === $semicolon) && (false === $brace)) return false;

            if ((false !== $semicolon) && ($semicolon < 3)) return false;

            if ((false !== $brace) && ($brace < 4)) return false;
        }

        $token = $data[0];

        switch ($token)
        {
            case 's':
                if ($strict)
                {
                    if ('"' !== substr($data, -2, 1))
                    {
                        return false;
                    }
                }
                else if (false === strpos($data, '"'))
                {
                    return false;
                }
            case 'a':
            case 'O':
                return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
            case 'b':
            case 'i':
            case 'd':
                $end = $strict ? '$' : '';

                return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
            }

            return false;
    }

    /**
     * Serialize data, if needed.
     *
     * @param  mixed  $data
     * @return mixed
     */
    function maybe_serialize($data)
    {
        if (is_array($data) || is_object($data))
        {
            return serialize($data);
        }

        return $data;
    }

    /**
     * Unserialize value only if it was serialized.
     *
     * @param  string $original
     * @return mixed
     */
    function maybe_unserialize($original)
    {
        if (\is_serialized($original))
        {
            return @unserialize($original);
        }

        return $original;
    }

    /** Array helpers. */

    /**
     * Fetch a flattened array of a nested array element.
     *
     * @param  array   $array
     * @param  string  $key
     * @return array
     */
    function array_fetch($array, $key)
    {
        foreach (explode('.', $key) as $segment)
        {
            $results = array();

            foreach ($array as $value)
            {
                $value = (array)$value;

                $results[] = $value[$segment];
            }

            $array = array_values($results);
        }

        return array_values($results);
    }

    /**
     * Determine if the given object has a toString method.
     *
     * @param  object  $object
     * @return bool
     */
    function str_object($object)
    {
        return (is_object($object) && method_exists($object, '__toString'));
    }

    /** Common data lookup methods. */

    /**
     * Dump the passed variables and end the script.
     *
     * @param  dynamic  mixed
     * @return void
     */
    function dd()
    {
        array_map(function ($x)
        {
            var_dump($x);
        }
        , func_get_args());
        die;
    }

    /**
     * print_r call wrapped in pre tags
     *
     * @param  string or array $data
     * @param  boolean $exit
     */
    function pr($data, $exit = false)
    {
        echo "<pre>";
        print_r($data);
        echo "</pre>";

        if ($exit == true)
        {
            exit;
        }
    }

    /**
     * print_p call wrapped in pre tags highlighter
     *
     * @param  boolean $value
     * @param  boolean $exit
     * @param  boolean $return
     * @param  boolean $recurse
     */
    function pp($value = false, $exit = false, $return = false, $recurse = false)
    {
        if ($return === true && $exit === true) $return = false;
        $tab = str_repeat("&nbsp;", 8);
        if ($recurse == false)
        {
            $recurse = 0;
            $output = '<div style="width:100%;border: 2px dotted red;background-color: #f5f9be;display: block;padding: 2px;color: #364444;font-weight: 500;">';
            $backtrace = debug_backtrace();
            $output .= '<b>Line: </b>' . $backtrace[0]['line'] . '<br>';
            $output .= '<b>File: </b> ' . $backtrace[0]['file'] . '<br>';
            $indent = "";
        }
        else
        {
            $output = '';
            $indent = str_repeat("&nbsp;", $recurse * 8);
        }
        if (is_array($value))
        {
            if ($recurse == false)
            {
                $output .= '<b>Type: </b> Array<br>';
                $output .= "<br>array (<br>";
            }
            else
            {
                $output .= "array (<br>";
            }
            $items = array();
            foreach ($value as $k => $v)
            {
                if (is_object($v) || is_array($v)) $items[] = $indent . $tab . "'" . $k . "'=>" . pp($v, false, true, ($recurse + 1));
                else $items[] = $indent . $tab . "'" . $k . "'=>" . ($v === null ? "NULL" : "'" . $v . "'");
            }
            $output .= implode(',<br>', $items);
            if ($recurse == false) $output .= '<br>)';
            else $output .= '<br>' . $indent . ')';
        }
        elseif (is_object($value))
        {
            if ($recurse == false)
            {
                $output .= '<b>Type: </b> Object<br>';
                $output .= '<br>object (' . get_class($value) . '){' . "<br>";
            }
            else
            {
                $output .= "object (" . get_class($value) . "){<br>";
            }

            // needed conditional because base class function dump is protected
            $vars = get_object_vars($value);
            $vars = (is_array($vars) == true ? $vars : array());

            $items = array();
            foreach ($vars as $k => $v)
            {
                if (is_object($v) || is_array($v)) $items[] = $indent . $tab . "'" . $k . "'=>" . pp($v, false, true, ($recurse + 1));
                else $items[] = $indent . $tab . "'" . $k . "'=>" . ($v === null ? "NULL" : "'" . $v . "'");
            }
            $output .= implode(',<br>', $items);
            $vars = get_class_methods($value);
            $items = array();
            foreach ($vars as $v)
            {
                $items[] = $indent . $tab . $tab . $v;
            }
            $output .= '<br>' . $indent . $tab . '<b>Methods</b><br>' . implode(',<br>', $items);
            if ($recurse == false) $output .= '<br>}';
            else $output .= '<br>' . $indent . '}';
        }
        else
        {
            if ($recurse == false)
            {
                $output .= '<b>Type: </b> ' . gettype($value) . '<br>';
                $output .= '<b>Value: </b> ' . $value;
            }
            else
            {
                $output .= '(' . gettype($value) . ') ' . $value;
            }
        }
        if ($recurse == false) $output .= '</div>';
        if ($return === false) echo $output;
        if ($exit === true) die();
        return $output;
    }

    /**
     * var_dump call
     *
     * @param  string or array $data
     * @param  boolean $exit
     *
     */
    function vd($data, $exit = false)
    {
        var_dump($data);

        if ($exit == true)
        {
            exit;
        }
    }

    /**
     * strlen call - count the length of the string.
     *
     * @param  string $data
     * @return string return the count
     */
    function sl($data)
    {
        return strlen($data);
    }

    /**
     * strtoupper - convert string to uppercase.
     *
     * @param  string $data
     * @return string
     */
    function stu($data)
    {
        return strtoupper($data);
    }

    /**
     * strtolower - convert string to lowercase.
     *
     * @param  string $data
     * @return string
     */
    function stl($data)
    {
        return strtolower($data);
    }

    /**
     * ucwords - the first letter of each word to be a capital.
     *
     * @param  string $data
     * @return string
     */
    function ucw($data)
    {
        return ucwords($data);
    }

    /**
     * key - this will generate a 32 character key
     * @return string
     */
    function createKey($length = 32)
    {
        return str_random($length);
    }

    /**
     * addhttp - this will ensire $url starts with http
     *
     * @param $url string
     * @param $scheme string
     * @return string
     */
    function add_http($url, $scheme = 'http://')
    {
        return parse_url($url, PHP_URL_SCHEME) === null ? $scheme . $url : $url;
    }

    /**
     * Get mime type for extension
     */
    function getmime($ext)
    {
        $mime_types = ['txt' => 'text/plain', 'htm' => 'text/html', 'html' => 'text/html', 'php' => 'text/html', 'css' => 'text/css', 'js' => 'application/javascript', 'json' => 'application/json', 'xml' => 'application/xml', 'swf' => 'application/x-shockwave-flash', 'flv' => 'video/x-flv',

        // images
        'png' => 'image/png', 'jpe' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'jpg' => 'image/jpeg', 'gif' => 'image/gif', 'bmp' => 'image/bmp', 'ico' => 'image/vnd.microsoft.icon', 'tiff' => 'image/tiff', 'tif' => 'image/tiff', 'svg' => 'image/svg+xml', 'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip', 'rar' => 'application/x-rar-compressed', 'exe' => 'application/x-msdownload', 'msi' => 'application/x-msdownload', 'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg', 'qt' => 'video/quicktime', 'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf', 'psd' => 'image/vnd.adobe.photoshop', 'ai' => 'application/postscript', 'eps' => 'application/postscript', 'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword', 'rtf' => 'application/rtf', 'xls' => 'application/vnd.ms-excel', 'ppt' => 'application/vnd.ms-powerpoint',

        // open office
        'odt' => 'application/vnd.oasis.opendocument.text', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet', ];

        if (array_key_exists($ext, $mime_types))
        {
            return $mime_types[$ext];
        }
        else
        {
            return 'application/octet-stream';
        }
    }

    /**
     * get Directory Size
     *
     * @param $path string
     * @return number
     */
    function getDirectorySize($path)
    {
        return \Support\File::getDirectorySize($path);
    }

    /**
     * convert Format Bytes
     *
     * @param $size number
     * @param $precision number
     * @return number + string -> round
     */
    function convertFormatBytes($size, $precision = 4)
    {
        return \Support\File::convertFormatBytes($size, $precision);
    }

    /**
     * get Directory Bytes
     *
     * @param $path string
     * @param $size number
     * @return result
     */
    function getDirectoryBytes($path, $size)
    {
        return \Support\File::getDirectoryBytes($path, $size);
    }

    /**
     * change Array Key Name
     *
     * @param $array array
     * @param $newkey variable
     * @param $oldkey variable
     * @return array
     */
    function changeArrayKey($array, $newkey, $oldkey)
    {
        foreach ($array as $key => $value)
        {
            if (is_array($value)) $array[$key] = changeArrayKey($value, $newkey, $oldkey);
            else
            {
                $array[$newkey] = $array[$oldkey];
            }

        }
        unset($array[$oldkey]);
        return $array;
    }

    /**
     * search Array key by value
     *
     * @param $array array
     * @param $key variable
     * @param $value variable
     * @return array
     */
    function searchArray($array, $key, $value)
    {
        $results = array();

        if (is_array($array))
        {
            if (isset($array[$key]) && $array[$key] == $value)
            {
                $results[] = $array;
            }
            else
            {
                foreach ($array as $subarray) $results = array_merge($results, searchArray($subarray, $key, $value));
            }
        }

        return $results;

    }

    /**
     * unique Array by key
     *
     * @param $array array
     * @param $key variable
     * @return array
     */
    function unique_multidim_array($array, $key)
    {
        $temp_array = array();
        $i = 0;
        $key_array = array();

        foreach ($array as $val)
        {
            if (!in_array($val[$key], $key_array))
            {
                $key_array[$i] = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }

    /**
     * array_merge_numeric_values
     *
     * @param $array array
     * @return array
     */
    function array_merge_numeric_values()
    {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array)
        {
            foreach ($array as $key => $value)
            {
                if (!is_numeric($value))
                {
                    continue;
                }
                if (!isset($merged[$key]))
                {
                    $merged[$key] = $value;
                }
                else
                {
                    $merged[$key] += $value;
                }
            }
        }
        return $merged;
    }

    /**
     * Get All Filtered Client Real IP Address
     * @return string
     */
    function getRealIPAddress()
    {
        $request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();

        return ($request->getClientIp() == '::1') ? '127.0.0.1' : $request->getClientIp();
    }

    /**
     * Key value sprintf
     * @return string
     */
    function fsprintf($str, $data)
    {
        return preg_replace_callback('#{{(\w+?)(\.(\w+?))?}}#', function ($m) use ($data)
        {
            return count($m) === 2 ? $data[$m[1]] : $data[$m[1]][$m[3]];
        }
        , $str);
    }

    /**
     * hoursBetween
     * @param  datetime $dt [date]
     * @return hours format
     */
    function hoursBetween($dt)
    {

        $date = \Carbon\Carbon::parse($dt);
        $now = \Carbon\Carbon::now();
        $diff = $date->diffInHours($now);

        return $diff;
    }

    /**
     * secondsBetween
     * @param  datetime $dt [date]
     * @return seconds format
     */
    function secondsBetween($dt)
    {

        $date = \Carbon\Carbon::parse($dt);
        $now = \Carbon\Carbon::now();
        $diff = $date->diffInSeconds($now);

        return $diff;
    }

endif;

