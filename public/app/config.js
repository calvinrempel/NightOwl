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
		"dc1"
	]
};