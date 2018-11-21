/**
 * @Author : qiaoweizhen <qiaoweizhen@gmail.com>
 * @Github : https://www.github.com/qiaoweizhen
 * @Date   : 2016/8/19
 */

//默认配置
var CONFIG = {
    province: '#province',
    city: '#city',
    area: '#area'
};

//省市区选择方法
function selector(userConfig,address) {

    //加载用户配置
    if (userConfig) {
        CONFIG = userConfig;
    }

    //获取省市区对象
    var province_obj = $(CONFIG.province);
    var city_obj = $(CONFIG.city);
    var area_obj = $(CONFIG.area);

    //初始化省市区
    province_obj.html('').append('<option value="">请选择</option>');
    city_obj.html('').append('<option value="">请选择</option>');
    area_obj.html('').append('<option value="">请选择</option>');

    //初始化省份列表
    for (i in DATA) {
        if (DATA[i].pid == 0) {
			if(address.province  == DATA[i].name){
				province_obj.append('<option selected value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
				changecity(DATA[i].id);
			}else{
				province_obj.append('<option value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
			}
        }
    }

    //省份切换事件
    $(CONFIG.province).on('change', function () {
        var cur_province_id = $(this).val();
        //将市、区初始化
        city_obj.html('').append('<option value="">请选择</option>');
        area_obj.html('').append('<option value="">请选择</option>');
        if (cur_province_id != '') {
            for (i in DATA) {
                if (DATA[i].pid == cur_province_id) {
					if(address.city  == DATA[i].name){
						city_obj.append('<option selected value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}else{
						city_obj.append('<option value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}
                }
            }
        }
    });

    //城市切换事件
    $(CONFIG.city).on('change', function () {
        var cur_city_id = $(this).val();
        //将区初始化
        area_obj.html('').append('<option value="">请选择</option>');
        if (cur_city_id != '') {
            for (i in DATA) {
                if (DATA[i].pid == cur_city_id) {
					if(address.area  == DATA[i].name){
						area_obj.append('<option selected value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}else{
						area_obj.append('<option value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}
                }
            }
        }
    });
	
	function changecity(id){
		var cur_province_id = id;
        //将市、区初始化
        city_obj.html('').append('<option value="">请选择</option>');
        area_obj.html('').append('<option value="">请选择</option>');
        if (cur_province_id != '') {
            for (i in DATA) {
                if (DATA[i].pid == cur_province_id) {
					if(address.city  == DATA[i].name){
						city_obj.append('<option selected value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
						changearea(DATA[i].id);
					}else{
						city_obj.append('<option value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}
                }
            }
        }
	}
	
	function changearea(id){
		var cur_city_id = id;
        //将区初始化
        area_obj.html('').append('<option value="">请选择</option>');
        if (cur_city_id != '') {
            for (i in DATA) {
                if (DATA[i].pid == cur_city_id) {
					if(address.area  == DATA[i].name){
						area_obj.append('<option selected value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}else{
						area_obj.append('<option value="' + DATA[i].id + '">' + DATA[i].name + '</option>');
					}
                }
            }
        }
	}
}