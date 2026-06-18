$(function(){
                $.ajax({
                    url: "/v1/wxauth/getUnionid2",
                    dataType: "json",
                    type: "get",           
                    success: function (returns) {
                        //  alert(JSON.stringify(returns));return false;
                        if (returns.code == 200) {
                          //  alert(returns.msg);
                           // layer.msg(returns.msg, {icon: 1});
                            location.href = returns.data.url;
                            return false;
                        }
                    }
                });
})