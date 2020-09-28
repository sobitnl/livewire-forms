# Livewire Forms


A dynamic, responsive [Livewire](https://laravel-livewire.com) form component with realtime validation, file uploads, array fields, and more.

- based on [Kevin Dion his work](https://github.com/kdion4891/laravel-livewire-forms/issues) and improved on several aspects

- [Support](https://github.com/sobitNL/livewire-forms/issues)
- [Contributions](https://github.com/SobitNL/livewire-forms/pulls)

# Installation

Make sure you've [installed Laravel Livewire](https://laravel-livewire.com/docs/installation/).

Installing this package via composer:

    composer require sobitnl/livewire-forms
    
This package was designed to work well with [Laravel frontend scaffolding](https://laravel.com/docs/master/frontend).

If you're just doing scaffolding now, you'll need to add `@stack('scripts')`, `@livewireScripts`, and `@livewireStyles` blade directives to your `resources/views/layouts/app.blade.php` file:

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @livewireStyles
    
    ...

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @livewireScripts
    @stack('scripts')

This package also uses [Font Awesome](https://fontawesome.com) for icons. If you don't already have it installed, it's as simple as:

    npm install @fortawesome/fontawesome-free
    
Then add the following line to `resources/sass/app.scss`:
    
    @import '~@fortawesome/fontawesome-free/css/all.min.css';
    
Now all that's left is to compile the assets:
    
    npm install && npm run dev

# Making Form Components

Using the `make` command:

    php artisan make:form UserCreateForm --model=User

This creates your new form component in the `app/Http/Livewire` folder.

After making a component, you may want to edit the `fields`, `success`, `cancel`, `saveAndStayResponse` and `saveAndGoBackResponse` methods:

    class UserCreateForm extends FormComponent
    {
        public function fields()
        {
        
            $this->addExtraButtons('btn btn-primary','logHello','hello');
            $this->addExtraButtons('btn btn-secondary','slogGoodbye','goodbye');
            
            return [
                Field::make('Name')->input()->rules('required'),
            ];
        }
        
        // extra button methods
        public function logHello()
        {
            logger('say hello');
        }
    
        public function logGoodbye()
        {
            logger('say goodbye');
        }
    
        public function success()
        {
            User::create($this->form_data);
        }
    
        public function saveAndStayResponse()
        {
            return redirect()->route('users.create');
        }
    
        public function saveAndGoBackResponse()
        {
            return redirect()->route('users.index');
        }
        
        public function cancel()
        {
            return redirect()->route('users.index');
        }
    }
    
You don't have to use the `render()` method in your form component or worry about a component view, because the package handles that automatically.

**Protips:**

you can add the `FillsColumns` trait to your model for automatic `$fillable`s via database column names.

if you want to add extra buttons, you can do so within the fields method. Add a method call:
```
$this->addExtraButton('btn btnPrimary','logHello','hello');
```
variables:
- classNames: the different classnames you want to attach to the button
- methodName: called if you click the button. **Important:** create the corresponding method within your file
- translationVar: the translation variable to set for the button 

# Using Form Components

You use form components in views just like any other Livewire component:

    @livewire('user-create-form')

Now all you have to do is update your form component class!

# Form Component Properties

### `$model`

Optional [Eloquent](https://laravel.com/docs/master/eloquent) model instance attached to the form component. This is passed in via the `@livewire` blade directive.

Example:

    @livewire('user-edit-form', ['model' => $user])
    
Example of using the model in the component `success` method:

    public function success()
    {
        $this->model->update($this->form_data);
    }
    
### `$form_data`

An array of the current data present in the form. This data is keyed with each field name.

Example:

    $name = $this->form_data['name'];
    
### `$storage_disk`

A static property which sets the disk to use for file uploads. Defaults to `public`.

Example:

    private static $storage_disk = 's3';
    
Or, via `.env` to apply globally:

    FORM_STORAGE_DISK="s3"
    
### `$storage_path`

A static property which sets the path to use for file uploads. Defaults to `uploads`.

Example:

    private static $storage_path = 'avatars';
    
Or, via `.env` to apply globally:

    FORM_STORAGE_PATH="avatars"
    
# Form Component Methods

### `fields()`

This method returns an array of `Field`s to use in the form.

Example:

    public function fields()
    {
        return [
            Field::make('Name')->input()->rules('required'),
            Field::make('Email')->input('email')->rules(['required', 'email', 'unique:users,email']),
            Field::make('Password')->input('password')->rules(['required', 'min:8', 'confirmed']),
            Field::make('Confirm Password', 'password_confirmation')->input('password'),
        ];
    }

Declaring `Field`s is similar to declaring Laravel Nova fields. [Jump to the field declaration section](#form-field-declaration) to learn more.

### `rulesIgnoreRealtime()`

This method is used to set rules to ignore during realtime validation.

Example:

    public function rulesIgnoreRealtime()
    {
        return ['confirmed', new MyCustomRule];
    }
    
### `success()`

This method defines what actions should take place when the form is successfully submitted and validation has passed.

Example:

    public function success()
    {
        $this->form_data['password'] = Hash::make($this->form_data['password']);

        User::create($this->form_data);
    }
    
### `saveAndStayResponse()`

This method defines the response after successful submission via the `Save` button.

Example:

    public function saveAndStayResponse()
    {
        return redirect()->route('users.edit', $this->model->id);
    }
    
### `saveAndGoBackResponse()`

This method defines the response after successful submission via the `Save & Go Back` button.

Example:

    public function saveAndGoBackResponse()
    {
        return redirect()->route('users.index');
    }
    
### `mount($model = null)`

This method sets the initial form properties. If you have to override it, be sure to call `$this->setFormProperties()`.

##### `$model`

The model instance passed to the form component.

Example:

    public function mount($model = null)
    {
        $this->setFormProperties();
        
        // my custom code
    }

### `render()`

This method renders the form component view. If you have to override it, be sure to `return $this->formView()`.

Example:

    public function render()
    {
        // my custom code
        
        return $this->formView();
    }

# Form Field Declaration

The `Field` class is used to declare your form fields.

    public function fields()
    {
        $brand_options = Brand::orderBy('name')->get()->pluck('id', 'name')->all();

        return [
            Field::make('Brand', 'brand_id')->select($brand_options)->help('Please select a brand.'),
            Field::make('Name')->input()->rules(['required', Rule::unique('cars', 'name')->ignore($this->model->id)]),
            Field::make('Photos')->file()->multiple()->rules('required'),
            Field::make('Color')->select(['Red', 'Green', 'Blue']),
            Field::make('Owners')->array([
                ArrayField::make('Name')->input()->placeholder('Name')->rules('required'),
                ArrayField::make('Phone')->input('tel')->placeholder('Phone')->rules('required'),
            ])->rules('required'),
            Field::make('Insurable')->checkbox()->placeholder('Is the car insurable?')->rules('accepted'),
            Field::make('Fuel Type')->radio(['Gas', 'Diesel', 'Electric'])->default('Diesel'),
            Field::make('Features')->checkboxes(['Stereo', 'Bluetooth', 'Navigation'])->rules('required|min:2'),
            Field::make('Description')->textarea(),
        ];
    }
    
### `make($label, $name = null)`

##### `$label`

The label to use for the form field, e.g. `First Name`.

##### `$name`

The name to use for the form field. If null, it will use a snake cased `$label`.

Basic field example:

    Field::make('First Name')->input()->rules('required|min:2'),
    
Relationship field example:

    $brand_options = Brand::orderBy('name')->get()->pluck('id', 'name')->all();

    return [
        Field::make('Brand', 'brand_id')->select($brand_options)->rules(['required', Rule::exists('brands', 'id')]),
        ...

### `input($type = 'text')`

Sets the field to be an `input` element. Defaults to `text`.

##### `$type`

Optional HTML5 input type to use for the input.

Example:

    Field::make('Email Address')->input('email'),
    
### `content($content = '')`

Create an content entry in which you can paste anything.

##### `$content`

Anything you like

Example:

    Field::make('startDiv')->content('<div class="startContainer">'),
    Field::make('endDiv')->content('</div>'),
    
### `file()`

Sets the field to be a `file` input element.

File fields should have a nullable `text` database column, and be cast to `array` in your model. 
This array will be populated with useful info for each file, including `file`, `disk`, `name`, `size`, and `mime_type`.

Example migration:

    $table->text('photos')->nullable();

Example model casting:

    protected $casts = ['photos' => 'array'];

Example field declaration:

    Field::make('Photos')->file(),
    
You can allow multiple file selections using the `multiple()` method:

    Field::make('Photos')->file()->multiple(),
    
### `textarea($rows = 2)`

Sets the field to be a `textarea` element.

##### `$rows`

The amount of rows to use for the textarea. Defaults to `2`.

Example:

    Field::make('Description')->textarea(5),
    
### `select($options = [])`

Sets the field to be a `select` dropdown element.

##### `$options`

An array of options to use for the select.

Example using a sequential array:

    Field::make('Colors')->select(['Red', 'Green', 'Blue']),
    
Example using an associative array:

    Field::make('Colors')->select(['Red' => '#ff0000', 'Green' => '#00ff00', 'Blue' => '#0000ff']),

When using associative arrays, the keys will be used for the option labels, and the values for the option values.

### `checkbox()`

Sets the field to be a `checkbox` element.

Checkbox fields should have a nullable `boolean` database column.

Example migration:

    $table->boolean('accepts_terms')->nullable();
    
Example field declaration:

    Field::make('Accepts Terms')->checkbox()->placeholder('Do you accept our TOS?')->rules('accepted'),
    
If a `placeholder()` is specified, it will be used as the checkbox label.

### `checkboxes($options = [])`

Sets the field to be multiple `checkbox` elements.

##### `$options`

An array of options to use for the checkboxes. Works the same as the `select()` method.

Checkboxes fields should have a nullable `text` database column, and be cast to `array` in your model.

Example migration:

    $table->text('features')->nullable();

Example model casting:

    protected $casts = ['features' => 'array'];

Example field declaration:

    Field::make('Features')->checkboxes(['Stereo', 'Bluetooth', 'Navigation'])->rules('required|min:2'),
    
### `radio($options = [])`

Sets the field to be a `radio` element.

##### `$options`

An array of options to use for the radio. Works the same as the `select()` method.

Example:

    Field::make('Fuel Type')->radio(['Gas', 'Diesel', 'Electric'])->default('Diesel'),

### `array($fields = [])`

Sets the field to be an array of fields.

##### `$fields`

An array of `ArrayField`s to use. [Jump to the array field declaration section](#array-field-declaration) to learn more.

Example:

    Field::make('Owners')->array([
        ArrayField::make('Full Name')->input()->placeholder('Full Name')->rules('required'),
        ArrayField::make('Phone Number')->input('tel')->placeholder('Phone Number'),
    ]),

Use the `sortable()` method to make the array fields sortable:

    Field::make('Owners')->array([
        ArrayField::make('Full Name')->input()->placeholder('Full Name')->rules('required'),
        ArrayField::make('Phone Number')->input('tel')->placeholder('Phone Number'),
    ])->sortable(),

### `default($default)`

Sets the default value to use for the field.

##### `$default`

The default value.

Example:

    Field::make('City')->input()->default('Toronto'),

##### `$inputClass`

An class you want to be added to the input field.

Example:

    Field::make('price')->input()->inputClass('text-right'),

### `autocomplete($autocomplete)`

Sets the autocomplete value to use for the field.

##### `$autocomplete`

The autocomplete value.

Example:

    Field::make('Password')->input('password')->autocomplete('new-password'),
    
### `placeholder($placeholder)`

Sets the placeholder value to use for the field.

##### `$placeholder`

The placeholder value.

Example:

    Field::make('Country')->input()->placeholder('What country do you live in?'),
    
### `help($help)`

Sets the help text to use below the field.

##### `$help`

The help text.

Example:

    Field::make('City')->input()->help('Please enter your current city.'),
    
### `rules($rules)`

Sets the [Laravel validation rules](https://laravel.com/docs/master/validation#available-validation-rules) to use for the field.

##### `$rules`

A string or array of Laravel validation rules.

Example using a string:

    Field::make('Name')->input()->rules('required|min:2'),
    
Example using an array:

    Field::make('City')->input()->rules(['required', Rule::in(['Toronto', 'New York']), new MyCustomRule]),
    
### `view($view)`

Sets a custom view to use for the field. Useful for more complex field elements not included in the package.

##### `$view`

The custom view.

Example custom view file:

    {{-- fields/custom-field.blade.php --}}
    <div class="form-group row">
        <label for="{{ $field->name }}" class="col-md-2 col-form-label text-md-right">
            {{ $field->label }}
        </label>
    
        <div class="col-md">
            <input
                id="{{ $field->name }}"
                type="text"
                class="custom-field-class form-control @error($field->key) is-invalid @enderror"
                wire:model.lazy="{{ $field->key }}">
    
            @include('slivewire-forms::fields.error-help')
        </div>
    </div>
    
Custom views are passed `$field`, `$form_data`, and `$model` variables, as well as any other public component properties.

Example custom view field declaration:

    Field::make('Custom Field')->view('fields.custom-field');

# Array Field Declaration

`ArrayField`s are slightly different than `Field`s. They should only be declared within the field `array()` method.
They have most of the same methods available, except for the `file()` and `array()` methods.
They also have a `columnWidth()` method unavailable to `Field`s.

### `make($name)`

##### `$name`

The name to use for the array field, e.g. `phone_number`. 
Array fields do not use labels. Rather, you should specify a `placeholder()` for them instead.

Example:

    ArrayField::make('phone_number')->input('tel')->placeholder('Phone Number')->rules('required'),

### `columnWidth($width)`

Optional [Bootstrap 4 grid](https://getbootstrap.com/docs/4.4/layout/grid/) column width to use for the array field on desktop. 
If this is not set, the column will uniformly fit in the grid by default.

Example:

    ArrayField::make('province')->select(['AB', 'BC', 'ON'])->placeholder('Province')->columnWidth(4),

You can also use `auto` to have the column auto-fit the array field width:

    ArrayField::make('old_enough')->checkbox()->placeholder('Old Enough')->columnWidth('auto'),

# Publishing Files

Publishing files is optional.

Publishing the form view files:

    php artisan vendor:publish --tag=form-views

Publishing the config file:

    php artisan vendor:publish --tag=form-config

### Credits

- [kdion4891](https://github.com/kdion4891)