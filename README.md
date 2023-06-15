# wordpress-setting-fields

WordPress setting field functions to output different type of fields.

## Field types

### Text

Name: **Kuuak\WordPressSettingFields\Fields::text**

Arguments:
| name | type | mandatory | Description |
| --- | --- | --- | --- |
| type | string | false | Optional. HTML input type attribute or `textarea`. Default: text
| id | string | false | Optional. Id attribute for the input. Default. the name argument
| name | string | true | Name of the input
| value | string | false | Optional. Current value of the input
| required | boolean | false | Optional. Whether the input is required. Default false
| placeholder | string | false |
Optional. | attrs | array | false | Optional. Extra html atrributes for the input. Attributes' name are the keys of the associative array.
| help | string | false | Optional. Help / description

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::text',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for' => 'my-setting-id',
      'name'      => 'my-setting-name',
      'value'     => 'Value',
      'help'      => 'This is a text to help the user to understand the setting',
      'attrs'     => [
        'class' => 'large-text'
      ],
    ]
  );
} );
```


### Dropdown

Name: **Kuuak\WordPressSettingFields\Fields::dropdown**

Arguments:
| name | type | mandatory | Description |
| --- | --- | --- | --- |
| id | string | false | Optional. Id attribute for the dropdown.  Default. the name argument
| name | string | true | Name of the dropdown
| multiple | boolean | false | Optional. Define if multiple options can be selected in the list.
| selected | string|string[] | false | Optional. Selected value or values
| required | boolean | false | Optional. Whether the dropdown is required. Default false
| show_option_all | string | false | Optional. Text to display for showing all items. Default empty.
| options | Array | true | Array of dropdown items. Items with keys `value` & `title`.
| placeholder | string | false |
| help | string | false | Optional. Help / description. Default empty.
| echo | boolean | false | Optional. Whether to echo or return the generated markup. Default true.

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::dropdown',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for' => 'my-setting-id',
      'name'      => 'my-setting-name',
      'selected'  => 'Value',
      'options'   => [
        [ 'value' => 'opt-1', 'title' => 'Option 1' ],
        [ 'value' => 'opt-2', 'title' => 'Option 2' ],
        [ 'value' => 'opt-3', 'title' => 'Option 3' ],
      ],
    ]
  );
} );
```


### Switch (toggle)

Name: **Kuuak\WordPressSettingFields\Fields::switch**

Arguments:
| name | type | mandatory | Description |
| --- | --- | --- | --- |
| id | string | false | Optional. Id attribute for the input. Default. the name argument
| name | string | true | Name of the input
| checked | boolean | true |
| help | string | false | Optional. Help / description

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::switch',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for' => 'my-setting-id',
      'name'      => 'my-setting-name',
      'checked'   => true,
    ]
  );
} );
```


### Taxonomy (dropdown)

Name: **Kuuak\WordPressSettingFields\Fields::dropdown**

Arguments:

_See WP_Term_Query::__construct() for information on additional accepted arguments_

| name | type | mandatory | Description |
| --- | --- | --- | --- |
| name | string | true | Name of the dropdown
| taxonomy | string | true | Name of the taxonomy to
| selected | int|string|int[]|string[] | false | Optional. Value of the option that should be selected. Default 0.
| required | boolean | false | Optional. Whether the dropdown is required. Default false
| show_option_all | string | false | Optional. Option all label for the Multiple version. Default `All`
| hide_empty | string | false | Optional. Option all label
| help | string | false | Optional. Help / description
| echo | boolean | false | Optional. Either to print the dropdown or not. Default true.

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::taxonomy_dropdown',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for'   => 'my-setting-id',
      'name'        => 'my-setting-name',
      'taxonomy'    => 'category',
      'selected'    => 254,
    ]
  );
} );
```

### Pages (dropdown)

Name: **Kuuak\WordPressSettingFields\Fields::pages_dropdown**

Arguments:

_See get_pages() for additional arguments_

| name | type | mandatory | Description |
| --- | --- | --- | --- |
| name | string | true | Name of the dropdown
| selected | int|string | false | Optional. Value of the option that should be selected. Default 0.
| required | boolean | false | Optional. Whether the dropdown is required. Default false
| help | string | false | Optional. Help / description. Default empty
| echo | boolean | false | Optional. Either to print the dropdown or not. Default true.

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::pages_dropdown',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for'   => 'my-setting-id',
      'name'        => 'my-setting-name',
      'selected'    => 25,
    ]
  );
} );
```


### Button

Name: **Kuuak\WordPressSettingFields\Fields::button**

Arguments:

| name | type | mandatory | Description |
| --- | --- | --- | --- |
| id | string | false | Optional. Id attribute for the button. Default. the name argument
| name | string | true | Name of the button
| label | string | true | Label of the button
| variant | string | false | Optional. Variant of the button. `primary` or `secondary`. Default secondary
| action | array | false | Action data
| action.name | string | true | Action name
| action.value | string | true | Action value
| wrapper_attrs | array | false | Action data
| help | string | false | Optional. Help / description. Default empty

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::button',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for' => 'my-setting-id',
      'name'      => 'my-setting-name',
      'label'     => 'Register now',
    ]
  );
} );
```



