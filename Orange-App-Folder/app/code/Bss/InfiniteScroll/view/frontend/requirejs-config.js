var config = {
    paths: {
    	'bss/goup':        	'Bss_InfiniteScroll/js/jquery.goup.min',
        'bss/ias' : 		'Bss_InfiniteScroll/js/jquery-ias.min',
        'bss/lazyload' : 		'Bss_InfiniteScroll/js/jquery.lazyload',
    },
	shim: {
		'bss/ias': {
			deps: ['jquery']
		},
		'bss/goup': {
			deps: ['jquery']
		},
		'bss/lazyload': {
			deps: ['jquery']
		},
	}
};