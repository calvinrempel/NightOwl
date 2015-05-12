var  app = angular.module('NightOwl', []).constant( 'API_CONFIG',{

	"API_URL":"http://nightowlAPI.local",

	"restrictions" : [
		"boolean",
		"member_list",
		"memberID",
		"percent"
	],

	"filters" : [
		"Key",
		"Value"
	],

	

	"dataCenters": [
		{
			"name": "Data Center 1", 
			"value" : "dc1",
			"prefixes":{ 
				"darklaunch" : {
					"dashboard": {
						"core" : {},

						"api":{
							"1":{
								"ANDROID" : {},
								"APPLE" : {}
							},

							"2":{
								"ANDROID" : {},
								"APPLE" : {}
							}
						},

						"apiold":{}
					},

					"service":{
						"social-communication" : {}
					}
				}
			},
		},
		{"name": "Data Center 2", "value" : "dc2"},
		{"name": "Data Center 3", "value" : "dc3"}
	],

	"tokenName": "NightOwlAuth",

	"auditFilters" : [
        "All",
        "Owner",
        "Code",
        "Message"
    ]
});
