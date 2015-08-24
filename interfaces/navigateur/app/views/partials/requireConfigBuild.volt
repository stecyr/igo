define([], function() {
	return function(version, debug, uri){
		require.config({
		    baseUrl: uri.navigateur,
		    urlArgs: "version="+version,
		    waitSeconds: 30,
		    paths: {
		    	afficherProprietesFct: 'js/app/helper/afficherProprietesFct',
				evenement: 'js/app/helper/evenement',
				requireAide: 'js/app/helper/requireAide',
				contexte: 'js/app/helper/contexte',
				metadonnee: 'js/app/helper/metadonnee',
				browserDetect: 'js/app/helper/browserDetect',
				ajaxProxy: 'js/app/helper/ajaxProxy',
				aide: 'js/app/helper/aide',
				fonctions: 'js/app/helper/fonctions',
				cluster: 'js/app/occurence/cluster',
				style: 'js/app/occurence/style',
				multiLigne: 'js/app/occurence/geometrie/multiLigne',
				multiPoint: 'js/app/occurence/geometrie/multiPoint',
				point: 'js/app/occurence/geometrie/point',
				limites: 'js/app/occurence/geometrie/limites',
				polygone: 'js/app/occurence/geometrie/polygone',
				ligne: 'js/app/occurence/geometrie/ligne',
				multiPolygone: 'js/app/occurence/geometrie/multiPolygone',
				occurence: 'js/app/occurence/occurence',
				carte: 'js/app/carte',
				panneau: 'js/app/panneau/panneau',
				panneauAccordeon: 'js/app/panneau/panneauAccordeon',
				panneauInfo: 'js/app/panneau/panneauInfo',
				panneauMenu: 'js/app/panneau/panneauMenu',
				panneauTable: 'js/app/panneau/panneauTable',
				panneauEntete: 'js/app/panneau/panneauEntete',
				panneauCarte: 'js/app/panneau/panneauCarte',
				panneauOnglet: 'js/app/panneau/panneauOnglet',
				gestionCouches: 'js/app/couche/gestionCouches',
				XYZ: 'js/app/couche/protocole/XYZ',
				vecteurCluster: 'js/app/couche/protocole/vecteurCluster',
				WMTS: 'js/app/couche/protocole/WMTS',
				couche: 'js/app/couche/protocole/couche',
				TMS: 'js/app/couche/protocole/TMS',
				vecteur: 'js/app/couche/protocole/vecteur',
				WMS: 'js/app/couche/protocole/WMS',
				WFS: 'js/app/couche/protocole/WFS',
				blanc: 'js/app/couche/protocole/blanc',
				OSM: 'js/app/couche/protocole/OSM',
				google: 'js/app/couche/protocole/google',
				marqueurs: 'js/app/couche/protocole/marqueurs',
				localisation: 'js/app/menu/localisation',
				googleItineraire: 'js/app/menu/googleItineraire',
				arborescence: 'js/app/menu/arborescence',
				itineraire: 'js/app/menu/itineraire',
				googleStreetView: 'js/app/menu/googleStreetView',
				impression: 'js/app/menu/impression',
				recherche: 'js/app/menu/recherche/recherche',
				rechercheGoogle: 'js/app/menu/recherche/rechercheGoogle',
				rechercheCadastreReno: 'js/app/menu/recherche/rechercheCadastreReno',
				rechercheGPS: 'js/app/menu/recherche/rechercheGPS',
				rechercheHQ: 'js/app/menu/recherche/rechercheHQ',
				rechercheInverseAdresse: 'js/app/menu/recherche/rechercheInverseAdresse',
				rechercheAdresse: 'js/app/menu/recherche/rechercheAdresse',
				rechercheLieu: 'js/app/menu/recherche/rechercheLieu',
				rechercheBorne: 'js/app/menu/recherche/rechercheBorne',
				contexteMenuCarte: 'js/app/contexteMenu/contexteMenuCarte',
				contexteMenu: 'js/app/contexteMenu/contexteMenu',
				contexteMenuArborescence: 'js/app/contexteMenu/contexteMenuArborescence',
				contexteMenuTable: 'js/app/contexteMenu/contexteMenuTable',
				analyseurConfig: 'js/app/analyseur/analyseurConfig',
				analyseurGML: 'js/app/analyseur/analyseurGML',
				analyseurGeoJSON: 'js/app/analyseur/analyseurGeoJSON',
				outilDessin2: 'js/app/outil/outilDessin2',
				outilEdition: 'js/app/outil/outilEdition',
				outilMenu: 'js/app/outil/outilMenu',
				outilDeselectWMS: 'js/app/outil/outilDeselectWMS',
				outilRapporterBogue: 'js/app/outil/outilRapporterBogue',
				outilItineraire: 'js/app/outil/outilItineraire',
				outilHistoriqueNavigation: 'js/app/outil/outilHistoriqueNavigation',
				outilPartagerCarte: 'js/app/outil/outilPartagerCarte',
				outilInfo: 'js/app/outil/outilInfo',
				outilAjoutWMS: 'js/app/outil/outilAjoutWMS',
				outilDessin: 'js/app/outil/outilDessin',
				outilExportGPX: 'js/app/outil/outilExportGPX',
				outilProfil: 'js/app/outil/outilProfil',
				outilZoomPreselection: 'js/app/outil/outilZoomPreselection',
				outilControleMenu: 'js/app/outil/outilControleMenu',
				outilSelection: 'js/app/outil/outilSelection',
				outilImportFichier: 'js/app/outil/outilImportFichier',
				outilZoomEtendueMaximale: 'js/app/outil/outilZoomEtendueMaximale',
				outilExportSHP: 'js/app/outil/outilExportSHP',
				outilTableSelection: 'js/app/outil/outilTableSelection',
				outilDeplacerCentre: 'js/app/outil/outilDeplacerCentre',
				outilAide: 'js/app/outil/outilAide',
				outilAssocierFichier: 'js/app/outil/outilAssocierFichier',
				outil: 'js/app/outil/outil',
				outilMesure: 'js/app/outil/outilMesure',
				outilDeplacement: 'js/app/outil/outilDeplacement',
				outilZoomRectangle: 'js/app/outil/outilZoomRectangle',
				barreOutils: 'js/app/barreOutils',
				navigateur: 'js/app/navigateur',
		        async : "libs/require/src/async",
		        noAMD : "libs/require/src/noAMD",
		        css : "libs/require/src/css",
		        text : "libs/require/src/text",
		        hbars : "libs/require/src/hbars",
		        handlebars: uri.librairies+'/handlebars/handlebars',
		        jquery: debug ? uri.librairies+"/jquery/jquery.min",
		        proj4js: 'libs/proj/Proj4js',
		        epsgDef: 'libs/proj/epsgDef',
		        build: "js/main-build"
		    }, 
		    shim: {
		        build: {
		            deps: ['requireAide']
		        },
		        epsgDef: {
		            deps: ['proj4js']
		        },
		        Handlebars: {
		            exports: 'Handlebars'
		        }
		    }
		});
	}
});