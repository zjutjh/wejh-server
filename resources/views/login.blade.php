<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>绑定精弘通行证</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <!-- rem 正比例缩放 -->
    <script>!function(a,b,c){function q(){var d=Math.min((o?e[h]().width:f.innerWidth)/(a/b),c);d!=p&&(k.innerHTML="html{font-size:"+d+"px!important;"+n+"}",p=d)}function r(){clearTimeout(l),l=setTimeout(q,500)}var l,d=document,e=d.documentElement,f=window,g="addEventListener",h="getBoundingClientRect",i="pageshow",j=d.head||d.getElementsByTagName("HEAD")[0],k=d.createElement("STYLE"),m="text-size-adjust:100%;",n="-webkit-"+m+"-moz-"+m+"-ms-"+m+"-o-"+m+m,o=h in e,p=null;a=a||320,b=b||16,c=c||32,j.appendChild(k),d[g]("DOMContentLoaded",q,!1),"on"+i in f?f[g](i,function(a){a.persisted&&r()},!1):f[g]("load",r,!1),f[g]("resize",r,!1),q()}(320,10,100);</script>
    <!-- /rem -->
</head>
<style>
    body {
        background-color: #fafafa;
    }
</style>
<!-- 引入样式 -->
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/element-ui@2.0.3/lib/theme-chalk/index.css">
<body>
<div id="app">
    <div v-if="isBind" style="margin-top: 4rem;text-align: center;font-size: 3rem;">
        你已经绑定成功
    </div>
    <template v-else>
        <el-header class="header" style="margin-top: 4rem;text-align: center;font-size: 3rem;">绑定精弘通行证</el-header>
        <div style="margin-top: 3rem;margin-bottom: 3rem">
            <el-input v-model="username" placeholder="请输入精弘通行证(学号)" style="margin-bottom: 1rem;"></el-input>
            <el-input placeholder="请输入你的密码" v-model="password" class="input-with-select" style="margin-bottom: 2rem;"></el-input>
            <el-row type="flex" class="row-bg" justify="space-around">
                <el-col :span="6">
                    <el-button type="success" @click="login" :loading="loading">绑定</el-button>
                </el-col>
            </el-row>
        </div>
    </template>
</div>
<script src="//cdn.jsdelivr.net/npm/vue@2.5.3"></script>
<script src="//cdn.jsdelivr.net/npm/vue-resource@1.3.4"></script>
<!-- 引入组件库 -->
<script src="//cdn.jsdelivr.net/npm/element-ui@2.0.3/lib/index.js"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            isBind: {{ $isBind }},
            loading: false,
            openid: '{{$openid}}',
            username: '',
            password: ''
        },
        methods: {
            login: function () {
                var _this = this
                this.loading = true
                if (!_this.username || !_this.password) {
                    _this.loading = false
                    return _this.$message.error('请输入精弘通行证和密码')
                }
                _this.$http.post('/api/login', {
                    username: _this.username,
                    password: _this.password,
                    openid: _this.openid,
                    type: 'wechat'
                }).then(function (response) {
                    const result = response.body
                    if (result.errcode < 0) {
                        _this.loading = false
                        return _this.$message({
                            showClose: true,
                            message: result.errmsg || '发生了一点错误',
                            type: 'warning'
                        })
                    }
                    _this.loading = false
                    _this.$message({
                        showClose: true,
                        message: '绑定成功',
                        type: 'success'
                    })
                    _this.isBind = true
                }, function () {
                    _this.$message.error('好像发生了一点错误')
                    _this.loading = false
                })
            }
        }
    })
</script>
</body>
</html>