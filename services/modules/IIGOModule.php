<?php
namespace IGO\Modules;

use Phalcon\Mvc\ModuleDefinitionInterface;

/**
 * Interface définit la structure d'un module IGO.
 *
 * @package IGO\Modules
 */
interface IIGOModule {
	/**
	 * Initialise le module.
	 *
	 * @return void
	 */
	public function initialiser();

	/**
	 * Obtient le nom du module.
	 *
	 * @return string
	 */
	public function obtenirNom($capitale=false);

	/**
	 * Ajoute les fonctionnalitées supplémentaires à l'api IGO.
	 *
	 * @return void
	 */
	public function chargerApi($api);

	/**
	 * Retour la liste de tous les librairies Javascript inclus par le module.
	 *
	 * @return array
	 */
	public function obtenirJavascript();

	/**
	 * Obtient la configuration du module.
	 *
	 * @return array
	 */
	public function obtenirConfiguration();

    /**
	 * Retour la liste de tous les services inclus par le module avec le type défini.
	 *
	 * @return array
	 */
	public function obtenirServices($type);
}
