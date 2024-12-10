import Masonry from 'masonry-layout';
$.fn.masonry = function(opts) {
    for(let elm of this) {
        new Masonry(elm, opts);
    }
};