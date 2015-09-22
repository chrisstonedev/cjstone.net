/**
 * Created by Christopher on 6/21/2015.
 */

var header=document.querySelector('header'),
    header_height=getComputedStyle(header).height.split('px')[0],
    title=header.querySelector('p'),
    title_height=getComputedStyle(title).height.split('px')[0],
    nav=document.querySelector('nav'),
    nav_height=getComputedStyle(nav).height.split('px')[0],
    fix_class='is_fixed',
    header_shadow='header_shadow';
function stickyScroll(){
    if(window.pageYOffset>(header_height-title_height)/2) {
        title.classList.add(fix_class);
    } else if(window.pageYOffset<(header_height-title_height)/2){
        title.classList.remove(fix_class);
    }
    if(window.pageYOffset>header_height-title_height) {
        title.classList.add(header_shadow);
    } else if(window.pageYOffset<header_height-title_height){
        title.classList.remove(header_shadow);
    }
    if(window.pageYOffset>+header_height + (nav_height/2)) {
        nav.classList.add(fix_class);
    } else if(window.pageYOffset<+header_height + (nav_height/2)) {
        nav.classList.remove(fix_class);
    }
}
window.addEventListener('scroll',stickyScroll,false);
if(document.location.search.match(/type=embed/gi)){
    window.parent.postMessage("resize");
}