<?php

use Phalcon\DI\FactoryDefault;
use Phalcon\Mvc\View;
use Phalcon\Text;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Db\Adapter\Pdo\Postgresql as DbAdapter;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Files as SessionAdapter;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

$di->set('chargeurModules', function () {
    $chargeurModules = new \IGO\Modules\ChargeurModules();
    $chargeurModules->initialiser();

    return $chargeurModules;
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function () use ($config) {
    $url = new UrlResolver();
    $url->setBaseUri($config->uri->navigateur);
    return $url;
}, true);

$di->set('config', function () use ($config) {
    return $config;
});

/**
 * Setting up the view component
 */
$di->set('view', function () use ($config) {

    $view = new igoView();
    $view->config = $config;

    if(isset($config->mapserver) && isset($config->mapserver->host)){
        $view->host = $config->mapserver->host;
    }

    $view->viewsDir=$config->application->navigateur->viewsDir;
    
    $view->setViewsDir($config->application->navigateur->viewsDir);

    $view->registerEngines(array(
            '.volt' => function ($view, $di) use ($config) {

            $volt = new VoltEngine($view, $di);

            $volt->setOptions(array(
                'compiledPath' => $config->application->navigateur->cacheDir,
                'compiledSeparator' => '_',
                'compileAlways' => (isset($config->application->debug) && $config->application->debug ? true : false)
            ));
            
            return $volt;
        },
        '.phtml' => 'Phalcon\Mvc\View\Engine\Php'
    ));
    return $view;
}, true);
   

$di->set('dispatcher', function() use($di){

    //Create/Get an EventManager
    $eventsManager = new Phalcon\Events\Manager();

    //Attach a listener
    $eventsManager->attach("dispatch", function($event, $dispatcher, $exception) {

        //The controller exists but the action not
        
        if ($event->getType() == 'beforeNotFoundAction') {
            $dispatcher->forward(array(
                'controller' => 'error',
                'action' => 'error404'
            ));
            return false;
        }
        
        //Alternative way, controller or action doesn't exist
        
        if ($event->getType() == 'beforeException') {
            switch ($exception->getCode()) {
                case Phalcon\Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Phalcon\Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward(array(
                        'controller' => 'error',
                        'action' => 'error404'
                    ));      
                    
                    return false;
            }
        }
    });
    
    $securityPlugin = new SecurityPlugin($di);
    $eventsManager->attach("dispatch", $securityPlugin);

    $dispatcher = new Phalcon\Mvc\Dispatcher();

    //Bind the EventsManager to the dispatcher
    $dispatcher->setEventsManager($eventsManager);

    return $dispatcher;

}, true);

/**
 *    Logger
**/
$di->set('logger', function () use ($config) {
    $pathLogFile = $config->repertoireLogs . "igo.log";
    return new IGO\Modules\Logger($pathLogFile, $config->application->debug);
}, true);


/**
 * Encryption pour les mots de passes des couches securisées
 */

$di->set('crypt', function () use ($config) {

    $crypt = new Phalcon\Crypt();
    $crypt->setCipher('blowfish');
    $crypt->setMode('cbc');

    if (isset($config->application->authentification['secretXmlFile'])) {
       $xmlPath = $config->application->authentification->secretXmlFile;
    }
    
    if (empty($xmlPath)) {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(401);
        die("Le paramètre secretXmlFile n'a pas été trouvé dans le config.php");
    }
    
    if (file_exists($xmlPath) && !empty($xmlPath) ) {
        $key = simplexml_load_file($xmlPath, 'SimpleXMLElement', LIBXML_NOCDATA);
    }

    if (empty($key)) {
        header('Content-Type: text/html; charset=utf-8');
        http_response_code(401);
        die("La clé n'a pas été trouvée dans ce chemin" . $xmlPath . "ou elle n'existe pas!");
    }

    $crypt->setKey($key['authentification']);

    return $crypt;
}, true);


/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function () use ($config) {

    $adapter = '\\Phalcon\\Db\\Adapter\\Pdo\\' . $config->database->adapter;
    if ( ! class_exists($adapter)){
        throw new \Phalcon\Exception('Invalid database Adapter!');
    }
    
    $connection= new $adapter(array(
        'host' => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname' => $config->database->dbname
    ));
	
/*
 *  //Décommenter pour activer le profilage de PGSQL
	//TODO Activer le profilage PGSQL quand on est en mode debug. On ne devrait pas avoir à décommenter des lignes 
    $eventsManager = new \Phalcon\Events\Manager();

    $eventsManager->attach('db', function($event, $connection) {
        if ($event->getType() == 'beforeQuery') {
            error_log($connection->getSQLStatement());
        }
    });

    $connection->setEventsManager($eventsManager);
*/
    return $connection;
});


/*
$debug = new \Phalcon\Debug();
$debug->listen();
*/

if($config->offsetExists("database")) {
    if($config->database->modelsMetadata == 'Apc'){
        $di->set('modelsMetadata', function() {   
            // Create a meta-data manager with APC
            $metaData = new \Phalcon\Mvc\Model\MetaData\Apc(array(
                "lifetime" => 86400,
                "prefix"   => "igo"
            ));
            return $metaData;   
        });
    }else if($config->database->modelsMetadata == 'Xcache'){
        $di->set('modelsMetadata', function() {       
            $metaData = new Phalcon\Mvc\Model\Metadata\Xcache(array(
            'prefix' => 'igo',
            'lifetime' => 86400 //24h
            ));
        return $metaData;   
        });    
    }
}

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $cookieName = 'sessionIGO';
    $session = new SessionAdapter();

    if (isset($_COOKIE[$cookieName])) {
        $sessid = $_COOKIE[$cookieName];
        if (!preg_match('/^[a-zA-Z0-9,\-]{22,40}$/', $sessid)) {
            unset($_COOKIE[$cookieName]);
            setcookie($cookieName, '', time() - 3600, '/');
        }     
    } 
    session_name($cookieName);
    $session->start();

    return $session;
});

/**
* Ajout du routing pour le navigateur construit, en utilisant les paramètres REST plutot que KVP.
*/

$di->set('router', function(){
    $router = new \Phalcon\Mvc\Router();
    //Define a route
    $router->add(
        "#^/([a-zA-Z0-9_-]++)#",
        array(
            "controller" => "error",
            "action" => "error404"
        )
    );

    $router->add(
        "/contexte/{contexte}",
        array(
            "controller" => "igo",
            "action"     => "contexte",
            "contexteid"   => 1
        )
    );
    $router->add(
        "/configuration/{configuration}",
        array(
            "controller" => "igo",
            "action"     => "configuration",
            "configuration" => 1
        )
    );
     $router->add(
        "/couche/{coucheId}",
        array(
            "controller" => "igo",
            "action"     => "couche",
            "coucheid" => 1
        )
    );
    $router->add(
        "/groupe/{groupeId}",
        array(
            "controller" => "igo",
            "action"     => "groupe",
            "coucheid" => 1
        )
    );

    $router->add(
        "/connexion/{action}",
        array(
            "controller" => "connexion",
            "action" => 1
        )
    );
       
    $router->setDefaults(array('controller' => 'index', 'action' => 'index'));
    
    return $router;
});

if(isset($config->application->authentification->module)){
    $authentificationModule = new $config->application->authentification->module;
    if($authentificationModule instanceof AuthentificationController){
        $di->set("authentificationModule", $authentificationModule);
    }else{
        error_log("Le module d'authentificaiton n'est pas une instance d'AuthentificationController");
    }
}else{
    $di->set("authentificationModule", 'AuthentificationTest');
}




class igoView extends Phalcon\Mvc\View {

    public $config = null;

    public function ajouterJavascript($chemin, $estExterne, $dansUriLibrairies=null){
        if($estExterne === true){
            if($dansUriLibrairies === true){
                print('<script src="'. $this->config->uri->librairies . $chemin . "?version=" . $this->config->application->version . '" type="text/javascript"></script>'. "\n");
            } else {
                print('<script src="'. $chemin .'" type="text/javascript"></script>'. "\n");
            }   
        }else{
            print('<script src="'. $this->config->application->baseUri . $chemin . "?version=" . $this->config->application->version . '" type="text/javascript"></script>'. "\n");
        }
    }

    public function ajouterCss($chemin, $estExterne, $dansUriLibrairies=null){       
        if($estExterne === true){
            if($dansUriLibrairies === true){
                print('<link rel="stylesheet" href="'. $this->config->uri->librairies . $chemin . "?version=" . $this->config->application->version .  '" type="text/css"/>'. "\n");
            } else {
                print('<link rel="stylesheet" href="'. $chemin . '" type="text/css"/>'. "\n");
            }   
        }else{
            print('<link rel="stylesheet" href="'. $this->config->application->baseUri . $chemin . "?version=" . $this->config->application->version .  '" type="text/css"/>'. "\n");
        }
    }
    
    public function ajouterImage($source, $alt){        
        print('<img src="' . $this->config->application->baseUri . $source . '" alt="'. $alt . '">'. "\n");
    }
    
    public function ajouterBaseUri(){
        print($this->config->application->baseUri);
    }
}