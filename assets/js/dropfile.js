;
(function($) {
	$.fn.dropfile = function(param, callback_success, callback_error)
	{
		/* Définition de l'objet principal */
		var objdrop = {
			/* Attributs */
			'message'    : 'Drop',
			'formname'   : 'file',
			'formvalue'  : null,
			'uploadpath' : 'php/connector.php',
			'onefile'    : true,

			/* Méthodes */
			setListeners : function(o) {

				//Vérification qu'il n'y ai pas déjà de dropzone
				if( $('.dropzone').length == 0 ) {

					console.info('Ajout des listener');
					//Désactive les évènements en dehors de la zone
					$(document).on(
					{
						dragenter : function(event)
						{
							event.stopPropagation();
		    				event.preventDefault();
						},
						dragover : function(event)
						{
							event.stopPropagation();
		    				event.preventDefault();
						},
						drop : function(event)
						{
							event.stopPropagation();
		    				event.preventDefault();
						},
					});

					//Création des écoutes sur les zones de drag n drop
					$('body').on(
					{
						dragenter : function(event)
						{
							event.preventDefault(); 
						},
						dragover : function(event)
						{
							event.preventDefault();
							$(this).addClass('dropover');
						},
						dragleave : function(event)
						{
							event.preventDefault();
							$(this).removeClass('dropover');
						},
						drop : function(event)
						{
							event.preventDefault();
							$(this).removeClass('dropover');

							//Récupération de la zone drop
							var area = $(this);

							//Récupération d'un formulaire contenant tous les fichiers drop
							var files = event.originalEvent.dataTransfer.files;

							//Vérification que l'on a bien récupéré un tableau
							$.isArray(files) 
							{
								//Parcours de chaque fichier
								$.each(files, function(key, file)
								{
									//Création d'un formulaire
									var fileform = drop.makeForm(file);

									//Upload du fichier
									drop.uploadToServeur(drop.uploadpath, {file: fileform, action: 'create'}, area);

									// Si mode onefile
									if(drop.onefile) return false;
								});
							}
						}
					}, '.dropzone');
				}
			},
			createDropZone : function(area)	{

				console.info('Création de la zone de "Drop"');
				//Ajout du message d'instruction
				area.addClass('dropzone');

				//Si il n'y a pas de contenu dans la zone, on le défini
				if( area.find('span').length == 0 )
				{
					console.info('Ajout du contenu de la zone de "Drop"');
					$('<span>').addClass('dropmessage').append(drop.message).appendTo(area);
					$('<span>').addClass('uploadprogress hidden').appendTo(area);
				}
			},
			makeForm(file) {

		        //Création d'un objet FormData 
		        var fd = new FormData();
		        fd.append('file', file);

		        return fd;
			},

			uploadToServeur(url, data, area) {		        
		        $.ajax(
				{
					xhr: function() 
					{  
					   	//On récupère une instance de paramètrage Ajax
						var setXhr       = $.ajaxSettings.xhr();

						//Récupération de la zone de progression
						var zoneprogress = $(area).find('.uploadprogress');

		                //Si l'upload est disponible
		                if(setXhr.upload)
		                {
		                    //Ajout d'un évènement sur la progression d'envoi et supprime le comportement par défaut
		                    setXhr.upload.addEventListener('progress', function(event)
	                    	{
	                    		//Appel de la fonction de progression du téléversement
	                    		drop.makeProgress(event, zoneprogress);
	                    	}, false);
		                }
		                return setXhr;
		        	},
					url        : url,
					headers	   : { fileaction: data.action,  filename: data.filename },
					type       : 'POST',
					contentType: false,
					processData: false,
					dataType   : 'json',
					data       : data.file,
					success    : function(result){ drop.ended(area, result); },
					error      : function(result){ alert(result);},
				});
			},
			ended(area, datareceive) {

				//console.log(datareceive);
				if(datareceive.status == 'success') {

					// Vérification qu'il y a un callback
					if (typeof callback_success == 'function') {
				        callback_success(datareceive); // On l'appelle en lui passant les données recues
				    } else {
				    	alert("Fichier uploadé :" + datareceive.name);
				    }

				} else {

					// Vérification qu'il y a un callback
					if (typeof callback_error == 'function') {
				        callback_error(datareceive); // On l'appelle en lui passant les données recues
				    } else {
				    	alert(datareceive.info);
				    }
				}
			},
			makeProgress(event, zoneprogress) {
				
				if(event.lengthComputable)
				{
					zoneprogress.removeClass('hidden');

					//Création des valeurs pour le calcul de la progression du chargement
					var sizeTotal  = event.total;
					var sizeLoaded = event.loaded;

			        //Calcul du poucentage avec arrondissement
			        var percentage = Math.ceil((sizeLoaded * 100) / sizeTotal);

			        //Ajustement du poucentage
			        zoneprogress.css({ left: percentage + '%' });

			        //Upload terminé
			        if(percentage >= 100)
			        {
			           zoneprogress.addClass('hidden');
			           zoneprogress.css({ left: '0%' });
			        }
		    	}  
			},

		};

		//Fusion defaults & Options
		var drop = $.extend(objdrop, param);

		//Ajout des écoutes du plugin
		drop.setListeners();

		//On applique le plugin à tous les élements séléctionné
		return this.each(function() {
			
			//Création de la zone de drop pour chaque élement
			drop.createDropZone($(this));
		});
	}
})(jQuery);