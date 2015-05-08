var  app = angular.module('NightOwl', []);

var NIGHTOWL_CONFIG = {
	API_URL:"http://nightowlAPI.local",

	restrictions : [
		"boolean",
		"member_list",
		"memberID",
		"percent"
	],

	filters : [
		"Key",
		"Value"
	],

	prefixes: [
		"darklaunch"
	],

	dataCenters: [
		{"name": "Data Center 1", "value" : "dc1"},
		{"name": "Data Center 2", "value" : "dc2"},
		{"name": "Data Center 3", "value" : "dc3"},
		{"name": "All", "value" : "all"}
	],

	tokenName: "NightOwlAuth",

	searches : [
        "Owner",
        "Code",
        "Message"
    ]
};