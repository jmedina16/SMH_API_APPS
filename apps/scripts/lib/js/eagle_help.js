var HelpMap = {
	getName		: function() {
		var key = location.hash.split("#")[1];	
		return this.map[key] || "";
	},
	map			: {
		"section11"				: "_Toc302932132",
		"section12" 			: "_Toc302932141",
		"section115" 			: "_Toc302932137",
		"section113" 			: "_Toc302932135",
		"section114" 			: "_Toc302932135",
		"section23" 			: "_Toc302932139",
		"section123" 			: "_Toc302932139",
//		"section11" 			: "_Toc302932130",
		"section13" 			: "_Toc302932154",
		"section14" 			: "_Toc302932159",
		"section151" 			: "_Toc302932164",
		"section152" 			: "_Toc302932165",
		"section17" 			: "_Toc302932166",
		"section31" 			: "_Toc302932192",
		"section32" 			: "_Toc302932196",
		"section21" 			: "_Toc302932176",
		"section22" 			: "_Toc302932178",
//		"section23" 			: "_Toc302932182",
		"section231" 			: "_Toc302932184",
		"section24" 			: "_Toc302932185",
		"section242" 			: "_Toc302932187",
//		"section21" 			: "_Toc302932148",
		"section26" 			: "_Toc302932190",
//		"section11" 			: "_Toc302932202",
		"Content Reports"		: "_Toc302932205",
//		"section11" 			: "_Toc302932206",
//		"section11" 			: "_Toc302932207",
//		"section11" 			: "_Toc302932208",
//		"section11" 			: "_Toc302932209",
//		"section11" 			: "_Toc302932206",
//		"section11" 			: "_Toc302932207",
//		"section11" 			: "_Toc302932208",
//		"section11" 			: "_Toc302932209",
		"Community Reports" 	: "_Toc302932210",
//		"section11" 			: "_Toc302932211",
//		"section11" 			: "_Toc302932212",
//		"section11" 			: "_Toc302932213",
//		"section11" 			: "_Toc302932213",
//		"section11" 			: "_Toc302932212",
		"section41" 			: "_Toc302932215",
		"section412" 			: "_Toc302932216",
		"section42" 			: "_Toc302932220",
		"section422" 			: "_Toc302932222",
		"upload_menu" 			: "_Toc302932121",
		"entry_metadata" 		: "_Toc302932142",
		"entry_tumbnails" 		: "_Toc302932143",
		"entry_accesscontrol" 	: "_Toc302932144",
		"entry_scheduling" 		: "_Toc302932145",
		"entry_flavors" 		: "_Toc302932146",
		"entry_content" 		: "_Toc302932147",
		"entry_customdata" 		: "_Toc302932148",
		"entry_distribution" 	: "_Toc302932149",
		"entry_captions" 		: "_Toc302932150",
		"entry_ads" 			: "_Toc302932151",
		"entry_related" 		: "_Toc302932152",
		"entry_clips" 			: "_Toc302932153",
		"Section11" 			: "_Toc302932130",
		"upload_menu" 			: "_Toc302932121",
		"desktop_upload" 		: "_Toc302932123",
//		"section11" 			: "_Toc302932130",
		"flavor_upload" 		: "_Toc302932146",
		"flavor_import" 		: "_Toc302932146",
		"flavor_link" 			: "_Toc302932146",
		"flavor_dropfolder" 	: "_Toc302932146",
		"entry_replacement" 	: "_Toc302932146",
		"section16" 			: "_Toc302932172",
		"bulk_uploads" 			: "_Toc302932173",
		"drop_folders" 			: "_Toc302932174",
		"transcoding_profiles" 	: "_Toc302932186"
	}
};

if( document.location.hash ) 
	window.location.hash = '#' + HelpMap.getName();