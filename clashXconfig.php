<?php
function get($url){
    $ch = curl_init();
    $options = array(CURLOPT_URL => $url,
        CURLOPT_HEADER => FALSE,
        CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_SSL_VERIFYHOST => FALSE,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_CONNECTTIMEOUT => 5
    );
    curl_setopt_array($ch, $options);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return $file_contents;
}

function getNodeStr($data){
    $node = '  - name: "' . $data->ps . '"' . PHP_EOL;
    $node .= '    type: vmess' . PHP_EOL;
    $node .= '    server: ' . $data->add . PHP_EOL;
    $node .= '    port: ' . $data->port . PHP_EOL;
    $node .= '    uuid: ' . $data->id . PHP_EOL;
    $node .= '    alterId: ' . $data->aid . PHP_EOL;
    $node .= '    cipher: ' . $data->type . PHP_EOL;
    $node .= '    udp: false' . PHP_EOL;
    $node .= '    tls: ' . ($data->tls == '' ? 'false' : 'true') . PHP_EOL;
    $node .= '    skip-cert-verify: false' . PHP_EOL;
    $node .= ($data->net == 'ws' ? '' : '# ') . '    network: ' . $data->net . PHP_EOL;
    $node .= '    ws-path: "' . $data->path . '"' . PHP_EOL;
    $node .= '    ws-headers:' . PHP_EOL;
    $node .= '      Host: "' . $data->host . '"' . PHP_EOL;
    return $node;
}

function creatProxyList($v2Info){
    $ret = array(
        'proxyInfo' => '',
        'proxyName' => ''
    );
    $deData = base64_decode($v2Info);
    $vm = preg_split("/\\n/", $deData);

    for ($i = 0; $i < count($vm); $i++){
        if ($vm[$i] != '' && substr_compare($vm[$i], 'vmess://', 0, 8, TRUE) == 0){
            $json = json_decode(base64_decode(substr($vm[$i], 8)));
            $ret['proxyName'] .= '      - "' . $json->ps . '"' . PHP_EOL;
            $ret['proxyInfo'] .= getNodeStr($json) . PHP_EOL;
        }
    }
    return $ret;
}

#url为v2订阅链接
$url = 'https://raw.githubusercontent.com/cdp2020/v2ray/master/README.md';

$all = get($url);
$proxy = creatProxyList($all);
//开始输出配置信息

echo '#---------------------------------------------------#
## 配置文件需要放置在 $HOME/.config/clash/*.yaml
## 如果您不知道如何操作，请参阅 SS-Rule-Snippet：https://github.com/Hackl0us/SS-Rule-Snippet/blob/master/LAZY_RULES/clash.yaml
## 或者官方Github文档 https://github.com/Dreamacro/clash/blob/master/README.md
#---------------------------------------------------#

port: 7890
socks-port: 7891
allow-lan: false
mode: Rule
log-level: info
external-controller: 127.0.0.1:9090
experimental:
  ignore-resolve-fail: true
dns:
  enable: true
  ipv6: false

  nameserver:
     - 8.8.8.8
     - 114.114.114.114
     - 223.5.5.5
     - tls://dns.rubyfish.cn:853
     
  fallback:
     - tls://1.0.0.1:853
     - tls://dns.google:853

Proxy:
';
echo $proxy['proxyInfo'];
$list = $proxy['proxyName'];

/*
#自动使用延迟最短的服务器
  - name: \"Auto\"
    type: url-test
    proxies:
$list
    url: 'http://www.gstatic.com/generate_204'
    interval: 300

#尽量按照服务器顺序，在确保服务器可用的情况下，自动选择服务器
  - name: \"Order\"
    type: fallback
    proxies:
$list
    url: 'http://www.gstatic.com/generate_204'
    interval: 300

#使相同 eTLD 请求在同一条代理线路上
  - name: \"Balance\"
    type: load-balance
    proxies:
$list
    url: 'http://www.gstatic.com/generate_204'
    interval: 300

 */

echo "
Proxy Group:
#使用手动选择的 代理服务器 或 服务器组
  - name: \"Proxy\"
    type: select
    proxies:
$list

Rule:
# Apple
- DOMAIN-SUFFIX,apps.apple.com,Proxy
- DOMAIN-SUFFIX,itunes.apple.com,Proxy
- DOMAIN-SUFFIX,blobstore.apple.com,Proxy
- DOMAIN-SUFFIX,music.apple.com,DIRECT
- DOMAIN-SUFFIX,icloud.com,DIRECT
- DOMAIN-SUFFIX,icloud-content.com,DIRECT
- DOMAIN-SUFFIX,me.com,DIRECT
- DOMAIN-SUFFIX,mzstatic.com,DIRECT
- DOMAIN-SUFFIX,akadns.net,DIRECT
- DOMAIN-SUFFIX,aaplimg.com,DIRECT
- DOMAIN-SUFFIX,cdn-apple.com,DIRECT
- DOMAIN-SUFFIX,apple.com,DIRECT
- DOMAIN-SUFFIX,apple-cloudkit.com,DIRECT


# 自定义规则
## 您可以在此处插入您补充的自定义规则

# 国内网站
- DOMAIN-SUFFIX,cn,DIRECT
- DOMAIN-KEYWORD,-cn,DIRECT

- DOMAIN-SUFFIX,126.com,DIRECT
- DOMAIN-SUFFIX,126.net,DIRECT
- DOMAIN-SUFFIX,127.net,DIRECT
- DOMAIN-SUFFIX,163.com,DIRECT
- DOMAIN-SUFFIX,360buyimg.com,DIRECT
- DOMAIN-SUFFIX,36kr.com,DIRECT
- DOMAIN-SUFFIX,acfun.tv,DIRECT
- DOMAIN-SUFFIX,air-matters.com,DIRECT
- DOMAIN-SUFFIX,aixifan.com,DIRECT
- DOMAIN-SUFFIX,akamaized.net,DIRECT
- DOMAIN-KEYWORD,alicdn,DIRECT
- DOMAIN-KEYWORD,alipay,DIRECT
- DOMAIN-KEYWORD,taobao,DIRECT
- DOMAIN-SUFFIX,amap.com,DIRECT
- DOMAIN-SUFFIX,autonavi.com,DIRECT
- DOMAIN-KEYWORD,baidu,DIRECT
- DOMAIN-SUFFIX,bdimg.com,DIRECT
- DOMAIN-SUFFIX,bdstatic.com,DIRECT
- DOMAIN-SUFFIX,bilibili.com,DIRECT
- DOMAIN-SUFFIX,caiyunapp.com,DIRECT
- DOMAIN-SUFFIX,clouddn.com,DIRECT
- DOMAIN-SUFFIX,cnbeta.com,DIRECT
- DOMAIN-SUFFIX,cnbetacdn.com,DIRECT
- DOMAIN-SUFFIX,cootekservice.com,DIRECT
- DOMAIN-SUFFIX,csdn.net,DIRECT
- DOMAIN-SUFFIX,ctrip.com,DIRECT
- DOMAIN-SUFFIX,dgtle.com,DIRECT
- DOMAIN-SUFFIX,dianping.com,DIRECT
- DOMAIN-SUFFIX,douban.com,DIRECT
- DOMAIN-SUFFIX,doubanio.com,DIRECT
- DOMAIN-SUFFIX,duokan.com,DIRECT
- DOMAIN-SUFFIX,easou.com,DIRECT
- DOMAIN-SUFFIX,ele.me,DIRECT
- DOMAIN-SUFFIX,feng.com,DIRECT
- DOMAIN-SUFFIX,fir.im,DIRECT
- DOMAIN-SUFFIX,frdic.com,DIRECT
- DOMAIN-SUFFIX,g-cores.com,DIRECT
- DOMAIN-SUFFIX,godic.net,DIRECT
- DOMAIN-SUFFIX,gtimg.com,DIRECT
- DOMAIN,cdn.hockeyapp.net,DIRECT
- DOMAIN-SUFFIX,hongxiu.com,DIRECT
- DOMAIN-SUFFIX,hxcdn.net,DIRECT
- DOMAIN-SUFFIX,iciba.com,DIRECT
- DOMAIN-SUFFIX,ifeng.com,DIRECT
- DOMAIN-SUFFIX,ifengimg.com,DIRECT
- DOMAIN-SUFFIX,ipip.net,DIRECT
- DOMAIN-SUFFIX,iqiyi.com,DIRECT
- DOMAIN-SUFFIX,jd.com,DIRECT
- DOMAIN-SUFFIX,jianshu.com,DIRECT
- DOMAIN-SUFFIX,knewone.com,DIRECT
- DOMAIN-SUFFIX,le.com,DIRECT
- DOMAIN-SUFFIX,lecloud.com,DIRECT
- DOMAIN-SUFFIX,lemicp.com,DIRECT
- DOMAIN-SUFFIX,licdn.com,DIRECT
- DOMAIN-SUFFIX,linkedin.com,DIRECT
- DOMAIN-SUFFIX,luoo.net,DIRECT
- DOMAIN-SUFFIX,meituan.com,DIRECT
- DOMAIN-SUFFIX,meituan.net,DIRECT
- DOMAIN-SUFFIX,mi.com,DIRECT
- DOMAIN-SUFFIX,miaopai.com,DIRECT
- DOMAIN-SUFFIX,microsoft.com,DIRECT
- DOMAIN-SUFFIX,microsoftonline.com,DIRECT
- DOMAIN-SUFFIX,miui.com,DIRECT
- DOMAIN-SUFFIX,miwifi.com,DIRECT
- DOMAIN-SUFFIX,mob.com,DIRECT
- DOMAIN-SUFFIX,netease.com,DIRECT
- DOMAIN-SUFFIX,office.com,DIRECT
- DOMAIN-SUFFIX,office365.com,DIRECT
- DOMAIN-KEYWORD,officecdn,DIRECT
- DOMAIN-SUFFIX,oschina.net,DIRECT
- DOMAIN-SUFFIX,ppsimg.com,DIRECT
- DOMAIN-SUFFIX,pstatp.com,DIRECT
- DOMAIN-SUFFIX,qcloud.com,DIRECT
- DOMAIN-SUFFIX,qdaily.com,DIRECT
- DOMAIN-SUFFIX,qdmm.com,DIRECT
- DOMAIN-SUFFIX,qhimg.com,DIRECT
- DOMAIN-SUFFIX,qhres.com,DIRECT
- DOMAIN-SUFFIX,qidian.com,DIRECT
- DOMAIN-SUFFIX,qihucdn.com,DIRECT
- DOMAIN-SUFFIX,qiniu.com,DIRECT
- DOMAIN-SUFFIX,qiniucdn.com,DIRECT
- DOMAIN-SUFFIX,qiyipic.com,DIRECT
- DOMAIN-SUFFIX,qq.com,DIRECT
- DOMAIN-SUFFIX,qqurl.com,DIRECT
- DOMAIN-SUFFIX,rarbg.to,DIRECT
- DOMAIN-SUFFIX,ruguoapp.com,DIRECT
- DOMAIN-SUFFIX,segmentfault.com,DIRECT
- DOMAIN-SUFFIX,sinaapp.com,DIRECT
- DOMAIN-SUFFIX,smzdm.com,DIRECT
- DOMAIN-SUFFIX,snapdrop.net,DIRECT
- DOMAIN-SUFFIX,sogou.com,DIRECT
- DOMAIN-SUFFIX,sogoucdn.com,DIRECT
- DOMAIN-SUFFIX,sohu.com,DIRECT
- DOMAIN-SUFFIX,soku.com,DIRECT
- DOMAIN-SUFFIX,speedtest.net,DIRECT
- DOMAIN-SUFFIX,sspai.com,DIRECT
- DOMAIN-SUFFIX,suning.com,DIRECT
- DOMAIN-SUFFIX,taobao.com,DIRECT
- DOMAIN-SUFFIX,tencent.com,DIRECT
- DOMAIN-SUFFIX,tenpay.com,DIRECT
- DOMAIN-SUFFIX,tianyancha.com,DIRECT
- DOMAIN-SUFFIX,tmall.com,DIRECT
- DOMAIN-SUFFIX,tudou.com,DIRECT
- DOMAIN-SUFFIX,umetrip.com,DIRECT
- DOMAIN-SUFFIX,upaiyun.com,DIRECT
- DOMAIN-SUFFIX,upyun.com,DIRECT
- DOMAIN-SUFFIX,veryzhun.com,DIRECT
- DOMAIN-SUFFIX,weather.com,DIRECT
- DOMAIN-SUFFIX,weibo.com,DIRECT
- DOMAIN-SUFFIX,xiami.com,DIRECT
- DOMAIN-SUFFIX,xiami.net,DIRECT
- DOMAIN-SUFFIX,xiaomicp.com,DIRECT
- DOMAIN-SUFFIX,ximalaya.com,DIRECT
- DOMAIN-SUFFIX,xmcdn.com,DIRECT
- DOMAIN-SUFFIX,xunlei.com,DIRECT
- DOMAIN-SUFFIX,yhd.com,DIRECT
- DOMAIN-SUFFIX,yihaodianimg.com,DIRECT
- DOMAIN-SUFFIX,yinxiang.com,DIRECT
- DOMAIN-SUFFIX,ykimg.com,DIRECT
- DOMAIN-SUFFIX,youdao.com,DIRECT
- DOMAIN-SUFFIX,youku.com,DIRECT
- DOMAIN-SUFFIX,zealer.com,DIRECT
- DOMAIN-SUFFIX,zhihu.com,DIRECT
- DOMAIN-SUFFIX,zhimg.com,DIRECT
- DOMAIN-SUFFIX,zimuzu.tv,DIRECT
- DOMAIN-SUFFIX,zoho.com,DIRECT

# 抗 DNS 污染 
- DOMAIN-KEYWORD,amazon,Proxy
- DOMAIN-KEYWORD,google,Proxy
- DOMAIN-KEYWORD,gmail,Proxy
- DOMAIN-KEYWORD,youtube,Proxy
- DOMAIN-KEYWORD,facebook,Proxy
- DOMAIN-SUFFIX,fb.me,Proxy
- DOMAIN-SUFFIX,fbcdn.net,Proxy
- DOMAIN-KEYWORD,twitter,Proxy
- DOMAIN-KEYWORD,instagram,Proxy
- DOMAIN-KEYWORD,dropbox,Proxy
- DOMAIN-SUFFIX,twimg.com,Proxy
- DOMAIN-KEYWORD,blogspot,Proxy
- DOMAIN-SUFFIX,youtu.be,Proxy
- DOMAIN-KEYWORD,whatsapp,Proxy

# 常见广告域名屏蔽
- DOMAIN-KEYWORD,admarvel,REJECT
- DOMAIN-KEYWORD,admaster,REJECT
- DOMAIN-KEYWORD,adsage,REJECT
- DOMAIN-KEYWORD,adsmogo,REJECT
- DOMAIN-KEYWORD,adsrvmedia,REJECT
- DOMAIN-KEYWORD,adwords,REJECT
- DOMAIN-KEYWORD,adservice,REJECT
- DOMAIN-KEYWORD,domob,REJECT
- DOMAIN-KEYWORD,duomeng,REJECT
- DOMAIN-KEYWORD,dwtrack,REJECT
- DOMAIN-KEYWORD,guanggao,REJECT
- DOMAIN-KEYWORD,lianmeng,REJECT
- DOMAIN-SUFFIX,mmstat.com,REJECT
- DOMAIN-KEYWORD,omgmta,REJECT
- DOMAIN-KEYWORD,openx,REJECT
- DOMAIN-KEYWORD,partnerad,REJECT
- DOMAIN-KEYWORD,pingfore,REJECT
- DOMAIN-KEYWORD,supersonicads,REJECT
- DOMAIN-KEYWORD,uedas,REJECT
- DOMAIN-KEYWORD,umeng,REJECT
- DOMAIN-KEYWORD,usage,REJECT
- DOMAIN-KEYWORD,wlmonitor,REJECT
- DOMAIN-KEYWORD,zjtoolbar,REJECT

# 国外网站
- DOMAIN-SUFFIX,9to5mac.com,Proxy
- DOMAIN-SUFFIX,abpchina.org,Proxy
- DOMAIN-SUFFIX,adblockplus.org,Proxy
- DOMAIN-SUFFIX,adobe.com,Proxy
- DOMAIN-SUFFIX,alfredapp.com,Proxy
- DOMAIN-SUFFIX,amplitude.com,Proxy
- DOMAIN-SUFFIX,ampproject.org,Proxy
- DOMAIN-SUFFIX,android.com,Proxy
- DOMAIN-SUFFIX,angularjs.org,Proxy
- DOMAIN-SUFFIX,aolcdn.com,Proxy
- DOMAIN-SUFFIX,apkpure.com,Proxy
- DOMAIN-SUFFIX,appledaily.com,Proxy
- DOMAIN-SUFFIX,appshopper.com,Proxy
- DOMAIN-SUFFIX,appspot.com,Proxy
- DOMAIN-SUFFIX,arcgis.com,Proxy
- DOMAIN-SUFFIX,archive.org,Proxy
- DOMAIN-SUFFIX,armorgames.com,Proxy
- DOMAIN-SUFFIX,aspnetcdn.com,Proxy
- DOMAIN-SUFFIX,att.com,Proxy
- DOMAIN-SUFFIX,awsstatic.com,Proxy
- DOMAIN-SUFFIX,azureedge.net,Proxy
- DOMAIN-SUFFIX,azurewebsites.net,Proxy
- DOMAIN-SUFFIX,bing.com,Proxy
- DOMAIN-SUFFIX,bintray.com,Proxy
- DOMAIN-SUFFIX,bit.com,Proxy
- DOMAIN-SUFFIX,bit.ly,Proxy
- DOMAIN-SUFFIX,bitbucket.org,Proxy
- DOMAIN-SUFFIX,bjango.com,Proxy
- DOMAIN-SUFFIX,bkrtx.com,Proxy
- DOMAIN-SUFFIX,blog.com,Proxy
- DOMAIN-SUFFIX,blogcdn.com,Proxy
- DOMAIN-SUFFIX,blogger.com,Proxy
- DOMAIN-SUFFIX,blogsmithmedia.com,Proxy
- DOMAIN-SUFFIX,blogspot.com,Proxy
- DOMAIN-SUFFIX,blogspot.hk,Proxy
- DOMAIN-SUFFIX,bloomberg.com,Proxy
- DOMAIN-SUFFIX,box.com,Proxy
- DOMAIN-SUFFIX,box.net,Proxy
- DOMAIN-SUFFIX,cachefly.net,Proxy
- DOMAIN-SUFFIX,chromium.org,Proxy
- DOMAIN-SUFFIX,cl.ly,Proxy
- DOMAIN-SUFFIX,cloudflare.com,Proxy
- DOMAIN-SUFFIX,cloudfront.net,Proxy
- DOMAIN-SUFFIX,cloudmagic.com,Proxy
- DOMAIN-SUFFIX,cmail19.com,Proxy
- DOMAIN-SUFFIX,cnet.com,Proxy
- DOMAIN-SUFFIX,cocoapods.org,Proxy
- DOMAIN-SUFFIX,comodoca.com,Proxy
- DOMAIN-SUFFIX,crashlytics.com,Proxy
- DOMAIN-SUFFIX,culturedcode.com,Proxy
- DOMAIN-SUFFIX,d.pr,Proxy
- DOMAIN-SUFFIX,danilo.to,Proxy
- DOMAIN-SUFFIX,dayone.me,Proxy
- DOMAIN-SUFFIX,db.tt,Proxy
- DOMAIN-SUFFIX,deskconnect.com,Proxy
- DOMAIN-SUFFIX,disq.us,Proxy
- DOMAIN-SUFFIX,disqus.com,Proxy
- DOMAIN-SUFFIX,disquscdn.com,Proxy
- DOMAIN-SUFFIX,dnsimple.com,Proxy
- DOMAIN-SUFFIX,docker.com,Proxy
- DOMAIN-SUFFIX,dribbble.com,Proxy
- DOMAIN-SUFFIX,droplr.com,Proxy
- DOMAIN-SUFFIX,duckduckgo.com,Proxy
- DOMAIN-SUFFIX,dueapp.com,Proxy
- DOMAIN-SUFFIX,dytt8.net,Proxy
- DOMAIN-SUFFIX,edgecastcdn.net,Proxy
- DOMAIN-SUFFIX,edgekey.net,Proxy
- DOMAIN-SUFFIX,edgesuite.net,Proxy
- DOMAIN-SUFFIX,engadget.com,Proxy
- DOMAIN-SUFFIX,entrust.net,Proxy
- DOMAIN-SUFFIX,eurekavpt.com,Proxy
- DOMAIN-SUFFIX,evernote.com,Proxy
- DOMAIN-SUFFIX,fabric.io,Proxy
- DOMAIN-SUFFIX,fast.com,Proxy
- DOMAIN-SUFFIX,fastly.net,Proxy
- DOMAIN-SUFFIX,fc2.com,Proxy
- DOMAIN-SUFFIX,feedburner.com,Proxy
- DOMAIN-SUFFIX,feedly.com,Proxy
- DOMAIN-SUFFIX,feedsportal.com,Proxy
- DOMAIN-SUFFIX,fiftythree.com,Proxy
- DOMAIN-SUFFIX,firebaseio.com,Proxy
- DOMAIN-SUFFIX,flexibits.com,Proxy
- DOMAIN-SUFFIX,flickr.com,Proxy
- DOMAIN-SUFFIX,flipboard.com,Proxy
- DOMAIN-SUFFIX,g.co,Proxy
- DOMAIN-SUFFIX,gabia.net,Proxy
- DOMAIN-SUFFIX,geni.us,Proxy
- DOMAIN-SUFFIX,gfx.ms,Proxy
- DOMAIN-SUFFIX,ggpht.com,Proxy
- DOMAIN-SUFFIX,ghostnoteapp.com,Proxy
- DOMAIN-SUFFIX,git.io,Proxy
- DOMAIN-KEYWORD,github,Proxy
- DOMAIN-SUFFIX,globalsign.com,Proxy
- DOMAIN-SUFFIX,gmodules.com,Proxy
- DOMAIN-SUFFIX,godaddy.com,Proxy
- DOMAIN-SUFFIX,golang.org,Proxy
- DOMAIN-SUFFIX,gongm.in,Proxy
- DOMAIN-SUFFIX,goo.gl,Proxy
- DOMAIN-SUFFIX,goodreaders.com,Proxy
- DOMAIN-SUFFIX,goodreads.com,Proxy
- DOMAIN-SUFFIX,gravatar.com,Proxy
- DOMAIN-SUFFIX,gstatic.com,Proxy
- DOMAIN-SUFFIX,gvt0.com,Proxy
- DOMAIN-SUFFIX,hockeyapp.net,Proxy
- DOMAIN-SUFFIX,hotmail.com,Proxy
- DOMAIN-SUFFIX,icons8.com,Proxy
- DOMAIN-SUFFIX,ifixit.com,Proxy
- DOMAIN-SUFFIX,ift.tt,Proxy
- DOMAIN-SUFFIX,ifttt.com,Proxy
- DOMAIN-SUFFIX,iherb.com,Proxy
- DOMAIN-SUFFIX,imageshack.us,Proxy
- DOMAIN-SUFFIX,img.ly,Proxy
- DOMAIN-SUFFIX,imgur.com,Proxy
- DOMAIN-SUFFIX,imore.com,Proxy
- DOMAIN-SUFFIX,instapaper.com,Proxy
- DOMAIN-SUFFIX,ipn.li,Proxy
- DOMAIN-SUFFIX,is.gd,Proxy
- DOMAIN-SUFFIX,issuu.com,Proxy
- DOMAIN-SUFFIX,itgonglun.com,Proxy
- DOMAIN-SUFFIX,itun.es,Proxy
- DOMAIN-SUFFIX,ixquick.com,Proxy
- DOMAIN-SUFFIX,j.mp,Proxy
- DOMAIN-SUFFIX,js.revsci.net,Proxy
- DOMAIN-SUFFIX,jshint.com,Proxy
- DOMAIN-SUFFIX,jtvnw.net,Proxy
- DOMAIN-SUFFIX,justgetflux.com,Proxy
- DOMAIN-SUFFIX,kat.cr,Proxy
- DOMAIN-SUFFIX,klip.me,Proxy
- DOMAIN-SUFFIX,libsyn.com,Proxy
- DOMAIN-SUFFIX,linode.com,Proxy
- DOMAIN-SUFFIX,lithium.com,Proxy
- DOMAIN-SUFFIX,littlehj.com,Proxy
- DOMAIN-SUFFIX,live.com,Proxy
- DOMAIN-SUFFIX,live.net,Proxy
- DOMAIN-SUFFIX,livefilestore.com,Proxy
- DOMAIN-SUFFIX,llnwd.net,Proxy
- DOMAIN-SUFFIX,macid.co,Proxy
- DOMAIN-SUFFIX,macromedia.com,Proxy
- DOMAIN-SUFFIX,macrumors.com,Proxy
- DOMAIN-SUFFIX,mashable.com,Proxy
- DOMAIN-SUFFIX,mathjax.org,Proxy
- DOMAIN-SUFFIX,medium.com,Proxy
- DOMAIN-SUFFIX,mega.co.nz,Proxy
- DOMAIN-SUFFIX,mega.nz,Proxy
- DOMAIN-SUFFIX,megaupload.com,Proxy
- DOMAIN-SUFFIX,microsofttranslator.com,Proxy
- DOMAIN-SUFFIX,mindnode.com,Proxy
- DOMAIN-SUFFIX,mobile01.com,Proxy
- DOMAIN-SUFFIX,modmyi.com,Proxy
- DOMAIN-SUFFIX,msedge.net,Proxy
- DOMAIN-SUFFIX,myfontastic.com,Proxy
- DOMAIN-SUFFIX,name.com,Proxy
- DOMAIN-SUFFIX,nextmedia.com,Proxy
- DOMAIN-SUFFIX,nsstatic.net,Proxy
- DOMAIN-SUFFIX,nssurge.com,Proxy
- DOMAIN-SUFFIX,nyt.com,Proxy
- DOMAIN-SUFFIX,nytimes.com,Proxy
- DOMAIN-SUFFIX,omnigroup.com,Proxy
- DOMAIN-SUFFIX,onedrive.com,Proxy
- DOMAIN-SUFFIX,onenote.com,Proxy
- DOMAIN-SUFFIX,ooyala.com,Proxy
- DOMAIN-SUFFIX,openvpn.net,Proxy
- DOMAIN-SUFFIX,openwrt.org,Proxy
- DOMAIN-SUFFIX,orkut.com,Proxy
- DOMAIN-SUFFIX,osxdaily.com,Proxy
- DOMAIN-SUFFIX,outlook.com,Proxy
- DOMAIN-SUFFIX,ow.ly,Proxy
- DOMAIN-SUFFIX,paddleapi.com,Proxy
- DOMAIN-SUFFIX,parallels.com,Proxy
- DOMAIN-SUFFIX,parse.com,Proxy
- DOMAIN-SUFFIX,pdfexpert.com,Proxy
- DOMAIN-SUFFIX,periscope.tv,Proxy
- DOMAIN-SUFFIX,pinboard.in,Proxy
- DOMAIN-SUFFIX,pinterest.com,Proxy
- DOMAIN-SUFFIX,pixelmator.com,Proxy
- DOMAIN-SUFFIX,pixiv.net,Proxy
- DOMAIN-SUFFIX,playpcesor.com,Proxy
- DOMAIN-SUFFIX,playstation.com,Proxy
- DOMAIN-SUFFIX,playstation.com.hk,Proxy
- DOMAIN-SUFFIX,playstation.net,Proxy
- DOMAIN-SUFFIX,playstationnetwork.com,Proxy
- DOMAIN-SUFFIX,pushwoosh.com,Proxy
- DOMAIN-SUFFIX,rime.im,Proxy
- DOMAIN-SUFFIX,servebom.com,Proxy
- DOMAIN-SUFFIX,sfx.ms,Proxy
- DOMAIN-SUFFIX,shadowsocks.org,Proxy
- DOMAIN-SUFFIX,sharethis.com,Proxy
- DOMAIN-SUFFIX,shazam.com,Proxy
- DOMAIN-SUFFIX,skype.com,Proxy
- DOMAIN-SUFFIX,smartdnsProxy.com,Proxy
- DOMAIN-SUFFIX,smartmailcloud.com,Proxy
- DOMAIN-SUFFIX,sndcdn.com,Proxy
- DOMAIN-SUFFIX,sony.com,Proxy
- DOMAIN-SUFFIX,soundcloud.com,Proxy
- DOMAIN-SUFFIX,sourceforge.net,Proxy
- DOMAIN-SUFFIX,spotify.com,Proxy
- DOMAIN-SUFFIX,squarespace.com,Proxy
- DOMAIN-SUFFIX,sstatic.net,Proxy
- DOMAIN-SUFFIX,st.luluku.pw,Proxy
- DOMAIN-SUFFIX,stackoverflow.com,Proxy
- DOMAIN-SUFFIX,startpage.com,Proxy
- DOMAIN-SUFFIX,staticflickr.com,Proxy
- DOMAIN-SUFFIX,steamcommunity.com,Proxy
- DOMAIN-SUFFIX,symauth.com,Proxy
- DOMAIN-SUFFIX,symcb.com,Proxy
- DOMAIN-SUFFIX,symcd.com,Proxy
- DOMAIN-SUFFIX,tapbots.com,Proxy
- DOMAIN-SUFFIX,tapbots.net,Proxy
- DOMAIN-SUFFIX,tdesktop.com,Proxy
- DOMAIN-SUFFIX,techcrunch.com,Proxy
- DOMAIN-SUFFIX,techsmith.com,Proxy
- DOMAIN-SUFFIX,thepiratebay.org,Proxy
- DOMAIN-SUFFIX,theverge.com,Proxy
- DOMAIN-SUFFIX,time.com,Proxy
- DOMAIN-SUFFIX,timeinc.net,Proxy
- DOMAIN-SUFFIX,tiny.cc,Proxy
- DOMAIN-SUFFIX,tinypic.com,Proxy
- DOMAIN-SUFFIX,tmblr.co,Proxy
- DOMAIN-SUFFIX,todoist.com,Proxy
- DOMAIN-SUFFIX,trello.com,Proxy
- DOMAIN-SUFFIX,trustasiassl.com,Proxy
- DOMAIN-SUFFIX,tumblr.co,Proxy
- DOMAIN-SUFFIX,tumblr.com,Proxy
- DOMAIN-SUFFIX,tweetdeck.com,Proxy
- DOMAIN-SUFFIX,tweetmarker.net,Proxy
- DOMAIN-SUFFIX,twitch.tv,Proxy
- DOMAIN-SUFFIX,txmblr.com,Proxy
- DOMAIN-SUFFIX,typekit.net,Proxy
- DOMAIN-SUFFIX,ubertags.com,Proxy
- DOMAIN-SUFFIX,ublock.org,Proxy
- DOMAIN-SUFFIX,ubnt.com,Proxy
- DOMAIN-SUFFIX,ulyssesapp.com,Proxy
- DOMAIN-SUFFIX,urchin.com,Proxy
- DOMAIN-SUFFIX,usertrust.com,Proxy
- DOMAIN-SUFFIX,v.gd,Proxy
- DOMAIN-SUFFIX,v2ex.com,Proxy
- DOMAIN-SUFFIX,vimeo.com,Proxy
- DOMAIN-SUFFIX,vimeocdn.com,Proxy
- DOMAIN-SUFFIX,vine.co,Proxy
- DOMAIN-SUFFIX,vivaldi.com,Proxy
- DOMAIN-SUFFIX,vox-cdn.com,Proxy
- DOMAIN-SUFFIX,vsco.co,Proxy
- DOMAIN-SUFFIX,vultr.com,Proxy
- DOMAIN-SUFFIX,w.org,Proxy
- DOMAIN-SUFFIX,w3schools.com,Proxy
- DOMAIN-SUFFIX,webtype.com,Proxy
- DOMAIN-SUFFIX,wikiwand.com,Proxy
- DOMAIN-SUFFIX,wikileaks.org,Proxy
- DOMAIN-SUFFIX,wikimedia.org,Proxy
- DOMAIN-SUFFIX,wikipedia.com,Proxy
- DOMAIN-SUFFIX,wikipedia.org,Proxy
- DOMAIN-SUFFIX,windows.com,Proxy
- DOMAIN-SUFFIX,windows.net,Proxy
- DOMAIN-SUFFIX,wire.com,Proxy
- DOMAIN-SUFFIX,wordpress.com,Proxy
- DOMAIN-SUFFIX,workflowy.com,Proxy
- DOMAIN-SUFFIX,wp.com,Proxy
- DOMAIN-SUFFIX,wsj.com,Proxy
- DOMAIN-SUFFIX,wsj.net,Proxy
- DOMAIN-SUFFIX,xda-developers.com,Proxy
- DOMAIN-SUFFIX,xeeno.com,Proxy
- DOMAIN-SUFFIX,xiti.com,Proxy
- DOMAIN-SUFFIX,yahoo.com,Proxy
- DOMAIN-SUFFIX,yimg.com,Proxy
- DOMAIN-SUFFIX,ying.com,Proxy
- DOMAIN-SUFFIX,yoyo.org,Proxy
- DOMAIN-SUFFIX,ytimg.com,Proxy

# Telegram
- DOMAIN-SUFFIX,telegra.ph,Proxy
- DOMAIN-SUFFIX,telegram.org,Proxy

- IP-CIDR,91.108.4.0/22,Proxy,no-resolve
- IP-CIDR,91.108.8.0/22,Proxy,no-resolve
- IP-CIDR,91.108.12.0/22,Proxy,no-resolve
- IP-CIDR,91.108.16.0/22,Proxy,no-resolve
- IP-CIDR,91.108.56.0/22,Proxy,no-resolve
- IP-CIDR,149.154.160.0/22,Proxy,no-resolve
- IP-CIDR,149.154.164.0/22,Proxy,no-resolve
- IP-CIDR,149.154.168.0/22,Proxy,no-resolve
- IP-CIDR,149.154.172.0/22,Proxy,no-resolve

# LAN
- DOMAIN-SUFFIX,local,DIRECT
- IP-CIDR,127.0.0.0/8,DIRECT
- IP-CIDR,172.16.0.0/12,DIRECT
- IP-CIDR,192.168.0.0/16,DIRECT
- IP-CIDR,10.0.0.0/8,DIRECT
- IP-CIDR,17.0.0.0/8,DIRECT
- IP-CIDR,100.64.0.0/10,DIRECT

# 最终规则
- GEOIP,CN,DIRECT
- MATCH,Proxy";
