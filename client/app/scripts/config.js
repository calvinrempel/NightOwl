var NIGHTOWL_CONFIG = {
	API_URL:"http://nightowlAPI.local",

	restrictions : [
		"boolean",
		"member_list",
		"memberID",
		"percent"
	],

	trees : [
		{"name":"dashboard", "subtrees":["core"]},
		{"name":"service", "subtrees":["social-communication"]},
		{"name":"test"}
	]
};