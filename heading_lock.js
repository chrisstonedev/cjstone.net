/**
 * Created by Christopher on 6/6/2015.
 */

var header = document.querySelector('header'), header_height = getComputedStyle(header).height.split('px')[0],
    title = header.querySelector('nav'), title_height = getComputedStyle(title).height.split('px')[0],
    fix_class = 'is_fixed';
function stickyScroll() {
    if (window.pageYOffset > (header_height - title_height) / 2) {
        title.classList.add(fix_class);
    }
    if (window.pageYOffset < (header_height - title_height) / 2) {
        title.classList.remove(fix_class);
    }
}
window.addEventListener('scroll', stickyScroll, false);
if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize");
}