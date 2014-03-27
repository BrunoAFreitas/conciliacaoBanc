// JavaScript Document	
	//funcao para abrir um popup em qualquer lugar da tela		
		POP_win=2
		POP_client=1
		POP_tot=0
		
		function popup(url,w,h,halign,valign,parent){
		
			var t=0,l=0
			box=new getbox(parent)
			
			switch(halign){
				case "":
				case "left":
					l=0
				break;
				case "right":
					l=box.width-w
				break;
				case "center":
					l=(box.width-w)/2
				break;
				default:
					if(typeof(halign)=="string"){
						if(halign.search(/%/g)!="-1"){
							l=(box.width-w)*parseInt(halign)/100
						}else{
							l=parseInt(halign)
						}
					}else if(typeof(halign)=="number"){
						l=halign
					}
			}
		
			switch(valign){
				case "":
				case "top":
					t=0
				break;
				case "bottom":
					t=box.height-h
				break;
				case "center":
					t=(box.height-h)/2
				break;
				default:
					if(typeof(valign)=="string"){
						if(valign.search(/%/g)!="-1"){
							t=(box.height-h)*parseInt(valign)/100
						}else{
							t=parseInt(valign)
						}
					}else if(typeof(valign)=="number"){
						t=valign
					}
			}
			t+=box.top
			l+=box.left
		
			window.open(url,"","width="+w+",height="+h+",top="+t+",left="+l+",scrollbars=yes")
		}
		
		function getbox(parent){
			if(typeof(parent)=="undefined")parent=0
			this.top=0
			this.left=0
			this.width=screen.width
			this.height=screen.height
			if(parent==2){
				this.top=window.screenTop
				this.left=window.screenLeft
				this.width=document.body.offsetWidth
				this.height=document.body.offsetHeight
			}else if(parent==1){
				this.width=screen.availWidth
				this.height=screen.availHeight
			}else if(parent==0){
				this.width=screen.width
				this.height=screen.height
			}else{
				this.top=parent.screenTop
				this.left=parent.screenLeft 
				this.width=parent.document.body.offsetWidth 
				this.height=parent.document.body.offsetHeight
			}
		}
		
		
		
		//formata 5000 em 5.000,00 
		/*function formata_valor(numero) {
			//numero = obj.value;		
			numero = numero.replace(".","");
			numero = numero.replace(".","");
			numero = numero.replace(".","");
			numero = numero.replace(",",".");	
			numero_formatado = (parseFloat(numero) * 1000)/10;
			numero_formatado = parseFloat(numero_formatado);
			numero_formatado = numero_formatado.toString();
			var tam = numero_formatado.length;
			if ( tam <= 1 ){
			numero_formatado = '0,0' + numero_formatado.substr( tam - 2, tam ); }
			if ( tam == 2 ){
			numero_formatado = '0,' + numero_formatado.substr( tam - 2, tam ); }
			if ( (tam > 2) && (tam <= 5) ){
			numero_formatado = numero_formatado.substr( 0, tam - 2 ) + ',' + numero_formatado.substr( tam - 2, tam ); }
			if ( (tam >= 6) && (tam <= 8) ){
			numero_formatado = numero_formatado.substr( 0, tam - 5 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 9) && (tam <= 11) ){
			numero_formatado = numero_formatado.substr( 0, tam - 8 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 12) && (tam <= 14) ){
			numero_formatado = numero_formatado.substr( 0, tam - 11 ) + '.' + numero_formatado.substr( tam - 11, 3 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ; }
			if ( (tam >= 15) && (tam <= 17) ){
			numero_formatado = numero_formatado.substr( 0, tam - 14 ) + '.' + numero_formatado.substr( tam - 14, 3 ) + '.' + numero_formatado.substr( tam - 11, 3 ) + '.' + numero_formatado.substr( tam - 8, 3 ) + '.' + numero_formatado.substr( tam - 5, 3 ) + ',' + numero_formatado.substr( tam - 2, tam ) ;}
			//obj.value = numero_formatado;		
			return numero_formatado;		
		} */
		
		//converte campos do formato 5000 para 5.000,00;
		function converte(obj){
			if(obj.value != ""){
				var valor = obj.value;
				valor = (valor);
				obj.value = formata_valor(valor);
			}
		}