const jry_wb_keycode_backspace	 = 8;
const jry_wb_keycode_tab		 = 9;
const jry_wb_keycode_clear		 = 12;
const jry_wb_keycode_enter		 = 13;
const jry_wb_keycode_shift		 = 16;
const jry_wb_keycode_control	 = 17;
const jry_wb_keycode_alt		 = 18;
const jry_wb_keycode_pause		 = 19;
const jry_wb_keycode_capslock	 = 20;
const jry_wb_keycode_escape		 = 27;
const jry_wb_keycode_space		 = 32;
const jry_wb_keycode_prior		 = 33;
const jry_wb_keycode_next		 = 34;
const jry_wb_keycode_end		 = 35;
const jry_wb_keycode_home		 = 36;
const jry_wb_keycode_left		 = 37;
const jry_wb_keycode_up			 = 38;
const jry_wb_keycode_right		 = 39;
const jry_wb_keycode_down		 = 40;
const jry_wb_keycode_select		 = 41;
const jry_wb_keycode_print		 = 42;
const jry_wb_keycode_execute	 = 43;
const jry_wb_keycode_insert		 = 45;
const jry_wb_keycode_delete		 = 46;
const jry_wb_keycode_help		 = 47;
const jry_wb_keycode_0			 = 48;
const jry_wb_keycode_1			 = 49;
const jry_wb_keycode_2			 = 50;
const jry_wb_keycode_3			 = 51;
const jry_wb_keycode_4			 = 52;
const jry_wb_keycode_5			 = 53;
const jry_wb_keycode_6			 = 54;
const jry_wb_keycode_7			 = 55;
const jry_wb_keycode_8			 = 56;
const jry_wb_keycode_9			 = 57;
const jry_wb_keycode_a			 = 65;
const jry_wb_keycode_b			 = 66;
const jry_wb_keycode_c			 = 67;
const jry_wb_keycode_d			 = 68;
const jry_wb_keycode_e			 = 69;
const jry_wb_keycode_f			 = 70;
const jry_wb_keycode_g			 = 71;
const jry_wb_keycode_h			 = 72;
const jry_wb_keycode_i			 = 73;
const jry_wb_keycode_j			 = 74;
const jry_wb_keycode_k			 = 75;
const jry_wb_keycode_l			 = 76;
const jry_wb_keycode_m			 = 77;
const jry_wb_keycode_n			 = 78;
const jry_wb_keycode_o			 = 79;
const jry_wb_keycode_p			 = 80;
const jry_wb_keycode_q			 = 81;
const jry_wb_keycode_r			 = 82;
const jry_wb_keycode_s			 = 83;
const jry_wb_keycode_t			 = 84;
const jry_wb_keycode_u			 = 85;
const jry_wb_keycode_v			 = 86;
const jry_wb_keycode_w			 = 87;
const jry_wb_keycode_x			 = 88;
const jry_wb_keycode_y			 = 89;
const jry_wb_keycode_z			 = 90;
const jry_wb_keycode_win		 = 91;
const jry_wb_keycode_0_			 = 96;
const jry_wb_keycode_1_			 = 97;
const jry_wb_keycode_2_			 = 98;
const jry_wb_keycode_3_			 = 99;
const jry_wb_keycode_4_			 = 100;
const jry_wb_keycode_5_			 = 101;
const jry_wb_keycode_6_			 = 102;
const jry_wb_keycode_7_			 = 103;
const jry_wb_keycode_8_			 = 104;
const jry_wb_keycode_9_			 = 105;
const jry_wb_keycode_f1			 = 112;
const jry_wb_keycode_f2			 = 113;
const jry_wb_keycode_f3			 = 114;
const jry_wb_keycode_f4			 = 115;
const jry_wb_keycode_f5			 = 116;
const jry_wb_keycode_f6			 = 117;
const jry_wb_keycode_f7			 = 118;
const jry_wb_keycode_f8			 = 119;
const jry_wb_keycode_f9			 = 120;
const jry_wb_keycode_f10		 = 121;
const jry_wb_keycode_f11		 = 122;
const jry_wb_keycode_f12		 = 123;
var jry_wb_short_cut_list=[];
function jry_wb_set_shortcut(code,func)
{
	if(typeof code!='object')
		code=[code];
	code=code.sort();
	var ctrl	=code.indexOf(jry_wb_keycode_control);	if(ctrl!=-1)	code.splice(ctrl,1)	,ctrl	=true;else ctrl=false;
	var alt		=code.indexOf(jry_wb_keycode_alt);		if(alt!=-1)		code.splice(alt,1)	,alt	=true;else alt=false;
	var shift	=code.indexOf(jry_wb_keycode_shift);	if(shift!=-1)	code.splice(shift,1),shift	=true;else shift=false;
	for(var i=0;i<jry_wb_short_cut_list.length;i++)
		if(jry_wb_short_cut_list[i].ctrl==ctrl&&jry_wb_short_cut_list[i].alt==alt&&jry_wb_short_cut_list[i].shift==shift&&jry_wb_short_cut_list[i].code.toString()==code.toString())
			return jry_wb_short_cut_list[i].func=func;
	jry_wb_short_cut_list.push({'ctrl':ctrl,'alt':alt,'shift':shift,'code':code,'func':func});
	jry_wb_short_cut_list.sort(function(a,b){return b.code.length-a.code.length});
}
var jry_wb_short_cut_key_buf=[];
var jry_wb_short_cut_timer=null;
window.onkeyup=function(e)
{
	e=window.event||e;
	if(jry_wb_short_cut_timer!=null)clearTimeout(jry_wb_short_cut_timer);
	jry_wb_short_cut_timer=setTimeout(function(){jry_wb_short_cut_timer=null;jry_wb_short_cut_key_buf=[];},500);
	jry_wb_short_cut_key_buf.push(e.keyCode);
	jry_wb_short_cut_key_buf=jry_wb_short_cut_key_buf.sort();
	var ctrl	=jry_wb_short_cut_key_buf.indexOf(jry_wb_keycode_control);	if(ctrl!=-1)	jry_wb_short_cut_key_buf.splice(ctrl,1)	,ctrl	=true;else ctrl=false;
	var alt		=jry_wb_short_cut_key_buf.indexOf(jry_wb_keycode_alt);		if(alt!=-1)		jry_wb_short_cut_key_buf.splice(alt,1)	,alt	=true;else alt=false;
	var shift	=jry_wb_short_cut_key_buf.indexOf(jry_wb_keycode_shift);	if(shift!=-1)	jry_wb_short_cut_key_buf.splice(shift,1),shift	=true;else shift=false;	
	for(var i=0;i<jry_wb_short_cut_list.length;i++)
		if(jry_wb_short_cut_list[i].ctrl==e.ctrlKey&&jry_wb_short_cut_list[i].alt==e.altKey&&jry_wb_short_cut_list[i].shift==e.shiftKey&&jry_wb_short_cut_list[i].code.toString()==jry_wb_short_cut_key_buf.toString())
			return setTimeout(function(){if(jry_wb_short_cut_timer!=null)clearTimeout(jry_wb_short_cut_timer);jry_wb_short_cut_timer=null;jry_wb_short_cut_key_buf=[];jry_wb_short_cut_list[i].func(e)},1),e.preventDefault(),false;
};
