pharos
======

pharos智能估值

*[pharos](http://github.com/tkorays/pharos.git)代码托管在github上注意随时pull*

理论方面的就不在此介绍了，反正我也不懂。这里说下项目的文件，以及使用。


关于项目文件夹以及文件
----------------------

* pharos站点放在文件夹内的1、2、3等目录内
* hv_sys是codeigniter的系统文件夹
* hv_app存放网站以及应用服务等
* index.php是网页入口
* webservice.php是web服务入口
* assets存放一些css、js、img等静态文件
* scripts存放php脚本文件
* .htaccess是apache文件，用来重写url


如何安装到自己的电脑上
----------------------

1. 在网站根目录下执行`git clone https://github.com/tkorays/pharos.git`，然后进入创建的`pharos`文件夹。
2. 找到最新的站点文件夹，数字最大的，如3。修改服务器如apache配置文件，为/path/to/pharos/3/ 设置别名`Alias /pharos/ "/path/to/pharos/3/"`。
3. 假设版本号为3，修改/path/to/pharos/3/hv_app/site/config/config.php，`$config['base_url']	= '';`改为自己的如`http://localhost/pharos/`。
4. 修改codeigniter其他配置，以后介绍。


关于scripts里的脚本文件
-----------------------

主要操作在op.php文件里，使用方法:`php op.php [operation] [start] [end]`，operation可以参考里面代码。

写在最后
--------

祝大家安装成功！