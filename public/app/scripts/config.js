var config = {
	API_URL:"http://nightowl.local",

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