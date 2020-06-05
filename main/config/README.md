# main/config
- Contem a configuração básica do sistema.

File: ``db.json``

	{
	    "_DB_HOST": "",
	    "_DB_NAME": "",
	    "_DB_USER": "",
	    "_DB_PSWD": ""
	}

File: ``site.json``

	{
	    "site_title": "Lista de Compras",
	    "site_subtitle": null,
	    "site_author": null,
	    "description": null,
	    "key_words": null,
	    "site_prefix": "lcp_"
	}

## main/config/menu/

File: ``public.json``

	{
	    "lista": {
	        "name": "lista",
	        "ctlr": "index",
	        "act": "main",
	        "title": "Gerencia lista de compras",
	        "permitions": null
	    },
	    "mercadorias": {
	        "name": "mercadorias",
	        "ctlr": "mcd",
	        "act": "main",
	        "title": "Gerencia produtos",
	        "permitions": null
	    },
	    "produtos": {
	        "name": "produtos",
	        "ctlr": "pdt",
	        "act": "main",
	        "title": "Gerencia produtos",
	        "permitions": null
	    },
	    "Corredores": {
	        "name": "Corredores",
	        "ctlr": "pdt_tp",
	        "act": "main",
	        "title": "Gerencia tipo de produto",
	        "permitions": null
	    },
	    "Embalagens": {
	        "name": "Embalagens",
	        "ctlr": "emb",
	        "act": "main",
	        "title": "Gerencia embalagens",
	        "permitions": null
    }
}