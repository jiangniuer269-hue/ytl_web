//获取图形验证码
function refreshCode(){
		                $.ajax({
		                    url: "/v1/Login/getImgCode",
		                    dataType: "json",
		                    type: "post",           
		                    success: function (returns) {
		                        //  alert(JSON.stringify(returns));return ;
		                        if (returns.code == 200) {
		                          //  alert(returns.msg);                       
		                           $('#imagecode').attr('src',returns.data.imgurl)                     
		                            return false;
		                        }
		                    }
	                });
}
//获取url请求参数
function getQueryVariable(variable){
       var query = window.location.search.substring(1);
       var vars = query.split("&");
       for (var i=0;i<vars.length;i++) {
               var pair = vars[i].split("=");
               if(pair[0] == variable){return pair[1];}
       }
       return(false);
}

//退出登录
function loginout(){
	    var agent_id = getQueryVariable("agent_id");
		window.location.href ='http://' +window.location.host+'?loginout=1&clean=1&agent_id='+agent_id+'#/loginbyphone';	
		return;
}

function addLogout(){
	if(!document.getElementById('loginout')){
	  // console.log('不存在');
	   $('.dddd').parent().append('<button onclick="loginout()" id="loginout" type="button" class="el-button login-button el-button--default is-round" style="background-color: rgb(243, 228, 112);display:block;color:#A63D1C;margin:0 auto;margin-top:380px"><span>退出登录</span></button>');
	}
}

function changeiframeheight(){
	if(document.getElementById('videoCanvas')){
		console.log('存在videoCanvas');
	    document.getElementById('videoCanvas').style.height='225px';
	}
}

$(function(){
 var timer = setInterval(function(){
				changeiframeheight();
   },1000);
    changeiframeheight();
	$('.login-button').parent().append('<a id="tourist"  style="font-size:20px; margin-top: 20px;display:block;color:#A63D1C;text-decoration:underline;float:left">游客登录</a>');
    $('.login-button').parent().append('<a href ="./updatepwd.html"  style="font-size:20px; margin-left: 200px;margin-top: 20px;display:block;color:#A63D1C;text-decoration:underline;">修改密码</a>');
    var agent_id = getQueryVariable("agent_id");
	//是否需要验证码
	    $.ajax({
		   url: "/v1/Login/needcode?agent_id="+agent_id,
		   dataType: "json",
		   type: "get",       
		   success: function (returns) {
		          // alert(JSON.stringify(returns));return ;
		     if (returns.code == 200) {
		             // alert(returns.data.url); 
		       $("<div><input  name='imgcode' id='imgcode'  value='' placeholder='请输入图形验证码' class='el-input__inner' autocomplete='on' autofocus='autofocus' style='border-radius:28px;width:170px;margin-bottom: 20px;'><img onclick='refreshCode()' id='imagecode' src='' style='float:right'></div>").insertBefore('.login-button');
	var cururl = window.location.href;
	var clean = getQueryVariable("clean");
	if((clean !=1 && cururl.indexOf('loginbyphone') !=-1 )){
		var agent_id = getQueryVariable("agent_id");
		window.location.href ='http://' +window.location.host+'?loginout=1&clean=1&agent_id='+agent_id+'#/loginbyphone';
		return;
	}
	           refreshCode();
				$('.login-button').click(function(){
					var imagecodevalue = $('#imgcode').val();
					if(imagecodevalue == ''){
						alert('请填写图形验证码');return;
					}
				    $.ajax({
					   url: "/v1/Login/checkImgCode",
					   dataType: "json",
					   type: "post",   
					   data:{
						 'imagecode':imagecodevalue          
					   },     
					   success: function (returns) {
					         //  alert(JSON.stringify(returns));return ;
					         if (returns.code == 200) {
					            //  alert(returns.msg); 
					                          		                        
					            return ;
					        }
					   }
				   });
				})
	
 
			       //检测图形验证码
			    	var timer = setInterval(function(){					 
					  if(document.getElementById('imgcode')){
						var imagecodevalue = $('#imgcode').val();	
				        console.log('imagecodevalue.length:'+imagecodevalue.length);
				      if(imagecodevalue.length ==4){
				       $.ajax({
								   url: "/v1/Login/checkImgCode",
								   dataType: "json",
								   type: "post",   
								   data:{
									 'imagecode':imagecodevalue          
								   },     
								   success: function (returns) {
								         //  alert(JSON.stringify(returns));return ;
								         if (returns.code == 200) {
								            //  alert(returns.msg); 
								          ///  $('.login-button').click(); 
								             window.clearInterval(timer)                     		                        
								             return ;
								        }
								   }
				      });
				     }
				     }
					},1000);
		        }
		   }
	   })


		
	//游客登录
	$('#tourist').click(function(){
		var agent_id = getQueryVariable("agent_id");
	    $.ajax({
		   url: "/v1/login/getLoginToken",
		   dataType: "json",
		   type: "post",   
		   data:{
			    'agent_id':agent_id    
		   },     
		   success: function (returns) {
		         //  alert(JSON.stringify(returns));return ;
		         if (returns.code == 200) {
		             // alert(returns.data.url); 
		            window.location.href = returns.data.url; 		                        
		            return ;
		        }
		   }
	   });
	})
	//缓存登录	
	/*$.ajax({
		   url: "v1/Login/getLoginInfo",
		   dataType: "json",
		   type: "post",   
		   data:{
			        
		   },     
		   success: function (returns) {
		         //  alert(JSON.stringify(returns));return ;
		         if (returns.code == 200) {
		             // alert(returns.data.url); 
		            $(".el-input__inner").eq(0).val(returns.data.username);	  
		            $(".el-input__inner").eq(1).val(returns.data.secret);	  
		           // console.log('zhuang'+zhuang);                    
		            return ;
		        }
		   }
	   });*/
})

