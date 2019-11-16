<?php

namespace Digitalis\Core\Models;

use Digitalis\Core\Models\EnvironmentManager as EnvMgr;

/**
 * Reseller 
 *
 * This class allows you to graphically 
 * format the reseller interface. 
 * 
 * The host is considered here without the port.
 *
 * @copyright  2018 IMEDIATIS SARL
 * @license    Intellectual property rights of IMEDIATIS SARL
 * @version    Release: 1.0
 * @about      http://www.imediatis.net
 * @author     Cyrille WOUPO (UX Designer)
 */
class Reseller implements \Serializable
{
    public static $defaultHost = '192.168.100.84';
    public function __construct()
    {
        $this->file = EnvMgr::getResellerFile();
        $thost = explode(":", (isset($_SERVER['HTTP_HOST']) && !is_null($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : "");
        $uri = (isset($thost[0]) && !is_null($thost[0]) && strlen($thost[0]) > 0) ? $thost[0] : self::$defaultHost;
        if (strlen($uri) > 3) {
            $www = strtolower(substr($uri, 0, 4));
            if ($www == "www.") {
                $uri = substr($uri, 4);
            }
        }
        $this->currentExist = $this->getByHost($uri);
    }

    public function __destruct()
    { }

    private $file;
    private $ref;
    private $uri;
    private $keyof;
    private $logo;
    private $css;
    private $bg;
    private $icon;
    private $dbHost;
    private $dbPort;
    private $dbUser;
    private $dbPwd;
    private $dbName;
    private $name;

    /**
     * token pour l'appelle d'api
     *
     * @var string
     */
    private $apiToken;
    private $apiUrl;

    /**
     * Dossier de classement des fichiers du client
     *
     * @var string
     */
    private $folder;

    /**
     * Détermine si le revendeur courant existe ou pas
     *
     * @var boolean
     */
    private $currentExist;

    public function serialize()
    {
        return serialize(array(
            $this->file,
            $this->ref,
            $this->uri,
            $this->keyof,
            $this->logo,
            $this->bg,
            $this->css,
            $this->icon,
            $this->currentExist,
            $this->dbHost,
            $this->dbPort,
            $this->dbUser,
            $this->dbPwd,
            $this->dbName,
            $this->folder,
            $this->apiToken,
            $this->apiUrl,
            $this->name
        ));
    }

    public function unserialize($serialized)
    {
        list(
            $this->file,
            $this->ref,
            $this->uri,
            $this->keyof,
            $this->logo,
            $this->bg,
            $this->css,
            $this->icon,
            $this->currentExist,
            $this->dbHost,
            $this->dbPort,
            $this->dbUser,
            $this->dbPwd,
            $this->dbName,
            $this->folder,
            $this->apiToken,
            $this->apiUrl,
            $this->name
        ) = unserialize($serialized);
    }

    /**
     * Formate le reseller pour l'injection dans twig
     */
    public function forTwig()
    {
        return [
            'ref' => $this->ref,
            'uri' => $this->uri,
            'logo' => $this->logo,
            'keyof' => $this->keyof,
            'icon' => $this->icon,
            'bg' => $this->bg,
            'css' => explode(";", $this->css),
            'folder' => $this->folder,
            'apiToken' => $this->apiToken,
            'apiUrl' => $this->apiUrl,
            'name' => $this->name
        ];
    }

    /**
     * Retourne la valeur de $currentExist
     *
     * @return boolean
     */
    public function currentExist()
    {
        return $this->currentExist;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getFolder()
    {
        return $this->folder;
    }
    public function getRef()
    {
        return $this->ref;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function getKeyof()
    {
        return $this->keyof;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function getCss()
    {
        return $this->css;
    }

    public function getBg()
    {
        return $this->bg;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    function getDbHost()
    {
        return $this->dbHost;
    }
    public function getDbPort()
    {
        return $this->dbPort;
    }

    function getDbUser()
    {
        return $this->dbUser;
    }

    function getDbPwd()
    {
        return $this->dbPwd;
    }

    function getDbName()
    {
        return $this->dbName;
    }
    /**
     * Retourne la valeur de $apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Définit la valeur de $apiToken
     *
     * @param string $apiToken
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;
    }
    public function setApiUrl($apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function setFile($_file)
    {
        $this->file = $_file;
    }

    public function setFolder($folder)
    {
        $this->folder = $folder;
    }
    public function setRef($_ref)
    {
        $this->ref = $_ref;
    }

    public function setUri($_uri)
    {
        $this->uri = $_uri;
    }

    public function setKeyof($_keyof)
    {
        $this->keyof = $_keyof;
    }

    public function setLogo($_logo)
    {
        $this->logo = $_logo;
    }

    public function setCss($_css)
    {
        $this->css = $_css;
    }

    public function setBg($_bg)
    {
        $this->bg = $_bg;
    }

    public function setIcon($_icon)
    {
        $this->icon = $_icon;
    }

    function setDbHost($dbHost)
    {
        $this->dbHost = $dbHost;
    }

    public function setDbPort($dbport)
    {
        $this->dbPort = $dbport;
    }

    function setDbUser($dbUser)
    {
        $this->dbUser = $dbUser;
    }

    function setDbPwd($dbPwd)
    {
        $this->dbPwd = $dbPwd;
    }

    function setDbName($dbName)
    {
        $this->dbName = $dbName;
    }

    /**
     * Définit la valeur de name
     *
     * @param  $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    private function fullSet($ref, $uri, $keyof, $logo, $css, $bg, $icon, $dbHost, $dbPort, $dbUser, $dbPwd, $dbName, $folder, $apiToken, $apiUrl, $name)
    {
        $this->ref = $ref;
        $this->uri = $uri;
        $this->keyof = $keyof;
        $this->logo = $logo;
        $this->css = $css;
        $this->bg = $bg;
        $this->icon = $icon;
        $this->dbHost = $dbHost;
        $this->dbPort = $dbPort;
        $this->dbUser = $dbUser;
        $this->dbPwd = $dbPwd;
        $this->dbName = $dbName;
        $this->folder = $folder;
        $this->apiToken = $apiToken;
        $this->name = $name;
        $this->apiUrl = $apiUrl;
    }

    /**
     * Reads the XML file and sets a dealer from its code
     * @param $code - Reseller code
     * @return Boolean (true) if set/found or (false) else
     */
    public function getItem($code)
    {
        if (file_exists($this->getFile())) {
            $xml = simplexml_load_file($this->getFile());
            $target = $xml->xpath("//dealer[@ref='" . $code . "']");
            if (!$target) {
                return false;
            }

            $this->fullSet(
                (string) $target[0]['ref'],
                (string) $target[0]['uri'],
                (string) $target[0]['keyof'],
                (string) $target[0]['logo'],
                (string) $target[0]['css'],
                (string) $target[0]['bg'],
                (string) $target[0]['icon'],
                (string) $target[0]['dbhost'],
                (string) $target[0]['dbport'],
                (string) $target[0]['dbuser'],
                (string) $target[0]['dbpwd'],
                (string) $target[0]['dbname'],
                (string) $target[0]['folder'],
                (string) $target[0]['apiToken'],
                (string) $target[0]['apiUrl'],
                (string) $target[0]['name']
            );
            return true;
        }
        return false;
    }

    /**
     * Chooses the client based on the URL
     * @param $code - Reseller code
     * @return Boolean (true) if set/found or (false) else
     */
    public function getByHost($uri)
    {
        if (file_exists($this->getFile())) {
            $xml = simplexml_load_file($this->getFile());
            $target = $xml->xpath("//dealer[@uri='" . $uri . "']");
            if (!$target) {
                return false;
            }

            $this->fullSet(
                (string) $target[0]['ref'],
                (string) $target[0]['uri'],
                (string) $target[0]['keyof'],
                (string) $target[0]['logo'],
                (string) $target[0]['css'],
                (string) $target[0]['bg'],
                (string) $target[0]['icon'],
                (string) $target[0]['dbhost'],
                (string) $target[0]['dbport'],
                (string) $target[0]['dbuser'],
                (string) $target[0]['dbpwd'],
                (string) $target[0]['dbname'],
                (string) $target[0]['folder'],
                (string) $target[0]['apiToken'],
                (string) $target[0]['apiUrl'],
                (string) $target[0]['name']
            );
            return true;
        }
        return false;
    }

    /**
     * get a list of all resellers
     * @return Array of resellers false if not
     */
    public function getAll()
    {
        $Countries = array();
        if (file_exists($this->getFile())) {
            $xml = simplexml_load_file($this->getFile());
            if (is_object($xml) && (strcmp(get_class($xml), 'SimpleXMLElement') == 0) && count($xml)) {
                foreach ($xml as $target) {
                    $C = new Reseller();
                    $C->fullSet(
                        (string) $target[0]['ref'],
                        (string) $target[0]['uri'],
                        (string) $target[0]['keyof'],
                        (string) $target[0]['logo'],
                        (string) $target[0]['css'],
                        (string) $target[0]['bg'],
                        (string) $target[0]['icon'],
                        (string) $target[0]['dbhost'],
                        (string) $target[0]['dbport'],
                        (string) $target[0]['dbuser'],
                        (string) $target[0]['dbpwd'],
                        (string) $target[0]['dbname'],
                        (string) $target[0]['folder'],
                        (string) $target[0]['apiToken'],
                        (string) $target[0]['apiUrl'],
                        (string) $target[0]['name']
                    );
                    $Countries[] = $C;
                }
                unset($C);
                return $Countries;
            }
        }
        return false;
    }

    /**
     * Add a new dealer to the list
     * @param $dealer - Indexed array with the dealer structure
     * @return Boolean (true) if recorded or (false) else
     */
    public function register(array $dealer)
    {
        $xml = new \DOMDocument('1.0');
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;

        if (file_exists($this->getFile())) {
            if (!is_writable($this->getFile())) {
                return false;
            }

            $xml->load($this->getFile());
            $newNode = $xml->createElement('dealer');
            foreach ($dealer as $key => $value) {
                $newNode->setAttribute($key, $value);
            }

            $xmlEdit = $xml->getElementsByTagName('resellers')->item(0);
            $xmlEdit->appendChild($newNode);
            return $xml->save($this->getFile());
        }
        return false;
    }

    /**
     * Checks if an occurrence exists in the file with the same code
     * @return Boolean (true) if found or (false) else
     */
    public function exist()
    {
        $xml = simplexml_load_file($this->getFile());
        $target = $xml->xpath("//dealer[@ref='" . $this->_code . "']");
        if (!$target) {
            return false;
        }
        return true;
    }

    /**
     * Remove a dealer from the list
     * @return Boolean save status or false if item not exist
     */
    public function remove()
    {
        $xml = simplexml_load_file($this->getFile());
        $target = $xml->xpath("//dealer[@ref='" . $this->_code . "']");
        if (!$target) {
            return false;
        }

        $domRef = dom_import_simplexml($target[0]);
        $domRef->parentNode->removeChild($domRef);

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());
        return $dom->save($this->getFile());
    }
}
