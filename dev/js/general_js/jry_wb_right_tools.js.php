<?php if(false){ ?><script><?php } ?>
var jry_wb_right_tools = new function()
{
	this.init=function()
	{
		if(typeof this.body!='undefined')
			return;
		this.body=document.createElement("div");document.body.appendChild(this.body);
		this.body.style.right=0;
		this.body.style.height='100%';
		this.body.style.top='0';
		this.body.style.position='fixed';
		this.body.style.width='40px';
		this.body.style.zIndex='9999';
		this.body.style.transitionDuration='1s';
	};
	this.list=[];
	this.add=function(button)
	{
		if(typeof this.body=='undefined')
			this.init();
		if(button.parentElement!=null)
			button.parentElement.removeChild(button);
		this.body.appendChild(button);
		button.style.position='absolute';
		this.list[this.list.length]=button;
		this.fresh();
	};
	this.fresh=function()
	{
		for(var i=0;i<this.list.length;i++)
		{
			this.list[i].style.position='absolute';			
			this.list[i].style.top=this.body.clientHeight*0.05+this.body.clientHeight*(i+1)/(this.list.length+3);
		}
	};
	this.left=function(x)
	{
<?php if(JRY_WB_BACKGROUND_MUSIC_SWITCH){ ?>
		if(typeof jry_wb_background_music.backgroundmusic_control!='undefined')jry_wb_background_music.backgroundmusic_control.style.right=x+jry_wb_background_music.backgroundmusic_icon.clientWidth;
<?php } ?>
		this.body.style.right=x;
	};
	this.right=function()
	{
<?php if(JRY_WB_BACKGROUND_MUSIC_SWITCH){ ?>
		if(typeof jry_wb_background_music.backgroundmusic_control!='undefined')jry_wb_background_music.backgroundmusic_control.style.right=jry_wb_background_music.backgroundmusic_icon.clientWidth;
<?php } ?>
		this.body.style.right=0;		
	};
	jry_wb_add_onresize(()=>{this.fresh();});
	jry_wb_add_onload(()=>{this.init()});
};
<?php if(false){ ?></script><?php } ?>