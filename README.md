# wordpress-setting-fields

WordPress setting field functions to output different type of fields.

## Field Types Summary

Quick reference to all available field types:

- [Text](#text) - Text input and textarea fields
- [Dropdown](#dropdown) - Select dropdown with custom options
- [Switch (toggle)](#switch-toggle) - Toggle/checkbox switch field
- [Post type (dropdown)](#post-type-dropdown) - Dropdown populated with posts of specified post types
- [Taxonomy (dropdown)](#taxonomy-dropdown) - Dropdown populated with taxonomy terms
- [Pages (dropdown)](#pages-dropdown) - Dropdown populated with WordPress pages
- [Button](#button) - Button field with action support
- [Link (picker)](#link-picker) - Link picker field using WordPress's built-in link modal

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
| placeholder | string | false | Optional.
| attrs | array | false | Optional. Extra html atrributes for the input. Attributes' name are the keys of the associative array.
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
| id | string | false | Optional. Id attribute for the dropdown. Default. the name argument
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
| nice_ui | boolean | false | Optional. Display as a nicer ui. Default true.

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

### Post type (dropdown)

Name: **Kuuak\WordPressSettingFields\Fields::post_type_dropdown**

Arguments:

| name            | type    | mandatory | Description                                                                                                   |
| --------------- | ------- | --------- | ------------------------------------------------------------------------------------------------------------- | -------- | ----- | ----------------------------------------------------------------- |
| query_args      | array   | false     | WP*Query arguments. \_See WP_Query::\_\_construct() for accepted arguments*                                   |
| name            | string  | true      | Name of the dropdown                                                                                          |
| selected        | int     | string    | int[]                                                                                                         | string[] | false | Optional. Value of the option that should be selected. Default 0. |
| required        | boolean | false     | Optional. Whether the dropdown is required. Default false                                                     |
| placeholder     | string  | false     | Optional.                                                                                                     |
| show_option_all | string  | false     | Optional. Option all label for the Multiple version. Default `All`                                            |
| help            | string  | false     | Optional. Help / description                                                                                  |
| echo            | boolean | false     | Optional. Either to print the dropdown or not. Default true.                                                  |
| nice_ui         | boolean | false     | Optional. Display as a nicer ui. Default true.                                                                |
| attrs           | array   | false     | Optional. Extra html atrributes for the select input. Attributes' name are the keys of the associative array. |

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::post_type_dropdown',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for'   => 'my-setting-id',
      'name'        => 'my-setting-name',
      'selected'    => 254,
      'query_args'  => [
        'post_type'   => ['my-custome-post-type'],
        'orderby'     => 'title',
        'order'       => 'ASC',
      ],
       'attrs'     => [
        'class' => 'large-text'
      ],
    ]
  );
} );
```

### Taxonomy (dropdown)

Name: **Kuuak\WordPressSettingFields\Fields::taxonomy_dropdown**

Arguments:

_See WP_Term_Query::\_\_construct() for information on additional accepted arguments_

| name            | type    | mandatory | Description                                                        |
| --------------- | ------- | --------- | ------------------------------------------------------------------ | -------- | ----- | ----------------------------------------------------------------- |
| name            | string  | true      | Name of the dropdown                                               |
| taxonomy        | string  | true      | Name of the taxonomy to                                            |
| selected        | int     | string    | int[]                                                              | string[] | false | Optional. Value of the option that should be selected. Default 0. |
| required        | boolean | false     | Optional. Whether the dropdown is required. Default false          |
| show_option_all | string  | false     | Optional. Option all label for the Multiple version. Default `All` |
| hide_empty      | string  | false     | Optional. Option all label                                         |
| help            | string  | false     | Optional. Help / description                                       |
| echo            | boolean | false     | Optional. Either to print the dropdown or not. Default true.       |

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

| name     | type    | mandatory | Description                                                  |
| -------- | ------- | --------- | ------------------------------------------------------------ | ----------------------------------------------------------------- |
| name     | string  | true      | Name of the dropdown                                         |
| selected | int     | string    | false                                                        | Optional. Value of the option that should be selected. Default 0. |
| required | boolean | false     | Optional. Whether the dropdown is required. Default false    |
| help     | string  | false     | Optional. Help / description. Default empty                  |
| echo     | boolean | false     | Optional. Either to print the dropdown or not. Default true. |

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

| name          | type   | mandatory | Description                                                                                              |
| ------------- | ------ | --------- | -------------------------------------------------------------------------------------------------------- |
| id            | string | false     | Optional. Id attribute for the button. Default. the name argument                                        |
| name          | string | true      | Name of the button                                                                                       |
| label         | string | true      | Label of the button                                                                                      |
| variant       | string | false     | Optional. Variant of the button. `primary` or `secondary`. Default secondary                             |
| action        | array  | false     | Action data                                                                                              |
| action.name   | string | true      | Action name                                                                                              |
| action.value  | string | true      | Action value                                                                                             |
| wrapper_attrs | array  | false     | Optional. Extra html atrributes for the wrapper. Attributes' name are the keys of the associative array. |
| help          | string | false     | Optional. Help / description. Default empty                                                              |

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

### Link (picker)

Name: **Kuuak\WordPressSettingFields\Fields::link**

Renders a link field with WordPress's built-in link picker modal. Allows users to search for internal posts/pages or enter a custom URL. The field stores URL, link text, and target (for "open in new tab") values.

Arguments:

| name     | type          | mandatory | Description                                                                                                             |
| -------- | ------------- | --------- | ----------------------------------------------------------------------------------------------------------------------- |
| id       | string        | false     | Optional. Id attribute for the field. Default. the name argument                                                        |
| name     | string        | true      | Name of the field. The field will create sub-fields: `name[url]`, `name[text]`, `name[target]`                          |
| value    | array\|string | false     | Optional. Current value. Can be a string (URL only) or an array with keys: `url`, `text`, `target`. Default empty array |
| required | boolean       | false     | Optional. Whether the URL input is required. Default false                                                              |
| help     | string        | false     | Optional. Help / description. Default empty                                                                             |

> Example of usage

```php
add_action( 'admin_init', function() {
  add_settings_field(
    'my-setting-id',
    __('My setting', 'my-setting-domain'),
    'Kuuak\WordPressSettingFields\Fields::link',
    'setting-page-id',
    'setting-section-id',
    [
      'label_for' => 'my-setting-id',
      'name'      => 'my-setting-name',
      'value'     => [
        'url'    => 'https://example.com',
        'text'   => 'Link Text',
        'target' => '_blank'
      ],
      'help'      => 'Pick a post/page or paste a custom URL.',
    ]
  );
} );
```

> Note: When saving the form, the field will submit as an array with `url`, `text`, and `target` keys. The `target` value will be `_blank` if "open in new tab" is checked, or empty string otherwise.

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.

## Release Process

For release instructions, see the `.cursor/rules/release-process.mdc` file. This file contains a cursor rule that can be optionally used to guide the release process.
