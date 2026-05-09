#### Зачем нужны фильтры Wordpress?

Фильтры `apply_filters()` используется там, где нужно предоставить возможность изменять данные без вмешательства 
в исходный код плагина.

#### Пример фильтра в коде плагина

```php
$value = 'Hello, world!';
echo apply_filters( 'example_filter_name', $value );
```

#### Пример использования фильтра пользователем

```php
add_filter( 'example_filter_name', 'my_filter_function_name' );
function my_filter_function_name( $data )
{
	// filter data...

	return $data;
}
```

## Список доступных фильтров

### `travelpayouts_tables_attributes_wp_filter`

> wp-content/plugins/travelpayouts/src/components/tables/TableModel.php

Фильтр для работы с атрибутами шорткодов таблиц

#### Пример использования

```php
add_filter( 'travelpayouts_tables_attributes_wp_filter', 'example_function_name', 10, 2 );

/**
* $attributes массив атрибутов шорткода
* @param array $attributes
* @return array
*/
function example_function_name( $attributes ) 
{
    if (isset($attributes['origin'])) {
        $attributes['origin'] = 'MOW';
    }
    
    return $attributes;
}
```