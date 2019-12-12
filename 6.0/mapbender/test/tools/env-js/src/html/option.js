
/**
 * HTMLOptionElement - DOM Level 2
 */
HTMLOptionElement = function(ownerDocument) {
    HTMLInputCommon.apply(this, arguments);
};
HTMLOptionElement.prototype = new HTMLInputCommon;
__extend__(HTMLOptionElement.prototype, {
    get defaultSelected(){
        return this.getAttribute('defaultSelected');
    },
    set defaultSelected(value){
        this.setAttribute('defaultSelected',value);
    },
    get index(){
        var options = this.parent.childNodes;
        for(var i; i<options.length;i++){
            if(this == options[i])
                return i;
        }
        return -1;
    },
    get label(){
        return this.getAttribute('label');
    },
    set label(value){
        this.setAttribute('label',value);
    },
    get selected(){
        return (this.getAttribute('selected')=='selected');
    },
    set selected(value){
        if(this.defaultSelected===null && this.selected!==null){
            this.defaultSelected = this.selected;
        }
        var selectedValue = (value ? 'selected' : '');
        if (this.getAttribute('selected') == selectedValue) {
            // prevent inifinite loops (option's selected modifies 
            // select's value which modifies option's selected)
            return;
        }
        this.setAttribute('selected', selectedValue);
        if (value) {
            // set select's value to this option's value (this also 
            // unselects previously selected value)
            this.parentNode.value = this.value;
        } else {
            // if no other option is selected, select the first option in the select
            var i, anythingSelected;
            for (i=0; i<this.parentNode.options.length; i++) {
                if (this.parentNode.options[i].selected) {
                    anythingSelected = true;
                    break;
                }
            }
            if (!anythingSelected) {
                this.parentNode.value = this.parentNode.options[0].value;
            }
        }

    },
    get text(){
         return ((this.nodeValue === null) ||  (this.nodeValue ===undefined)) ?
             this.innerHTML :
             this.nodeValue;
    },
    get value(){
        return ((this.getAttribute('value') === undefined) || (this.getAttribute('value') === null)) ?
            this.text :
            this.getAttribute('value');
    },
    set value(value){
        this.setAttribute('value',value);
    }
});

