# Picture resizing properties Bitrix module

Module adds picture resizing iblock and user properties.

## Classic Installing

* Download module and place it in `/bitrix/modules/custompropertiesmodule/` or `/local/modules/custompropertiesmodule/`
* Initialize module in your init.php like this:

```
CModule::IncludeModule('custompropertiesmodule');
```

or

```
 \Bitrix\Main\Loader::includeModule('custompropertiesmodule');
```

## Composer Installing

* Add `"htccs/bitrix_custom_properties_module": "1.0.0-beta"` to require section of your `composer.json`
* Initialize Composer installing
* Initialize module in your init.php like this:

```
CModule::IncludeModule('custompropertiesmodule');
```

or

```
 \Bitrix\Main\Loader::includeModule('custompropertiesmodule');
```


## Using

You can see new property types in iblock properties settings and in user properties settings.
You can change resize type (resize/crop) and picture height/width in properties settings.