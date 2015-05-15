var  app = angular.module('NightOwl', []).constant( 'API_CONFIG',{

	"API_URL":"http://nightowlAPI.local",

	"loadingID" : "#loading",

	"restrictions" : [
		"boolean",
		"member_list",
		"memberID",
		"percent"
	],

	"filters" : [
		"All",
		"Key",
		"Value",
		"Date",
		"Owner",
		"Description"
	],

	"defaultFilters" : {
		"prefix" : "darklaunch",
		"dataCenter" : "dc1",
		"filterBy" : "All",
		"filter" : ""
	},

	"metadata" : {
		"restrictions":{
			"boolean":{"type":"select", "values":["true","false"]},
			"member_list":{"type":"text"},
			"memberID":{"type":"text"},
			"percent":{"type":"text"}
		}
	},

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
        "Most Recent",
        "All",
        "User",
        "Code",
        "Message"
    ]
});
