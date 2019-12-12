/* 
 * $Id: map_obj.js 2413 2008-04-23 16:21:04Z christoph $
 * COPYRIGHT: (C) 2001 by ccgis. This program is free software under the GNU General Public
 * License (>=v2). Read the file gpl.txt that comes with Mapbender for details. 
 */

//global variables
var wms = [];
var wms_layer_count = 0;
var epsg_axis_order = [4326,4258,31466,31467,31468,31469,2166,2167,2168,2036,2044,2045,2065,2081,2082,2083,2085,2086,2091,2092,2093,2096,2097,2098,2105,2106,2107,2108,2109,2110,2111,2112,2113,2114,2115,2116,2117,2118,2119,2120,2121,2122,2123,2124,2125,2126,2127,2128,2129,2130,2131,2132,2169,2170,2171,2172,2173,2174,2175,2176,2177,2178,2179,2180,2193,2199,2200,2206,2207,2208,2209,2210,2211,2212,2319,2320,2321,2322,2323,2324,2325,2326,2327,2328,2329,2330,2331,2332,2333,2334,2335,2336,2337,2338,2339,2340,2341,2342,2343,2344,2345,2346,2347,2348,2349,2350,2351,2352,2353,2354,2355,2356,2357,2358,2359,2360,2361,2362,2363,2364,2365,2366,2367,2368,2369,2370,2371,2372,2373,2374,2375,2376,2377,2378,2379,2380,2381,2382,2383,2384,2385,2386,2387,2388,2389,2390,2391,2392,2393,2394,2395,2396,2397,2398,2399,2400,2401,2402,2403,2404,2405,2406,2407,2408,2409,2410,2411,2412,2413,2414,2415,2416,2417,2418,2419,2420,2421,2422,2423,2424,2425,2426,2427,2428,2429,2430,2431,2432,2433,2434,2435,2436,2437,2438,2439,2440,2441,2442,2443,2444,2445,2446,2447,2448,2449,2450,2451,2452,2453,2454,2455,2456,2457,2458,2459,2460,2461,2462,2463,2464,2465,2466,2467,2468,2469,2470,2471,2472,2473,2474,2475,2476,2477,2478,2479,2480,2481,2482,2483,2484,2485,2486,2487,2488,2489,2490,2491,2492,2493,2494,2495,2496,2497,2498,2499,2500,2501,2502,2503,2504,2505,2506,2507,2508,2509,2510,2511,2512,2513,2514,2515,2516,2517,2518,2519,2520,2521,2522,2523,2524,2525,2526,2527,2528,2529,2530,2531,2532,2533,2534,2535,2536,2537,2538,2539,2540,2541,2542,2543,2544,2545,2546,2547,2548,2549,2551,2552,2553,2554,2555,2556,2557,2558,2559,2560,2561,2562,2563,2564,2565,2566,2567,2568,2569,2570,2571,2572,2573,2574,2575,2576,2577,2578,2579,2580,2581,2582,2583,2584,2585,2586,2587,2588,2589,2590,2591,2592,2593,2594,2595,2596,2597,2598,2599,2600,2601,2602,2603,2604,2605,2606,2607,2608,2609,2610,2611,2612,2613,2614,2615,2616,2617,2618,2619,2620,2621,2622,2623,2624,2625,2626,2627,2628,2629,2630,2631,2632,2633,2634,2635,2636,2637,2638,2639,2640,2641,2642,2643,2644,2645,2646,2647,2648,2649,2650,2651,2652,2653,2654,2655,2656,2657,2658,2659,2660,2661,2662,2663,2664,2665,2666,2667,2668,2669,2670,2671,2672,2673,2674,2675,2676,2677,2678,2679,2680,2681,2682,2683,2684,2685,2686,2687,2688,2689,2690,2691,2692,2693,2694,2695,2696,2697,2698,2699,2700,2701,2702,2703,2704,2705,2706,2707,2708,2709,2710,2711,2712,2713,2714,2715,2716,2717,2718,2719,2720,2721,2722,2723,2724,2725,2726,2727,2728,2729,2730,2731,2732,2733,2734,2735,2738,2739,2740,2741,2742,2743,2744,2745,2746,2747,2748,2749,2750,2751,2752,2753,2754,2755,2756,2757,2758,2935,2936,2937,2938,2939,2940,2941,2953,2963,3006,3007,3008,3009,3010,3011,3012,3013,3014,3015,3016,3017,3018,3019,3020,3021,3022,3023,3024,3025,3026,3027,3028,3029,3030,3034,3035,3038,3039,3040,3041,3042,3043,3044,3045,3046,3047,3048,3049,3050,3051,3058,3059,3068,3114,3115,3116,3117,3118,3120,3126,3127,3128,3129,3130,3131,3132,3133,3134,3135,3136,3137,3138,3139,3140,3146,3147,3150,3151,3152,3300,3301,3328,3329,3330,3331,3332,3333,3334,3335,3346,3350,3351,3352,3366,3386,3387,3388,3389,3390,3396,3397,3398,3399,3407,3414,3416,3764,3788,3789,3790,3791,3793,3795,3796,3819,3821,3823,3824,3833,3834,3835,3836,3837,3838,3839,3840,3841,3842,3843,3844,3845,3846,3847,3848,3849,3850,3851,3852,3854,3873,3874,3875,3876,3877,3878,3879,3880,3881,3882,3883,3884,3885,3888,3889,3906,3907,3908,3909,3910,3911,4001,4002,4003,4004,4005,4006,4007,4008,4009,4010,4011,4012,4013,4014,4015,4016,4017,4018,4019,4020,4021,4022,4023,4024,4025,4026,4027,4028,4029,4030,4031,4032,4033,4034,4035,4036,4037,4038,4040,4041,4042,4043,4044,4045,4046,4047,4052,4053,4054,4055,4074,4075,4080,4081,4120,4121,4122,4123,4124,4125,4126,4127,4128,4129,4130,4131,4132,4133,4134,4135,4136,4137,4138,4139,4140,4141,4142,4143,4144,4145,4146,4147,4148,4149,4150,4151,4152,4153,4154,4155,4156,4157,4158,4159,4160,4161,4162,4163,4164,4165,4166,4167,4168,4169,4170,4171,4172,4173,4174,4175,4176,4178,4179,4180,4181,4182,4183,4184,4185,4188,4189,4190,4191,4192,4193,4194,4195,4196,4197,4198,4199,4200,4201,4202,4203,4204,4205,4206,4207,4208,4209,4210,4211,4212,4213,4214,4215,4216,4218,4219,4220,4221,4222,4223,4224,4225,4226,4227,4228,4229,4230,4231,4232,4233,4234,4235,4236,4237,4238,4239,4240,4241,4242,4243,4244,4245,4246,4247,4248,4249,4250,4251,4252,4253,4254,4255,4256,4257,4259,4260,4261,4262,4263,4264,4265,4266,4267,4268,4269,4270,4271,4272,4273,4274,4275,4276,4277,4278,4279,4280,4281,4282,4283,4284,4285,4286,4287,4288,4289,4291,4292,4293,4294,4295,4296,4297,4298,4299,4300,4301,4302,4303,4304,4306,4307,4308,4309,4310,4311,4312,4313,4314,4315,4316,4317,4318,4319,4322,4324,4327,4329,4339,4341,4343,4345,4347,4349,4351,4353,4355,4357,4359,4361,4363,4365,4367,4369,4371,4373,4375,4377,4379,4381,4383,4386,4388,4417,4434,4463,4466,4469,4470,4472,4475,4480,4482,4483,4490,4491,4492,4493,4494,4495,4496,4497,4498,4499,4500,4501,4502,4503,4504,4505,4506,4507,4508,4509,4510,4511,4512,4513,4514,4515,4516,4517,4518,4519,4520,4521,4522,4523,4524,4525,4526,4527,4528,4529,4530,4531,4532,4533,4534,4535,4536,4537,4538,4539,4540,4541,4542,4543,4544,4545,4546,4547,4548,4549,4550,4551,4552,4553,4554,4555,4557,4558,4568,4569,4570,4571,4572,4573,4574,4575,4576,4577,4578,4579,4580,4581,4582,4583,4584,4585,4586,4587,4588,4589,4600,4601,4602,4603,4604,4605,4606,4607,4608,4609,4610,4611,4612,4613,4614,4615,4616,4617,4618,4619,4620,4621,4622,4623,4624,4625,4626,4627,4628,4629,4630,4631,4632,4633,4634,4635,4636,4637,4638,4639,4640,4641,4642,4643,4644,4645,4646,4652,4653,4654,4655,4656,4657,4658,4659,4660,4661,4662,4663,4664,4665,4666,4667,4668,4669,4670,4671,4672,4673,4674,4675,4676,4677,4678,4679,4680,4681,4682,4683,4684,4685,4686,4687,4688,4689,4690,4691,4692,4693,4694,4695,4696,4697,4698,4699,4700,4701,4702,4703,4704,4705,4706,4707,4708,4709,4710,4711,4712,4713,4714,4715,4716,4717,4718,4719,4720,4721,4722,4723,4724,4725,4726,4727,4728,4729,4730,4731,4732,4733,4734,4735,4736,4737,4738,4739,4740,4741,4742,4743,4744,4745,4746,4747,4748,4749,4750,4751,4752,4753,4754,4755,4756,4757,4758,4759,4760,4761,4762,4763,4764,4765,4766,4767,4768,4769,4770,4771,4772,4773,4774,4775,4776,4777,4778,4779,4780,4781,4782,4783,4784,4785,4786,4787,4788,4789,4790,4791,4792,4793,4794,4795,4796,4797,4798,4799,4800,4801,4802,4803,4804,4805,4806,4807,4808,4809,4810,4811,4812,4813,4814,4815,4816,4817,4818,4819,4820,4821,4822,4823,4824,4839,4855,4856,4857,4858,4859,4860,4861,4862,4863,4864,4865,4866,4867,4868,4869,4870,4871,4872,4873,4874,4875,4876,4877,4878,4879,4880,4883,4885,4887,4889,4891,4893,4895,4898,4900,4901,4902,4903,4904,4907,4909,4921,4923,4925,4927,4929,4931,4933,4935,4937,4939,4941,4943,4945,4947,4949,4951,4953,4955,4957,4959,4961,4963,4965,4967,4969,4971,4973,4975,4977,4979,4981,4983,4985,4987,4989,4991,4993,4995,4997,4999,5012,5013,5017,5048,5105,5106,5107,5108,5109,5110,5111,5112,5113,5114,5115,5116,5117,5118,5119,5120,5121,5122,5123,5124,5125,5126,5127,5128,5129,5130,5132,5167,5168,5169,5170,5171,5172,5173,5174,5175,5176,5177,5178,5179,5180,5181,5182,5183,5184,5185,5186,5187,5188,5224,5228,5229,5233,5245,5246,5251,5252,5253,5254,5255,5256,5257,5258,5259,5263,5264,5269,5270,5271,5272,5273,5274,5275,5801,5802,5803,5804,5808,5809,5810,5811,5812,5813,5814,5815,5816,20004,20005,20006,20007,20008,20009,20010,20011,20012,20013,20014,20015,20016,20017,20018,20019,20020,20021,20022,20023,20024,20025,20026,20027,20028,20029,20030,20031,20032,20064,20065,20066,20067,20068,20069,20070,20071,20072,20073,20074,20075,20076,20077,20078,20079,20080,20081,20082,20083,20084,20085,20086,20087,20088,20089,20090,20091,20092,21413,21414,21415,21416,21417,21418,21419,21420,21421,21422,21423,21453,21454,21455,21456,21457,21458,21459,21460,21461,21462,21463,21473,21474,21475,21476,21477,21478,21479,21480,21481,21482,21483,21896,21897,21898,21899,22171,22172,22173,22174,22175,22176,22177,22181,22182,22183,22184,22185,22186,22187,22191,22192,22193,22194,22195,22196,22197,25884,27205,27206,27207,27208,27209,27210,27211,27212,27213,27214,27215,27216,27217,27218,27219,27220,27221,27222,27223,27224,27225,27226,27227,27228,27229,27230,27231,27232,27391,27392,27393,27394,27395,27396,27397,27398,27492,28402,28403,28404,28405,28406,28407,28408,28409,28410,28411,28412,28413,28414,28415,28416,28417,28418,28419,28420,28421,28422,28423,28424,28425,28426,28427,28428,28429,28430,28431,28432,28462,28463,28464,28465,28466,28467,28468,28469,28470,28471,28472,28473,28474,28475,28476,28477,28478,28479,28480,28481,28482,28483,28484,28485,28486,28487,28488,28489,28490,28491,28492,29701,29702,30161,30162,30163,30164,30165,30166,30167,30168,30169,30170,30171,30172,30173,30174,30175,30176,30177,30178,30179,30800,31251,31252,31253,31254,31255,31256,31257,31258,31259,31275,31276,31277,31278,31279,31281,31282,31283,31284,31285,31286,31287,31288,31289,31290,31700];

/**
 * global function to add wms to the wms-object
 * 
 * @param {String} wms_id the unique id of the wms 
 * @param {String} wms_version the version assumed from capabilities
 * @param {String} wms_title the title of the wms
 * @param {String} wms_abstract the abstract of the wms
 * @param {String} wms_getmap the url for map requests
 * @param {String} wms_getfeatureinfo the url for featureInof requests
 * @param {String} wms_getlegendurl the url for legend requests
 * @param {String} wms_filter a filter (deprecated)
 * @param {String} gui_wms_mapformat the image-format in the actual gui
 * @param {String} gui_wms_featureinfoformat the current format for featureInfos
 * @param {String} gui_wms_exceptionformat the exceptionformat for map requests
 * @param {String} gui_wms_epsg the current srs
 * @param {Integer} gui_wms_visible the visibility of this service
 * @param {Integer} gui_wms_opacity the initial display opacity in percent
 * @param {String} gui_wms_sldurl url to an actual sld
 */
function add_wms(
	wms_id,
	wms_version,
	wms_title,
	wms_abstract,
	wms_getmap,
	wms_getfeatureinfo,
	wms_getlegendurl,
	wms_filter,
	gui_wms_mapformat,
	gui_wms_featureinfoformat,
	gui_wms_exceptionformat,
	gui_wms_epsg,
	gui_wms_visible,
	gui_wms_opacity,
	gui_wms_sldurl){
	wms[wms.length] = new wms_const(
		wms_id,
		wms_version,
		wms_title,
		wms_abstract,
		wms_getmap,
		wms_getfeatureinfo,
		wms_getlegendurl,
		wms_filter,
		gui_wms_mapformat,
		gui_wms_featureinfoformat,
		gui_wms_exceptionformat,
		gui_wms_epsg,
		parseInt(gui_wms_visible, 10),
		parseInt(gui_wms_opacity),
		gui_wms_sldurl);
	wms_layer[wms.length - 1] = [];
}
/**
 * @class A class representing the wms
 *
 * @constructor
 * @param {String} wms_id the unique id of the wms 
 * @param {String} wms_version the version assumed from capabilities
 * @param {String} wms_title the title of the wms
 * @param {String} wms_abstract the abstract of the wms
 * @param {String} wms_getmap the url for map requests
 * @param {String} wms_getfeatureinfo the url for featureInof requests
 * @param {String} wms_getlegendurl the url for legend requests
 * @param {String} wms_filter a filter (deprecated)
 * @param {String} gui_wms_mapformat the image-format in the actual gui
 * @param {String} gui_wms_featureinfoformat the current format for featureInfos
 * @param {String} gui_wms_exceptionformat the exceptionformat for map requests
 * @param {String} gui_wms_epsg the current srs
 * @param {String} gui_wms_visible the visibility of this service
 * @param {Integer} gui_wms_opacity the initial display opacity in percent
 * @param {String} gui_wms_sldurl url to an actual sld
 * 
 */
function wms_const(  
	wms_id,
	wms_version,
	wms_title,
	wms_abstract,
	wms_getmap,
	wms_getfeatureinfo,
	wms_getlegendurl,
	wms_filter,
	gui_wms_mapformat,
	gui_wms_featureinfoformat,
	gui_wms_exceptionformat,
	gui_wms_epsg,
	gui_wms_visible,
	gui_wms_opacity,
	gui_wms_sldurl){
   
	if (!wms_id) {
		var id_ok = false;
		while (id_ok === false) {
			wms_id = "a"+Math.round(10000*Math.random());
			id_ok = true;
			for (var i=0; i < wms.length && id_ok === true; i++) {
				if (wms_id == wms[i].wms_id) { 
					id_ok = false;
				}
			}
		}
	}
	
	this.wms_id = wms_id;
	this.wms_version = wms_version;
	this.wms_title = wms_title;
	this.wms_currentTitle = wms_title;
	this.wms_abstract = wms_abstract;
	this.wms_getmap = wms_getmap;
	this.wms_getfeatureinfo = wms_getfeatureinfo;
	this.wms_getlegendurl = wms_getlegendurl;
	this.wms_filter = wms_filter;
	this.data_type = [];
	this.data_format = [];
	this.objLayer = [];
	this.gui_wms_mapformat = gui_wms_mapformat;
	this.gui_wms_featureinfoformat = gui_wms_featureinfoformat;
	this.gui_wms_exceptionformat = gui_wms_exceptionformat;
	this.gui_wms_epsg = gui_wms_epsg;
	this.gui_wms_visible = gui_wms_visible;
	this.gui_epsg = [];
	this.gui_epsg_supported = [];
	this.gui_minx = [];
	this.gui_miny = [];
	this.gui_maxx = [];
	this.gui_maxy = [];
	
	// opacity version
	this.gui_wms_mapopacity = gui_wms_opacity/100;
	// sld version
	this.gui_wms_sldurl = gui_wms_sldurl;    

	this.setCrs = function (options) {
		var crsIndex = $.inArray(options.source.srsCode, this.gui_epsg);
		if (crsIndex !== -1 && 
			typeof this.gui_minx[crsIndex] === 'number' &&
			typeof this.gui_miny[crsIndex] === 'number' &&	
			typeof this.gui_maxx[crsIndex] === 'number' &&
			typeof this.gui_maxy[crsIndex] === 'number'
			) {
			var sw = new Proj4js.Point(
				this.gui_minx[crsIndex], 
				this.gui_miny[crsIndex]
				);
			var ne = new Proj4js.Point(
				this.gui_maxx[crsIndex], 
				this.gui_maxy[crsIndex]
				);
			sw = Proj4js.transform(options.source, options.dest, sw);
			ne = Proj4js.transform(options.source, options.dest, ne);
			var extent = new Mapbender.Extent(sw.x, sw.y, ne.x, ne.y);
			this.setBoundingBoxBySrs(options.dest.srsCode, extent);
		}
		else {
			this.setBoundingBoxBySrs(options.dest.srsCode);
		}
	};
	
	this.setBoundingBoxBySrs = function (srs, ext) {
		for (var i = 0; i < this.gui_epsg.length && ext !== undefined; i++) {
			if (srs == this.gui_epsg[i]) {
				this.gui_minx[i] = parseFloat(ext.minx);
				this.gui_miny[i] = parseFloat(ext.miny);
				this.gui_maxx[i] = parseFloat(ext.maxx);
				this.gui_maxy[i] = parseFloat(ext.maxy);
				return i;
			}
		}
		this.gui_epsg.push(srs);
		this.gui_epsg_supported.push(false);
		
		if (ext !== undefined) {
			this.gui_minx.push(ext.minx);
			this.gui_miny.push(ext.miny);
			this.gui_maxx.push(ext.maxx);
			this.gui_maxy.push(ext.maxy);
		}
		
		return this.gui_epsg.length - 1;
	};
}

wms_const.prototype.getBoundingBoxBySrs = function (srs) {
	for (var i = 0; i < this.gui_epsg.length; i++) {
		if (srs == this.gui_epsg[i]) {
			var bbox_minx = parseFloat(this.gui_minx[i]);
			var bbox_miny = parseFloat(this.gui_miny[i]);
			var bbox_maxx = parseFloat(this.gui_maxx[i]);
			var bbox_maxy = parseFloat(this.gui_maxy[i]);
			if (bbox_minx !== null && !isNaN(bbox_minx) &&
				bbox_miny !== null && !isNaN(bbox_miny) &&
				bbox_maxx !== null && !isNaN(bbox_maxx) &&
				bbox_maxy !== null && !isNaN(bbox_maxy)
				) {
				return new Extent(bbox_minx, bbox_miny, bbox_maxx, bbox_maxy);
			}
		}
	}
	return null;
};

/**
 * rephrases the featureInfoRequest
 *
 * @param {Object} mapObj the mapbender mapObject of the wms  
 * @param {Point} clickPoint map-click position {@link Point}
 * @return featureInfoRequest, onlineresource + params
 * @type string
 */
wms_const.prototype.getFeatureInfoRequest = function(mapObj, clickPoint){	
	
	//check layers and querylayers first 
	var layers = this.getLayers(mapObj);
	var querylayers = this.getQuerylayers(mapObj);
	
	if(!layers || !querylayers){
		return false;
	}
	
	var rq = this.wms_getfeatureinfo;
	rq += mb_getConjunctionCharacter(this.wms_getfeatureinfo);
	if(this.wms_version === "1.0.0"){
		rq += "WMTVER=" + this.wms_version + "&REQUEST=feature_info";
	}
	else{
		rq += "VERSION=" + this.wms_version + "&REQUEST=GetFeatureInfo&SERVICE=WMS";
	}
	
	rq += "&LAYERS=" + layers.join(",");
	rq += "&QUERY_LAYERS=" + querylayers.join(",");
	rq += "&WIDTH=" + mapObj.getWidth();
	rq += "&HEIGHT=" + mapObj.getHeight();
	if(this.wms_version === "1.3.0"){
		rq += "&CRS=" + mapObj.getSRS();
	}else{	
		rq += "&SRS=" + mapObj.getSRS();
	}
	tmp_epsg = mapObj.getSRS();
	tmp_epsg = tmp_epsg.replace(/EPSG:/g,'');
	tmp_epsg = tmp_epsg.replace(/CRS:/g,'');
	//if(this.wms_version === "1.3.0" &&  tmp_epsg >= 4000 && tmp_epsg < 5000){
	// epsg_axis_order
	if(this.wms_version === "1.3.0" && epsg_axis_order.indexOf(parseInt(tmp_epsg))>= 0){ 		
                rq += "&BBOX=" + mapObj.getExtentSwitch();
	}else{
                rq += "&BBOX=" + mapObj.getExtent();
    }
	rq += "&STYLES=" + this.getLayerstyles(mapObj).join(",");
	rq += "&FORMAT=" + this.gui_wms_mapformat;
	rq += "&INFO_FORMAT=" + this.gui_wms_featureinfoformat;
	rq += "&EXCEPTIONS=" + this.gui_wms_exceptionformat;
	rq += "&X=" + clickPoint.x;
	rq += "&Y=" + clickPoint.y;
	if(mb_feature_count > 0){             
		rq += "&FEATURE_COUNT="+mb_feature_count;
	}
	rq += "&";
	// add vendor-specific
	var currentWms = this;
	for (var v = 0; v < mb_vendorSpecific.length; v++) {
		var functionName = 'setFeatureInfoRequest';
		var currentWms_wms_title = this.wms_title;
		var vendorSpecificString = eval(mb_vendorSpecific[v]);
		// if eval doesn't evaluate a function, the result is undefined.
		// Sometimes it is necessary not to evaluate a function, for
		// example if you want to change a variable from the current
		// scope (see mod_addSLD.php) 
		if (typeof(vendorSpecificString) != "undefined") {
			rq += vendorSpecificString + "&";
			try {
				if (this.wms_title == removeLayerAndStylesAffectedWMSTitle) {
					rq = url.replace(/LAYERS=[^&]*&/, '');
					rq = url.replace(/STYLES=[^&]*&/, '');
				}
			}
			catch (exc) {
				new Mb_warning(exc.message);
			}
		}
	}
	return rq;
};

/**
 * sets Opacity of WMS
 * 
 * @param {Integer} new opacity percentage value
 */
wms_const.prototype.setOpacity = function(opacity){
	//calc new opacity
	this.gui_wms_mapopacity = parseInt(opacity)/100;
	if(this.gui_wms_mapopacity>1||isNaN(this.gui_wms_mapopacity))
		this.gui_wms_mapopacity=1;
	if(this.gui_wms_mapopacity<0)
		this.gui_wms_mapopacity=0;
		
	if (this.gui_wms_visible > 0) {

		//get div id
		var divId = null;
		for (var i=0; i < wms.length; i++) {
			if (this.wms_id == wms[i].wms_id) { 
				var divId = 'div_'+i;
				break;
			}
		}
		if(!divId)
			return;	
		
		//TODO: check if mapframe1 is the right mapframe
		var ind = getMapObjIndexByName("mapframe1");
		var el = mb_mapObj[ind].getDomElement();
		wmsImage = el.ownerDocument.getElementById(divId);
		if (wmsImage != null) {
			wmsImage.style.opacity = this.gui_wms_mapopacity;
			wmsImage.style.MozOpacity = this.gui_wms_mapopacity;
			wmsImage.style.KhtmlOpacity = this.gui_wms_mapopacity;
			wmsImage.style.filter = "alpha(opacity=" + this.gui_wms_mapopacity*100 + ")";
		}
	}
}

/**
 * get all visible layers
 *
 * @return array of layernames 
 * @type string[]
 */
wms_const.prototype.getLayers = function(mapObj){
	var scale = null;
	if (arguments.length === 2) {
		scale = arguments[1];
	}
	try {
		//visibility of the wms
		var wmsIsVisible = (this.gui_wms_visible > 0);
		if(!wmsIsVisible){
			return [];
		}
		visibleLayers = [];
		for(var i=0; i< this.objLayer.length; i++){
			var isVisible = (this.objLayer[i].gui_layer_visible === 1);
			var hasNoChildren = (!this.objLayer[i].has_childs);
			if (isVisible && hasNoChildren){
				if(this.objLayer[i].checkScale(mapObj, scale)){
					visibleLayers.push(this.objLayer[i].layer_name);
				}
			}
		}
		if(visibleLayers.length === 0){
			return [];
		}
		return visibleLayers;
	}
	catch (e) {
		alert(e);
	}
	return [];
};

/**
 * get the actual style of all visible layers
 *
 * @return commaseparated list of actual layerstyles
 * @type string
 */
wms_const.prototype.getLayerstyles = function(mapObj){
	
	var layers = this.getLayers(mapObj);
	var layerstyles = '';
	var styles = [];
	if(layers){
		for(i = 0; i < layers.length; i++){
			var style = this.getCurrentStyleByLayerName(layers[i]);
			if(!style){
				style = '';
			}
			styles.push(style);
		}
		return styles;
	}
	return false;
};

/**
 * check if layer is parentLayer
 *
 * @param layername
 * @return the parent value of the given layer
 * @type integer
 */
wms_const.prototype.checkLayerParentByLayerName = function(layername){
	for(var i=0; i< this.objLayer.length; i++){
		if(this.objLayer[i].layer_name == layername){
			return this.objLayer[i].layer_parent;
		}
	}
};

/**
 * get the title of the current layer
 *
 * @param layername
 * @return the title of the given layer
 * @type string
 */
wms_const.prototype.getTitleByLayerName = function(layername){
	for(var i=0; i< this.objLayer.length; i++){
		if(this.objLayer[i].layer_name == layername){
			return this.objLayer[i].layer_title;
		}
	}
};

wms_const.prototype.getLayerByLayerName = function(layername){
	for(var i=0; i< this.objLayer.length; i++){
		if(this.objLayer[i].layer_name === layername){
			return this.objLayer[i];
		}
	}
};

/**
 * get the current style of the layer
 *
 * @param layername
 * @return the stylename of the given layer
 * @type string
 */
wms_const.prototype.getCurrentStyleByLayerName = function(layername){
	for(var i=0; i< this.objLayer.length; i++){
		var currentLayer = this.objLayer[i];
		if (currentLayer.layer_name === layername) {
			if (currentLayer.gui_layer_style === '' || currentLayer.gui_layer_style === null){
				return "";
			//				return false;
			}
			else{
				return currentLayer.gui_layer_style;	
			}
		}
	}
	return false;
};

/**
 * get the legendurl of the gui layer style
 *
 * @param stylename
 * @return the legendurl of the given style
 * @type string
 */
wms_const.prototype.getLegendUrlByGuiLayerStyle = function(layername,guiLayerStyle){
	for(var i=0; i< this.objLayer.length; i++){
		if(this.objLayer[i].layer_name == layername){
			if(this.objLayer[i].layer_style.length === 0){
				return false;
			}
			for(var k=0; k< this.objLayer[i].layer_style.length; k++){
				var legendUrl = '';
				if(guiLayerStyle == '' && k == 0){
					legendUrl = this.objLayer[i].layer_style[k].legendurl;
					if (this.gui_wms_sldurl !== "") {
						legendUrl += "&SLD="+escape(this.gui_wms_sldurl);
					}				
					if(legendUrl !=='' && legendUrl !== null && typeof(legendUrl) != 'undefined'){
						return legendUrl;
					}
					else {
						return false;
					}
				}else if(this.objLayer[i].layer_style[k].name == guiLayerStyle){
					legendUrl = this.objLayer[i].layer_style[k].legendurl;
					if (this.gui_wms_sldurl !== "") {
						legendUrl += "&SLD="+escape(this.gui_wms_sldurl);
					}				
					if(legendUrl !=='' && legendUrl !== null && typeof(legendUrl) != 'undefined'){
						return legendUrl;
					}
					else {
						return false;
					}
				}
			}
		}
	}
	return false;
};

/**
 * get all querylayers
 *
 * @return array of layernames
 * @type string[]
 */
wms_const.prototype.getQuerylayers = function(map){
	var currentScale = map.getScale();
	queryLayers = [];
	for(var i=0; i< this.objLayer.length; i++){
		
		var isVisible = this.objLayer[i].gui_layer_visible === 1 && 
		this.objLayer[i].gui_layer_minscale <= currentScale &&
		(this.objLayer[i].gui_layer_maxscale >= currentScale ||
			this.objLayer[i].gui_layer_maxscale === 0);
		if(this.objLayer[i].gui_layer_querylayer === 1 && !this.objLayer[i].has_childs && isVisible){
			queryLayers.push(this.objLayer[i].layer_name);
		}
	}
	if(queryLayers.length === 0){
		return false;
	}
	return queryLayers;
};

/**
 * get a layer Object by layer_pos
 * 
 * @param int payer_pos layer_pos of layer you want to get
 * @return object layer
 */

wms_const.prototype.getLayerByLayerPos = function(layer_pos){
	for(var i=0;i<this.objLayer.length;i++){
		if(this.objLayer[i].layer_pos == layer_pos) {
			return this.objLayer[i];
		}
	}
	return null;
};

wms_const.prototype.getLayerById = function(id){
	for(var i=0;i<this.objLayer.length;i++){
		if(parseInt(this.objLayer[i].layer_uid, 10) === parseInt(id,10)) {
			return this.objLayer[i];
		}
	}
	return null;
};
/**
 * get the state of sublayers from a specified layer
 * 
 * @param int layer_id of the parent layer
 * @param String type "visible" or "querylayer"
 * @return int -1 if state differs else the state
 */

wms_const.prototype.getSublayerState = function(layer_id, type){
	var i;
	var state=-1,value;
	for(i = 0; i < this.objLayer.length; i++){
		if(this.objLayer[i].layer_id==layer_id) {
			break;
		}
	}
	
	//go throught sublayers
	for(var j = i+1; j < this.objLayer.length; j++){
		if(this.objLayer[i].parent_layer == this.objLayer[j].parent_layer) {
			break;
		}
		if(type == "visible") {
			value = this.objLayer[j].gui_layer_visible;
		}
		else if(type == "querylayer") {
			value = this.objLayer[j].gui_layer_querylayer;
		}
		if(state == -1) {
			state = value;
		}
		if(state != value) {
			return -1;
		}
	}
	
	return state;
};
/**
 * handle change of visibility / quaryability of a layer
 * 
 * @param string layer_name of layer to handle
 * @param string type of change ("visible" or "querylayer")
 * @param int value of the change
 */
wms_const.prototype.handleLayer = function(layer_name, type, value){
	var i;
	var found = false;
	for(i = 0; i < this.objLayer.length; i++){
		if(this.objLayer[i].layer_name==layer_name) {
			found = true;
			break;
		}
	}
	// layer not found
	if (!found) {
		return;
	}
	
	//Set visibility/queryability of Layer and Sublayers
	for(var j = i; j < this.objLayer.length; j++){
		if (i != j && this.objLayer[i].layer_parent >= this.objLayer[j].layer_parent) {
			break;
		}
		if(type == "visible") {
			this.objLayer[j].gui_layer_visible = parseInt(value, 10);
		}
		else if(type=="querylayer" && this.objLayer[j].gui_layer_queryable) {
			this.objLayer[j].gui_layer_querylayer = parseInt(value, 10);
		}
	}

	//Update visibility/queryability of parent layer
	var parentLayer = this.getLayerByLayerPos(this.objLayer[i].layer_parent);
	if(parentLayer){
		var state = this.getSublayerState(parentLayer.layer_id, type);
		if(state!=-1){
			if(type == "visible") {
				this.objLayer[j].gui_layer_visible = state;
			}
			else if(type=="querylayer" && this.objLayer[j].gui_layer_queryable) {
				this.objLayer[j].gui_layer_querylayer = state;
			}
		}
	}
};


/**
 * move a layer (with his sublayers) up or down
 * 
 * @param int layerId layer_id of layer to move
 * @param boolean moveUp true to move up or false to move down
 * @return boolean success
 */

wms_const.prototype.moveLayer = function(layerId, moveUp){
	var iLayer=-1;
	var i;
	
	//find layer to move
	for(i=0;i<this.objLayer.length;i++){
		if(this.objLayer[i].layer_id==layerId){
			iLayer=i;
			break;
		}
	}
	if(iLayer==-1) {
		return false;
	}
	
	var upperLayer = -1;
	var lowerLayer = -1;
	
	//find layer to swap position with
	var parentLayer = this.objLayer[iLayer].layer_parent;	
	if(moveUp){
		lowerLayer = iLayer;
		
		//find previous layer on same level
		for(i=iLayer-1;i>0;i--){
			if(parentLayer == this.objLayer[i].layer_parent){
				upperLayer = i;
				break;
			}
		}
		if(upperLayer == -1){
			//alert("The Layer you selected is already on top of parent Layer/WMS");
			return false;
		}
	}
	else{
		upperLayer = iLayer;
		
		//find next layer on same level
		for(i=iLayer+1;i<this.objLayer.length;i++){
			if(parentLayer == this.objLayer[i].layer_parent){
				lowerLayer = i;
				break;
			}
		}
		if(lowerLayer == -1){
			//alert("The Layer you selected is already on bottom of parent Layer/WMS");
			return false;
		}
	}
	
	//calc number of layers to move down
	var layersDown = lowerLayer - upperLayer;
	
	//get number of layers to move up
	for(i=lowerLayer+1; i<this.objLayer.length; i++){
		if(parentLayer == this.objLayer[i].layer_parent){
			break;
		}
	}
	var layersUp = i - lowerLayer;
	
	//do moving
	var temp = [];
	for(i=0;i<layersDown+layersUp;i++){
		temp[temp.length]=this.objLayer[upperLayer+i];
	}
	for(i=0;i<layersUp;i++){
		this.objLayer[upperLayer+i]=temp[i+layersDown];
	}
	for(i=0;i<layersDown;i++){
		this.objLayer[upperLayer+layersUp+i]=temp[i];
	}

	return true;
};

function wms_add_data_type_format(datatype,dataformat){
	var insertDataFormat = true;
	for (var i = 0 ; i < wms[wms.length-1].data_type.length ; i ++) {
		if (wms[wms.length-1].data_type[i] == datatype && wms[wms.length-1].data_format[i] == dataformat) {
			insertDataFormat = false;
		}
	}
	if (insertDataFormat === true) {
		wms[wms.length-1].data_type[wms[wms.length-1].data_type.length] = datatype;
		wms[wms.length-1].data_format[wms[wms.length-1].data_format.length] = dataformat;
	}
}
function wms_addSRS(epsg,minx,miny,maxx,maxy){
	wms[wms.length-1].gui_epsg[wms[wms.length-1].gui_epsg.length] = epsg;
	wms[wms.length-1].gui_epsg_supported[wms[wms.length-1].gui_epsg_supported.length] = true;
	wms[wms.length-1].gui_minx[wms[wms.length-1].gui_minx.length] = minx;
	wms[wms.length-1].gui_miny[wms[wms.length-1].gui_miny.length] = miny;
	wms[wms.length-1].gui_maxx[wms[wms.length-1].gui_maxx.length] = maxx;
	wms[wms.length-1].gui_maxy[wms[wms.length-1].gui_maxy.length] = maxy;
}
function wms_addLayerStyle(styleName, styleTitle, count, layerCount, styleLegendUrl, styleLegendUrlFormat){
	//TODO for debug purposes:	
	//alert(styleName+":"+styleTitle+":"+count+":"+layerCount+":"+styleLegendUrl+":"+styleLegendUrlFormat);
	//var test = wms.length-1;
	//alert("add layer style["+count+"] for layer["+layerCount+"] for wms["+test+"]:"+styleLegendUrl);
	var currentLayer = wms[wms.length-1].objLayer[layerCount]; 

	if (currentLayer) {
		currentLayer.layer_style[count] = {};
		currentLayer.layer_style[count].name = styleName;
		currentLayer.layer_style[count].title = styleTitle;
		currentLayer.layer_style[count].legendurl = styleLegendUrl;
		currentLayer.layer_style[count].legendurlformat = styleLegendUrlFormat;
	}
}
//TODO: add layerstyle handling....
//layer
function wms_add_layer(
	layer_parent,
	layer_uid,
	layer_name,
	layer_title,
	layer_dataurl_href,
	layer_pos,
	layer_queryable,
	layer_minscale,
	layer_maxscale,
	layer_metadataurl,
	gui_layer_wms_id,
	gui_layer_status,
	gui_layer_style,
	gui_layer_selectable,
	gui_layer_visible,
	gui_layer_queryable,
	gui_layer_querylayer,
	gui_layer_minscale,
	gui_layer_maxscale,
	gui_layer_wfs_featuretype,
	gui_layer_title){
                      
	wms[wms.length-1].objLayer[wms[wms.length-1].objLayer.length] = new wms_layer(
		layer_parent,
		layer_uid,
		layer_name,
		layer_title,
		layer_dataurl_href,
		layer_pos,
		layer_queryable,
		layer_minscale,
		layer_maxscale,
		layer_metadataurl,
		gui_layer_wms_id,
		gui_layer_status,
		gui_layer_style,
		parseInt(gui_layer_selectable, 10),
		parseInt(gui_layer_visible, 10),
		parseInt(gui_layer_queryable, 10),
		parseInt(gui_layer_querylayer, 10),
		parseInt(gui_layer_minscale, 10),
		parseInt(gui_layer_maxscale, 10),
		gui_layer_wfs_featuretype,
		gui_layer_title );
	var parentLayer = wms[wms.length-1].getLayerByLayerPos(parseInt(layer_parent, 10));
	if(parentLayer) {
		parentLayer.has_childs = true;
	}
}
function layer_addEpsg(epsg,minx,miny,maxx,maxy){
	var j = wms[wms.length-1].objLayer.length-1;
	var k = wms[wms.length-1].objLayer[j].layer_epsg.length;
	var currentLayer = wms[wms.length-1].objLayer[j];
	currentLayer.layer_epsg[k]={};
	currentLayer.layer_epsg[k].epsg = epsg;
	currentLayer.layer_epsg[k].minx = minx;
	currentLayer.layer_epsg[k].miny = miny;
	currentLayer.layer_epsg[k].maxx = maxx;
	currentLayer.layer_epsg[k].maxy = maxy;
}
function wms_layer(
	layer_parent,
	wms_layer_uid,
	layer_name,
	layer_title,
	layer_dataurl_href,
	layer_pos,
	layer_queryable,
	layer_minscale,
	layer_maxscale,
	layer_metadataurl,
	gui_layer_wms_id,
	gui_layer_status,
	gui_layer_style,
	gui_layer_selectable,
	gui_layer_visible,
	gui_layer_queryable,
	gui_layer_querylayer,
	gui_layer_minscale,
	gui_layer_maxscale,
	gui_layer_wfs_featuretype,
	gui_layer_title){
	this.layer_id = wms_layer_count;
	this.layer_uid = wms_layer_uid;
	this.layer_parent = layer_parent;
	this.layer_name = layer_name;
	this.layer_title = layer_title;
	this.gui_layer_title = gui_layer_title || layer_title;
	this.layer_currentTitle = this.gui_layer_title;
	this.layer_dataurl_href = layer_dataurl_href;
	this.layer_pos = layer_pos;
	this.layer_queryable = layer_queryable;
	this.layer_minscale = layer_minscale;
	this.layer_maxscale = layer_maxscale;
	this.layer_metadataurl = layer_metadataurl;
	this.layer_epsg = [];
	this.gui_layer_wms_id = gui_layer_wms_id;
	this.gui_layer_status = gui_layer_status;
	this.gui_layer_selectable = gui_layer_selectable;
	this.gui_layer_visible = gui_layer_visible;
	this.gui_layer_queryable = gui_layer_queryable;
	this.gui_layer_querylayer = gui_layer_querylayer;
	this.gui_layer_minscale = gui_layer_minscale;
	this.gui_layer_maxscale = gui_layer_maxscale;
	this.gui_layer_style = gui_layer_style;
	this.gui_layer_wfs_featuretype = gui_layer_wfs_featuretype;
	this.has_childs = false;
	this.layer_style = [];
	wms_layer_count++;
}
/**
 * check the scale of the layer
 *
 * @param Object mapObj the mapbender mapObject of the layer
 * @return boolean if the layer is in scale or not
 * @type boolean
 */
wms_layer.prototype.checkScale = function(mapObj){
	var currentScale = parseInt(mapObj.getScale(), 10);
	if (arguments.length === 2 && arguments[1] !== null) {
		currentScale = arguments[1];
	}
	var minScale = parseInt(this.gui_layer_minscale, 10);
	var maxScale = parseInt(this.gui_layer_maxscale, 10);
	if(minScale === 0 && maxScale === 0){
		return true;
	}
	if(minScale > currentScale || (maxScale !== 0 && maxScale < currentScale)) {
		return false;
	}	
	return true;
};
/**
 * set visibility of the layer
 * @param boolean visible visibility on/off
 */
wms_layer.prototype.setVisible = function(visible){
	this.gui_layer_visible = parseInt(visible, 10);
};

/**
 * set queryability of the layer
 * @param boolean queryable queryability on/off
 */

wms_layer.prototype.setQueryable = function(queryable){
	this.gui_layer_querylayer = parseInt(queryable, 10);
};
