<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <title>排行榜|取精用弘新一年，抢楼得钱看机缘</title>
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
    .header {
        text-align: center;
        font-size: 3rem;
        color: #666;
    }
</style>
<!-- 引入样式 -->
<link rel="stylesheet" href="//cdn.jsdelivr.net/npm/element-ui@2.0.3/lib/theme-chalk/index.css">
<body>
<div id="app">
    <el-table
            :data="data"
            style="width: 100%">
        <el-table-column
                type="index"
                label="排名"
                width="80">
        </el-table-column>
        <el-table-column
                prop="nickname"
                label="昵称"
                width="180">
        </el-table-column>
        <el-table-column
                prop="score"
                label="抢楼数">
        </el-table-column>
    </el-table>
</div>
<script src="//cdn.jsdelivr.net/npm/vue@2.5.3"></script>
<!-- 引入组件库 -->
<script src="//cdn.jsdelivr.net/npm/element-ui@2.0.3/lib/index.js"></script>
<script>
    var app = new Vue({
        el: '#app',
        data: {
            json: `{!!$list!!}`,
            data: []
        },
        mounted: function () {
            this.data = JSON.parse(this.json)
        }
    })
</script>
</body>
</html>
