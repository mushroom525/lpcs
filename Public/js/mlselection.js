/* 多级选择相关函数，如地区选择，分类选择
 * multi-level selection
 */

/* 商品分类选择函数 */
function gcategoryInit(divId)
{
    $("#" + divId + " > select").get(0).onchange = gcategoryChange; // select的onchange事件
    window.onerror = function(){return true;}; //屏蔽jquery报错
    $("#" + divId + " .edit_gcategory").click(gcategoryEdit); // 编辑按钮的onclick事件
}

function gcategoryChange()
{
    // 删除后面的select
    $(this).nextAll("select").remove();

    // 计算当前选中到id和拼起来的name
    var selects = $(this).siblings("select").andSelf();
    var id = 0;
    var names = new Array();
    for (i = 0; i < selects.length; i++)
    {
        sel = selects[i];
        if (sel.value > 0)
        {
            id = sel.value;
            name = sel.options[sel.selectedIndex].text;
            names.push(name);
        }
    }
    $(".mls_id").val(id);
    $(".mls_name").val(name);
    $(".mls_names").val(names.join("\t"));

    // ajax请求下级分类
    if (this.value > 0)
    {
        var _self = this;
        var url = 'http://www.heeyhome.com/lpcs/Admin/goods/ajax_cate';
        $.getJSON(url, {'pid':this.value}, function(data){
            if (data)
            {
                if (data.length > 0)
                {
                    $("<select name='cate_id[]' style='height:23px'><option value='0'>请选择...</option></select>").change(gcategoryChange).insertAfter(_self);
                    for (i = 0; i < data.length; i++)
                    {
                        $(_self).next("select").append("<option value='" + data[i].cate_id + "'>" + data[i].cate_name + "</option>");
                    }
                }
            }
            else
            {
                alert(data.msg);
            }
        });
    }
}

function gcategoryEdit()
{
    $(this).siblings("select").show();
    $(this).siblings("span").andSelf().remove();
}
