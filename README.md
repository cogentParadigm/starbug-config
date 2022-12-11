# Config

Let's suppose you have the following module directories.

```
app/modules/example1
app/modules/example2
```

Let's also suppose you have the following yml files within those modules.

```
app/modules/example1/etc/test.yml
app/modules/example2/etc/test.yml
```



```php
use Starbug\Config\Config;

$config = new Config($locator);

$data = $config->get("test");

// $data will contain the merged data from both files
var_dump($data);

```

See [starbug-resource-locator](https://github.com/cogentParadigm/starbug-resource-locator) for details on the locator which is passed to Config constructor.

