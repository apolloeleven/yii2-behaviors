# Yii2 behaviors


#### Global Page Cache Behavior

Following behavior gives you the ability to enable Yii2 PageCache globally 
from config. Copy the following config to your respective config file. 
```php
...
'as globalCache' => [
    'class' => '\apollo11\behaviors\behaviors\GlobalPageCacheBehavior',
    'rules' => [
        [
            'controller' => 'about',
            'actions' => ['index', 'main'],
            'except' => ['view'],
            'duration' => 70,
            'variations' => [
                \apollo11\behaviors\behaviors\GlobalPageCacheBehavior::VARIATION_BY_LANGUAGE,
                \apollo11\behaviors\behaviors\GlobalPageCacheBehavior::VARIATION_BY_URL,
            ],
        ]
    ]
],
...
``` 

<table>
<tr>
<td>Param</td>
<td>Value</td>
</tr>
<tr>
<td>Controller</td>
<td><b>String</b> : Controller ID : Required</td>
<tr>
<tr>
<td>Duration</td>
<td><b>Integer</b> : Cache duration in seconds : Optional : Default - 60 seconds</td>
<tr>
<tr>
<td>Actions</td>
<td><b>Array</b> : Action IDs to cache: Required : '*' - for all actions</td>
<tr>
<tr>
<td>Except</td>
<td><b>Array</b> : Action IDs to skip cache on : Optional </td>
<tr>
<tr>
<td>Variations</td>
<td><b>Array</b> : Strings for respective vartiations of caching : Optional</td>
<tr>
</table>

