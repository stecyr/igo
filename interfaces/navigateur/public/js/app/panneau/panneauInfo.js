/** 
 * Module pour l'objet {@link Panneau.PanneauInfo}.
 * @module panneauInfo
 * @requires panneau 
 * @author Marc-André Barbeau, MSP
 * @version 1.0
 */
define(['panneau'], function(Panneau) {
     /** 
     * Création de l'object Panneau.PanneauInfo.
     * Pour la liste complète des paramètres, voir {@link Panneau}
     * @constructor
     * @name Panneau.PanneauInfo
     * @class Panneau.PanneauInfo
     * @alias panneauInfo:Panneau.PanneauInfo
     * @extends Panneau
     * @requires panneauInfo
     * @param {string} [options.id='info-panneau'] Identifiant du panneau.
     * @param {string} [options.position='sud'] Position du navigateur. Choix possibles: nord, sud, ouest, est
     * @param {string} [options.titre='Informations additionnelles'] Titre du Panneau
     * @param {Entier} [options.dimension=75] Dimension du panneau. Largeur si positionné à l'ouest ou à l'est. Hauteur pour le nord et le sud.
     * @param {Boolean} [options.ouvert=false] Ouvert à l'ouverture
     * @returns {Panneau.PanneauInfo} Instance de {@link Panneau.PanneauInfo}
    */
    function PanneauInfo(options){
        
        this.options = options || {};
        this._timeUpdateCtrl=0;
        var firstExpand=true;
        
        var epsgArray = new Array();
        
        for(var index in Proj4js.defs){
            if (Proj4js.defs.hasOwnProperty(index)) {
                
                var units = Proj4js.defs[index].match(/units=(\S*)/);
                var title = Proj4js.defs[index].match(/title=(\S*)/);
                
                var libelle = units!==null?index+'('+units[1]+')':index;
                var info = title!==null?title[1]:index;
                
                epsgArray.push([index, libelle, info]);
            }
        }
            
        var projStore = new Ext.data.ArrayStore({id:0,
            fields: ['code', 'libelle', 'info'],
            data: epsgArray
        });
   
        this.defautOptions.items = [/*{
            id: 'currentTimeComponent',
            title: 'Heure'
        },*/{
            id: 'currentMousePositionComponent',
            title: 'Position souris',
            items:[{
                    ctCls: 'x-form-field infoPosition'
            }]
        },{
            id: 'currentScaleComponent',
            title: 'Échelle',
             items:[{       
                   ctCls: 'x-form-field infoPosition'
            }]
        },{
            id: 'currentProjectionComponent',
            title: 'Projection',
            items:[{
                id: 'currentProjectionComboBox',
                tpl: '<tpl for="."><div ext:qtip="{libelle}. {info}" class="x-combo-list-item" style="text-align:center;">{libelle}</div></tpl>',
                xtype: 'combo',
                store: projStore ,
                valueField: 'code',
                displayField: 'libelle',
                typeAhead: true,
                triggerAction: 'all',
                selectOnFocus:true,
                lazyRender : true,
                mode:'local',
                editable:false,
                forceSelection:true,
                style: { 'text-align':'center'},
                listeners: {
                    select : function(combo, record, index){
                        var projDemande = combo.getValue();
                        Igo.nav.carte.definirProjectionAffichage(projDemande);                                                  
                    }
                }
            }]
        }];
        this.defautOptions.defaults = {
            split: true,
            height: 50,
            width: 200,
            minSize: 100,
            maxSize: 200,
            margins: '0 0 0 0'
        };
        
        this.defautOptions.position = 'sud';
        this.defautOptions.id = 'info-panneau';
        this.defautOptions.titre = 'Informations additionnelles';
        this.defautOptions.dimension = 75;
        this.defautOptions.minDimension = 75;
        this.defautOptions.maxDimension = 400;
        this.defautOptions.ouvert = false;
        this.defautOptions.listeners = {
            expand: function(panneau) {
                if (firstExpand){
                    firstExpand=false;
                    panneau.scope.initialiserEchelle();
                    panneau.scope.initialiserPositionPointeur();
                    panneau.scope.afficherProjectionAffichage();
                }
                //panneau.scope.activerHorloge();
            },
            collapse: function(panneau) {
                //panneau.scope.desactiverHorloge();
            },
            afterrender: function(panneau) {
                //panneau.scope.afficherHorloge();
            }
        };

    };

    PanneauInfo.prototype = new Panneau();
    PanneauInfo.prototype.constructor = PanneauInfo;
    
    /** 
     * Afficher l'heure dans le panneau
     * @method 
     * @private
     * @param {String} [heure] Heure à afficher
     * @name PanneauInfo#afficherHorloge
    */
    PanneauInfo.prototype.afficherHorloge = function(heure) {
        if (!this._currentTimeBody){
            var body = this._getPanel().get('currentTimeComponent').body; 
            if (body){
                this._currentTimeBody = body.dom;
            } else {
                return;
            };
        }
        heure = heure || this.obtenirHeure();
        this._currentTimeBody.innerHTML = heure;
    };
    
    /** 
     * Afficher la projection dans le panneau
     * @method 
     * @private
     * @name PanneauInfo#afficherProjection
    */
    PanneauInfo.prototype.afficherProjectionAffichage = function() {
        var combobox = this._getPanel().get("currentProjectionComponent")
                                .getComponent("currentProjectionComboBox");
        combobox.setValue(this.carte.obtenirProjectionAffichage());         
    };
    
    /** 
     * Obtenir l'heure actuelle formatée
     * @method 
     * @private
     * @name PanneauInfo#obtenirHeure
     * @returns {String} Heure formatée
    */
    PanneauInfo.prototype.obtenirHeure = function() {
        var currentDate = new Date();
        var currentHour = this.prefixerHeure(currentDate.getHours());
        var currentMinute = this.prefixerHeure(currentDate.getMinutes());
        var currentSecond = this.prefixerHeure(currentDate.getSeconds());  
        return currentHour + ':' + currentMinute + ':' + currentSecond;
    };
    
    /** 
     * Préfixe l'heure avec un 0 si requis.
     * @method 
     * @private
     * @param {String} t Heure à formater
     * @name PanneauInfo#prefixerHeure
     * @returns {String} Heure avec 2 chiffres
    */
    PanneauInfo.prototype.prefixerHeure = function(t){
        if (Number(t)<10){
            return "0"+t;
        }else{
            return t;
        }
    };
    
    /** 
     * Activer la mise à jour de l'heure à chaque seconde
     * @method 
     * @private
     * @name PanneauInfo#activerHorloge
    */
    PanneauInfo.prototype.activerHorloge = function(){
        var that = this;
        that.afficherHorloge();
        this._timeUpdateCtrl = setInterval(function(){that.afficherHorloge()},1000);
    };
    
    /** 
     * Désactiver la mise à jour de l'heure
     * @method 
     * @private
     * @name PanneauInfo#desactiverHorloge
    */
    PanneauInfo.prototype.desactiverHorloge = function(){
        clearInterval(this._timeUpdateCtrl);
        this.afficherHorloge(' ');
    };
    
    /** 
     * Activer la mise à jour du niveau de zoom de la carte
     * @method 
     * @private
     * @name PanneauInfo#initialiserEchelle
    */
    PanneauInfo.prototype.initialiserEchelle = function(){
        var body = this._getPanel().get('currentScaleComponent').body; 
        if (body){
            var currentScaleBody = body.dom;
            this.carte._getCarte().addControl(new OpenLayers.Control.Scale(currentScaleBody));
        };
    };

    /** 
     * Activer la mise à jour de la position de la souris sur la carte
     * @method 
     * @private
     * @name PanneauInfo#initialiserPositionPointeur
    */
    PanneauInfo.prototype.initialiserPositionPointeur = function(){
        var body = this._getPanel().get('currentMousePositionComponent').body; 
        if (body){
            var currentMousePositionBody = body.dom;
            this.carte._getCarte().addControl(new OpenLayers.Control.MousePosition({
                div: currentMousePositionBody,
                numDigits: 6
            }));
        };
        
        
    };

    return PanneauInfo;
    
});