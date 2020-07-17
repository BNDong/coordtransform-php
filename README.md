<h1 align="center">Welcome tocoordtransform-php 👋</h1>

> [wandergis/coordtransform](https://github.com/wandergis/coordtransform) 的 PHP 版。<br>
> 提供百度坐标系(bd-09)、火星坐标系(国测局坐标系、gcj02)、WGS84坐标系的相互转换，基于 PHP 语言，无特殊依赖。

Python version：https://github.com/wandergis/coordTransform_py

Cli version：https://github.com/wandergis/coordtransform-cli

Go version：https://github.com/qichengzx/coordtransform

Java version：https://github.com/ipcjs/coordtransform

## Example

```php
require_once 'coordtransform.php';

Coordtransform::gcj02ToBd09($lng, $lat);
Coordtransform::gcj02ToWgs84($lng, $lat);
Coordtransform::wgs84ToGcj02($lng, $lat);
Coordtransform::wgs84ToBd09($lng, $lat);
Coordtransform::bd09ToGcj02($lng, $lat);
Coordtransform::bd09ToWgs84($lng, $lat);
```

## Test

```
PHP：

array (size=2)
    0 => float 101.66758917038
    1 => float 34.446565679398

array (size=2)
    0 => float 101.6593935994
    1 => float 34.44201870302

array (size=2)
    0 => float 101.6628764006
    1 => float 34.43883929698

array (size=2)
    0 => float 101.66933760126
    1 => float 34.444950045745

array (size=2)
    0 => float 101.65471487632
    1 => float 34.434211121566

array (size=2)
    0 => float 101.65298716899
    1 => float 34.435816401314

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

Javascript: 

(2) [101.66758917038334, 34.446565679397786]
(2) [101.65939359940022, 34.442018703020054]
(2) [101.66287640059979, 34.43883929697995]
(2) [101.66933760125637, 34.44495004574466]
(2) [101.65471487632013, 34.434211121566484]
(2) [101.65298716898793, 34.43581640131362]
```

## Show your support

Give a ⭐️ if this project helped you!