<div id="{{$id}}">
    <div class="row custom-menu-wraper">
        <div class="col-md-2" style="width:360px">
            <div class="custom-phone-box">
                <h3 class="custom-nav-title">{{$label}}</h3>
                <p class="custom-phone-body">内容区域</p>
                <div class="custom-phone-footer">
                    <ul class="pre_menu_list">
                        <li class="jsMenu pre_menu_item "
                            v-bind:class="{ selected: zindex == index , active: zindex == index&&subZindex ==-1 }"
                            v-for="item,index in menu.button">
                            <a href="javascript:void(0);" class="custom-a-button pre_menu_link"
                               v-on:click="selectedMenuItem(index)">
                                <?php echo"{{ item.name }}" ?>
                            </a>
                            <div class="sub_pre_menu_box">
                                <ul class="sub_pre_menu_list">
                                    <li class="sub_pre_menu_item" v-bind:class="{ active: subZindex == v }"
                                        v-if="item.sub_button && item.sub_button.length" v-for="subItem,v in item.sub_button">
                                        <a href="javascript:void(0);" class="custom-a-button sub_pre_menu_link"
                                           v-on:click="selectedSubMenuItem(v)">
                                            <?php echo"{{ subItem.name }}" ?>
                                        </a>
                                    </li>
                                    <li class="sub_pre_menu_item" v-if="!item.sub_button || item.sub_button.length < 5">
                                        <a href="javascript:void(0);" class="custom-a-button" title="最多添加五个菜单"
                                           v-on:click="addMenuHandler(1)">
                                            <i class="custom-svg-size">
                                            </i>
                                        </a>
                                    </li>
                                </ul>
                                <i class="arrow arrow_out"></i>
                                <i class="arrow arrow_in"></i>
                            </div>
                        </li>
                        <li class="pre_menu_item" v-if="menu.button.length < 3">
                            <a href="javascript:void(0);" class="custom-a-button" title="最多添加三个菜单" v-on:click="addMenuHandler(0)">
                                <i class="custom-svg-size">
                                </i>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="custom-edit-menu">
          <span class="custom-editor_arrow_wrp">
            <i class="editor_arrow editor_arrow_out"></i>
            <i class="editor_arrow editor_arrow_in"></i>
          </span>
                <div v-if="zindex>=0">
                    <div class="row custom-editor-title">
                        <h2 class="col-md-10">编辑菜单</h2>
                        <a class="col-md-2" href="javascript:void(0);" v-on:click="removeMenuHandler">删除菜单</a>
                    </div>
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">菜单名称：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" v-model.trim="nowItem.name" placeholder="请输入菜单名称" />
                            </div>
                        </div>
                        <div v-if="!nowItem.sub_button||!nowItem.sub_button.length">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">菜单类型：</label>
                                <div class="col-sm-10">
                                    <div class="radio menu-type-select" v-for="item,index in menuTypeList">
                                        <label :for="index">
                                            <input type="radio" name="menuType" v-model="nowItem.type" :id="index"
                                                   v-on:click="selectedMenuType(item.value)" :value="item.value" />
                                            <?php echo"{{ item.label }}" ?>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-2 control-label"></label>
                                <div class="col-sm-10">
                                    <div class="custom-panel-default" v-if='nowItem.type == "miniprogram" '>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">小程序路径：</label>
                                            <p class="col-sm-9">
                                                <input type="text" class="form-control" v-model="nowItem.pagepath" placeholder="请输入小程序路径" />
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">备用链接：</label>
                                            <p class="col-sm-9">
                                                <input type="text" v-model="nowItem.url" class="form-control" placeholder="请输入备用的地址" />
                                            </p>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">AppId：</label>
                                            <p class="col-sm-9">
                                                <input type="text" v-model="nowItem.appid" class="form-control" placeholder="请输入小程序appId" />
                                            </p>
                                        </div>
                                    </div>
                                    <div class="custom-panel-default" v-else-if='nowItem.type == "view" '>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">跳转地址：</label>
                                            <p class="col-sm-9">
                                                <input type="text" class="form-control" v-model="nowItem.url" placeholder="跳转地址" />
                                            </p>
                                        </div>
                                    </div>
                                    <div class="custom-panel-default" v-else>
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">事件KEY：</label>
                                            <p class="col-sm-9">
                          <textarea type="text" class="form-control" v-model="nowItem.key"
                                    placeholder="用户点击时，需传给后端的事件KEY"></textarea>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="col-sm-10">
                                <button type="button" class="btn btn-primary submit-button" v-on:click="confirmEditor">
                                    确认修改
                                </button>
                                <div class="custom-toast" if='toastMsg!=""'>
                                    <?php echo"{{ toastMsg }}" ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <p v-else class="editor-handler-tips">请选择左侧菜单</p>
            </div>
        </div>
    </div>
    <input type="hidden" id="{{$id}}_input" name="{{$name}}" v-model="resultJson" />

</div>

<script>
  var initJson = <?php echo $value ?>;
  console.log(initJson)
  $(document).ready(function(){
    VueInit(initJson, function (data) {
      console.log(data);
    },"#{{$id}}");
  })
</script>
