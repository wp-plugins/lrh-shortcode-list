/* Javascript helpers for lrh_shortcode_information_manager */
function lrhsim_js_init() {

/* toggle hide/show of descriptions */
jQuery('.lrhsim_item_name').click(
  function(){
    jQuery(this).parent().children('.lrhsim_item_desc_hide').toggleClass('lrhsim_item_desc');
    return false;
  }
);

/* clicking on a "button" will insert that shortcode (and maybe parameters)
   into the memo. We're assuming that tinyMCE is being used */
jQuery('.lrhsim_item_button').click(
  function(){
    id=jQuery(this).attr('id');
    pp=lrhsim_data1[id];
    h=pp['b']+'<span id="lrhsim__caret">.</span>'+pp['a'];
    ed=tinyMCE.activeEditor;
    ed.focus();
    ok=ed.execCommand("mceInsertContent",false,h);
    ed.selection.select(ed.dom.select('span#lrhsim_caret')[0]);
    ed.dom.remove(ed.dom.select('span#lrhsim_caret')[0]);
    return false;
  }
);

}
