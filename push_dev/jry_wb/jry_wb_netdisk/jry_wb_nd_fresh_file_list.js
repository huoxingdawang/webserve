function jry_wb_nd_fresh_file_list(qiangzhi)
{
	if(jry_nd_share_mode_flag)
		return;
	if(qiangzhi==undefined)
		qiangzhi=false;
	jry_nd_load_count++;
	if(qiangzhi)
		jry_wb_cache.delete('nd_file_list');
	var file_list;
	if(jry_wb_compare_time(jry_wb_cache.get_last_time('nd_file_list').split(/ /)[0],jry_wb_login_user.jry_wb_nd_extern_information.lasttime)<0||qiangzhi)
		jry_wb_sync_data_with_server('nd_file_list',jry_wb_message.jry_wb_host+'jry_wb_netdisk/jry_nd_get_information.php?action=file_list&lasttime='+jry_wb_cache.get_last_time('nd_file_list',qiangzhi),null,function(a)
		{
			return this.buf.file_id==a.file_id;
		},function(data)
		{
			file_list=data;
			var flag=false;
			if(file_list!=null)
				for(var i=0;i<file_list.length;i++)
					if(file_list[i].delete)
						file_list.splice(i,1),flag=true,i--;
					else
						file_list[i].name=file_list[i].name.replace(/\/37/g,'&');
			if(flag)
				jry_wb_cache.set('nd_file_list',file_list);
			jry_nd_load_count--;
			if(!jry_nd_share_mode_flag)
			{
				jry_nd_file_list=file_list;			
				if(jry_nd_load_count==0)
					jry_wb_nd_show_files_by_dir(decodeURI(document.location.hash)!=''?decodeURI(document.location.hash).split('#')[1]:'/');
			}
		});
	else
		jry_nd_load_count--,file_list=jry_wb_cache.get('nd_file_list');
	if(!jry_nd_share_mode_flag)
		jry_nd_file_list=file_list;
	return file_list;
}