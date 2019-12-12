
/** 
 * HTMLImageElement - DOM Level 2
 */
HTMLImageElement = function(ownerDocument) {
    HTMLElement.apply(this, arguments);
};
HTMLImageElement.prototype = new HTMLElement;
__extend__(HTMLImageElement.prototype, {
    get alt(){
        return this.getAttribute('alt');
    },
    set alt(value){
        this.setAttribute('alt', value);
    },
    get height(){
        return this.getAttribute('height');
    },
    set height(value){
        this.setAttribute('height', value);
    },
    get isMap(){
        return this.hasAttribute('map');
    },
    set useMap(value){
        this.setAttribute('map', value);
    },
    get longDesc(){
        return this.getAttribute('longdesc');
    },
    set longDesc(value){
        this.setAttribute('longdesc', value);
    },
    get name(){
        return this.getAttribute('name');
    },
    set name(value){
        this.setAttribute('name', value);
    },
    get src(){
        return this.getAttribute('src');
    },
    set src(value){
        this.setAttribute('src', value);

        var event = document.createEvent();
        event.initEvent("load");
        this.dispatchEvent( event, false );
    },
    get width(){
        return this.getAttribute('width');
    },
    set width(value){
        this.setAttribute('width', value);
    },
    onload: function(event){
        __eval__(this.getAttribute('onload')||'', this)
    }
});
